<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CustomerFavouriteProduct;
use App\Models\AppSetting;
use App\Models\PaymentMode;
use App\Models\PromoCode;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderProduct;
// use App\Models\CustomerAddress;
use Redirect;
use Auth;
use Validator;
use Session;
use Input;
use DateTime;
use DateInterval;
use DatePeriod;
use Mail;
class WebController extends Controller
{
    public function index(){
//        $currency = AppSetting::where('id',1)->value('default_currency');
//        $data = Category::where('parent_id',NULL)->get();
//         $top_liked = DB::table('products')
//            ->leftJoin('customer_favourite_products', 'customer_favourite_products.product_id', '=', 'products.id')
//            ->select('products.id','products.product_name','products.cover_image','products.short_description','products.product_price')
//            ->where('products.status', 1)
//            ->orderBy('id', 'DESC')->get()->take(4);;
//        return view('home', ['categories' => $data, 'top_liked' => $top_liked,'currency' => $currency]);

        return view('landing');
    }


    public function shop(){
        $currency = AppSetting::where('id',1)->value('default_currency');
        $categories = Category::select('id','category_name')->where('parent_id',NULL)->get();
        $products = Product::where('category_id',4)->get();
         foreach($categories as $key => $value){
        $categories[$key]['sub_categories'] = Category::where('parent_id',$value->id)->get()->toArray();
        }
        return view('shop', ['categories' => $categories, 'products' => $products,'currency' => $currency, 'category_id' => 4]);

    }

     public function shop_by_category($id){
        $currency = AppSetting::where('id',1)->value('default_currency');
        $categories = Category::select('id','category_name')->where('parent_id',NULL)->get();
        $category = Category::select('id','category_name')->where('parent_id',$id)->first();
        $products = Product::where('category_id',$category['id'])->get();
         foreach($categories as $key => $value){
        $categories[$key]['sub_categories'] = Category::where('parent_id',$value->id)->get()->toArray();
        }
        return view('shop', ['categories' => $categories, 'products' => $products,'currency' => $currency, 'category_id' => $category['id']]);

    }
     public function shop_by_subcategory($id){
        $currency = AppSetting::where('id',1)->value('default_currency');
        $categories = Category::select('id','category_name')->where('parent_id',NULL)->get();
        $products = Product::where('category_id',$id)->get();
         foreach($categories as $key => $value){
        $categories[$key]['sub_categories'] = Category::where('parent_id',$value->id)->get()->toArray();
        }
        return view('shop', ['categories' => $categories, 'products' => $products,'currency' => $currency,  'category_id' => 0]);

    }

    public function profile()
    {   if(Auth::user() == []){
              return view('login',['message' => 'Please Login to Continue']);
        }else{
        $profile = Customer::where('id', Auth::user()->id)
            ->first();
        $currency = AppSetting::where('id', 1)->value('default_currency');
        $addresses = CustomerAddress::where('customer_id', Auth::user()->id)
            ->get();

        return view('my_accounts', ['profile' => $profile, 'currency' => $currency, 'addresses' => $addresses]);
        }
    }

