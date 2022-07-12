<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    public function customer_faq()
    {
        $data = Faq::where('status',1)->where('type',1)->get();
        
        return response()->json([
            "result" => $data,
            "count" => count($data),
            "message" => 'Success',
            "status" => 1
        ]);
    }
    
    public function vendor_faq()
    {
        $data = Faq::where('status',1)->where('type',2)->get();
        
        return response()->json([
            "result" => $data,
            "count" => count($data),
            "message" => 'Success',
            "status" => 1
        ]);
    }
    
     public function partner_faq()
    {
        $data = Faq::where('status',1)->where('type',3)->get();
        
        return response()->json([
            "result" => $data,
            "count" => count($data),
            "message" => 'Success',
            "status" => 1
        ]);
    }
}
