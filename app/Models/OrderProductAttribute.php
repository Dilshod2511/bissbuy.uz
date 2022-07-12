<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductAttribute extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 'order_id','product_id', 'product_option_id','product_option_value'
    ];
}
