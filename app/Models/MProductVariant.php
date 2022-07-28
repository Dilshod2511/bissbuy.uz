<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MProductVariant extends Model
{
    use HasFactory;
    protected $table = 'm_product_variants';
    protected $guarded = [];
    public $timestamps = false;
    public function options()
    {
        return $this->hasMany(MVariantValue::class, 'variant_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'sku_id', 'sku_id')->orderBy('order_number');
    }

    public function oneimage()
    {
        return $this->hasMany(ProductImage::class, 'sku_id', 'sku_id')->take(1)->orderBy('order_number');
    }

}
