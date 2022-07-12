<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\DeliveryPartner;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\PaymentMode;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\CustomerWalletHistory;
use App\Models\CustomerAddress;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\OrderStatus;
use App\Models\OrderProduct;
use App\Models\OrderProductAttribute;
use App\Models\ProductImage;
use Validator;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Illuminate\Support\Facades\DB;
class OrderController extends Controller
{
    public function payment_modes(){
        $payment_modes = PaymentMode::where('status',1)->get();
        return response()->json([
            "result" => $payment_modes,
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function place_order(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'customer_id' => 'required',
            'vendor_id' => 'required',
            'total' => 'required',
            'order_products' => 'required',
            'end_point_delivery' => 'required',
        ]); 

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vendor = Vendor::find($input['vendor_id']);
        $order_products = is_array($input['order_products']) ? $input['order_products'] : json_decode($input['order_products'], true);

			
	
        $lat_from = $vendor->warehouse_address['lat'];
        $long_from =  $vendor->warehouse_address['long'];
        $lat_to = $input['end_point_delivery'][0]['lat'];
        $long_to = $input['end_point_delivery'][0]['long'];

       
        //$input['distance'] = $this->get_distance($lat_from, $long_from, $lat_to, $long_to);
        
        $input['distance'] = $this->distance($lat_from, $long_from, $lat_to, $long_to, 'K');
        
        $input['delivery_amount'] = $input['distance'] * intval(AppSetting::where('id',1)->value('delivery_charge'));
       // $input['end_point_delivery'] = $input['end_point_delivery'][0];
       
        $input['status'] = 1;

      // dd($input);

        $order = Order::create($input);
        $commission = DB::table('app_settings')->value('referral_commission');
        if(is_object($order)){
            foreach ($order_products as $key => $value) {
                if($value){
                    $value['order_id'] = $order->id;

                    $value['product_image'] = \DB::table('product_images')->where('product_id',$value['product_id'])->first() != null ? \DB::table('product_images')->where('product_id',$value['product_id'])->first()->product_image : 'default.png' ;

                    $product_option_values = isset($value['product_option_values']) ? $value['product_option_values'] : null ;
                    unset($value['product_option_values']);
                    
                    if($value['referred_id'] != 0 && $value['referred_id'] != $input['customer_id']){
                        // $com = ($commission / 100) * $value['total_price'];
                        // //CustomerWalletHistory::create([ "customer_id" => $input['customer_id'], "type" => 2, "message" => "Кредит вашего дохода по этому заказу #".$order->id, "amount" => $com, "order_id" => $order->id ]);
                        // $old_wallet = DB::table('customers')->where('id',$value['referred_id'])->value('wallet');
                        // $new_wallet = $old_wallet + $com;
                        // DB::table('customers')->where('id',$value['referred_id'])->update([ "wallet" => $new_wallet ]);
                        DB::table('orders')->where('id',$order->id)->update([ "referred_id" => $value['referred_id'] ]);
                    }
            
                    $tax_percent = Product::find($value['product_id'])->subcategory->tax;

                    $value['tax'] = ($value['qty'] * $value['product_price'] / 100) * $tax_percent;
               
                    OrderProduct::create($value);
                    if($product_option_values){
                        foreach($product_option_values as $key => $value1){
                            $attribute['order_id'] = $order->id;
                            $attribute['product_id'] = $value['product_id'];
                            $attribute['product_option_id'] = $value1[0];
                            $attribute['product_option_value'] = $value1[1];
                            OrderProductAttribute::create($attribute);
                        }
                    }
                }
            }


           //$order->update(['tax'=> OrderProduct::where('order_id', $order->id)->sum('tax')]);


            $message = [
                "title" => "Новое сообщение", 
                "body" => "У вас новый заказ {$order->id}. Перейдите на страницу моих заказов, чтобы увидеть"
            ];
            $API_KEY = env('VENDOR_KEY');
            $this->sendNotification($vendor->fcm_token, $message, $API_KEY);


            // $factory = (new Factory())->withDatabaseUri(env('FIREBASE_DB'));
            // $database = $factory->createDatabase();

            // $status_name = DB::table('order_statuses')->where('id',1)->value('status');
            // $database->getReference('order/'.$order->id.'/status_name')
            //         ->set($status_name);
            // $database->getReference('order/'.$order->id.'/status')
            //         ->set(1);

            return response()->json([
                "message" => 'Success',
                "status" => 1
            ]);
        }else{
            return response()->json([
                "message" => 'Sorry something went wrong',
                "status" => 0
            ]);
        }
    }

    public function get_order_products(Request $request){

        $input = $request->all();
        $validator = Validator::make($input, [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $order = OrderProduct::where('order_id',$input['order_id'])->get();
        foreach($order as $key => $value){
            $review = DB::table('product_reviews')->where('order_id',$input['order_id'])->where('product_id',$value->product_id)->first();
            if(is_object($review)){
                $order[$key]->review_status = 1;
                $order[$key]->review = $review->review;
                $order[$key]->rating = $review->rating;
            }else{
                $order[$key]->review_status = 0;
                $order[$key]->review = '';
                $order[$key]->rating = '';
            }
        }
        if($order){
            return response()->json([
                "message" => 'Success',
                "result" => $order,
                "status" => 1
            ]);
        }else{
            return response()->json([
                "message" => 'Sorry something went wrong',
                "status" => 0
            ]);
        }
    }

    public function order_accept(Request $request){
        $input = $request->all();
        Order::where('id',$input['order_id'])->update([ 'delivered_by' => $input['delivered_by'], 'status' => 3]);
        $order = Order::where('id',$input['order_id'])->first();
        
        
        $message = [
            "title" => "Новое сообщение", 
            "body" => "Статус вашего заказа №$order->id: В Пути."
        ];  
        $API_KEY = env('USER_KEY');
        $this->sendNotification($order->customer->fcm_token, $message, $API_KEY);


        $message = [
            "title" => "Новое сообщение", 
            "body" => "Статус вашего заказа №$order->id: В Пути."
        ];  
        $API_KEY = env('VENDOR_KEY');
        $this->sendNotification($order->vendor->fcm_token, $message, $API_KEY);


        //$this->update_history($input['order_id']);
        //$factory = (new Factory())->withDatabaseUri(env('FIREBASE_DB'));
        //$database = $factory->createDatabase();
        //$newPost = $database
        //->getReference('delivery_partners/'.$input['delivered_by'])
        //->update([
        //    'booking_status' => 0,
        //    'customer_name' => '',
        //    'order_id' => 0
        //]);
        //$this->find_fcm_message('order_status_'.$order->status,$order->customer_id,$order->vendor_id,0);
        return response()->json([
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function update_history($id){
        $order_count = OrderHistory::where('order_id',$id)->count();
        $new_order = Order::where('id',$id)->first();
        if($order_count == 0){
            OrderHistory::create([ 'order_id'=>$id, 'delivery_partner_id'=>$new_order->delivered_by,'status'=>$new_order->status ]);
            if($new_order->delivered_by){
                $this->update_firebase(0,$new_order->delivered_by,$id);
            }

        }else{
            $last_order_history = OrderHistory::where('order_id',$id)->orderBy('id', 'DESC')->first();
            OrderHistory::create([ 'order_id'=>$id, 'delivery_partner_id'=>$new_order->delivered_by,'status'=>$new_order->status ]);
            if($last_order_history->delivery_boy_id == null && $new_order->delivered_by > 0){
                $this->update_firebase(0,$new_order->delivered_by,$id);
            }else if($last_order_history->delivery_boy_id > 0 && $new_order->delivered_by > 0){
                $this->update_firebase($last_order_history->delivery_boy_id,$new_order->delivered_by,$id);
            }else if($last_order_history->delivery_boy_id > 0 && $new_order->delivered_by == null){
                $this->update_firebase($last_order_history->delivery_boy_id,0,$id);
            }
        }
    }

    public function update_firebase($old_del_boy,$new_del_boy,$id){
        $order = Order::where('id',$id)->first();
        $address = CustomerAddress::where('id',$order->customer_address_id)->first();
        $customer = Customer::where('id',$order->customer_id)->first();
        $vendor = Vendor::where('id',$order->vendor_id)->first();
        $data = array();
        $factory = (new Factory())->withDatabaseUri(env('FIREBASE_DB'));
        $database = $factory->createDatabase();

        if($new_del_boy > 0){
            $data['cus_address'] = $address->google_address;
            $data['cus_address_lat'] = $address->lat;
            $data['cus_address_lng'] = $address->lng;
            $data['cus_name'] = $customer->first_name;
            $data['cus_phone'] = $customer->phone_number;
            $data['vendor_number'] = $vendor->phone_number;
            $data['store_name'] = $vendor->store_name;
            $data['cus_phone'] = $customer->phone_number;
            $data['discount'] = $order->discount;
            $data['id'] = $order->id;
            $data['order_id'] = $order->order_id;
            $data['payment_mode'] = PaymentMode::where('id',$order->payment_mode)->value('payment_name');
            $data['status'] = $order->status;
            $data['status_name'] = OrderStatus::where('id',$order->status)->value('status');
            $data['sub_total'] = $order->sub_total;
            $data['tax'] = $order->tax;
            $data['delivery_charge'] = $order->delivery_charge;
            $data['total'] = $order->total;
            if($order->status != 2){
                $new_label = OrderStatus::where('id',$order->status+1)->first();
                $data['new_status'] = $new_label->id;
                $data['new_status_name'] = $new_label->status;
            }
            $newPost = $database
            ->getReference('delivery_partners/'.$new_del_boy.'/orders/'.$id)
            ->update($data);

        }

        if($old_del_boy > 0 && $old_del_boy != $new_del_boy){
            $database->getReference('delivery_partners/'.$old_del_boy.'/orders/'.$id)->remove();
        }
    }

    public function order_reject(Request $request){
        $input = $request->all();
        Order::where('id',$input['order_id'])->update([ 'delivered_by' => $input['delivered_by'], 'status' => 4]);
        DB::table('partner_rejections')->insert([ 'order_id' => $input['order_id'], 'partner_id' => $input['delivered_by']]);
        $factory = (new Factory())->withDatabaseUri(env('FIREBASE_DB'));
        $database = $factory->createDatabase();
        $newPost = $database
        ->getReference('delivery_partners/'.$input['delivered_by'])
        ->update([
            'booking_status' => 0,
            'customer_name' => '',
            'order_id' => 0
        ]);
        return response()->json([
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function order_status_change(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'order_id' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $order = Order::where('id',$input['order_id'])->first();
        $this->update_customer_status($order->id,$order->status,$order->customer_id);
        if(is_object($order)){
            $old_label = OrderStatus::where('id',$input['status'])->first();
            Order::where('id',$input['order_id'])->update([ 'status' => $old_label->id ]);
            $factory = (new Factory())->withDatabaseUri(env('FIREBASE_DB'));
            $database = $factory->createDatabase();
            $database->getReference('delivery_partners/'.$order->delivered_by.'/orders/'.$order->id.'/status')
                ->set($old_label->id);
            $database->getReference('delivery_partners/'.$order->delivered_by.'/orders/'.$order->id.'/status_name')
                ->set($old_label->label_name);
            if($input['status'] != 4){
                $new_label = OrderStatus::where('id',$input['status']+1)->first();
                $database->getReference('delivery_partners/'.$order->delivered_by.'/orders/'.$order->id.'/new_status')
                ->set($new_label->id);
                $database->getReference('delivery_partners/'.$order->delivered_by.'/orders/'.$order->id.'/new_status_name')
                ->set($new_label->status);
            }

            if($input['status'] == 4){
                $this->commission_calculations($input['order_id']);
            }

            //$this->find_fcm_message('order_status_'.$old_label->id,$order->customer_id,0,0);
            $response['message'] = "Success";
            $response['status'] = 1;
            return response()->json($response, 200);
        }else{
            $response['message'] = "Invalid order id";
            $response['status'] = 0;
            return response()->json($response, 200);
        }

    }

    public function commission_calculations($order_id){
        $order = Order::where('id',$order_id)->first();
        $vendor_percent = $order->vendor_percent;

        $vendor_commission = ($order->sub_total / 100) * $vendor_percent;
        $vendor_commission = number_format((float)$vendor_commission, 2, '.', '');

        $admin_commission = $order->sub_total - $vendor_commission;
        $admin_commission = number_format((float)$admin_commission, 2, '.', '');

        $order_commission['order_id'] = $order_id;
        $order_commission['role'] = 'vendor';
        $order_commission['user_id'] = $order->vendor_id;
        $order_commission['amount'] = $vendor_commission;
        OrderCommission::create($order_commission);

        $order_commission['order_id'] = $order_id;
        $order_commission['role'] = 'admin';
        $order_commission['user_id'] = 1;
        $order_commission['amount'] = $admin_commission;
        OrderCommission::create($order_commission);

        VendorEarning::create([ 'order_id' => $order_id, 'vendor_id' => $order->vendor_id, 'amount' => $vendor_commission]);
        VendorWalletHistory::create([ 'vendor_id' => $order->vendor_id, 'type' => 1, 'message' => 'Кредит вашего дохода по этому заказу #'.$order->order_id, 'amount' => $vendor_commission]);

        $wallet = Vendor::where('id',$order->vendor_id)->value('wallet');
        $new_wallet = $wallet + $vendor_commission;
        $new_wallet = number_format((float)$new_wallet, 2, '.', '');

        Vendor::where('id',$order->vendor_id)->update([ 'wallet' => $new_wallet]);

    }

    public function getVendorOrders(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'vendor_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $data = DB::table('orders')
                ->leftjoin('customer_addresses','customer_addresses.id','=','orders.customer_address_id')
                ->where('orders.vendor_id',$input['vendor_id'])
                ->select('orders.*','customer_addresses.customer_address','customer_addresses.lat','customer_addresses.lng')
                ->orderBy('orders.id', 'DESC')
                ->get();
        foreach($data as $key => $value){
            $data[$key]->image = DB::table('order_products')->where('order_id',$value->id)->value('product_image');
            $data[$key]->products = DB::table('order_products')->where('order_id',$value->id)->get();
            $data[$key]->payment_mode = "Cash";
            $data[$key]->customer_name = DB::table('customers')->where('id',$value->customer_id)->value('first_name');
        }


        $result = $data;
        return response()->json([
            "result" => $result,
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function order_status_update(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'order_id' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $factory = (new Factory())->withDatabaseUri(env('FIREBASE_DB'));
        $database = $factory->createDatabase();

        Order::where('id',$input['order_id'])->update([ 'status' => $input['status']]);

        $status_name = DB::table('order_statuses')->where('id',$input['status'])->value('status');
        $database->getReference('order/'.$input['order_id'].'/status_name')
                ->set($status_name);
        $database->getReference('order/'.$input['order_id'].'/status')
                ->set($input['status']);

        return response()->json([
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function update_customer_status($id,$status,$customer_id){
        $factory = (new Factory())->withDatabaseUri(env('FIREBASE_DB'));
        $database = $factory->createDatabase();
        $newPost = $database
        ->getReference('customers/'.$customer_id.'/orders/'.$id)
        ->update([
            'status' => $status,
        ]);
    }
    public function sendError($message) {
        $message = $message->all();
        $response['error'] = "validation_error";
        $response['message'] = implode('',$message);
        $response['status'] = "0";
        return response()->json($response, 200);
    }


    public function products($category_id)
    {
        $result = Product::where('status', 1)
        ->with('subcategory', 'brand')
        ->where(function($query) use ($category_id){
            $query->where('category_id', $category_id);
            $query->orWhere('subcategory_id', $category_id);
        })
        // ->orderBy('created_at', 'desc')
        ->inRandomOrder()
        ->get();

        foreach($result as $like)
        {
           $price = \DB::table('m_variants_values')->where('product_id', $like->id)->whereNotNull('price')->exists() ? \DB::table('m_variants_values')->where('product_id', $like->id)->whereNotNull('price')->first()->price : $like->product_price;
           $like->product_price = $price;
        }



        $result = $this->customPaginate($result, 12);
	$result = $result->toArray();

	$result['data'] = array_values($result['data']);

	//$result->setCollection(array_values($result->items())) ;
        return response()->json([
            "result" => $result,
            "message" => 'success',
            "status" => 1
        ]);
    }

    
    public function subcategories($text = null)
    {
        if($text != null){
            $categories = Category::whereNotNull('parent_id')->where('category_name', 'like', '%' . $text . '%')->get();
        }
        else{
            $categories = Category::whereNotNull('parent_id')->get();
        }
       

        return response()->json([
            "result" => $categories,
            "message" => 'success',
            "status" => 1
        ]);
    }

    public function vendor_products($vendor_id)
    {
        $result = Product::withoutGlobalScope('active')->with('subcategory')->where('vendor_id', $vendor_id)->where('is_discount', '!=', 1)->select('product_name','id', 'cover_image', 'product_price', 'status', 'subcategory_id')->get();
	$data = [];
	foreach($result as $result)
	{
	    $data[] = [
		'product_name' => $result->product_name,
		'subcategory' => $result->subcategory->category_name,
		'id' => $result->id,
		'product_price' => $result->product_price,
		'status' => $result->status,
		'cover_image' => $result->cover_image
	    ];
	}

        return response()->json([
            "result" => $data,
            "message" => 'success',
            "status" => 1
        ]);
    }

    public function vendor_products_with_discount($vendor_id)
    {
        $result = Product::withoutGlobalScope('active')->with('subcategory')->where('vendor_id', $vendor_id)->where('is_discount', '=', 1)->select('product_name','id', 'cover_image', 'product_price', 'status', 'subcategory_id', 'discount_percent', 'discount_from', 'discount_to', 'discount_price')->get();
	$data = [];
	foreach($result as $result)
	{
	    $data[] = [
		'product_name' => $result->product_name,
		'subcategory' => $result->subcategory->category_name,
		'id' => $result->id,
		'product_price' => $result->product_price,
		'status' => $result->status,
		'cover_image' => $result->cover_image,
		'discount_percent' => $result->discount_percent,
		'discount_from' => $result->discount_from,
		'discount_to' => $result->discount_to,
		'discount_price' => $result->discount_price
	    ];
	}

        return response()->json([
            "result" => $data,
            "message" => 'success',
            "status" => 1
        ]);
    }


    public function products_search(Request $request, $name = null)
    {
       $per_page = $request->input('per-page') != null ? $request->input('per-page') : 20; 
     
       if($name != null){
        $products = Product::inRandomOrder()->where('status', 1)
        ->with('images','brand')
        //->orderBy('created_at', 'desc')
        ->where(function($query) use ($name){
            $query->where('product_name', 'like', '%' . $name . '%');
            $query->orWhere('key_words', 'like', '%' . $name . '%');
        })


       
        // ->where('product_name', 'like', '%' . $name . '%')
        // ->orWhere('key_words', 'like', '%' . $name . '%')
        ->get();
       }else{
        $products = Product::inRandomOrder()->active()->with('brand')
        // ->orderBy('created_at', 'desc')
        ->paginate($per_page);
       }
      
        return response()->json([
            "result" => $products,
            "message" => 'success',
            "status" => 1
        ]);
    }

    public static function get_distance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
      {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
      
        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
          pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
      
        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
      }
      
            function distance($lat1, $lon1, $lat2, $lon2, $unit) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
          return 0;
        }
        else {
          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $unit = strtoupper($unit);
      
          if ($unit == "K") {
            return ($miles * 1.609344);
          } else if ($unit == "N") {
            return ($miles * 0.8684);
          } else {
            return $miles;
          }
        }
      }
      
      public function getProduct($id)
      {
          $product = Product::with('category','subcategory', 'brand', 'vendor')->find($id);
          
         
          
         
          $images = $product->images->pluck('product_image')->toArray();
          
          $result = [
              'id' => $product->id,
              'category' => $product->category,
              'subcategory' => $product->subcategory,
              'brand' => $product->brand,
              'vendor' => Vendor::where('id', $product->vendor_id)->select('id', 'vendor_name', 'store_image')->first(),
              'product_name' => $product->product_name,
              'total_comment' => $product->total_comment,
              'total_view' => $product->total_view,
              'total_sharing' => $product->total_sharing,
              'total_like' => $product->total_like,
              'tags' => json_decode($product->tags, true),
              'product_price' => $product->product_price,
              'short_description' => $product->short_description,
              'images' => $images,
              'type' => $product->type,
              'discount_from' => $product->discount_from,
              'discount_to' => $product->discount_to,
              'discount_price' => $product->discount_price,
              'discount_percent' => $product->discount_percent,
              'var_type' => $product->var_type
          ];
          
          
          return response()->json([
            "result" => $result,
            "message" => 'success',
            "status" => 1
        ]);
      }

	public function cancel_order(Request $request)
	{
	$input = $request->all();
        $validator = Validator::make($input, [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

	Order::find($input['order_id'])->update([
	'status' => 5,
	'canceled_at' => now()
	]);

	 return response()->json([
            "message" => 'success',
            "status" => 1
        ]);


	}










}
