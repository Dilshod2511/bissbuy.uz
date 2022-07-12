<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getOptionValueIdAttribute($value)
    {
        return explode(',', $value);
    }

    public function setOptionValueIdAttribute($value)
    {
        $this->attributes['option_value_id'] = implode(',', $value);
    }
}
