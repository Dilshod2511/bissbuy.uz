<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MVariantValue extends Model
{
    use HasFactory;
    protected $table = 'm_variants_values';
    protected $fillable=[
        'price',
        'product_id',
        'variant_id',
        'option_id',
        'value_id',
        'short_description'
    ];

    public $timestamps = false;
}