    public function product_details($id){
        $currency = AppSetting::where('id',1)->value('default_currency');
        $customer = Customer::where('id',Auth::user()->id)->first();
        $product = DB::table('products')
            ->leftJoin('vendors', 'vendors.id', '=', 'products.vendor_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->where('products.id',$id)
            ->select('products.*', 'brands.brand_name','vendors.vendor_name', 'categories.category_name')
            ->first();
        //$product = Product::where('id',$input['product_id'])->first();
        $favourite = CustomerFavouriteProduct::where('product_id',$id)->where('customer_id', $customer->id)->first();
        if($favourite){
            $product->is_like = 1;
        }else{
            $product->is_like = 0;
        }
        $product->option_values = DB::table('product_attributes')
                                  ->join('product_options','product_options.id','=','product_attributes.option_id')
                                  ->select('product_attributes.*','product_options.option_name','product_options.option_code')
                                  ->where('product_attributes.product_id',$id)->get();
                                  // print_r($product); exit;
        return view('detail', ['data' => $product,'currency' => $currency]);

    }
    public function showLogin()
    {
        return view('login');
    }

    public function showRegister()
    {
        return view('register');
    }

    public function doLogout()
    {
        Auth::logout(); // logging out user
        Session::put('cart', []);
            Session::put('promo', '');
            Session::put('sub_total', 0);
            Session::put('vendor_id', 0);
        return Redirect::to('login'); // redirection to login screen
    }

    public function doRegister(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
          'first_name' => 'required', // make sure the email is an actual email
          'last_name' => 'required', // make sure the email is an actual email
          'phone_number' => 'required|numeric|digits_between:9,20|unique:customers,phone_number',
          'email' => 'required|email|regex:/^[a-zA-Z]{1}/|unique:customers,email',
          'password' => 'required',
        ]);
        $w_password =  $input['password'];
        if ($validator->fails()) {
            return Redirect::to('register')->withErrors($validator)
            ->withInput(Input::except('password'));
        }
        else
        {
            $options = [
                'cost' => 12,
            ];
            $password = $input["password"];
            $input['password'] = password_hash($password, PASSWORD_DEFAULT, $options);
            $input['status'] = 1;
            $input['fcm_token'] = "websiteuser";
            $customer = Customer::create($input);

            if(is_object($customer)) {
                //$this->register_mail($customer->id,$w_password);
                $userdata = array(
                  'phone_number' => $input['phone_number'],
                  'password' => $password
                );
                // attempt to do the login
                if (Auth::attempt($userdata))
                {
                  return Redirect::to('shop');
                }
                else
                {
                  return view('login');
                }
            }
        }
    }

    public function doLogin(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
          'phone_number' => 'required|numeric',
          'password' => 'required'
        ]);
        if ($validator->fails()) {
            return Redirect::to('login')->withErrors($validator)
            ->withInput(Input::except('password'));
        }
        else
        {
            $userdata = array(
              'phone_number' => $input['phone_number'],
              'password' => $input['password']
            );
            // attempt to do the login
            if (Auth::attempt($userdata))
            {
              return Redirect::to('shop');
            }
            else
            {
              return view('login',['message' => 'Invalid Phone number or password']);
            }
        }
    }

    public function profile_update(Request $request)
    {
        $input = $request->all();
        $id = $input['customer_id'];
        unset($input['customer_id']);
        unset($input['_token']);
        if($request->password){
            $options = [
                'cost' => 12,
            ];
            $input['password'] = password_hash($input["password"], PASSWORD_DEFAULT, $options);
            $input['status'] = 1;
        }else{
            unset($input['password']);
        }
        $update = Customer::where('id',$id)->update($input);
        return $update;
    }

    public function profile_image(Request $request)
    {
        $input = $request->all();
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/images');
            $image->move($destinationPath, $name);
            $image_path = 'images/'.$name;

            $update = Customer::where('id',$input['id'])->update([ 'profile_picture' => $image_path ]);
            return $update;
        }
    }

    public function cart()
    {
        if(Auth::user() == []){
              return view('login',['message' => 'Please Login to Continue']);
        }else{
       $payment_modes = PaymentMode::where('status',1)->get();
       $promo_codes = PromoCode::where('status',1)->get();
       $currency = AppSetting::where('id',1)->value('default_currency');
       $addresses = CustomerAddress::where('customer_id',Auth::user()->id)->get();
       $sub_total = Session::get('sub_total', 0);
       $promo = Session::get('promo', '');
       $total = 0;
       $promo_amount = 0;
       $error = '';
       $promo_id = 0;
       $order_count = Order::where('customer_id',Auth::id())->count();
       $delivery_cost =AppSetting::where('id',1)->value('delivery_charge');
        if($promo == ''){
            $total = $sub_total + $delivery_cost;
        }else{
            $promo_details = PromoCode::where('promo_code',$promo)->first();
            $promo_id = $promo_details->id;
            if($promo_details->min_amount == 0){
                if($promo_details->discount_type == 1){
                    $discount = $sub_total - $promo_details->amount;
                    if($discount > 1){
                        $promo_amount = $promo_details->amount;
                        $total = $discount + $delivery_cost;
                    }else{
                        $promo_amount = 0;
                        $total = $sub_total + $delivery_cost;
                        $error = 'Sorry this promo code not applicable';
                    }
                }else{
                    $discount = ($promo_details->amount /100) * $sub_total;
                    $promo_amount = $discount;
                    $total = ($sub_total - $discount) + $delivery_cost;
                }
            }else{
                if($sub_total >= $promo_details->min_amount){
                    if($promo_details->discount_type == 1){
                      $discount = $sub_total - $promo_details->amount;
                      if($discount >= 1){
                        $promo_amount = $promo_details->amount;
                        $total = $discount + $delivery_cost;
                      }else{
                        $promo = '';
                        $error = 'Sorry this promo code not applicable';
                      }
                    }else{
                      $discount = ($promo_details->amount / 100) * $sub_total;
                      $promo_amount = $discount;
                      $total = ($sub_total - $discount) + $delivery_cost;
                    }
                }else{
                    $promo_amount = 0;
                    $total = $sub_total + $delivery_cost;
                    $error = 'Sorry you are not eligible for this offer';
                    $promo = '';
                }
            }
       }
       $promo_amount = Session::put('discount', $promo_amount);

       // dump($payment_modes);
       // die();

       return view('cart',[ 'currency' => $currency, 'sub_total' => $sub_total, 'total'=>$total,'promo_codes'=>$promo_codes, 'promo_amount' => $promo_amount, 'error' => $error, 'promo' => $promo, 'promo_id' => $promo_id, 'addresses' => $addresses, 'payment_modes' => $payment_modes,'delivery_cost' => $delivery_cost ]);
   }
    }

      public function apply_promo(Request $request)
    {
        Session::put('promo', $request->promo_code);
        $data = $this->coupon_apply_ajax();
        return json_encode($data);
    }

    public function remove_promo(Request $request)
    {
        Session::put('promo','');
        $data = $this->coupon_apply_ajax();
        return json_encode($data);
        //return 1;
    }


    public function coupon_apply_ajax()
    {
       $currency = AppSetting::where('id',1)->value('default_currency');
       $sub_total = Session::get('sub_total', 0);
       $promo = Session::get('promo', '');
       $total = 0;
       $promo_amount = 0;
       $promo_id = 0;
       $order_count = Order::where('customer_id',Auth::id())->count();
       $order_count = 1;
        $data['sub_total'] = $sub_total;
        $data['total'] = $total;
        $data['promo_amount'] = $promo_amount;
        $data['promo_id'] = '';
        $data['error'] = 0;
        $data['currency'] = $currency;
        $data['delivery_cost'] = AppSetting::where('id',1)->value('delivery_charge');
        $delivery_cost = AppSetting::where('id',1)->value('delivery_charge');
        if($promo == ''){
            $data['total'] = $sub_total + $delivery_cost;
        }else{
            $promo_details = PromoCode::where('promo_code',$promo)->first();
            $promo_id = $promo_details->id;
            $data['promo_id'] = $promo_id;
            if($promo_details->min_amount == 0){
                if($promo_details->discount_type == 1){
                    $discount = $sub_total - $promo_details->amount;
                    if($discount > 1){
                        $data['promo_amount'] = number_format($promo_details->amount, 2, '.', '');
                        // $data['promo_amount'] = number_format((float)$promo_details->amount, 2, '.', '');
                        $data['total'] = $discount + $delivery_cost;
                    }else{
                        $data['promo_amount'] = 0;
                        $data['total'] = $sub_total;
                        $data['error'] = 1;
                    }
                }else{
                    $discount = ($promo_details->amount /100) * $sub_total;
                    $data['promo_amount'] = number_format($discount, 2, '.', '');
                    // $data['promo_amount'] = number_format((float)$discount, 2, '.', '');
                    $data['total'] = ($sub_total - $discount)+$delivery_cost;
                }
            }else{
                if($sub_total >= $promo_details->min_amount){
                    if($promo_details->discount_type == 1){
                      $discount = $sub_total - $promo_details->amount;
                      if($discount >= 1){
                        $data['promo_amount'] = number_format($promo_details->amount, 2, '.', '');
                        // $data['promo_amount'] = number_format((float)$promo_details->discount, 2, '.', '');
                        $data['total'] = $discount + $delivery_cost;
                      }else{
                        $promo = '';
                         $data['error'] = 1;
                      }
                    }else{
                      $discount = ($promo_details->amount / 100) * $sub_total;
                      $data['promo_amount'] = number_format($discount, 2, '.', '');
                      // $data['promo_amount'] = number_format((float)$discount, 2, '.', '');
                      $data['total'] = ($sub_total - $discount) + $delivery_cost;
                    }
                }else{
                    $data['promo_amount'] = 0;
                    $data['total'] = $sub_total + $delivery_cost;
                    $data['error'] = 1;
                    $promo = '';
                }
            }
       }
        Session::put('total', $data['total']);
        Session::put('discount', $data['promo_amount']);

       return $data;
       /*return view('cart',[ 'currency' => $currency, 'sub_total' => $sub_total, 'total'=>$total, 'promo_codes'=>$promo_codes, 'promo_amount' => $promo_amount, 'error' => $error, 'promo' => $promo, 'promo_id' => $promo_id, 'addresses' => $addresses, 'payment_modes' =>$payment_modes ]);*/
    }


    public function add_to_cart(Request $request)
    {
        // $product = Product::where('id',$request->product_id)->first();
         $product = DB::table('products')
            ->leftJoin('vendors', 'vendors.id', '=', 'products.vendor_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->where('products.id',$request->product_id)
            ->select('products.*', 'brands.brand_name','vendors.vendor_name', 'categories.category_name', 'categories.id as  category_id')
            ->first();
        $data = array();
        $cart = Session::get('cart', []);
        if(count($cart)>1){
            if(Session::get('vendor_id') == $product->vendor_id){
               if($request->qty > 0){
                $data['category_id'] = $product->category_id;
                $data['product_id'] = $request->product_id;
                $data['product_name'] = $product->product_name;
                $data['category_name'] = $product->category_name;
                $data['image'] = $product->cover_image;
                $data['qty'] = $request->qty;
                $data['price'] = $request->price;
                $data['total_price'] = $request->qty * $request->price;
                $cart[$request->category_id.'-'.$request->product_id] = $data;
            }else{
                unset($cart[$request->category_id.'-'.$request->product_id]);
            }
            Session::put('cart', $cart);
            $sub_total = 0;
            foreach ($cart as $key => $value) {
                $sub_total = $sub_total + $value['price'];
            }
            Session::put('sub_total', $sub_total);
        }else{
            return 0;
        }
        }else{
           if($request->qty > 0){
                 $data['category_id'] = $product->category_id;
                $data['product_id'] = $request->product_id;
                $data['product_name'] = $product->product_name;
                $data['category_name'] = $product->category_name;
                $data['image'] = $product->cover_image;
                $data['qty'] = $request->qty;
                $data['price'] = $request->price;
                $data['total_price'] = $request->qty * $request->price;
                $cart[$request->category_id.'-'.$request->product_id] = $data;
            }else{
                unset($cart[$request->category_id.'-'.$request->product_id]);
            }
            Session::put('cart', $cart);
            $sub_total = 0;
            foreach ($cart as $key => $value) {
                $sub_total = $sub_total + $value['price'];
            }
            Session::put('sub_total', $sub_total);
        }

        Session::put('vendor_id', $product->vendor_id);
        $cart_count = count(Session::get('cart', []));

        return $cart_count;

    }


    public function add_item_to_cart(Request $request)
    {
       $currency = AppSetting::where('id',1)->value('default_currency');
       $product = DB::table('products')
            ->leftJoin('vendors', 'vendors.id', '=', 'products.vendor_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->where('products.id',$request->product_id)
            ->select('products.*', 'brands.brand_name','vendors.vendor_name', 'categories.category_name', 'categories.id as  category_id')
            ->first();
    $delivery_cost = AppSetting::where('id',1)->value('delivery_charge');
        $data = array();
        $data_final = array();
        $cart = Session::get('cart', []);
        if($request->qty > 0){
            $data['category_id'] = $product->category_id;
            $data['product_id'] = $request->product_id;
            $data['product_name'] = $product->product_name;
            $data['category_name'] = $product->category_name;
            $data['image'] = $product->cover_image;
            $data['qty'] = $request->qty;
            $data['price'] = $request->price;
            $data['total_price'] = $request->qty * $request->price;
            $cart[$request->category_id.'-'.$request->product_id] = $data;
        }else{
            unset($cart[$request->category_id.'-'.$request->product_id]);
        }
        Session::put('cart', $cart);
        $sub_total = 0;
        foreach ($cart as $key => $value) {
            $sub_total = $sub_total + $value['total_price'];
        }
        Session::put('sub_total', $sub_total);
        $cart_count = count(Session::get('cart', []));
        $total_item_price = $request->qty * $request->price;
        $data_final['qty'] = $request->qty;
        $data_final['total_item_price'] = $total_item_price;
        $data_final['currency'] = $currency;
        $data_final['sub_total'] = $sub_total;
        $total = $sub_total + $delivery_cost;
        Session::put('total', $total);
        $data_final['total'] = $total;
        return json_encode($data_final);
    }


    public function remove_from_cart(Request $request)
{
    $input = $request->all();
    $cart = Session::get('cart', []);
    unset($cart[$request->category_id.'-'.$request->product_id]);
    Session::put('cart', $cart);
    $delivery_cost = AppSetting::where('id',1)->value('delivery_charge');
    $sub_total = 0;
    foreach ($cart as $key => $value) {
        $sub_total = $sub_total + $value['total_price'];
    }
    Session::put('sub_total', $sub_total);
    $data['sub_total'] = $sub_total;
    $data['delivery_cost'] = AppSetting::where('id',1)->value('delivery_charge');
    $total = $sub_total + $delivery_cost;
    $data['total'] = $total;
    Session::put('total', $total);
    $cart_count = count(Session::get('cart', []));
        return json_encode($data);
    }

    public function checkout_page()
    {
       $currency = AppSetting::where('id',1)->value('default_currency');
       $customer = Customer::where('id',Auth::user()->id)->first();

       $payment_modes = PaymentMode::where('status',1)->get();
       $addresses = CustomerAddress::where('customer_id',Auth::user()->id)->get();
       $sub_total = Session::get('sub_total', 0);
       $promo = Session::get('promo', '');
       $total =  Session::get('total', 0);
       $promo_amount = Session::get('discount', 0);
       $order_count = Order::where('customer_id',Auth::id())->count();
        $delivery_cost = AppSetting::where('id',1)->value('delivery_charge');
       return view('checkout',[ 'data' => $customer, 'currency' => $currency, 'sub_total' => $sub_total, 'total'=>$total,  'addresses' => $addresses, 'payment_modes' => $payment_modes, 'promo_amount' => $promo_amount, 'delivery_cost' => $delivery_cost, 'promo_id' => $promo]);
    }

    public function save_address(Request $request)
    {
        $input = $request->all();
    $pin_url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($input['lat']).','.trim($input['lng']).'&sensor=false&key='.env('MAP_KEY');
      // return $url;
      $json = @file_get_contents($pin_url);
      $data=json_decode($json);
      $var = $data->results[0]->address_components;
       foreach($var as $key => $value) {
          if($value->types[0]=='postal_code'){
           $input['post_code'] = $value->long_name;
           }else{
             $input['post_code'] ='';
           }
          }
        unset($input['_token']);
        $url = 'https://maps.googleapis.com/maps/api/staticmap?center='.$input['lat'].','.$input['lng'].'&zoom=16&size=600x300&maptype=roadmap&markers=color:red%7Clabel:L%7C'.$input['lat'].','.$input['lng'].'&key='.env('MAP_KEY');
            $img = 'static_map/'.md5(time()).'.png';
            file_put_contents('uploads/'.$img, file_get_contents($url));

        $input['static_map'] = $img;
        $input['status'] = 1;

        if ($address = CustomerAddress::create($input)) {
            return json_encode($address);
        } else {
            return 0;
        }
    }

      public function edit_address(Request $request)
    {
        $input = $request->all();
        $id = $input['address_id'];
        unset($input['address_id']);
        unset($input['_token']);

        $url = 'https://maps.googleapis.com/maps/api/staticmap?center=' . $input['lat'] . ',' . $input['lng'] . '&zoom=16&size=600x300&maptype=roadmap&markers=color:red%7Clabel:S%7C' . $input['lat'] . ',' . $input['lng'] . '&key=' . env('MAP_KEY');
        $img = 'static_map/' . md5(time()) . '.png';
        file_put_contents('uploads/' . $img, file_get_contents($url));

        $input['static_map'] = $img;
        $input['status'] = 1;

        if (CustomerAddress::where('id', $id)->update($input))
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function address_delete(Request $request)
    {
        $input = $request->all();

        $res = CustomerAddress::where('id', $input['address_id'])->delete();
        if ($res)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }


    public function place_order(Request $request)
    {
        $input = $request->all();
        $items_data = array();
        $i=0;
        foreach (Session::get('cart', []) as $key => $value) {
            $items_data[$i] = $value;
            $i++;
        }
        $encode_items = json_encode($items_data,true);
        $promo_id = PromoCode::where('promo_code',$input['promo_id'])->first();
        if(isset($promo_id )){
            $input['promo_id'] =  $promo_id->id;
        }else{
            $input['promo_id'] =  0;
        }
        $items = json_decode($encode_items, true);
        $input['status'] =  1;
        $order = Order::create($input);
        $customer_details = Customer::where('id',$input['customer_id'])->first();
        if(is_object($order)) {
            // $this->order_registers($order->id);
            foreach ($items as $key => $value) {
                $value['order_id'] = $order->id;
                $value['product_id'] = $value['product_id'];
                $value['product_name'] = $value['product_name'];
                $value['product_price'] = $value['price'];
                $value['product_image'] = $value['image'];
                $value['qty'] = $value['qty'];
                $value['total_price'] = $value['total_price'];

                OrderProduct::create($value);
            }
            Session::put('cart', []);
            Session::put('promo', '');
            Session::put('total', '');
            Session::put('discount', '');
            Session::put('sub_total', 0);
            return 1;
        } else {
            return 0;
        }
    }

    public function forgot_password()
    {
        return view('forgot_password');
    }

    public function generate_otp(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
          'email' => 'required|email'
        ]);
        if ($validator->fails()) {
            return Redirect::to('forgot_password')->withErrors($validator);
        }
        $customer = Customer::where('email',$input['email'])->first();
        $app_setting = AppSetting::where('id',1)->first();

        $data = array();
        $data['logo'] = $app_setting->logo;
        $data['name'] = $customer->customer_name;
        //$mail_header = array("data" => $data);
        if(is_object($customer)){
            $otp = rand(1000,9999);
            $data['otp'] = $otp;
            $mail_header = array("data" => $data);
            Customer::where('id',$customer->id)->update(['otp'=> $otp ]);
            $message ="Trouble signing in?";
            $this->send_mail_to_customer($mail_header,$message,$input['email']);
            return view('otp_page', [ 'otp' => $otp, 'id' => $customer->id ]);
        }
        else{
            return view('forgot_password');
        }
     }

    public function reset_password(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'customer_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        return view('reset_password', ['id' => $input['customer_id']]);
    }

    public function update_password(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'customer_id' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $options = [
            'cost' => 12,
        ];
        $customer_id = $input['customer_id'];
        unset($input['_token']);
        unset($input['con_password']);
        unset($input['customer_id']);

        $input['password'] = password_hash($input["password"], PASSWORD_DEFAULT, $options);

        if(Customer::where('id',$customer_id)->update($input)){
             return Redirect::to('login');
        }
    }

    public function order()
    {
        $currency = AppSetting::where('id',1)->value('default_currency');
        $my_orders = DB::table('orders')
            ->join('customer_addresses', 'customer_addresses.id', '=', 'orders.customer_address_id')
            ->join('order_statuses', 'order_statuses.id', '=', 'orders.status')
            ->select('orders.id','orders.promo_id','customer_addresses.google_address','orders.total','orders.discount','orders.sub_total','orders.tax','orders.status','orders.created_at','orders.updated_at','order_statuses.status as status_name')
            ->where('orders.customer_id',Auth::user()->id)
            ->orderBy('orders.created_at', 'desc')
            ->get();
            // print_r($my_orders); exit;
        return view('order',['orders_list' => $my_orders , 'currency' => $currency]);
    }


    public function order_detail($id)
    {
       $currency = AppSetting::where('id',1)->value('default_currency');
       $order_status = OrderStatus::all();
        $my_orders = DB::table('orders')
            ->join('customer_addresses', 'customer_addresses.id', '=', 'orders.customer_address_id')
            ->join('order_statuses', 'order_statuses.id', '=', 'orders.status')
            ->select('orders.id','orders.promo_id','customer_addresses.google_address','orders.total','orders.discount','orders.sub_total','orders.tax','orders.status','orders.created_at','orders.updated_at','order_statuses.status as status_name')
            ->where('orders.id',$id)
            ->first();
        $my_order_items = OrderProduct::where('order_id',$my_orders->id)->get();
            // print_r($my_orders); exit;
        return view('order_detail',['order_detail' => $my_orders , 'items' => $my_order_items , 'order_status' => $order_status , 'currency' => $currency]);
    }


}
