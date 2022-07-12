<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id', 'customer_address','google_address', 'lat','lng','post_code','static_map','status'
    ];
}
