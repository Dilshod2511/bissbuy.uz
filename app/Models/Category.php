<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children');
    }
    
        public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    // public function getCategoryImageAttribute($value)
    // {
    //     return  env('IMG_URL') . $value;
    // }

    // public function getTaxAttribute($value)
    // {
    //     if($this->parent_id != null) 
    //         return $value;
    //     return null;
    // }
    
    protected static function booted()
    {
        static::addGlobalScope('status', function (Builder $builder) {
            $builder->where('status', '=', 1);
        });
    }
}
