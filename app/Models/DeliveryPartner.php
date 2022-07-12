<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPartner extends Model
{
    use HasFactory;
     protected $fillable = [
        'id', 'delivery_boy_name', 'phone_number','delivery_email','password','profile_picture','online_status','status','otp', 'fcm_token'
    ];
}
