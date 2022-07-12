<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerWalletHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id', 'type','message', 'amount', 'order_id'
    ];
}
