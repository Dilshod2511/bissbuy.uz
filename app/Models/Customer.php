<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 'first_name','last_name', 'phone_number','phone_with_code','email','password','profile_picture','wallet','status','otp','fcm_token', 'app_review', 'location'
    ];
} 
