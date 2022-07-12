<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mail; 
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use App\FcmNotification;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\DeliveryBoy;
use Twilio\Rest\Client; 

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function send_mail($mail_header,$subject,$to_mail){
    	Mail::send('mail_templates.forgot_password', $mail_header, function ($message)
		 use ($subject,$to_mail) {
			$message->from(env('MAIL_USERNAME'), env('APP_NAME'));
			$message->subject($subject);
			$message->to($to_mail);
		});
    }
    
    public function send_fcm($title,$description,$token){
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $optionBuilder->setPriority("high");
        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($description)
        				    ->setSound('default')->setBadge(1);
        
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);
        
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        
        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
        
        return $downstreamResponse->numberSuccess();
    }

    public function find_fcm_message($slug,$customer_id,$vendor_id,$partner_id){
        $message = FcmNotification::where('slug',$slug)->first();
        //print_r($message);exit;
        if($customer_id){
            $fcm_token = Customer::where('id',$customer_id)->value('fcm_token');
            if($fcm_token){
                $this->send_fcm($message->customer_title, $message->customer_description, $fcm_token);
            }
        }
        
        if($vendor_id){
            $fcm_token = Vendor::where('id',$vendor_id)->value('fcm_token');
            if($fcm_token){
                $this->send_fcm($message->vendor_title, $message->vendor_description, $fcm_token);
            }
        }
        
        if($partner_id){
            $fcm_token = DeliveryBoy::where('id',$partner_id)->value('fcm_token');
            if($fcm_token){
                $this->send_fcm($message->partner_title, $message->partner_description, $fcm_token);
            }
        }
    }

     public function sendSms($phone_number,$message)
    {
        $sid    = env( 'TWILIO_SID' );
        $token  = env( 'TWILIO_TOKEN' );
        $client = new Client( $sid, $token );
        $client->messages->create($phone_number,[ 'from' => env( 'TWILIO_FROM' ),'body' => $message,]);
        return true;
   }





   public function sendNotification($device_token, $message, $SERVER_API_KEY)
    {
                       // FCM API Url
                       $url = 'https://fcm.googleapis.com/fcm/send';

                       // Put your Server Key here
                       $apiKey = env('VENDOR_KEY');
           
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
           
                    //    $dataPayload = ['to'=> 'My Name', 
                    //    'points'=>80, 
                    //    'other_data' => 'This is extra payload'
                    //    ];
           
                       // Create the api body
                       $apiBody = [
                           'notification' => $message, //$notifData 
                           //'data' => $dataPayload, //Optional
                           'time_to_live' => 600, // optional - In Seconds
                           //'to' => '/topics/mytargettopic'
                           //'registration_ids' = ID ARRAY
                           'to' => $device_token
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
    
    protected function customPaginate($items, $perPage = 10, $page = null, $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
	$res = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);	
	return $res;
    }
}
