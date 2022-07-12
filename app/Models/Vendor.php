<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'vendor_name','store_name','phone_number','vendor_email','password','profile_picture','status','user_name','admin_user_id','store_image','address','manual_address','latitude','longitude','static_map','address_update_status','warehouse_address'
    ];

    protected $casts = [
        'warehouse_address' => 'array',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id', 'id')->with('images');
    }

    // public function getStoreImageAttribute($value){
    //     return url('/upload/' . $value);
    // }
}
