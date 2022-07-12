<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\CustomerAddress;

class AddressController extends Controller
{
    public function add_address(Request $request)
    {   
        $input = $request->all();

        $validator =  Validator::make($input,[
            'customer_id' => 'required',
            'customer_address' => 'required',
            'google_address' => 'required',
            'lat' => 'required',
            'lng' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        

        $url = 'https://maps.googleapis.com/maps/api/staticmap?center='.$input['lat'].','.$input['lng'].'&zoom=16&size=600x300&maptype=roadmap&markers=color:red%7Clabel:L%7C'.$input['lat'].','.$input['lng'].'&key='.env('MAP_KEY');
            $img = 'static_map/'.md5(time()).'.png';
            file_put_contents('uploads/'.$img, file_get_contents($url));

        $input['static_map'] = $img;
        $input['status'] = 1;

        if (CustomerAddress::create($input)) {
            return response()->json([
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

    public function get_address(Request $request)
    {
        $input = $request->all();

        $validator =  Validator::make($input,[
            'id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());
        }

        $address = CustomerAddress::where('id',$input['id'])->first();

        if ($address) {
            return response()->json([
                "result" => $address,
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

    public function update_address(Request $request)
    {
        $input = $request->all();

        $validator =  Validator::make($input,[
            'customer_id' => 'required',
            'address_id' => 'required',
            'customer_address' => 'required',
            'google_address' => 'required',
            'lat' => 'required',
            'lng' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        

        $url = 'https://maps.googleapis.com/maps/api/staticmap?center='.$input['lat'].','.$input['lng'].'&zoom=16&size=600x300&maptype=roadmap&markers=color:red%7Clabel:S%7C'.$input['lat'].','.$input['lng'].'&key='.env('MAP_KEY');
            $img = 'static_map/'.md5(time()).'.png';
            file_put_contents('uploads/'.$img, file_get_contents($url));

        $input['static_map'] = $img;
        $input['status'] = 1;

        if (CustomerAddress::where('id',$input['address_id'])->update($input)) {
            return response()->json([
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

    public function delete_address(Request $request)
    {
        $input = $request->all();

        $validator =  Validator::make($input,[
            'customer_id' => 'required',
            'address_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $res = CustomerAddress::where('id',$input['address_id'])->delete();
        if ($res) {
            $addresses = CustomerAddress::where('customer_id',$input['customer_id'])->orderBy('created_at', 'desc')->get();
            return response()->json([
                "result" => $addresses,
                "message" => 'Deleted Successfully',
                "status" => 1
            ]);
        } else {
            return response()->json([
                "message" => 'Sorry, something went wrong !',
                "status" => 0
            ]);
        }
    }

    public function all_address(Request $request){

        $input = $request->all();

        $validator =  Validator::make($input,[
            'customer_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());
        }

        $addresses = CustomerAddress::where('customer_id',$input['customer_id'])->orderBy('created_at', 'desc')->get();

        if ($addresses) {
            return response()->json([
                "result" => $addresses,
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
        return response()->json($response, 200);
    }


}
