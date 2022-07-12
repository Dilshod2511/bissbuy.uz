<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\CustomerWalletHistory;
use Illuminate\Http\Request;
use App\Models\DeliveryPartner;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use App\Models\OrderProduct;
use App\Models\Vendor;

class DeliveryPartnerController extends Controller
{
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'delivery_boy_name' => 'required',
            'phone_number' => 'required|numeric|digits_between:9,20|unique:delivery_partners,phone_number|unique:customers,phone_number|unique:vendors,phone_number',
            'delivery_email' => 'required|email|regex:/^[a-zA-Z]{1}/|unique:delivery_partners,email|unique:customers,email|unique:vendors,email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $options = [
            'cost' => 12,
        ];
        $input['password'] = password_hash($input["password"], PASSWORD_DEFAULT, $options);
        $input['status'] = 1;

        $delivery_boy = DeliveryPartner::create($input);

        if (is_object($delivery_boy)) {
            return response()->json([
                "result" => $delivery_boy,
                "message" => 'Updated Successfully',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Sorry, something went wrong !',
                "status" => 0
            ]);
        }

    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'delivery_boy_name' => 'required',
            'phone_number' => 'required|numeric|unique:delivery_partners,id,'.$id,
            'delivery_email' => 'required|email|unique:delivery_partners,id,'.$id
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        if($request->password){
            $options = [
                'cost' => 12,
            ];
            $input['password'] = password_hash($input["password"], PASSWORD_DEFAULT, $options);
            $input['status'] = 1;
        }else{
            unset($input['password']);
        }

        if (DeliveryPartner::where('id',$id)->update($input)) {
            return response()->json([
                "result" => DeliveryPartner::select('id', 'delivery_boy_name','phone_number','delivery_email','profile_picture','status')->where('id',$id)->first(),
                "message" => 'Success',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Sorry, something went wrong...',
                "status" => 0
            ]);
        }

    }

    public function edit($id)
    {
        $input['id'] = $id;
        $validator = Validator::make($input, [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $result = DeliveryPartner::select('id', 'delivery_boy_name','phone_number','delivery_email','profile_picture','status')->where('id',$id)->first();

        if (is_object($result)) {
            return response()->json([
                "result" => $result,
                "message" => 'Success',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Sorry, something went wrong...',
                "status" => 0
            ]);
        }
    }

    public function login(Request $request){

        $input = $request->all();
        $validator = Validator::make($input, [
            'phone_number' => 'required',
            'password' => 'required|min:6',
            'fcm_token' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $credentials = request(['phone_number', 'password']);
        $delivery_boy = DeliveryPartner::where('phone_number',$credentials['phone_number'])->first();

        if (!($delivery_boy)) {
            return response()->json([
                "message" => 'Invalid phone number or password',
                "status" => 0
            ]);
        }

        if (Hash::check($credentials['password'], $delivery_boy->password)) {
            if($delivery_boy->status == 1){
                DeliveryPartner::where('id',$delivery_boy->id)->update([ 'fcm_token' => $input['fcm_token']]);
                return response()->json([
                    "result" => $delivery_boy,
                    "message" => 'Success',
                    "status" => 1
                ]);
            }else{
                return response()->json([
                    "message" => 'Your account has been blocked',
                    "status" => 0
                ]);
            }
        }else{
            return response()->json([
                "message" => 'Invalid phone number or password',
                "status" => 0
            ]);
        }

    }


    public function profile_picture(Request $request){

        $input = $request->all();
        $validator = Validator::make($input, [
            'delivery_partner_id' => 'required',
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/images');
            $image->move($destinationPath, $name);
            if(DeliveryPartner::where('id',$input['delivery_partner_id'])->update([ 'profile_picture' => 'images/'.$name ])){
                return response()->json([
                    "result" => DeliveryPartner::select('id', 'delivery_boy__name','phone_number','delivery_email','profile_picture','status')->where('id',$input['delivery_partner_id'])->first(),
                    "message" => 'Success',
                    "status" => 1
                ]);
            }else{
                return response()->json([
                    "message" => 'Sorry something went wrong...',
                    "status" => 0
                ]);
            }
        }

    }

    public function forgot_password(Request $request){
    
        $input = $request->all();
        $validator = Validator::make($input, [
            'phone_number' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $delivery_boy = DeliveryPartner::where('phone_number',$input['phone_number'])->first();
        if(is_object($delivery_boy)){
            $otp = rand(1000,9999);
            DeliveryPartner::where('id',$delivery_boy->id)->update(['otp'=> $otp ]);
            //$mail_header = array("otp" => $otp);
            //$this->send_mail($mail_header,'Reset Password',$input['delivery_email']);
            $message = "Hi".env('APP_NAME')." , Your OTP code is:".$otp;
            $this->sendSms('+91'.$input['phone_number'],$message);
            return response()->json([
                "result" => DeliveryPartner::select('id', 'otp')->where('id',$delivery_boy->id)->first(),
                "message" => 'Success',
                "status" => 1
            ]);
        }else{
            return response()->json([
                "message" => 'Invalid phone number',
                "status" => 0
            ]);
        }

    }

    public function reset_password(Request $request){

        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $options = [
            'cost' => 12,
        ];
        $input['password'] = password_hash($input["password"], PASSWORD_DEFAULT, $options);

        if(DeliveryPartner::where('id',$input['id'])->update($input)){
            return response()->json([
                "message" => 'Success',
                "status" => 1
            ]);
        }else{
            return response()->json([
                "message" => 'Invalid phone number',
                "status" => 0
            ]);
        }
    }

    public function change_online_status(Request $request){
        $input = $request->all();
        DeliveryPartner::where('id',$input['id'])->update([ 'online_status' => $input['online_status']]);


        $factory = (new Factory())->withDatabaseUri(env('FIREBASE_DB'));
        $database = $factory->createDatabase();
        $newPost = $database
        ->getReference('delivery_partners/'.$input['id'])
        ->update([
            'online_status' => (int) $input['online_status']
        ]);

        return response()->json([
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function dashboard_details(Request $request){

        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'type' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if($input['type']==1){
            $data = DB::table('orders')
                ->leftJoin('order_products','order_products.order_id','orders.id')
                ->leftJoin('order_statuses','order_statuses.id','orders.status')
                ->leftJoin('customer_addresses', 'customer_addresses.id', '=', 'orders.customer_address_id')
                ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                ->select('orders.*', 'order_statuses.status', 'order_products.product_image','order_products.product_name','order_products.product_price','order_products.qty', 'customers.phone_number', 'customers.first_name','customer_addresses.customer_address')
                ->where('delivered_by',$input['id'])
                ->orderBy('id', 'DESC')
                ->get();
            foreach($data as $key => $value){
            $data[$key]->image = DB::table('order_products')->where('order_id',$value->id)->value('product_image');
            $data[$key]->products = DB::table('order_products')->where('order_id',$value->id)->get();
            $data[$key]->payment_mode = "Cash";
            $data[$key]->customer_name = DB::table('customers')->where('id',$value->customer_id)->value('first_name');
        }
        }if($input['type']==2){
            $result = DB::table('orders')
               ->leftJoin('order_statuses','order_statuses.id','orders.status')
                ->leftJoin('customer_addresses', 'customer_addresses.id', '=', 'orders.customer_address_id')
                ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                ->select('orders.*', 'order_statuses.status', 'customers.phone_number', 'customers.first_name','customer_addresses.customer_address')
                ->where('delivered_by',$input['id'])
                ->where('orders.status','=',4)
                ->orderBy('id', 'DESC')
                ->get();
            foreach($data as $key => $value){
            $data[$key]->image = DB::table('order_products')->where('order_id',$value->id)->value('product_image');
            $data[$key]->products = DB::table('order_products')->where('order_id',$value->id)->get();
            $data[$key]->payment_mode = "Cash";
            $data[$key]->customer_name = DB::table('customers')->where('id',$value->customer_id)->value('first_name');
            }
        }if($input['type']==3){
            $result = DB::table('orders')
               ->leftJoin('order_statuses','order_statuses.id','orders.status')
                ->leftJoin('customer_addresses', 'customer_addresses.id', '=', 'orders.customer_address_id')
                ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                ->select('orders.*', 'order_statuses.status', 'customers.phone_number', 'customers.first_name','customer_addresses.customer_address')
                ->where('delivered_by',$input['id'])
                ->whereDay('orders.created_at', date('d'))
                ->orderBy('id', 'DESC')
                ->get();
            foreach($data as $key => $value){
            $data[$key]->image = DB::table('order_products')->where('order_id',$value->id)->value('product_image');
            $data[$key]->products = DB::table('order_products')->where('order_id',$value->id)->get();
            $data[$key]->payment_mode = "Cash";
            $data[$key]->customer_name = DB::table('customers')->where('id',$value->customer_id)->value('first_name');
            }
        }if($input['type']==4){
            $result = DB::table('orders')
               ->leftJoin('order_statuses','order_statuses.id','orders.status')
                ->leftJoin('customer_addresses', 'customer_addresses.id', '=', 'orders.customer_address_id')
                ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                ->select('orders.*', 'order_statuses.status', 'customers.phone_number', 'customers.first_name','customer_addresses.customer_address')
                ->where('delivered_by',$input['id'])
                ->where('orders.status','!=',4)
                ->orderBy('id', 'DESC')
                ->get();
            foreach($data as $key => $value){
            $data[$key]->image = DB::table('order_products')->where('order_id',$value->id)->value('product_image');
            $data[$key]->products = DB::table('order_products')->where('order_id',$value->id)->get();
            $data[$key]->payment_mode = "Cash";
            $data[$key]->customer_name = DB::table('customers')->where('id',$value->customer_id)->value('first_name');
            }
        }
        //$result['total_bookings'] = Order::where('delivered_by',$input['id'])->count();
        //$result['completed_bookings'] = Order::where('delivered_by',$input['id'])->where('status',4)->count();
        //$result['today_bookings'] = Order::where('delivered_by',$input['id'])->whereDay('created_at', date('d'))->count();
        //$result['pending_bookings'] = Order::where('delivered_by',$input['id'])->where('status','!=',8)->count();

        if($data){
            return response()->json([
                "result" => $data,
                "message" => 'Success',
                "status" => 1
            ]);
        }else{
            return response()->json([
                "message" => 'Something went wrong',
                "status" => 0
            ]);
        }
    }

    public function dashboard(Request $request){

        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $result['total_bookings'] = Order::where('delivered_by',$input['id'])->count();
        $result['completed_bookings'] = Order::where('delivered_by',$input['id'])->where('status',4)->count();
        $result['today_bookings'] = Order::where('delivered_by',$input['id'])->whereDay('created_at', date('d'))->count();
        $result['pending_bookings'] = Order::where('delivered_by',$input['id'])->where('status','!=',4)->count();


        if($result){
            return response()->json([
                "result" => $result,
                "message" => 'Success',
                "status" => 1
            ]);
        }else{
            return response()->json([
                "message" => 'Something went wrong',
                "status" => 0
            ]);
        }

    }


     public function get_my_orders(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'delivery_boy_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $data = DB::table('orders')
                ->leftjoin('customer_addresses','customer_addresses.id','=','orders.customer_address_id')
                ->where('orders.delivered_by',$input['delivery_boy_id'])
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

    public function sendError($message) {
        $message = $message->all();
        $response['error'] = "validation_error";
        $response['message'] = implode('',$message);
        $response['status'] = "0";
        return response()->json($response, 200);
    }


  public function delivery_ready_orders()
    {
        $result = Order::with('vendorInfo', 'customerInfo', 'products')->where('status', 2)->get();

        return response()->json([
            "result" => $result,
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function delivery_received_orders($partner_id)
    {
        $result = Order::with('vendorInfo', 'customerInfo','products')->whereIn('status', [3])->where('delivered_by', $partner_id)->get();

        return response()->json([
            "result" => $result,
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function delivery_archive_orders($partner_id)
    {
        $result = Order::with('vendorInfo', 'customerInfo','products')->whereIn('status', [4])->where('delivered_by', $partner_id)->get();

        return response()->json([
            "result" => $result,
            "message" => 'Success',
            "status" => 1
        ]);
    }



    public function show($id)
    {

    }


    public function receive_order(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'partner_id' => 'required',
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        Order::where('id', $input['order_id'])->update([
            'status' => 3,
            'delivered_by' => $input['partner_id'],
	    'delivery_received_at' => now()
        ]);

        $order = Order::find($input['order_id']);

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



        return response()->json([
            "result" =>Order::find($input['order_id']),
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function complete_order(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'partner_id' => 'required',
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $order = Order::find($input['order_id']);


        if($order->referred_id != 0 && $order->refferred_id != $input['partner_id']){
            $commission = DB::table('app_settings')->value('referral_commission');

            $com = ($commission / 100) * $order->total;
            CustomerWalletHistory::create([ "customer_id" => $order->referred_id, "type" => 2, "message" => "Кредит вашего дохода по этому заказу #".$order->id, "amount" => $com, "order_id" => $order->id ]);
            $old_wallet = DB::table('customers')->where('id',$order->total)->value('wallet');
            $new_wallet = $old_wallet + $com;
            DB::table('customers')->where('id',$order->referred_id)->update([ "wallet" => $new_wallet ]);

        }

        if ($order->delivered_by == $input['partner_id']){
            Order::find($input['order_id'])->update([
                'status' => 4,
            ]);

            $tax_debt = OrderProduct::where('order_id', $order->id)->sum('tax');
            $order->update(['tax'=> $tax_debt, 'debt' => $tax_debt]);
            $vendor = Vendor::find($order->vendor_id);
            $vendor->debt = $vendor->debt + $tax_debt;
            $vendor->save();

            $message = [
                "title" => "Новое сообщение", 
                "body" => "Ваш заказ №$order->id доставлен."
            ];  
            $API_KEY = env('USER_KEY');
            $this->sendNotification($order->customer->fcm_token, $message, $API_KEY);
    
    
            $message = [
                "title" => "Новое сообщение", 
                "body" => "Ваш заказ №$order->id доставлен."
            ];  
            $API_KEY = env('VENDOR_KEY');
            $this->sendNotification($order->vendor->fcm_token, $message, $API_KEY);





            return response()->json([
                "result" => Order::find($input['order_id']),
                "message" => 'Success',
                "status" => 1
            ]);
        }

        return response()->json([
            "message" => 'no access',
            "status" => 0
        ]);


    }



    public function categories()
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();

        return response()->json([
            "result" => $categories,
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function categories_search($search)
    {
        $categories = Category::where('category_name',  'like', '%' . $search . '%' )->with('children')->get();

        return response()->json([
            "result" => $categories,
            "message" => 'Success',
            "status" => 1
        ]);
    }


    public function change_credentials(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'deliveryman_name' => 'required',
            'phone_number' => 'required|numeric|digits_between:9,20',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $input['password'] = password_hash($input["password"], PASSWORD_DEFAULT);
        $input['status'] = 1;

        DeliveryPartner::find($input['id'])->update([
            'delivery_boy_name' => $input['deliveryman_name'],
            'phone_number' => $input['phone_number'],
            'password' => $input['password'],
        ]);

        return response()->json([
            "result" => DeliveryPartner::find($input['id']),
            "message" => 'Updated Successfully',
            "status" => 1
        ]);

    }





    public function delivery_statistics($deliveryman_id)
    {
        
          $year_sum = DB::table('orders')
          ->where( 'created_at', '>', Carbon::now()->subDays(365)) 
          ->where('status', 4)
          ->where('delivered_by', $deliveryman_id)
          ->select(DB::raw('count(id) as count'), DB::raw('sum(total) as total'))
          ->get();

          $month_sum = DB::table('orders')
          ->where( 'created_at', '>', Carbon::now()->subDays(30)) 
          ->where('status', 4)
          ->where('delivered_by', $deliveryman_id)
          ->select(DB::raw('count(id) as count'), DB::raw('sum(total) as total'))
          ->get();

          $week_sum = DB::table('orders')
          ->where( 'created_at', '>', Carbon::now()->subDays(7)) 
          ->where('status', 4)
          ->where('delivered_by', $deliveryman_id)
          ->select(DB::raw('count(id) as count'), DB::raw('sum(total) as total'))
          ->get();

          $day_sum = DB::table('orders')
          ->where( 'created_at', '>', Carbon::now()->subDays(1)) 
          ->where('status', 4)
          ->where('delivered_by', $deliveryman_id)
          ->select(DB::raw('count(id) as count'), DB::raw('sum(total) as total'))
          ->get();

          $year_sum[0]->total = $year_sum[0]->total == null ? $year_sum[0]->total = 0 : $year_sum[0]->total;
          $month_sum[0]->total = $month_sum[0]->total == null ? $month_sum[0]->total = 0 : $month_sum[0]->total;
          $week_sum[0]->total = $week_sum[0]->total == null ? $week_sum[0]->total = 0 : $week_sum[0]->total;
          $day_sum[0]->total = $day_sum[0]->total == null ? $day_sum[0]->total = 0 : $day_sum[0]->total;
            
        $result = [
            'last_year' => $year_sum[0],
            'last_month' => $month_sum[0],
            'last_week' => $week_sum[0],
            'last_day' => $day_sum[0]
        ];

          return response()->json([
            "result" => $result,
            "message" => 'Success',
            "status" => 1
        ]);

    }
    
    public function delivery_info($id)
    {
	 
          $partner = DeliveryPartner::find($id);
          
	  $data = [
		'name' => $partner->delivery_boy_name,
		'phone_number' => $partner->phone_number,
		'orders' => Order::where('delivered_by', $id)->count(),
		'earnings' => Order::where('delivered_by', $id)->where('status', 6)->sum('delivery_amount')
		];
             return response()->json([
            "result" => $data,
            "message" => 'Success',
            "status" => 1
        ]);
    }


    public function logout(Request $request)
    {
            $input = $request->all();
            $validator = Validator::make($input, [
                'fcm_token' => 'required',
                'driver_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            
            $driver = DeliveryPartner::find($id);
            $driver->fcm_token = null;
            $driver->save();
            
            return response()->json([
                "message" => 'Success',
                "status" => 1
            ]);
    }


   

}
