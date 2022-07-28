<?php

namespace App\Http\Controllers;
use App\Helpers\Folders;
use App\Models\Brand;
use App\Models\Category;
use App\Models\MOption;
use App\Models\MOptionValue;
use App\Models\MProductOption;
use App\Models\MProductVariant;
use App\Models\MVariantValue;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorEarning;
use App\Models\VendorWalletHistory;
use App\Models\VendorWithdrawal;
use App\Models\VendorDocument;
use App\Models\VendorBanner;
use App\Models\Status;
use App\Models\Order;
use App\Models\Customer;
use App\Models\DeliveryPartner;
use App\Models\OrderProduct;
use App\Models\ProductOptionValue;
use App\Models\VendorTax;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;


class VendorController extends Controller
{
    public function register(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'store_name' => 'required',
            'vendor_name' => 'required',
            'user_name' => 'required',
            'type_of_activity' => 'required',
            'warehouse_address' => 'required',
            'phone_number' => 'required|numeric|digits_between:9,20|unique:customers,phone_number|unique:vendors,phone_number',
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
        $input['profile_picture'] = 'https://bissbuy.uz/web/img/avatar.jpg';
        $id = DB::table('admin_users')->insertGetId(
            ['username' => $input['user_name'], 'password' => $input['password'], 'name' => $input['store_name'], 'avatar' => 'https://bissbuy.uz/web/img/avatar.jpg']
        );

        DB::table('admin_role_users')->insert(
            ['role_id' => 2, 'user_id' => $id]
        );

        $input['admin_user_id'] = $id;


        // dd($input);


        $vendor = Vendor::create($input);

        if (is_object($vendor)) {
            //$this->update_status($vendor->id,$vendor->status);
            return response()->json([
                "result" => $vendor,
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

    public function update_status($id, $status)
    {
        $factory = (new Factory())->withDatabaseUri(env('FIREBASE_DB'));
        $database = $factory->createDatabase();
        $newPost = $database
            ->getReference('vendors/' . $id)
            ->update([
                'status' => $status,
                'booking_status' => 0,
                'order_id' => 0,
                'customer_name' => '',
            ]);
    }

    public function sendError($message)
    {
        $message = $message->all();
        $response['error'] = "validation_error";
        $response['message'] = implode('', $message);
        $response['status'] = "0";
        return response()->json($response, 200);
    }


    public function vendor_earning(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $data['total_earnings'] = VendorEarning::where('vendor_id', $input['id'])->get()->sum("total_amount");
        $data['today_earnings'] = VendorEarning::where('vendor_id', $input['id'])->whereDay('created_at', now()->day)->sum("total_amount");
        $data['earnings'] = VendorEarning::where('vendor_id', $input['id'])->get();

        if ($data) {
            return response()->json([
                "result" => $data,
                "count" => count($data),
                "message" => 'Success',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Something went wrong',
                "status" => 0
            ]);
        }

    }

    public function vendor_wallet(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $data['wallet_amount'] = Vendor::where('id', $input['id'])->value('wallet');

        $data['wallets'] = VendorWalletHistory::where('vendor_id', $input['id'])->get();

        if ($data) {
            return response()->json([
                "result" => $data,
                "count" => count($data),
                "message" => 'Success',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Something went wrong',
                "status" => 0
            ]);
        }

    }

    public function vendor_withdrawal_request(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'vendor_id' => 'required',
            'amount' => 'required'

        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $input['status'] = 6;
        $vendor = VendorWithdrawal::create($input);
        $vendor_wallet_amount = Vendor::where('id', $input['vendor_id'])->value('wallet');
        Vendor::where('id', $input['vendor_id'])->update(['wallet' => $vendor_wallet_amount - $input['amount']]);
        if (is_object($vendor)) {
            return response()->json([
                "result" => $vendor,
                "message" => 'success',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Sorry, something went wrong !',
                "status" => 0
            ]);
        }
    }

    public function vendor_withdrawal_history(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $data['wallet_amount'] = Vendor::where('id', $input['id'])->value('wallet');

        $data['withdraw'] = DB::table('vendor_withdrawals')
            ->leftjoin('statuses', 'statuses.id', '=', 'vendor_withdrawals.status')
            ->select('vendor_withdrawals.*', 'statuses.status_name')
            ->get();

        if ($data) {
            return response()->json([
                "result" => $data,
                "count" => count($data),
                "message" => 'Success',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Something went wrong',
                "status" => 0
            ]);
        }
    }

    public function profile_update(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $validator = Validator::make($input, [
            'vendor_name' => 'required',
            'store_name' => 'required',
            'vendor_email' => 'email|regex:/^[a-zA-Z]{1}/|unique:vendors,vendor_email|unique:customers,email',
            'phone_number' => 'required|numeric|digits_between:9,20|unique:customers,phone_number|unique:vendors,phone_number'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        if ($request->password) {
            $options = [
                'cost' => 12,
            ];
            $input['password'] = password_hash($input["password"], PASSWORD_DEFAULT, $options);
            $input['status'] = 1;
        } else {
            unset($input['password']);
        }

        if (Vendor::where('id', $id)->update($input)) {
            return response()->json([
                "result" => Vendor::select('id', 'vendor_name', 'store_name', 'phone_number', 'vendor_email', 'profile_picture', 'status')->where('id', $id)->first(),
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

    public function get_profile(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'vendor_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vendor = Vendor::where('id', $input['vendor_id'])->first();
        if (is_object($vendor)) {
            return response()->json([
                "result" => $vendor,
                "message" => 'Success',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Something went wrong',
                "status" => 0
            ]);
        }
    }

    public function check_phone(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'phone_number' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $data = array();
        $vendor = Vendor::where('phone_number', $input['phone_number'])->first();

        if (is_object($vendor)) {
            $data['is_available'] = 1;
            $data['otp'] = "";
            return response()->json([
                "result" => $data,
                "message" => 'Success',
                "status" => 1
            ]);
        } else {
            $data['is_available'] = 0;
            $data['otp'] = rand(1000, 9999);
            $message = "Hi" . env('APP_NAME') . "  , Your OTP code is:" . $data['otp'];
            //$message = "Hi Esycab"." , Your OTP code is:".$data['otp'];
            $this->sendSms('+91' . $input['phone_number'], $message);
            return response()->json([
                "result" => $data,
                "message" => 'Success',
                "status" => 1
            ]);
        }

    }

    public function login(Request $request)
    {
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
        $vendor = Vendor::where('phone_number', $credentials['phone_number'])->first();
        //print_r($vendor);exit;

        if (!($vendor)) {
            return response()->json([
                "message" => 'Invalid username or password',
                "status" => 0
            ]);
        }

        if (Hash::check($credentials['password'], $vendor->password)) {
            if ($vendor->status == 1) {
                Vendor::where('id',$vendor->id)->update([ 'fcm_token' => $input['fcm_token']]);
                return response()->json([
                    "result" => $vendor,
                    "message" => 'Success',
                    "status" => 1
                ]);
            } else {
                return response()->json([
                    "message" => 'Your account has been blocked',
                    "status" => 0
                ]);
            }
        } else {
            return response()->json([
                "message" => 'Invalid username or password',
                "status" => 0
            ]);
        }

    }

    public function forgot_password(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'phone_number' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vendor = Vendor::where('phone_number', $input['phone_number'])->first();
        if (is_object($vendor)) {
            $otp = rand(1000, 9999);
            Vendor::where('id', $vendor->id)->update(['otp' => $otp]);
            //$mail_header = array("otp" => $otp);
            //$this->send_mail($mail_header,'Reset Password',$input['email']);
            $message = "Hi" . env('APP_NAME') . " , Your OTP code is:" . $otp;
            $this->sendSms('+91' . $input['phone_number'], $message);
            return response()->json([
                "result" => Vendor::select('id', 'otp')->where('id', $vendor->id)->first(),
                "message" => 'Success',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Invalid phone number',
                "status" => 0
            ]);
        }

    }


    public function reset_password(Request $request)
    {

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

        if (Vendor::where('id', $input['id'])->update($input)) {
            return response()->json([
                "message" => 'Success',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Invalid phone number',
                "status" => 0
            ]);
        }
    }

    public function vendor_address(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $validator = Validator::make($input, [
            'address' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'manual_address' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $url = 'https://maps.googleapis.com/maps/api/staticmap?center=' . $input['latitude'] . ',' . $input['longitude'] . '&zoom=16&size=600x300&maptype=roadmap&markers=color:red%7Clabel:L%7C' . $input['latitude'] . ',' . $input['longitude'] . '&key=' . env('MAP_KEY');
        $img = 'static_map/' . md5(time()) . '.png';
        file_put_contents('uploads/' . $img, file_get_contents($url));

        $input['static_map'] = $img;
        $input['address_update_status'] = 1;
        if (Vendor::where('id', $id)->update($input)) {
            return response()->json([
                "result" => Vendor::select('id', 'store_name', 'address', 'longitude', 'latitude', 'manual_address', 'address_update_status')->where('id', $id)->first(),
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

    public function details(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'vendor_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vendor = Vendor::where('id', $input['vendor_id'])->first();

        if (is_object($vendor)) {
            return response()->json([
                "result" => $vendor,
                "message" => 'Success',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Success',
                "status" => 0
            ]);
        }
    }

    public function upload(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/vendor_images');
            $image->move($destinationPath, $name);
            return response()->json([
                "result" => 'vendor_images/' . $name,
                "message" => 'Success',
                "status" => 1
            ]);

        }
    }

    public function document_upload(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'vendor_id' => 'required',
            'id_proof' => 'required',
            'certificate' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $input['id_proof_status'] = 3;
        $input['certificate_status'] = 3;
        VendorDocument::create($input);
        $vendor = Vendor::where('id', $input['vendor_id'])->update(['document_update_status' => 1, 'document_approved_status' => 3]);
        return response()->json([
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function document_update(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'vendor_id' => 'required',
            'id_proof' => 'required',
            'certificate' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $old_document = VendorDocument::where('vendor_id', $input['vendor_id'])->first();
        if ($old_document->id_proof != $input['id_proof']) {
            $input['id_proof_status'] = 3;
        }

        if ($old_document->certificate != $input['certificate']) {
            $input['certificate_status'] = 3;
        }

        VendorDocument::where('vendor_id', $input['vendor_id'])->update($input);
        $vendor = Vendor::where('id', $input['vendor_id'])->update(['document_update_status' => 1, 'document_approved_status' => 3]);
        return response()->json([
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function document_details(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'vendor_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $document = VendorDocument::where('vendor_id', $input['vendor_id'])->first();
        $document->id_proof_status_name = Status::where('id', $document->id_proof_status)->value('status_name');
        $document->certificate_status_name = Status::where('id', $document->certificate_status)->value('status_name');
        return response()->json([
            "result" => $document,
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function dashboard_details(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'type' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($input['type'] == 1) {
            $result = DB::table('orders')
                ->leftJoin('order_statuses', 'order_statuses.id', 'orders.status')
                ->leftJoin('customer_addresses', 'customer_addresses.id', '=', 'orders.customer_address_id')
                ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                //->leftJoin('payment_modes', 'payment_modes.id', '=', 'orders.payment_mode')
                ->select('orders.*', 'order_statuses.status', 'customers.phone_number', 'customers.first_name', 'customer_addresses.customer_address')
                ->where('vendor_id', $input['id'])
                ->where('orders.status', '!=', 4)
                ->orderBy('id', 'DESC')
                ->get();
        }
        if ($input['type'] == 2) {
            $result = DB::table('orders')
                ->leftJoin('order_statuses', 'order_statuses.id', 'orders.status')
                ->leftJoin('customer_addresses', 'customer_addresses.id', '=', 'orders.customer_address_id')
                ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                //->leftJoin('payment_modes', 'payment_modes.id', '=', 'orders.payment_mode')
                ->select('orders.*', 'order_statuses.status', 'customers.phone_number', 'customers.first_name', 'customer_addresses.customer_address')
                ->where('vendor_id', $input['id'])
                ->where('orders.status', '=', 4)
                ->orderBy('id', 'DESC')
                ->get();
        }
        if ($input['type'] == 3) {
            $result = VendorEarning::where('vendor_id', $input['id'])->get();
        }
        if ($input['type'] == 4) {
            $result = VendorWalletHistory::where('vendor_id', $input['id'])->get();
        }


        if ($result) {
            return response()->json([
                "result" => $result,
                "message" => 'Success',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Something went wrong',
                "status" => 0
            ]);
        }

    }

    public function dashboard(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'vendor_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $result['pending_orders'] = Order::where('vendor_id', $input['vendor_id'])->where('status', 1)->count();
        $result['accepted_orders'] = Order::where('vendor_id', $input['vendor_id'])->where('status', 2)->count();
        $result['ontheway_orders'] = Order::where('vendor_id', $input['vendor_id'])->where('status', 3)->count();
	$result['cancel_orders'] = Order::where('vendor_id', $input['vendor_id'])->where('status', 5)->count();
        $result['completed_orders'] = Order::where('vendor_id', $input['vendor_id'])->where('status', 6)->count();




        if ($result) {
            return response()->json([
                "result" => $result,
                "message" => 'Success',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Something went wrong',
                "status" => 0
            ]);
        }

    }

    public function order_accept(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'order_id' => 'required',
            //'status' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }


        Order::where('id', $input['order_id'])->update(['status' => 2]);

        $order = Order::where('id', $input['order_id'])->first();
        //$this->update_customer_status($order->id,$order->status,$order->customer_id);
        //$this->find_fcm_message('order_status_'.$order->status,$order->customer_id,0,0);
        //$this->update_vendor_booking($order->vendor_id);

        $message = [
            "title" => "Новое сообщение",
            "body" => "Ваш заказ под номером $order->id был принят."
        ];
        $API_KEY = env('USER_KEY');
        $this->sendNotification($order->customer->fcm_token, $message, $API_KEY);


        foreach(DeliveryPartner::get() as $delivery){
            $message = [
                "title" => "Новое сообщение",
                "body" => "Новый заказ принят под номером $order->id."
            ];
            $API_KEY = env('DELIVERY_KEY');
            $this->sendNotification($delivery->fcm_token, $message, $API_KEY);
        }


        return response()->json([
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function update_customer_status($id, $status, $customer_id)
    {
        $factory = (new Factory())->withDatabaseUri(env('FIREBASE_DB'));
        $database = $factory->createDatabase();
        $newPost = $database
            ->getReference('customers/' . $customer_id . '/orders/' . $id)
            ->update([
                'status' => $status,
            ]);
    }

    public function update_vendor_booking($vendor_id)
    {
        $order = Order::where('vendor_id', $vendor_id)->where('status', 1)->first();

        $factory = (new Factory())->withDatabaseUri(env('FIREBASE_DB'));
        $database = $factory->createDatabase();
        if (is_object($order)) {
            $customer_name = Customer::where('id', $order->customer_id)->value('first_name');
            $database->getReference('vendors/' . $vendor_id)
                ->update([
                    'booking_status' => 1,
                    'order_id' => $order->id,
                    'customer_name' => $customer_name
                ]);
        } else {
            $database->getReference('vendors/' . $vendor_id)
                ->update([
                    'booking_status' => 0,
                    'order_id' => 0,
                    'customer_name' => ''
                ]);
        }
    }

    public function vendor_banners()
    {

        $data = VendorBanner::where('status', 1)->select('banners as url')->get();

        $sliders = [];
        foreach ($data as $key => $value) {
            $sliders[$key]['url'] = env('APP_URL') . ('/public/upload/') . $value->url;
        }
        return response()->json([
            "result" => $sliders,
            "message" => 'Success',
            "status" => 1
        ]);
    }


    public function set_vendor_details(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'brand' => 'required',
            'model' => 'required',
            'manufacturer_county' => 'required',
            'authenticity' => 'required',
            'overall_dimensions_without_packaging' => 'required',
            'weight_without_packaging' => 'required',
            'guarantee' => 'required',
            'product_availability_period' => 'required',
            'delivery' => 'required',
            'delivery_price' => 'required',
            'delivery_term' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if (Vendor::where('id', $request->input('id'))->update($input)) {
            return response()->json([
                "result" => Vendor::select('id', 'vendor_name', 'store_name', 'brand', 'model', 'manufacturer_county', 'authenticity', 'overall_dimensions_without_packaging', 'weight_without_packaging', 'guarantee', 'product_availability_period', 'delivery', 'delivery_price', 'delivery_term')->where('id', $request->input('id'))->first(),
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

    public function get_vendor_orders($id)
    {
        $orders = Order::with('vendor')->where('vendor_id', $id)->whereNotIn('status', [6,5])->select('total', 'id', 'status', 'vendor_id')->get();
        $result = [];
        foreach ($orders as $order) {
            if ($order->products->count() > 0) {
                $image = $order->products->first()->product_image;
            } else {
                $image = '';
            }


            $result[] = [
                'total' => $order->total,
                'id' => $order->id,
                'status' => $order->status,
                'image' => $image,
                'vendor' => $order->vendor->vendor_name,
                'vendor_address' => $order->vendor->warehouse_address,
		'attributes' => json_decode($order->products->first()->attributes, true),
		'quantity' => $order->products->first()->qty
            ];
        }

        return response()->json([
            "result" => $result,
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function get_vendor_order_info($id)
    {
        $order = Order::with(['products', 'vendor'])->where('id', $id)->first();
        $result = null;
        $products = [];
        foreach ($order->products as $product) {
            $products[] = [
                'qty' => $product->qty,
                'product_name' => $product->product_name,
                'product_price' => $product->product_price
            ];
        }

        if ($order->products->count() > 0) {
            $image = $order->products->first()->product_image;
        } else {
            $image = '';
        }

        $result = [
            'id' => $order->id,
            'status' => $order->status,
            'total' => $order->total,
            'image' => $image,
            //'sub_total' => $order->sub_total,
            //'discount' => $order->discount,
            'products' => $products,
            'vendor' => $order->vendor->vendor_name,
            'vendor_address' => $order->vendor->warehouse_address
        ];

        return response()->json([
            "result" => $result,
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function get_vendor_archive_orders($id)
    {
        $orders = Order::with('vendor')->where('status', 6)->where('vendor_id', $id)->select('total', 'id', 'status', 'vendor_id')->get();
        $result = [];
        foreach ($orders as $order) {
            if ($order->products->count() > 0) {
                $image = $order->products->first()->product_image;
            } else {
                $image = '';
            }

            $result[] = [
                'total' => $order->total,
                'id' => $order->id,
                'status' => $order->status,
                'image' => $image,
                'vendor' => $order->vendor->vendor_name,
                'vendor_address' => $order->vendor->warehouse_address
            ];
        }

        return response()->json([
            "result" => $result,
            "message" => 'Success',
            "status" => 1
        ]);
    }


    public function create_product(Request $request)
    {
        $input = $request->except('photos', 'options');
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'brand_id' => 'required',
            'vendor_id' => 'required',
            'product_name' => 'required',
            'tags' => 'required',
            // 'photos' => 'required',
            // 'options' => 'required',
            //'short_description' => 'required',
            //'product_price' => 'required',

            //'current_stock' => 'required',
            //'min_qty' => 'required',
        ]);



        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($request->has('tags')) {
            $tags = json_encode($request->tags);
        } else {
            $tags = null;
        }
        //$i=0;
        // $extra_photos = [];
        // //$cover_image = 'default_path';

        // if($request->photos){
        //     foreach ($request->photos as $photo){
        //         //if ($i == 0)
        //         //{
        //         //    $cover_image = Storage::disk('admin')->put(time(), $photo);
        //         //}else{
        //             $extra_photos[] = Storage::disk('admin')->put(time(), $photo);
        //         //}
        //         //$i++;
        //      }
        // }





        $product = Product::create([
            'product_name' => $request->product_name,
            'vendor_id' => $request->vendor_id,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'brand_id' => $request->brand_id,
            'tags' => $tags,
            'status' => 1
        ]);



        // foreach ($extra_photos as $extra_photo){
        //     ProductImage::create([
        //         'product_id' => $product->id,
        //         'product_image' => $extra_photo,
        //         'status' => 1

        //     ]);
        // }

        // if($request->options != null){
        //     foreach($request->options as $option_id => $option_value_ids)
        //     {
        //       \App\Models\ProductAttribute::create([
        //          'product_id' => $product->id,
        //          'option_id' => $option_id,
        //          'option_value_id' => $option_value_ids
        //       ]);
        //     }
        // }

        // foreach ($request->options as $key => $value){
        //     $value = json_decode($value,true);

        //     \App\Models\ProductAttribute::create([
        //         'product_id' => $product->id,
        //         'option_id' => $key,
        //         'option_value_id' => $value
        //     ]);
        // }

        return response()->json([
            "result" => $product,
            "message" => 'Success',
            "status" => 1
        ]);

    }

    public function setOptions(Request $request)
    {
        return response()->json($request->all());
        $product = Product::find($request->product_id);

        if(!$product)
            return response()->json([
            "message" => 'product not found',
            "status" => 0
        ]);

        $variant = [];
        $variant['description'] = $request->description;
        $variant['images'] = [];
        $variant['options'] = [];
        $variant['price'] = $request->price;

        if ($request->has('options')) {
            foreach (json_decode($request->options, true)  as $option => $value) {
                $values = [];
                $opt = ProductOption::find($option)->option_name;
                foreach (explode(",", $value) as $val) {
                    $values[] = [
                        'id' => $val,
                        'name' => ProductAttributeOption::find($val)->product_option_value
                        ];
                }
                $variant["options"][] = [
                    'option' => ['id'=> $option,'name'=> $opt],
                    'values' => $values
                ] ;
            }
        }


        if ($request->has('photos')) {
            foreach ($request->img as $image) {
                $mime = $image->getMimeType();
                $filename = $this->upload($image, Folders::PRODUCT);

                $variant["images"][] = [
                    'path' => $filename,
                    'mime_type' => $mime
                ];
            }
        }


        $prod_variants = is_null($product->variants) ? '{}' : $product->variants;

        $arr_variants = json_decode($prod_variants, true);
        $arr_variants[] = $variant;
        $product->variants = $arr_variants;
        $product->save();

        return response()->json([
            "result" => $product,
            "message" => 'Success',
            "status" => 1
        ]);
    }


    public function get_product_options()
    {
        return response()->json([
            "result" => \App\Models\ProductOption::get(['id', 'option_name']),
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function get_product_options_values($id)
    {
        return response()->json([
            "result" => \App\Models\ProductOptionValue::where('product_option_id', $id)->get(['product_option_id', 'product_option_value']),
            "message" => 'Success',
            "status" => 1
        ]);
    }

    public function edit_product(Request $request)
    {
        $input = $request->except('photos', 'options','product_id');
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'brand_id' => 'required',
            'vendor_id' => 'required',
            'product_name' => 'required',
            // 'photos' => 'required',
            // 'options' => 'required',
            'short_description' => 'required',
            'product_price' => 'required',
            'key_words' => 'required',
            'current_stock' => 'required',
            'min_qty' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $i=0;
        $extra_photos = [];
        $cover_image = 'default_path';
        // foreach ($request->photos as $photo){
        //    if ($i == 0)
        //    {
        //        $cover_image = Storage::disk('admin')->put(time(), $photo);
        //    }else{
        //        $extra_photos[] = Storage::disk('admin')->put(time(), $photo);
        //    }
        //    $i++;
        // }

        //$input['cover_image'] = $cover_image;
        //$input['status'] = 1;


        $product = Product::find($request->product_id)->update($input);



        // foreach ($extra_photos as $extra_photo){
        //     ProductImage::create([
        //         'product_id' => $product->id,
        //         'product_image' => $extra_photo,
        //         'status' => 1

        //     ]);
        // }


        // foreach($request->options as $options)
        // {
        //     foreach($options as $key => $value){
        //     \App\Models\ProductAttribute::create([
        //         'product_id' => $product->id,
        //         'option_id' => $key,
        //         'option_value_id' => $value
        //     ]);
        //     }
        // }

        // foreach ($request->options as $key => $value){
        //     $value = json_decode($value,true);

        //     \App\Models\ProductAttribute::create([
        //         'product_id' => $product->id,
        //         'option_id' => $key,
        //         'option_value_id' => $value
        //     ]);
        // }

        return response()->json([
            "result" => $product,
            "message" => 'Success',
            "status" => 1
        ]);

    }


    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

	if($request->status == 6){
	$product = Product::find($request->product_id)->update([
            'completed_at' => now(),
	    'status' => $request->status
        ]);
	}
	elseif($request->status == 4){
	  $product = Product::find($request->product_id)->update([
	    'delivered_at' => now(),
            'status' => $request->status
        ]);
	}
	else{
	  $product = Product::find($request->product_id)->update([
            'status' => $request->status
        ]);
	}



        return response()->json([
            "result" => $product,
            "message" => 'Success',
            "status" => 1
        ]);

    }

    public function vendor_statistic($vendor_id)
    {
        $year_ids = DB::table('orders')
          ->where( 'created_at', '>', Carbon::now()->subDays(365))
          ->where('status', 4)
          ->pluck('id');

          $month_ids = DB::table('orders')
          ->where( 'created_at', '>', Carbon::now()->subDays(30))
          ->where('status', 4)
          ->pluck('id');

          $week_ids = DB::table('orders')
          ->where( 'created_at', '>', Carbon::now()->subDays(7))
          ->where('status', 4)
          ->pluck('id');

          $day_ids = DB::table('orders')
          ->where( 'created_at', '>', Carbon::now()->subDays(1))
          ->where('status', 4)
          ->pluck('id');




          $last_year =[
            'amount' => DB::table('orders')
             ->where( 'created_at', '>', Carbon::now()->subDays(30))
             ->where('status', 4)
             ->sum('total'),
             'products_count' => OrderProduct::whereIn('order_id', $year_ids)->count()
            ];


           $last_month =[
           'amount' => DB::table('orders')
            ->where( 'created_at', '>', Carbon::now()->subDays(30))
            ->where('status', 4)
            ->sum('total'),
            'products_count' => OrderProduct::whereIn('order_id', $month_ids)->count()
           ];

            $last_week = [
                'amount' => DB::table('orders')
                ->where( 'created_at', '>', Carbon::now()->subDays(7))
                ->where('status', 4)
                ->sum('total'),
                'products_count' =>  OrderProduct::whereIn('order_id', $week_ids)->count()
            ];

            $last_day = [
                'amount' => DB::table('orders')
                ->where( 'created_at', '>', Carbon::now()->subDays(1))
                ->where('status', 4)
                ->sum('total'),
                'products_count' => OrderProduct::whereIn('order_id', $day_ids)->count()
            ];




        $result = [
            'last_year' => $last_year,
            'last_month' => $last_month,
            'last_week' => $last_week,
            'last_day' => $last_day
        ];

          return response()->json([
            "result" => $result,
            "message" => 'Success',
            "status" => 1
        ]);

    }


    public function vendor_order_statistics($vendor_id)
    {
        $vendor = Vendor::find($vendor_id);
        if($vendor){
            $result = [
                'vendor_name' => $vendor->vendor_name,
                'total_orders_sum' => Order::where('vendor_id', $vendor->id)->sum('total'),
                'total_orders_sum_4_status' => Order::where('vendor_id', $vendor->id)->where('status', 4)->sum('total'),
                'total_tax' => Order::where('vendor_id', $vendor->id)->where('status', 4)->sum('tax'),
                'vendor_id' => $vendor->id,
                'total_tax_paid' => VendorTax::where('vendor_id', $vendor->id)->sum('amount'),
                'debt' => $vendor->debt
             ];

             return response()->json([
                "result" => $result,
                "message" => 'Success',
                "status" => 1
            ]);
        }

        return response()->json([
            "result" =>null,
            "message" => 'record not found',
            "status" => 0
        ]);
    }



    public function option_vals(Request $request)
    {

        $option_id = $request->get('q');

        //$q = DB::table('product_option_values')->where('product_option_id', $option_id)->pluck('product_option_value');

        $option_values = ProductOptionValue::where('product_option_id', $option_id)->pluck('product_option_value','id');

        return $option_values;
    }


    public function subcats(Request $request)
    {
        $category_id = $request->get('q');

        $subcategories = Category::where('parent_id', $category_id)->get(['id', DB::raw('category_name as text')]);

        return $subcategories;
    }




    public function send_notify(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'body' => 'required',
            'title' => 'required',
            'api_key' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $API_KEY = $request->api_key;
        return $this->sendNotification($request->token, array(
            "title" => $request->title,
            "body" => $request->body
          ), $API_KEY);
    }


    public function brands($text = null)
    {

        if($text != null){
            $result = Brand::where('brand_name', 'like', '%' . $text . '%')->get();
        }
        else{
            $result = Brand::get();
        }

        return response()->json([
            "result" => $result,
            "message" => 'record not found',
            "status" => 0
        ]);
    }


    function send_notification_FCM() {
                // FCM API Url
            $url = 'https://fcm.googleapis.com/fcm/send';

            // Put your Server Key here
            $apiKey = 'AAAA8WM1AIg:APA91bFzjhx2u3RiEUa_8OFpjbt36PxiJu4420HgOOg9oTfJncYWRG5sWkiylWAFigYNF0fbseESVXwkbE2kU3EOjH_RfQ9JPrKXiXQkAsIPF1bJAn8acmGve2lQ_vj827GakEeJT9YI';

            // Compile headers in one variable
            $headers = array (
                'Authorization:key=' . $apiKey,
                'Content-Type:application/json'
            );

            // Add notification content to a variable for easy reference
            $notifData = [
                'title' => "Test Title",
                'body' => "Test notification body",
                //  "image": "url-to-image",//Optional
                'click_action' => "activities.NotifHandlerActivity" //Action/Activity - Optional
            ];

            $dataPayload = ['to'=> 'My Name',
            'points'=>80,
            'other_data' => 'This is extra payload'
            ];

            // Create the api body
            $apiBody = [
                'notification' => $notifData,
                'data' => $dataPayload, //Optional
                'time_to_live' => 600, // optional - In Seconds
                //'to' => '/topics/mytargettopic'
                //'registration_ids' = ID ARRAY
                'to' => 'c0x_Rp63TxO-3Eyd1rVbKL:APA91bET1QYnxUOX8OwGR1YtTrR9gpSqe63qPfYiFVOb31B1jk9Bbn3-b5bx0gap0vWZ9PU8qn83plp3oQ3EGgE5ZsF8bcf_KmAxxWZlGldc71Q9ETYjkKJHwRa-nwnUOkBSQs7SvzJV'
            ];

            // Initialize curl with the prepared headers and body
            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_POST, true);
            curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode($apiBody));

            // Execute call and save result
            $result = curl_exec($ch);
            print($result);
            // Close curl after call
            curl_close($ch);

            return $result;
    }

    public function vendor_categories($vendor_id)
    {
       $vendor = Vendor::find($vendor_id);

      $cat_ids = Product::where('vendor_id', $vendor_id)->with('category')->distinct()->pluck('category_id');
      $categories = Category::whereIn('id', $cat_ids)->get(['id', 'category_name']);


      return response()->json([
            "result" => $categories,
            "status" => 1
        ]);

    }

    public function products_by_vendor_categroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'category_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }





            $vendor = Vendor::where('id',$request->vendor_id)->get(['vendor_name', 'vendor_email', 'phone_number', 'store_image', 'description']);

            $products = Product::active()->with('images', 'brand')->where('category_id', $request->category_id)->where('vendor_id', $request->vendor_id)->paginate(20);


            $total_liked = Product::active()->where('category_id', $request->category_id)->where('vendor_id', $request->vendor_id)->sum('total_like');
            $total_view  = Product::active()->where('category_id', $request->category_id)->where('vendor_id', $request->vendor_id)->sum('total_view');
            $total_share = Product::active()->where('category_id', $request->category_id)->where('vendor_id', $request->vendor_id)->sum('total_sharing');

            foreach($products as $item)
            {
                 $price = \DB::table('m_variants_values')->where('product_id', $item->id)->whereNotNull('price')->exists() ? \DB::table('m_variants_values')->where('product_id', $item->id)->whereNotNull('price')->first()->price : $item->product_price;
                 $item->product_price = $price;
            }

            $res = [
                'vendor' => $vendor,
                'total_like' => $total_liked,
                'total_view' => $total_view,
                'total_share' => $total_share,
                'products' => $products->items()
            ];





               $pagination = [
                    'total' => $products->total(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'next_page_url' => $products->nextPageUrl()
             ];


        //  foreach($prods as $prod)
        //     {
        //         $result['products'] = [
        //             'id'=> $prod->id,
        //             'brand'=> $prod->brand,
        //             'product_price'=> $prod->product_price,
        //             'product_name'=> $prod->product_name,
        //             'images' => $prod->images,

        //         ];
        //     }

        $result['pagination'] = $pagination;
        $result['data'] = $res;


           return response()->json([
                "message" => 'Success',
                "result" => $result,
                "status" => 1
            ]);






        // $prods = Product::where('vendor_id', $request->vendor_id)->where('category_id', $request->category_id)->paginate();

        // $pagination = [
        //             'total' => $prods->total(),
        //             'current_page' => $prods->currentPage(),
        //             'last_page' => $prods->lastPage(),
        //             'per_page' => $prods->perPage(),
        //             'nex_page_url' => $prods->nextPageUrl()
        // ];

        // $result = [];
        //  foreach($prods as $prod)
        //     {
        //         $result['products'] = [
        //             'id'=> $prod->id,
        //             'brand'=> $prod->brand,
        //             'product_price'=> $prod->product_price,
        //             'product_name'=> $prod->product_name,
        //             'images' => $prod->images,

        //         ];
        //     }

        // $result['pagination'] = $pagination;
        //   return response()->json([
        //     "result" => $result ,
        //     "status" => 1
        // ]);
    }


	public function change_product_status(Request $request)
	{
	   $validator = Validator::make($request->all(), [
            'product_id' => 'required'
           ]);
           if ($validator->fails()) {
              return $this->sendError($validator->errors());
            }

	$product = Product::where('id',$request->product_id)->withoutGlobalScope('active')->first();
		$product->status = $product->status == 1 ? 2 : 1;
	$product->save();

	 return response()->json([
                "message" => 'Success',
                "status" => 1
            ]);

	}


	public function createProduct(Request $request)
	{
	  $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'category_id' => 'required',
	    'subcategory_id' => 'required',
	    'brand_id' => 'required',
            'type' => 'required'
          ]);

          if ($validator->fails()) {
              return $this->sendError($validator->errors());
          }

	  $product = Product::create($request->all());

	return response()->json([
            "message" => 'success',
	    "result" => [ "id" => $product->id],
            "status" => 1
        ]);


	}



         public function create_product_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_price' => 'sometimes|required_if:type,1',
            'short_description' => 'sometimes|required_if:type,1',
//            'images' => 'required_if:type,1',
            'status' => 'required',
            'discount_period' => 'required_if:is_discount,1',
            'discount_price' => 'required_if:is_discount,1',
            'is_discount' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
             $data['is_discount']=$request->is_discount;
             $data['status']=$request->status;
             $data['short_description']=$request->short_description;
            $data['product_price']=$request->product_price;

        if ($request->is_discount == 1) {
            $from = substr($request->discount_period, 0, 10);
            $to = substr($request->discount_period, 13, 10);
            $data['discount_from'] = Carbon::createFromFormat('m/d/Y', $from)->endOfDay()->format('Y-m-d H:i:s');
            $data['discount_to'] = Carbon::createFromFormat('m/d/Y', $to)->endOfDay()->format('Y-m-d H:i:s');
            $data['discount_percent'] = round($request->discount_price / $request->product_price * 100);
            $data['discount_price']=$request->discount_price;
        } else {
            $data['discount_from'] = null;
            $data['discount_to'] = null;
            $data['discount_price'] = null;
            $data['discount_percent'] = null;
        }

       Product::find($request->product_id)->update($data);

           if ($request->type==1)
           {
               return response()->json([
                   'message'=>'success',
                   'status'=>1
               ]);
           }
           else
           {
               return response()->json([
                   'data'=> ['id'=> $request->id],
                   'message'=>'success',
                   'status'=>1,
               ]);
           }

    }

//    public function update(Request $request)
//    {
//
//        return $response= Http::post('http://admin.bissbuy.uz/api/getImages',[
//            'data'=>$request->all(),
//        ]);
//    }




    public function get_atr($product_id)
    {

        $product=Product::find($product_id);
        $attr_id=DB::table('attribute_category')->where('category_id',$product->subcategory_id)->pluck('attribute_id');
        $m_option=DB::table('m_options')->whereIn('id',$attr_id)->get();

        return response()->json([
            'data'=> ['atributes'=>$m_option,'product_id'=>$product_id],
            'message'=>'success',
            'status'=>1
        ]);

    }

    public function get_atr_col($option_id)
    {
        $mo_values=DB::table('m_option_values')->where('option_id',$option_id)->get();
        return response()->json([
            'data'=>$mo_values,
            'message'=>'success',
            'status'=>'1',
        ]);
    }

    public function post_to_option(Request $request)
    {
        //   return $request->value_ids;
        DB::table('m_product_options')->insert([
            'product_id' => $request->product_id,
            'option_id' =>$request->option_id,
            'values_ids' =>$request->value_ids,
        ]);


        $options=MProductOption::where('product_id',$request->product_id)->get();

        $i=0;
        $a=0;
        //          $value_name=[];
        $option_name=[];

        foreach ($options as $option)
        {
            $value_name=[];
            foreach (json_decode($option->values_ids) as $value_id)
            {
                $value_name['option_id']=MOptionValue::find($value_id)->option_id;
//                   return $value_id;
                $value_name['value'.$a]=MOptionValue::find($value_id)->value_name;
                $a++;

            }

            $remov_id=$option->id;
            $option_name[$option->option->option_name]=$value_name;
            $i++;
        }

        return response()->json([
            'attributes'=> $option_name,
            'product_id'=>$request->product_id,
            'remov'=>$option->id,
            'message'=>'success',
            'status'=>1,
        ]);


    }


    public function select_to_variant_col($product_id)
    {
        $parent = MProductOption::where('product_id', $product_id)->first();
        if ($parent)
        {
            $values = json_decode($parent->values_ids);
            $option= MOptionValue::whereIn('id', $values)->pluck('value_name', 'id')->toArray();
            $option_name=$parent->option->option_name;

        }

        return response()->json([
            'parent_list'=>['option_name'=>$option_name,'option'=>$option,'parent_opion_id'=>$parent->option_id],
            'message'=>'success',
            'status'=>1
        ]);

    }



    public function select_to_variant($product_id)
    {
        $child = MProductOption::where('product_id', $product_id)->latest('id')->first();

        if (isset($child) && $child->id != MProductOption::where('product_id', $product_id)->first()->id)
        {

            $values = json_decode($child->values_ids);
            $option_child = MOptionValue::whereIn('id', $values)->pluck('value_name', 'id')->toArray();
            $option_name=$child->option->option_name;
        }
        return response()->json([
            'child_list'=>['option_name'=>$option_name,'option'=>$option_child,'child_option_id'=>$child->option_id],
            'message'=>'success',
            'status'=>1
        ]);

    }



    public function create_new_variant(Request $request)

    {
        $validator = Validator::make($request->all(), [
            'parent_option_id' => 'required',
            'child_option_id' => 'sometimes',
            'price' => 'required',
//            'object.image' => 'sometimes',
            'short_description' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $variant = MProductVariant::create([
            'product_id' => $request->product_id,
            'sku_id' => Str::uuid(),
        ]);
        return isset($request->child_option_id);
        if (isset($request->child_option_id)) {
            MVariantValue::create([
                'product_id' => $request->product_id,
                'variant_id' => $variant->sku_id,
                'option_id' => $request->parent_option_id,
                'value_id' => $request->parent_value_id,
            ]);

            $imagable = MVariantValue::create([
                'product_id' => $request->product_id,
                'variant_id' => $variant->sku_id,
                'option_id' => $request->child_option_id,
                'value_id' => $request->child_value_id,
                'price' => $request->price,
                'status' => 1,
                'short_description' => isset($request->short_description) ? $request->short_description : null
            ]);
        } else {

            $imagable = MVariantValue::create([
                'product_id' => $request->product_id,
                'variant_id' => $variant->sku_id,
                'option_id' => $request->parent_option_id,
                'value_id' => $request->parent_value_id,
                'price' => $request->price,
                'status' => 1,
                'short_description' => isset($request->short_description) ?$request->short_description : null
            ]);
        }

        return response()->json([
            'sku_id'=>$variant->sku_id,
            'message'=>'success',
            'status'=>1,
        ]);

    }









    public function edit_attr(Request $request)
    {

        MProductOption::where('product_id', $request->product_id)->where('option_id', $request->option_id)->update([
            'values_ids' => json_encode($request->values_ids),
        ]);

        return response()->json([
            'message'=>'Category was update successfully',
            'status'=>1
        ]);
    }


    public function atr_table($product_id)
    {



        $parent = MProductOption::where('product_id', $product_id)->first();
        if ($parent)
        {

            $values = json_decode($parent->values_ids);
            $option= MOptionValue::whereIn('id', $values)->pluck('value_name', 'id')->toArray();
            $parent_name=$parent->option->option_name;

        }
        $child_name=null;
        $child = MProductOption::where('product_id', $product_id)->latest('id')->first();

        if (isset($child) && $child->id != MProductOption::where('product_id', $product_id)->first()->id) {

            $values = json_decode($child->values_ids);
            $option_child = MOptionValue::whereIn('id', $values)->pluck('value_name', 'id')->toArray();
            $child_name = $child->option->option_name;

        }



        $parents=[];
        $p=0;
        $list_prod_variants = \App\Models\MProductVariant::where('product_id', $product_id)->orderBy('order_number')->get();
//
        foreach ($list_prod_variants as $product_variant)
        {
            $parent = \App\Models\MVariantValue::where('product_id', $product_id)->where('variant_id', $product_variant->sku_id)->first();
            $child = \App\Models\MVariantValue::where('product_id', $product_id)->latest('id')->where('variant_id', $product_variant->sku_id)->first();
            $parent_option = \App\Models\MOption::find($parent->option_id);
            $parent_value = \App\Models\MOptionValue::find($parent->value_id);
            $child_option = \App\Models\MOption::find($child->option_id);
            $child_value = \App\Models\MOptionValue::find($child->value_id);
//
            $p++;
            $parents[]=['list'=>$child,'name'=>$child_value->value_name,'name1'=>$parent_value->value_name];
        }



        return response()->json([
            'data' => $parents,
            'child_list'=>['child_option_name'=>$child_name,'child'=>$child],
            'parent_list'=>['option'=>$option,'parent_option_name' =>$parent_name,'parent_opion_id'=>$parent->option_id],
            'message' => 'success',
            'status' => 1,
        ]);


    }




    public function show_edit(Request $request)

    {
//          $request->values_ids=["14"];
        MProductOption::where('product_id', $request->product_id)->where('option_id', $request->option_id)->update([
            'values_ids' => $request->values_ids,
        ]);

        return response()->json([
            'message'=>'success',
            'status'=>1
        ]);
    }


    public function show_delete(Request $request)

    {

        MProductOption::where('product_id', $request->product_id)->where('option_id', $request->option_id)->delete();
        return response()->json([
            'message'=>'success',
            'status'=>1
        ]);
    }


    public function chang_status($sku_id)
    {
//        return $sku_id;
        $vv = MVariantValue::where('variant_id',$sku_id)->latest('id')->first();
        $status = $vv->status == 1 ? 2 : 1;
        $vv->status = $status;
        $vv->save();

        return response()->json([
            'message'=>'success',
            'status'=>1
        ]);
    }

    public function remove_variatn(Request $request)
    {
        MProductVariant::where('sku_id', $request->variant_sku)->where('product_id', $request->product_id)->delete();
        MVariantValue::where('variant_id', $request->variant_sku)->where('product_id', $request->product_id)->delete();

        return response()->json([
            'message'=>'success',
            'status'=>1
        ]);

    }




    public function show_edit_table($sku_id)
    {
        $variants=MVariantValue::where('variant_id', $sku_id)->get();

        if ($variants->count()==2)
        {
            $first = $variants[0];
            $second = $variants[1];
            $data = [
                'parent' => [
                    'option_name' => MOption::find($first->option_id)->option_name,
                    'value_name' => MOptionValue::find($first->value_id)->value_name,
                ],
                'child' => [
                    'option_name' => MOption::find($second->option_id)->option_name,
                    'value_name' => MOptionValue::find($second->value_id)->value_name,
                    'images' => ProductImage::where('sku_id', $sku_id)->get(),
                    'price' => $second->price,
                    'status' => $second->status,
                    'short_description' => $second->short_description,
                ]
            ];
        }
        else
        {
            $first = $variants[0];
            $data = [
                'parent' => [
                    'option_name' => MOption::find($first->option_id)->option_name,
                    'value_name' => MOptionValue::find($first->value_id)->value_name,
                    'images' => ProductImage::where('sku_id', $sku_id)->get(),
                    'price' => $first->price,
                    'short_description' => $first->short_description,
                ]
            ];
        }
        return response()->json([
            'data'=>$data,
            'sku_id'=>$sku_id,
            'product_id'=>MVariantValue::where('variant_id', $sku_id)->first()->product_id,
            'message'=>'success',
            'status'=>1,
        ]);

    }


    public function show_edit_variant(Request $request)
    {
        $variants=MVariantValue::where('variant_id', $request->sku_id)->latest('id')->first()->update([
            'short_description'=>$request->short_description,
            'price'=>$request->price,
        ]);


        return response()->json([
            'message'=>'success',
            'status'=>1,
        ]);
    }


    public function destroyImage($id)
    {
        $image = ProductImage::find($id);
        $file = public_path() . '/upload/' . $image->product_image;
        if (File::exists($file)) {
            File::delete($file);
        }

        $image->delete();
        return response()->json([
            'message'=>'success',
            'status'=>1
        ]);


    }







}
