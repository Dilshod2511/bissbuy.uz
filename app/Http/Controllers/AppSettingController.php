<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
class AppSettingController extends Controller
{
    public function index()
    {
        $data = AppSetting::first();
        return response()->json([
            "result" => $data,
            "message" => 'Success',
            "status" => 1
        ]);
    }
}
