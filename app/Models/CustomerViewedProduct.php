<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerViewedProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 'customer_id','product_id', 'created_at','updated_at'
    ];
}
