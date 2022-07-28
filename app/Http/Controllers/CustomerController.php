<?php

namespace App\Http\Controllers;

    use App\Models\OrderProduct;
    use App\Models\AppSetting;
    use Illuminate\Http\Request;
    use App\Models\Customer;
    use App\Models\Wishlist;
    use App\Models\CustomerFavouriteProduct;
    use App\Models\CustomerViewedProduct;
    use App\Models\CustomerSharedProduct;
    use App\Models\Vendor;
    use App\Models\Order;
    use App\Models\Product;
    use App\Models\Category;
    use App\Models\HomeSlider;
    use App\Models\PaymentMode;
    use App\Models\PromoCode;
    use App\Models\Brand;

    use App\Models\ProductReview;
    use App\Models\CustomerWalletHistory;
    use Validator;
    use LaravelFCM\Message\OptionsBuilder;
    use LaravelFCM\Message\PayloadDataBuilder;
    use LaravelFCM\Message\PayloadNotificationBuilder;
    use FCM;
    use App\FcmNotification;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\DB;
    use Kreait\Firebase;
    use Kreait\Firebase\Factory;
    use Kreait\Firebase\ServiceAccount;
    use Kreait\Firebase\Database;

    class CustomerController extends Controller
    {
        public function register(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'first_name' => 'required',
                'phone_number' => 'required|numeric|digits_between:9,20',
                'password' => 'required',
                'fcm_token' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $options = [
                'cost' => 12,
            ];
            $input['password'] = password_hash($input["password"], PASSWORD_DEFAULT, $options);
            $input['status'] = 1;

            $customer = Customer::create($input);

            if (is_object($customer)) {
                // $this->update_status($customer->id,$customer->status);
                return response()->json([
                    "result" => $customer,
                    "message" => 'Registered Successfully',
                    "status" => 1
                ]);
            } else {
                return response()->json([
                    "message" => 'Sorry, something went wrong !',
                    "status" => 0
                ]);
            }

        }

        public function check_phone(Request $request){

            $input = $request->all();
            $validator = Validator::make($input, [
                'phone_number' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            $data = array();
            $customer = Customer::where('phone_number',$input['phone_number'])->first();

            if(is_object($customer)){
                $data['is_available'] = 1;
                $data['otp'] = "";
                return response()->json([
                    "result" => $data,
                    "message" => 'Success',
                    "status" => 1
                ]);
            }else{
                $data['is_available'] = 0;
                $data['otp'] = rand(1000,9999);
                $message = "Hi".env('APP_NAME'). "  , Your OTP code is:".$data['otp'];
                //$message = "Hi Esycab"." , Your OTP code is:".$data['otp'];
                $this->sendSms('+998'.$input['phone_number'],$message);
                return response()->json([
                    "result" => $data,
                    "message" => 'Success',
                    "status" => 1
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

            $result = Customer::select('id', 'first_name', 'last_name', 'phone_number','email','profile_picture','status')->where('id',$id)->first();

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

        public function profile_update(Request $request)
        {
            $input = $request->all();
            $id = $input['id'];
            $validator = Validator::make($input, [
                'first_name' => 'required',
                'phone_number' => 'required|numeric|unique:customers,phone_number,'.$id,

            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }


            if (Customer::where('id',$id)->update([
                'first_name' => $request->first_name,
                'phone_number' => $request->phone_number
                ])) {
                return response()->json([
                    "result" => Customer::select('id', 'first_name','last_name','phone_number','email','profile_picture','status')->where('id',$id)->first(),
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

        public function get_profile(Request $request){

            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $customer = Customer::where('id',$input['customer_id'])->first();

            $shared_count = CustomerSharedProduct::where('customer_id', $customer->id)->count();
            $delivered = Order::where('customer_id', $customer->id)->where('status', 4)->count();
            $cancelled = Order::where('customer_id', $customer->id)->where('status', 5)->count();

            if(is_object($customer)){
                $result = [
                    $customer,
                    'shared' => $shared_count,
                    'delivered' => $delivered,
                    'cancelled' => $cancelled
                ];

                return response()->json([
                    "result" => $result,
                    "message" => 'Success',
                    "status" => 1
                ]);
            }
            else{
                return response()->json([
                    "message" => 'Something went wrong',
                    "status" => 0
                ]);
            }
        }

        public function login(Request $request){

            $input = $request->all();
            $validator = Validator::make($input, [
                'phone_number' => 'required',
                'password' => 'required',
                'fcm_token' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $credentials = request(['phone_number', 'password']);
            $customer = Customer::where('phone_number',$credentials['phone_number'])->first();


            if (!($customer)) {
                return response()->json([
                    "message" => 'Invalid phone number or password',
                    "status" => 0
                ]);
            }

            if (Hash::check($credentials['password'], $customer->password)) {
                if($customer->status == 1){
                    Customer::where('id',$customer->id)->update([ 'fcm_token' => $input['fcm_token']]);
                    return response()->json([
                        "result" => $customer,
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
                'customer_id' => 'required',
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
                if(Customer::where('id',$input['customer_id'])->update([ 'profile_picture' => 'images/'.$name ])){
                    return response()->json([
                        "result" => Customer::select('id', 'first_name','last_name','phone_number','email','profile_picture','status')->where('id',$input['customer_id'])->first(),
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
                'phone_number' => 'required',
                'password' => 'required',
                'fcm_token' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $password =  \Hash::make($input['password']);


            if(Customer::where('phone_number',$input['phone_number'])->update([
                'password' => $password,
                'fcm_token' => $request->fcm_token
                ])){
                return response()->json([
                    "result" =>  Customer::where('phone_number',$input['phone_number'])->first()->id ,
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
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $options = [
                'cost' => 12,
            ];
            $input['password'] = password_hash($input["password"], PASSWORD_DEFAULT, $options);

            if(Customer::where('id',$input['id'])->update($input)){
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

        public function home_banners()
        {

            $data = HomeSlider::where('status',1)->select('slider_image as url')->get();

            $sliders = [];
            foreach($data as $key => $value){
                $sliders[$key]['url'] = env('IMG_URL').$value->url;
            }
                return response()->json([
                "result" => $sliders,
                "message" => 'Success',
                "status" => 1
            ]);
        }

        public function get_categories($category_name = null){

           if($category_name != null){

                 $data = Category::where('parent_id',NULL)->where('category_name', 'like', '%' . $category_name . '%')->get();

           }else{
                $data = Category::where('parent_id',NULL)->get();
           }


            foreach($data as $key => $value){
                $data[$key]->items = Category::where('parent_id',$value->id)->get();
            }
            return response()->json([
                "result" => $data,
                "message" => 'Success',
                "status" => 1
            ]);
        }

        public function customer_favourite_product(Request $request){

            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
                'product_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $favourite = CustomerFavouriteProduct::where('customer_id',$input['customer_id'])->where('product_id',$input['product_id'])->first();
            $like = Product::where('id',$input['product_id'])->value('total_like');
            if(!is_object($favourite)){
                $customer = CustomerFavouriteProduct::create($input);
                Product::where('id',$input['product_id'])->update([ 'total_like' => $like+1]);
                return response()->json([
                    "message" => 'Added to your favourite list',
                    "status" => 1
                ]);
            }else{
                Product::where('id',$input['product_id'])->update([ 'total_like' => $like - 1]);
                CustomerFavouriteProduct::where('customer_id',$input['customer_id'])->where('product_id',$input['product_id'])->delete();
                return response()->json([
                    "message" => 'Removed from your favourite list',
                    "status" => 0
                ]);
            }
        }

        public function customer_viewed_product(Request $request){

            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
                'product_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }



            if(!CustomerViewedProduct::where('customer_id', $request->customer_id)->where('product_id', $request->product_id)->exists()){
                 $view = Product::where('id',$input['product_id'])->value('total_view');
                 $customer = CustomerViewedProduct::create($input);
                 if(is_object($customer)){
                    Product::where('id',$input['product_id'])->update([ 'total_view' => $view+1]);
                }
            }


            return response()->json([
                "message" => 'Success',
                "status" => 1
            ]);

        }
        public function customer_shared_product(Request $request){

            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
                'product_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            //$shared = CustomerSharedProduct::where('customer_id',$input['customer_id'])->where('product_id',$input['product_id'])->first();
            $share = Product::where('id',$input['product_id'])->value('total_sharing');

            $customer = CustomerSharedProduct::create($input);
            if($customer){
                Product::where('id',$input['product_id'])->update([ 'total_sharing' => $share+1]);
                return response()->json([
                    "message" => 'Success',
                    "result" => $customer,
                    "status" => 1
                ]);
            }else{
                return response()->json([
                    "message" => 'Sorry something went wrong',
                    "status" => 0
                ]);
            }
        }

        public function vendor_detail(Request $request){

            $input = $request->all();
            $validator = Validator::make($input, [
                'vendor_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $data['vendor'] = Vendor::where('id',$input['vendor_id'])->first();
            $data['total_likes'] = Product::where('vendor_id',$input['vendor_id'])->sum('total_like');
            $data['total_shares'] = Product::where('vendor_id',$input['vendor_id'])->sum('total_sharing');
            $data['total_views'] = Product::where('vendor_id',$input['vendor_id'])->sum('total_view');
            $data['product'] = Product::where('vendor_id',$input['vendor_id'])->get();

            if($data){
                return response()->json([
                    "result" => $data,
                    "message" => 'Success',
                    "status" => 1
                ]);
            }
            else{
                return response()->json([
                    "message" => 'Something went wrong',
                    "status" => 0
                ]);
            }
        }

        public function get_favourite_products(){
            $data = [];
            $data['most_like'] = Product::where('total_like','!=',0)->orderBy('total_like', 'ASC')->get();
            $data['most_view'] = Product::where('total_view','!=',0)->orderBy('total_view', 'ASC')->get();
            $data['most_share'] = Product::where('total_sharing','!=',0)->orderBy('total_sharing', 'ASC')->get();
            return response()->json([
                "result" => $data,
                "message" => 'Success',
                "status" => 1
            ]);
        }

        public function get_product_detail(Request $request){

            $input = $request->all();
            $validator = Validator::make($input, [
                //'customer_id' => 'required',
                'product_id' => 'required'
            ]);

            //$input['customer_id'] = 1;

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            $product = DB::table('products')
                ->leftJoin('vendors', 'vendors.id', '=', 'products.vendor_id')
                ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                ->where('products.id',$input['product_id'])
                ->select('products.*', 'brands.brand_name','brands.brand_image', 'vendors.vendor_name', 'categories.category_name', 'vendors.profile_picture')
                ->first();

            $product->brand_image = 'http://bissbuy.uz/upload/' . $product->brand_image;
            //$product = Product::where('id',$input['product_id'])->first();
            // $favourite = CustomerFavouriteProduct::where('product_id',$input['product_id'])->where('customer_id',$input['customer_id'])->first();
            // if($favourite){
            //     $product->is_like = 1;
            // }else{
            //     $product->is_like = 0;
            // }

            $option_values = DB::table('product_attributes')
            ->join('product_options','product_options.id','=','product_attributes.option_id')
            ->select('product_attributes.*','product_options.option_name','product_options.option_code')
            ->where('product_attributes.product_id',$input['product_id'])->get();

            foreach($option_values as $option_value)
            {
                $option_value->option_value_id = explode(',', $option_value->option_value_id);
            }

            //dd($option_values);
            // $product->option_values = DB::table('product_attributes')
            //                         ->join('product_options','product_options.id','=','product_attributes.option_id')
            //                         ->select('product_attributes.*','product_options.option_name','product_options.option_code')
            //                         ->where('product_attributes.product_id',$input['product_id'])->get();



            if($product){
                $product->option_values = $option_values;
                $product->cover_image = Product::find($product->id)->cover_image;
                $product->discount_diff = $product->product_price - $product->discount_price;

                $product->images = ProductImage::where('product_id',$product->id)->pluck('product_image');

                return response()->json([
                    "message" => 'Success',
                    "result" => $product,
                    "status" => 1
                ]);
            }else{
                return response()->json([
                    "message" => 'Sorry something went wrong',
                    "status" => 0
                ]);
            }
        }

        public function get_products(Request $request){

            $input = $request->all();
            $validator = Validator::make($input, [
                'category_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }


            $products = DB::table('products')
                ->leftJoin('vendors', 'vendors.id', '=', 'products.vendor_id')
                ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                ->where('products.category_id',$input['category_id'])
                ->select('products.*', 'brands.brand_name','vendors.vendor_name', 'categories.category_name')
                ->get();
            if(($products)){
                return response()->json([
                    "message" => 'Success',
                    "result" => $products,
                    "status" => 1
                ]);
            }else{
                return response()->json([
                    "message" => 'Sorry something went wrong',
                    "status" => 0
                ]);
            }
        }

        public function get_payment_mode()
        {

            $data = PaymentMode::where('id',1)->where('status',1)->get();

            if(($data)){
                return response()->json([
                    "message" => 'Success',
                    "result" => $data,
                    "status" => 1
                ]);
            }else{
                return response()->json([
                    "message" => 'Sorry something went wrong',
                    "status" => 0
                ]);
            }
        }

        public function get_promo_code()
        {

            $data = PromoCode::where('status',1)->get();

            if(($data)){
                return response()->json([
                    "message" => 'Success',
                    "result" => $data,
                    "status" => 1
                ]);
            }else{
                return response()->json([
                    "message" => 'Sorry something went wrong',
                    "status" => 0
                ]);
            }
        }

        public function add_rating(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'product_id' => 'required',
                'order_id' => 'required',
                'customer_id' => 'required',
                'review' => 'required',
                'rating' => 'required',

            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $rating = ProductReview::create($input);
            $comment = Product::where('id',$input['product_id'])->value('total_comment');

            if (is_object($rating)) {
                Product::where('id',$input['product_id'])->update([ 'total_comment' => $comment+1]);
                return response()->json([
                    "result" => $rating,
                    "message" => 'Registered Successfully',
                    "status" => 1
                ]);
            } else {
                return response()->json([
                    "message" => 'Sorry, something went wrong !',
                    "status" => 0
                ]);
            }

        }

        public function get_rating(Request $request){

            $input = $request->all();
            $validator = Validator::make($input, [
                'order_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            $rating = ProductReview::where('order_id',$input['order_id'])->get();
            if($rating){
                return response()->json([
                    "message" => 'Success',
                    "result" => $rating,
                    "status" => 1
                ]);
            }else{
                return response()->json([
                    "message" => 'Sorry something went wrong',
                    "status" => 0
                ]);
            }
        }

        public function get_wallet(Request $request){

            $input = $request->all();
            $validator = Validator::make($input, [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $data['total_earnings'] = CustomerWalletHistory::where('customer_id',$input['id'])->get()->sum("amount");
            $data['today_earnings'] = CustomerWalletHistory::where('customer_id',$input['id'])->whereDay('created_at', now()->day)->sum("amount");

            $data['wallet_histories'] = CustomerWalletHistory::where('customer_id',$input['id'])->orderBy('id', 'desc')->get();

            if($data){
                return response()->json([
                    "result" => $data,
                    "count" => count($data['wallet_histories']),
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
        public function customer_top_product(Request $request){

            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            $data['shared'] = DB::table('customer_shared_products')
                ->leftJoin('products', 'products.id', '=', 'customer_shared_products.product_id')
                ->where('customer_id',$input['customer_id'])
                ->select('customer_shared_products.*', 'products.product_name','products.cover_image', 'products.product_price','products.rating','products.short_description','products.current_stock','products.min_qty','products.total_view','products.total_sharing','products.total_like','products.total_comment')
                ->get();
            $data['favourite'] = DB::table('customer_favourite_products')
                ->leftJoin('products', 'products.id', '=', 'customer_favourite_products.product_id')
                ->where('customer_id',$input['customer_id'])
                ->select('customer_favourite_products.*', 'products.product_name','products.cover_image', 'products.product_price','products.rating','products.short_description','products.current_stock','products.min_qty','products.total_view','products.total_sharing','products.total_like','products.total_comment')
                ->get();
            $data['viewed'] = DB::table('customer_viewed_products')
                ->leftJoin('products', 'products.id', '=', 'customer_viewed_products.product_id')
                ->where('customer_id',$input['customer_id'])
                ->select('customer_viewed_products.*', 'products.product_name','products.cover_image', 'products.product_price','products.rating','products.short_description','products.current_stock','products.min_qty','products.total_view','products.total_sharing','products.total_like','products.total_comment')
                ->get();
            //$data['shared'] = CustomerSharedProduct::where('customer_id',$input['customer_id'])->get();
            //$data['favourite'] = CustomerFavouriteProduct::where('customer_id',$input['customer_id'])->get();
            //$data['viewed'] = CustomerViewedProduct::where('customer_id',$input['customer_id'])->get();

            if($data){

                return response()->json([
                    "message" => 'Success',
                    "result" => $data,
                    "status" => 1
                ]);
            }else{
                return response()->json([
                    "message" => 'Sorry something went wrong',
                    "status" => 0
                ]);
            }
        }

        public function get_my_orders(Request $request){
            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $data = DB::table('orders')
                    ->leftjoin('customer_addresses','customer_addresses.id','=','orders.customer_address_id')
                    ->where('orders.customer_id',$input['customer_id'])
                    ->select('orders.*','customer_addresses.customer_address')
                    ->orderBy('orders.id', 'DESC')
                    ->whereNotIn('status', [6])
                    ->get();
            foreach($data as $key => $value){
                $data[$key]->image = DB::table('order_products')->where('order_id',$value->id)->value('product_image');
                $data[$key]->products = DB::table('order_products')->where('order_id',$value->id)->get();
                $data[$key]->payment_mode = "Cash";
            }

            $business = DB::table('orders')
                    ->leftjoin('customer_addresses','customer_addresses.id','=','orders.customer_address_id')
                    ->where('orders.referred_id',$input['customer_id'])
                    ->select('orders.*','customer_addresses.customer_address')
                    ->orderBy('orders.id', 'DESC')
                    ->get();
            foreach($business as $key => $value){
                $business[$key]->image = DB::table('order_products')->where('order_id',$value->id)->where('referred_id',$input['customer_id'])->value('product_image');
                $business[$key]->cashback = DB::table('customer_wallet_histories')->where('order_id',$value->id)->value('amount');
                $business[$key]->products = DB::table('order_products')->where('order_id',$value->id)->get();
                $business[$key]->payment_mode = "Cash";
                $business[$key]->customer_name = DB::table('customers')->where('id',$value->customer_id)->value('first_name');
            }

            $result['my_orders'] = $data;
            $result['business'] = $business;
            return response()->json([
                "result" => $result,
                "message" => 'Success',
                "status" => 1
            ]);
        }

        public function search_products(Request $request)
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'key_words' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $products = DB::table('products')
                ->leftJoin('vendors', 'vendors.id', '=', 'products.vendor_id')
                ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                ->where('products.key_words', 'like', '%' . $input['key_words'] . '%')
                ->select('products.*', 'brands.brand_name', 'vendors.vendor_name','categories.category_name')
                ->get();
            if(($products)){
                return response()->json([
                    "message" => 'Success',
                    "result" => $products,
                    "status" => 1
                ]);
            }else{
                return response()->json([
                    "message" => 'Sorry something went wrong',
                    "status" => 0
                ]);
            }

        }

        public function add_app_review(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
                'app_review' => 'required',

            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            Customer::where('id',$input['customer_id'])->update([ 'app_review' => $input['app_review']]);
            $customer = Customer::where('id',$input['customer_id'])->first();
            if($customer){
                return response()->json([
                    "result" => $customer,
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
        public function get_user_details(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            $customer = Customer::where('id',$input['customer_id'])->first();
            $total_shared = CustomerSharedProduct::where('customer_id',$input['customer_id'])->count();
            $total_delivered = Order::where('customer_id',$input['customer_id'])->where('status',4)->count();
            $total_returned = Order::where('customer_id',$input['customer_id'])->where('status',5)->count();
            $customer['total_shared']= $total_shared;
            $customer['total_delivered']= $total_delivered;
            $customer['total_returned']= $total_returned;
            if($customer){
                return response()->json([
                    "result" => $customer,
                    "message" => 'Success',
                    "status" => 1
                ]);
            } else {
                return response()->json([
                    "message" => 'Sorry, something went wrong !',
                    "status" => 0
                ]);
            }

        }
        public function sendError($message) {
            $message = $message->all();
            $response['error'] = "validation_error";
            $response['message'] = implode('',$message);
            $response['status'] = "0";
            return response()->json($response, 422);
        }

        public function add_comment(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
                'product_id' => 'required',
                'review' => 'required',
                'rating' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            Product::find($request->product_id)->increment('total_comment');

            $input['order_id'] = 0;

            $review = \App\Models\ProductReview::create($input);
            return response()->json([
                "message" => 'Success',
                //"result" => $review,
                "status" => 1
            ]);
        }

        public function get_comments($product_id)
        {
            $reviews = \App\Models\ProductReview::with('customer')->where('product_id',$product_id)->orderBy('created_at', 'desc')->get();
            return response()->json([
                "message" => 'Success',
                "result" => $reviews,
                "status" => 1
            ]);
        }


        public function get_top_products()
        {
            // $prod = Product::first();
            // return $prod;
            // dd($prod->cover_image);

            $viewed = \App\Models\Product::where('status', 1)->with('images', 'brand')->orderBy('total_view', 'desc')->take(10)->get(['id', 'total_view', 'total_like','total_sharing', 'product_name', 'cover_image', 'brand_id', 'vendor_id', 'product_price']);
            $liked =  \App\Models\Product::where('status', 1)->with('images','brand')->orderBy('total_like', 'desc')->take(10)->get(['id', 'total_like','total_view', 'total_sharing',  'product_name', 'cover_image', 'brand_id', 'vendor_id', 'product_price']);
            $shared =  \App\Models\Product::where('status', 1)->with('images','brand')->orderBy('total_sharing', 'desc')->take(10)->get(['id', 'total_sharing', 'total_like', 'total_view', 'product_name', 'cover_image', 'brand_id', 'vendor_id', 'product_price']);

            $result = [
                'most_viewed' => $viewed,
                'most_liked' => $liked,
                'most_shared' => $shared,
            ];

            return response()->json([
                "message" => 'Success',
                "result" => $result,
                "status" => 1
            ]);
        }

        public function top_like()
        {

            $liked =  \App\Models\Product::where('status',1)->with('brand', 'images')->orderBy('total_like', 'desc')->where('total_like', '>', 0)->select('id', 'total_view', 'total_like', 'total_sharing', 'total_view', 'product_name', 'cover_image', 'product_price', 'total_comment', 'brand_id', 'vendor_id', 'is_discount', 'discount_price', 'discount_from', 'discount_to','discount_percent')->paginate(15);

            foreach($liked as $like)
            {
                $like->product_price == null ? \DB::table('m_variants_values')->where('product_id', $like->id)->whereNotNull('price')->first()->price : $like->product_price;

            }


            $result = [
                'most_liked' => $liked
            ];

            return response()->json([
                "message" => 'Success',
                "result" => $result,
                "status" => 1
            ]);
        }

        public function top_view()
        {

            $viewed =  \App\Models\Product::where('status',1)->orderBy('total_view', 'desc')->with('brand', 'images')->where('total_view', '>', 0)->select('id', 'total_view', 'total_like', 'total_sharing', 'total_view', 'product_name', 'cover_image', 'product_price', 'total_comment', 'brand_id', 'vendor_id', 'is_discount', 'discount_price', 'discount_from', 'discount_to','discount_percent')->paginate(15);

             foreach($viewed as $like)
                {
                    $like->product_price == null ? \DB::table('m_variants_values')->where('product_id', $like->id)->whereNotNull('price')->first()->price : $like->product_price;

                }

            $result = [
                'most_viewed' => $viewed
            ];

            return response()->json([
                "message" => 'Success',
                "result" => $result,
                "status" => 1
            ]);
        }

        public function top_share()
        {

            $shared =  \App\Models\Product::where('status',1)->orderBy('total_sharing', 'desc')->with('brand', 'images')->where('total_sharing', '>', 0)->select('id', 'total_view', 'total_like', 'total_sharing', 'total_view', 'product_name', 'cover_image', 'product_price', 'total_comment', 'brand_id', 'vendor_id', 'is_discount', 'discount_price', 'discount_from', 'discount_to','discount_percent')->paginate(15);

            foreach($shared as $like)
            {
                $like->product_price == null ? \DB::table('m_variants_values')->where('product_id', $like->id)->whereNotNull('price')->first()->price : $like->product_price;
            }

            $result = [
                'most_shared' => $shared
            ];

            return response()->json([
                "message" => 'Success',
                "result" => $result,
                "status" => 1
            ]);
        }


        public function customer_business($customer_id)
        {
            $business_pending_order = Order::where('referred_id', $customer_id)->whereNotIn('status', [5,6])->sum('total');
            $business_completed_order = Order::where('referred_id', $customer_id)->whereIn('status', [5,6])->sum('total');
            $businessc_orders_list = Order::with(['vendor', 'products'])->where('referred_id', $customer_id)->whereNotIn('status', [5])->get();
            $business_order_result = [];
            $orders_result = [];
            $orders_archive_result = [];

            foreach ($businessc_orders_list as $order) {
                if ($order->products->count() > 0) {
                    $image = $order->products->first()->product_image;
                } else {
                    $image = '';
                }


                $prods = [];

                foreach($orderr->products as $prod)
                {
                    $pr = Product::find($prod->product_id);

                     if($pr){
                        $prods [] = [
                            "id" => $prod->id,
                            "referred_id"=> $prod->referred_id,
                            "product_id"=> $prod->product_id,
                            "product_name"=> $prod->product_name,
                            "product_price"=> $prod->product_price,
                            "product_image"=> $prod->product_image,
                            "qty"=> $prod->qty,
                            "total_price"=> $prod->total_price,
                            "tax"=> $prod->tax,
                            "brand" => \App\Models\Brand::find($pr->brand_id)
                           ];
                   }


                }

                $business_order_result[] = [
                    'total' => $order->total,
                    'id' => $order->id,
                    'status' => $order->status,
                    'image' => $image,
                    'vendor' => $order->vendor->vendor_name,
                    'products' => $prods
                ];
            }

            $pending_order = Order::where('customer_id', $customer_id)->whereNotIn('status', [5,6])->sum('total');
            $completed_order = Order::where('customer_id', $customer_id)->whereIn('status', [6])->sum('total');
            $orders_list = Order::with(['vendor', 'products'])->whereNotIn('status', [5,6])->where('customer_id', $customer_id)->get();


            $orders_list_archive = Order::with(['vendor', 'products'])->whereIn('status', [6])->where('customer_id', $customer_id)->get();



            foreach ($orders_list as $orderr) {
                if ($orderr->products->count() > 0) {
                    $image =  $orderr->products->first()->product_image;
                } else {
                    $image = '';
                }

                $prods = [];

                foreach($orderr->products as $prod)
                {
                    $pr = Product::find($prod->product_id);
                   if($pr){
                       $prods [] = [
                            "id" => $prod->id,
                            "referred_id"=> $prod->referred_id,
                            "product_id"=> $prod->product_id,
                            "product_name"=> $prod->product_name,
                            "product_price"=> $prod->product_price,
                            "product_image"=> $prod->product_image,
                            "qty"=> $prod->qty,
                            "total_price"=> $prod->total_price,
                            "tax"=> $prod->tax,
                            "brand" => \App\Models\Brand::find($pr->brand_id)
                           ];
                   }
                }

                $orders_result[] = [
                    'total' => $orderr->total,
                    'id' => $orderr->id,
                    'status' => $orderr->status,
                    'image' => $image,
                    'vendor' => isset($orderr->vendor->vendor_name) ? $orderr->vendor->vendor_name : null,
                    'products' => $prods
                ];
            }


             foreach ($orders_list_archive as $orderr) {
                if ($orderr->products->count() > 0) {
                    $image =  $orderr->products->first()->product_image;
                } else {
                    $image = '';
                }

                $orders_archive_result[] = [
                    'total' => $orderr->total,
                    'id' => $orderr->id,
                    'status' => $orderr->status,
                    'image' => $image,
                    'vendor' => isset($orderr->vendor->vendor_name) ? $orderr->vendor->vendor_name : null,
                    'products' => $orderr->products
                ];
            }



            $result = [
                'business' => [
                    'pending' => $business_pending_order,
                    'completed' => $business_completed_order,
                    'business_orders_list' => $business_order_result
                ],
                'orders' =>[
                    'pending' => $pending_order,
                    'completed' => $completed_order,
                    'orders_list' => $orders_result,
                    'archive_orders_list' => $orders_archive_result
                ]
            ];



                return response()->json([
                    "message" => 'Success',
                    "result" => $result,
                    "status" => 1
                ]);
        }




        public function vendor_statistic_page($vendor_id)
        {
            $vendor = Vendor::where('id',$vendor_id)->get(['vendor_name', 'vendor_email', 'phone_number', 'store_image', 'description']);

            $products = Product::active()->with('images')->where('vendor_id', $vendor_id)->get();

           // $vendor->store_image = url('/upload/' . $vendor->store_image);

            $total_liked = Product::active()->where('vendor_id', $vendor_id)->sum('total_like');
            $total_view  = Product::active()->where('vendor_id', $vendor_id)->sum('total_view');
            $total_share = Product::active()->where('vendor_id', $vendor_id)->sum('total_sharing');

            $result = [
                'vendor' => $vendor,
                'total_like' => $total_liked,
                'total_view' => $total_view,
                'total_share' => $total_share,
                'products' => $products
            ];

            return response()->json([
                "message" => 'Success',
                "result" => $result,
                "status" => 1
            ]);



        }


        public function check_is_liked(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
                'product_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $result = [
                'is_like' => CustomerFavouriteProduct::where('customer_id',$input['customer_id'])->where('product_id',$input['product_id'])->exists()
            ];

            return response()->json([
                "message" => 'Success',
                "result" => $result,
                "status" => 1
            ]);
        }



        public function cancel_order(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'order_id' => 'required',
                'status' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            Order::where('id', $input['order_id'])->update(['status' => $input['status']]);

            return response()->json([
                "message" => 'Success',
                "status" => 1
            ]);
        }

        public function delete_order(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'order_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

           // Order::where('id', $input['order_id'])->update(['status' =>7]);

           Order::where('id', $input['order_id'])->delete();
           OrderProduct::where('order_id', $input['order_id'])->delete();

            return response()->json([
                "message" => 'Success',
                "status" => 1
            ]);
        }

        public function logout(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'fcm_token' => 'required',
                'customer_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $customer = Customer::where('id',$input['customer_id'])->first();
            $customer->fcm_token = null;
            $customer->save();

            return response()->json([
                "message" => 'Success',
                "status" => 1
            ]);
        }

        public function addToWishlist(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
                'product_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            if(!Wishlist::where('customer_id',$input['customer_id'])->where('product_id',$input['product_id'])->exists()){
                 Wishlist::create([
                     'customer_id' => $input['customer_id'],
                     'product_id' =>  $input['product_id']
                     ]);
            }else{
                Wishlist::where('customer_id',$input['customer_id'])->where('product_id',$input['product_id'])->delete();
            }

            return response()->json([
                "message" => 'Success',
                "status" => 1
            ]);
        }

        public function checkWishlist(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
                'product_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            return response()->json([
                "message" => Wishlist::where('customer_id',$input['customer_id'])->where('product_id',$input['product_id'])->exists(),
                "status" => 1
            ]);
        }

        public function getWishlist(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $prod_ids = Wishlist::where('customer_id',$input['customer_id'])->orderByDesc('id')->pluck('product_id');


            $prods = Product::with(['brand', 'images', 'vendor'])->whereIn('id', $prod_ids)->get();
            $result = [];
            foreach($prod_ids as $id )
            {
		$prod = Product::with(['brand', 'images', 'vendor'])->find($id);

                $result[] = [
                    'id'=> $prod->id,
                    'brand'=> $prod->brand,
                    'product_price'=> $prod->product_price,
                    'product_name'=> $prod->product_name,
                    'images' => $prod->images,
                     'total_view' => $prod->total_view,
                      'total_sharing' => $prod->total_sharing,
                       'total_like' => $prod->total_like,
                        'total_comment' => $prod->total_comment,
                        'vendor_id' => $prod->vendor_id,
			'vendor' => Vendor::where('id', $prod->vendor_id)->select('id', 'vendor_name', 'store_image')->first()
                ];
            }

             return response()->json([
                "message" =>$result,
                "status" => 1
            ]);

        }

        public function resetPassword(Request $request)
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'customer_id' => 'required',
                'old_password' => 'required',
                'new_password' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $customer = Customer::find($input['customer_id']);

            if(!$customer)
               return response()->json([
                "message" => 'customer not found',
                "status" => 0
            ]);

           if(\Hash::check($input['old_password'], $customer->password)){
               $customer->password = \Hash::make($input['new_password']);
               $customer->save();

               return response()->json([
                "message" =>'success',
                "status" => 1
            ]);
           }else{

                return response()->json([
                "message" => 'password does not match',
                "status" => 0
            ]);

           }
        }

        public function top_sale()
        {

            $shared = \App\Models\Product::where('status',1)->whereDate('discount_from','<=', now())->with('brand', 'images')->whereDate('discount_to','>=', now())->whereNotNull('discount_price')->select('discount_price','discount_from','discount_to', 'discount_percent', 'id', 'total_view', 'total_like', 'total_sharing', 'total_view', 'product_name', 'cover_image', 'product_price', 'total_comment', 'brand_id', 'vendor_id')->paginate(10);
            //$shared = \App\Models\Product::where('status',1)->whereNotNull('discount_price')->take(10)->get(['id', 'discount_price','discount_from','discount_to', 'product_name', 'cover_image']);
            $result = [
                'top_sale' => $shared
            ];

            return response()->json([
                "message" => 'Success',
                "result" => $result,
                "status" => 1
            ]);
        }


	public function customer_ontheway_order($customer_id)
        {
           $orders = Order::with('products')->where('status', 3)->where('customer_id', $customer_id)->get();
	   $data = [];
		foreach($orders as $order){

		$product = Product::find($order->products->first()->product_id);
		$image = ProductImage::where('product_id', $product->id)->first();
		$brand = Brand::find($product->brand_id);

		$data[]	 = [
			'photo'=>$image,
			'product_name' => $order->products->first()->product_name,
			'brand' => $brand,
			'total' =>  $order->total,
			'attributes' => json_decode($order->products->first()->attributes, true),
			'quantity' => $order->products->first()->qty,
			'order_id' => $order->id
		];
		}
            return response()->json([
                "result" => $data,
                "status" => 1
            ]);

        }


	public function received_orders($customer_id)
        {
           $orders = Order::with('products')->where('status', 2)->where('vendor_id', $customer_id)->get();
	   $data = [];
		foreach($orders as $order){

		$product = Product::find($order->products->first()->product_id);
		$image = ProductImage::where('product_id', $product->id)->first();
		$brand = Brand::find($product->brand_id);

		$data[]	 = [
			'photo'=>$image,
			'product_name' => $order->products->first()->product_name,
			'brand' => $brand,
			'total' =>  $order->total,
			'attributes' => json_decode($order->products->first()->attributes, true),
			'quantity' => $order->products->first()->qty,
			'order_id' => $order->id
		];
		}
            return response()->json([
                "result" => $data,
                "status" => 1
            ]);

        }

	public function waiting_orders($customer_id)
        {
           $orders = Order::with('products')->where('status', 1)->where('vendor_id', $customer_id)->get();
	   $data = [];
		foreach($orders as $order){

		$product = Product::find($order->products->first()->product_id);
	        $image = ProductImage::where('product_id', $product->id)->first();
		$brand = Brand::find($product->brand_id);

		$data[]	 = [
			'photo'=>$image,
			'product_name' => $order->products->first()->product_name,
			'brand' => $brand,
			'total' =>  $order->total,
			'attributes' => json_decode($order->products->first()->attributes, true),
			'quantity' => $order->products->first()->qty,
			'order_id' => $order->id
		];
		}
            return response()->json([
                "result" => $data,
                "status" => 1
            ]);

        }


	public function shipping_orders($customer_id)
        {
           $orders = Order::with('products')->where('status', 3)->where('vendor_id', $customer_id)->get();
	   $data = [];
		foreach($orders as $order){

		$product = Product::find($order->products->first()->product_id);
		$image = ProductImage::where('product_id', $product->id)->first();
		$brand = Brand::find($product->brand_id);

		$data[]	 = [
			'photo'=>$image,
			'product_name' => $order->products->first()->product_name,
			'brand' => $brand,
			'total' =>  $order->total,
			'attributes' => json_decode($order->products->first()->attributes, true),
			'quantity' => $order->products->first()->qty,
			'order_id' => $order->id
		];
		}
            return response()->json([
                "result" => $data,
                "status" => 1
            ]);

        }

	public function cancelled_orders($customer_id)
        {
           $orders = Order::with('products')->where('status', 5)->where('vendor_id', $customer_id)->get();
	   $data = [];
		foreach($orders as $order){

		$product = Product::find($order->products->first()->product_id);
		$image = ProductImage::where('product_id', $product->id)->first();
		$brand = Brand::find($product->brand_id);

		$data[]	 = [
			'photo'=>$image,
			'product_name' => $order->products->first()->product_name,
			'brand' => $brand,
			'total' =>  $order->total,
			'attributes' => json_decode($order->products->first()->attributes, true),
			'quantity' => $order->products->first()->qty,
			'order_id' => $order->id
		];
		}
            return response()->json([
                "result" => $data,
                "status" => 1
            ]);

        }


	public function delivered_orders($customer_id)
        {
           $orders = Order::with('products')->where('status', 6)->where('vendor_id', $customer_id)->get();
	   $data = [];
		foreach($orders as $order){

		$product = Product::find($order->products->first()->product_id);
		$image = ProductImage::where('product_id', $product->id)->first();
		$brand = Brand::find($product->brand_id);

		$data[]	 = [
			'photo'=>$image,
			'product_name' => $order->products->first()->product_name,
			'brand' => $brand,
			'total' =>  $order->total,
			'attributes' => json_decode($order->products->first()->attributes, true),
			'quantity' => $order->products->first()->qty,
			'order_id' => $order->id
		];
		}
            return response()->json([
                "result" => $data,
                "status" => 1
            ]);

        }






        public function customer_completed_order($customer_id)
        {

            $orders = \DB::table('orders')
            ->select('brands.brand_name', 'brands.brand_image', 'products.product_name', 'products.id as product_id', 'orders.id','orders.created_at as ordered_at', 'orders.completed_at', 'orders.total', 'products.brand_id', 'products.vendor_id','products.variants')
            ->leftJoin('order_products', 'order_products.order_id', '=', 'orders.id')
            ->leftJoin('products', 'products.id', '=', 'order_products.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                      ->where('orders.status', 6)
            ->where('customer_id', $customer_id)->get();

            foreach($orders as $order)
            {
                //$order->completed_at =now()->format('Y-m-d H:i:s');
		$order->product_image = ProductImage::where('product_id', $order->product_id)->first()->product_image;
            }
                      return response()->json([
                "result" => $orders,
                "status" => 1
            ]);

        }


        public function customer_cancelled_order($customer_id)
        {
            $orders = \DB::table('orders')
            ->select('brands.brand_name', 'brands.brand_image','order_products.product_id', 'products.product_name', 'orders.id','orders.created_at as ordered_at', 'orders.completed_at', 'orders.total', 'products.brand_id', 'products.vendor_id', 'products.variants', 'orders.status', 'orders.customer_id')
            ->leftJoin('order_products', 'order_products.order_id', '=', 'orders.id')
            ->leftJoin('products', 'products.id', '=', 'order_products.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->where('orders.status', '=', 5)
            ->where('orders.customer_id', '=', $customer_id)
            ->get();

            foreach($orders as $order)
            {

                $order->brand_image = $order->brand_image;
                $order->completed_at =now()->format('Y-m-d H:i:s');
                $order->product_image = ProductImage::where('product_id', $order->product_id)->first()->product_image;
            }

            return response()->json([
                "result" => $orders,
                "status" => 1
            ]);
        }




        public function cards($customer_id)
        {
            $customer = Customer::find($customer_id);

             return response()->json([
                "message" => 'success',
                "result" => is_null($customer->cards) ? [] : json_decode($customer->cards),
                "status" => 1
            ]);
        }

        public function add_card(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'card_number' => 'required',
                'expire_date' => 'required',
                'title' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $customer = Customer::find($request->customer_id);

            $cards = is_null($customer->cards) ? [] : json_decode($customer->cards);

            $cards[] = [
                'card_number' => $request->card_number,
                'expire_date' => $request->expire_date,
                'title' => $request->title
                ];

            $customer->cards = $cards;
            $customer->save();

             return response()->json([
                "message" => 'success',
                "result" => $customer->cards,
                "status" => 1
            ]);
        }

        public function set_location(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'lat' => 'required',
                'long' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            Customer::find($request->customer_id)->update([
                'location' => json_encode(['lat'=> $request->lat, 'long'=> $request->long])
                ]);

              return response()->json([
                "result" => 'success',
                "status" => 1
            ]);
        }

      public function get_location($customer_id)
      {
          $customer = Customer::find($customer_id);
          $res = json_decode($customer->location, true);

           return response()->json([
                "result" => $res,
                "status" => 1
            ]);
      }

      public function create_order(Request $request)
      {
           $input = $request->all();
           $validator = Validator::make($request->all(), [
                'product_id' => 'required',
                'price' => 'required',
                'attributes' => 'sometimes',
                'end_point_delivery_lat' => 'required',
                'end_point_delivery_long' => 'required',
                'location' => 'required',
                'customer_id' => 'required',
                'quantity' => 'required',
                'referred_id' => 'required',
                'payment_type' => 'required',
                'phone_number' => 'required_if:payment_type,click,payme',
                'images' => 'sometimes'
            ]);

            if ($validator->fails()) {
                 return $this->sendError($validator->errors(), 422);
            }

            $product = Product::findOrFail($request->product_id);
            $commission = DB::table('app_settings')->value('referral_commission');
            $vendor = Vendor::find($product->vendor_id);


            $lat_from = $vendor->warehouse_address['lat'];
            $long_from =  $vendor->warehouse_address['long'];
            $lat_to = $input['end_point_delivery_lat'];
            $long_to = $input['end_point_delivery_long'];
            $input['vendor_id'] = $vendor->id;
            $input['total'] = $request->quantity * $request->price;
            $input['end_point_delivery'] = [
                'lat' => $input['end_point_delivery_lat'],
                'long'=>$input['end_point_delivery_long'],
                'location' => $input['location']
                ];
            $input['distance'] = $this->distance($lat_from, $long_from, $lat_to, $long_to, 'K');
            $input['delivery_amount'] = $input['distance'] * intval(AppSetting::where('id',1)->value('delivery_charge'));
           // $input['end_point_delivery'] = $input['end_point_delivery'][0];

            $input['status'] = 1;

          // dd($input);

            $order = Order::create($input);


            if($input['referred_id'] != 0 && $input['referred_id'] != $input['customer_id']){
                $com = ($commission / 100) * $input['total'];
                //CustomerWalletHistory::create([ "customer_id" => $input['customer_id'], "type" => 2, "message" => "Кредит вашего дохода по этому заказу #".$order->id, "amount" => $com, "order_id" => $order->id ]);
                $old_wallet = DB::table('customers')->where('id',$input['referred_id'])->value('wallet');
                $new_wallet = $old_wallet + $com;
                DB::table('customers')->where('id',$input['referred_id'])->update([ "wallet" => $new_wallet ]);
              DB::table('orders')->where('id',$order->id)->update([ "referred_id" => $value['referred_id'] ]);
            }




            $order_product = [];
            $order_product['order_id'] = $order->id;
            $order_product['product_id'] = $product->id ;
            $order_product['product_name'] = $product->product_name ;
            $order_product['product_price'] = $product->product_price ;
            $order_product['qty'] = $request->quantity ;
            $order_product['total_price'] = $request->quantity * $request->price;
            $order_product['product_image'] = \DB::table('product_images')->where('product_id', $product->id)->first()->product_image;
            $order_product['attributes'] = json_encode($request->input('attributes'));
            $order_product['images'] = json_encode($request->input('images'));

            $tax_percent = Product::find($input['product_id'])->subcategory->tax == null? 1 :  Product::find($input['product_id'])->subcategory->tax;

            $order_product['tax'] = ($input['price'] / 100) * $tax_percent;


            OrderProduct::create($order_product);



            if($request->payment_type == 'click')
            {
                $digest = sha1(time() . 'GjlQsHjMjJUDl');
                $auth = "24090:$digest:" . time();
                $res = \Illuminate\Support\Facades\Http::withHeaders([
                    'Accept' => ' application/json',
                    'Content-Type' => 'application/json',
                    'Auth' => $auth
                ])->post('https://api.click.uz/v2/merchant/invoice/create', [
                    'service_id' => 20990,
                    'amount' => 1000,
                    'phone_number' => $request->phone_number,
                    'merchant_trans_id' => $order->id
                ]);

            }


            $message = [
                        "title" => "Новое сообщение",
                        "body" => "У вас новый заказ {$order->id}. Перейдите на страницу моих заказов, чтобы увидеть"
                    ];
            $API_KEY = env('VENDOR_KEY');
            $this->sendNotification($vendor->fcm_token, $message, $API_KEY);


            return response()->json([
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
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
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




      public function getProductVariants($product_id)
      {
         $variants = \DB::table('m_product_variants')
         ->where('product_id', $product_id)
         ->get(['id', 'sku_id']);

           return response()->json([
                            "result" => $variants,
                            "message" => 'success',
                            "status" => 1
                        ]);

      }

      public function getAttributesBySku($product_id)
      {

          $product = Product::find($product_id);
          $option = \DB::table('m_product_options')
          ->select('m_options.option_name')
          ->where('product_id', $product_id)
          ->join('m_options', 'm_options.id', '=', 'm_product_options.option_id')
          ->first()->option_name;


    //   return response()->json([
    //                             "result" => $option,
    //                             "message" => 'success',
    //                             "status" => 1
    //                         ]);



      if($product->var_type == 1)
      {
          $result = \DB::table('m_variants_values')
          ->where('m_variants_values.product_id', '=', $product_id)
          ->join('m_option_values', 'm_option_values.id', '=', 'm_variants_values.value_id')
          ->join('m_options', 'm_options.id', '=', 'm_variants_values.option_id')
          ->get(['option_name', 'value_name', 'image', 'status', 'price', 'variant_id','m_variants_values.option_id', 'm_variants_values.value_id',]);

       foreach($result as $res)
       {
           $res->images = ProductImage::where('sku_id', $res->variant_id)->where('product_id', $product_id)->get(['product_image', 'is_main']);
       }

      }
      elseif($product->var_type == 2)
      {
          $result = \DB::table('m_variants_values')
          ->select('m_variants_values.value_id', 'm_option_values.value_name', 'm_variants_values.option_id',  'm_options.option_name')
          ->where('m_variants_values.price', '=', null)
          ->where('product_id', $product_id)
          ->join('m_option_values', 'm_option_values.id', '=', 'm_variants_values.value_id')
          ->join('m_options', 'm_options.id', '=', 'm_variants_values.option_id')
          ->groupBy('value_id', 'm_option_values.value_name', 'option_id', 'option_name')
          ->get();


      }

        $result = [
         "option_name" => $option,
         "list" => $result
       ];

         return response()->json([
                                "result" => $result,
                                "message" => 'success',
                                "status" => 1
                            ]);

      }



      public function getSecondAttributes($product_id, $option_id, $value_id)
      {

           $option = \DB::table('m_product_options')
          ->select('m_options.option_name')
           ->where('product_id', $product_id)
          ->orderByDesc('m_product_options.id')
          ->join('m_options', 'm_options.id', '=', 'm_product_options.option_id')
          ->first()->option_name;



      $parent_variant_ids = \DB::table('m_variants_values')
          ->where('m_variants_values.product_id', '=', $product_id)
          ->where('m_variants_values.option_id', '=', $option_id)
          ->where('m_variants_values.value_id', '=', $value_id)
          ->pluck('variant_id');



      $result = \DB::table('m_variants_values')
          ->where('m_variants_values.product_id', '=', $product_id)
          ->join('m_option_values', 'm_option_values.id', '=', 'm_variants_values.value_id')
          ->join('m_options', 'm_options.id', '=', 'm_variants_values.option_id')
          ->where('m_variants_values.option_id', '!=', $option_id)
          ->where('m_variants_values.value_id', '!=', $value_id)
          ->whereIn('variant_id', $parent_variant_ids)
          ->get(['option_name', 'm_variants_values.option_id', 'm_variants_values.value_id', 'value_name', 'image', 'm_variants_values.status', 'price' ,'variant_id']);



      foreach($result as $res)
      {
          $res->images = ProductImage::where('sku_id', $res->variant_id)->where('product_id', $product_id)->get(['product_image', 'is_main']);
      }

       $result = [
         "option_name" => $option,
         "list" => $result
       ];


       return response()->json([
                                "result" => $result,
                                "message" => 'success',
                                "status" => 1
                            ]);
      }




    // public function searchPage($text = null)
    // {

    //     if($text != null)
    //     {
    //      $exploded = explode(' ', $text);



    //       $products = Product::active()->where(function ($query) use($exploded) {
    //       foreach($exploded as $text) {
    //          $query->orWhere('product_name', 'like', "%$text%");
    //       }
    //   })->paginate(30);


    //          return response()->json([
    //         "result" => $products,
    //         "message" => 'success',
    //         "status" => 1
    //     ]);



    //         $products = Product::where('status', 1)->orderByDesc('created_at')->with('images');


    //       foreach($exploded as $item)
    //       {

    //       $products =  Product::where(function ($query) {
    //       foreach(\Input::get('myselect') as $select) {
    //          $query->orWhere('id', '=', $select);
    //       }
    //   })->get();

    //         //   $products->where(function ($query) use($text, $item) {
    //         //   $query->where('product_name', 'like', "%$text%")
    //         //          ->orWhere('product_name', 'like', "%$item%");
    //         //   });
    //       }
    //   //   $products = $products->paginate(30);
    //     }else{
    //       $products =  Product::where('status', 1)->orderByDesc('created_at')->with('images')->paginate(30);
    //     }


    //     return response()->json([
    //         "result" => $products,
    //         "message" => 'success',
    //         "status" => 1
    //     ]);
    // }

    public function searchPage($text)
    {


      return  $result = Product::where('product_name', 'like', "%$text%")->orderByDesc('created_at')->take(10)->get()->toArray();

        if(count($result) == 0)
        {
            $category = Category::where('category_name', 'like', "%$text%")->first();

            if($category){
                  $result = Product::where('subcategory_id', $category->id)->take(10)->orderByDesc('created_at')->get()->toArray();
            }
        }

        if(count($result) < 10)
        {
              if($result != null){
                $subcategory_id = $result[0]['subcategory_id'];

                $merge = Product::where('subcategory_id', $subcategory_id)->take(10)->orderByDesc('created_at')->get()->toArray();
                $result = array_merge($result, $merge);
              }
        }

          if(count($result) == 0)
          {
            $result = $res = Product::take(10)->whereJsonContains('tags',$text)->get()->toArray();

		if(count($result) < 5 && count($result) > 0)
	        {
		    $merge = Product::where('subcategory_id', $result[0]['subcategory_id'])->take(10)->orderByDesc('created_at')->get()->toArray();
 		    $result = array_merge($result, $merge);
		}

          }


              return response()->json([
            "result" => ['data' => $result] ,
            "message" => 'success',
            "status" => 1
        ]);
    }

    public function getSearchHistory($user_id)
    {
        $result = \DB::table('search_history')->where('user_id', $user_id)->orderByDesc('id')->paginate(10);
        return response()->json([
                                "result" => $result,
                                "message" => 'success',
                                "status" => 1
                            ]);
    }

    public function setSearchHistory(Request $request)
    {
         $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'text' => 'required'
         ]);

         if ($validator->fails()) {
              return $this->sendError($validator->errors());
         }

         \DB::table('search_history')->insert([
             'user_id' => $request->user_id,
             'text' => $request->text
             ]);

        return response()->json([
            "message" => 'success',
            "status" => 1
        ]);
    }

    public function removeSearchHistory(Request $request)
    {
          $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'text' => 'required'
         ]);

         if ($validator->fails()) {
              return $this->sendError($validator->errors());
         }

         \DB::table('search_history')->where('user_id', $request->user_id)
         ->where('text', $request->text)
         ->delete();

        return response()->json([
            "message" => 'success',
            "status" => 1
        ]);
    }

    public function searchInput($text)
    {
         return response()->json([
            "result" => Product::where('status', 1)->orderByDesc('created_at')->where('product_name', 'LIKE', "%$text%")->take(10)->pluck('product_name'),
            "message" => 'success',
            "status" => 1
        ]);
    }

    public function searchResult($text)
    {
        if(strlen($text) < 3){
           return response()->json([
            "result" => [
                 'category' => [],
                 'brand' => [],
                 'tags' => []
         ],
            "message" => 'success',
            "status" => 1
        ]);
        }

      // cyril   $res = \DB::table('products')->whereJsonContains('tags',$text)->get();



         // latin $res = \DB::table('products')->whereRaw("lower(tags)  like ?", ["%".strtolower($text)."%"])->get();


       if(preg_match('/[А-Яа-яЁё]/u', $text))
       {
           $res = \DB::table('products')->whereJsonContains('tags',$text)->get();
       }else{
           $res = \DB::table('products')->whereRaw("lower(tags)  like ?", ["%".strtolower($text)."%"])->get();
       }

        $tags = [];

        foreach($res as $res){
            $array = json_decode($res->tags);

            $tags[] = array_values(preg_grep("/^$text.*/i", $array));
        }


        $category = Category::where('category_name', 'like', '%' . $text . '%')->with('parent')->get()->toArray();

        if(count($category) === 0 ){
            $category = Product::where('product_name', 'like', '%' . $text . '%')->with('subcategory')->take(4)->groupBy('subcategory_id')->select('id', 'product_name', 'subcategory_id')->get();

        }

        $result = [
         'category' => $category,
         'brand' => Brand::where('brand_name', 'like', '%' . $text . '%')->get(),
         'tags' =>  array_slice( array_unique(\Arr::collapse($tags)), 0, 10)
        ];


         return response()->json([
            "result" => $result,
            "message" => 'success',
            "status" => 1
        ]);

        //
    }


	public function categoriesList()
	{
	  return response()->json([
            "result" => Category::whereNull('parent_id')->get(['id', 'category_name']),
            "message" => 'success',
            "status" => 1
        ]);
	}

	public function subcategoriesList($category_id)
	{
	  return response()->json([
            "result" => Category::where('parent_id', $category_id)->get(['id', 'category_name', 'tax']),
            "message" => 'success',
            "status" => 1
        ]);
	}

       	public function brandsList()
	{
	  return response()->json([
            "result" => Brand::get(['id', 'brand_name']),
            "message" => 'success',
            "status" => 1
        ]);
	}





    }
