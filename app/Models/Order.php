<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
       protected $fillable = [
        'id', 'customer_id', 'vendor_id','total','promo_id','discount','sub_total','tax','status','referred_id', 'end_point_delivery', 'distance', 'delivered_by', 'delivery_received_at', 'completed_at', 'paid_tax','delivery_amount', 'canceled_at'
    ];
      
    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }


    protected $casts = [
        'end_point_delivery' => 'array'
    ];


    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function vendorInfo()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id')->select([ 'id', 'vendor_name', 'phone_number', 'warehouse_address']);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function customerInfo()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->select([ 'id', 'first_name', 'last_name', 'phone_number']);
    }

}
