<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = array('is_like', 'is_saved', 'attribute_price');

    public function getCoverImageAttribute($image)
    {
        return $this->images->first() ? $this->images->first()->product_image : 'default.png';
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')->select('id', 'category_name');
    }
    
    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id', 'id')->select('id', 'category_name','category_image');
    }
    
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id')->select('id', 'brand_name','brand_image');
    }
    
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id')->select('id', 'vendor_name');
    }

    public function images()    
    {
        return $this->hasMany(ProductImage::class)->orderBy('order_number');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }
    
      public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    
    public function setGalleryAttribute($gallery)
    {
        if (is_array($gallery)) {
            $this->attributes['gallery'] = json_encode($gallery);
        }
    }

    public function getGalleryAttribute($gallery)
    {
        return json_decode($gallery, true);
    }
    
    public function getIsLikeAttribute()
    {
        if(\request()->has('customer_id'))
        {
            return CustomerFavouriteProduct::where('customer_id', \request()->input('customer_id'))->where('product_id', $this->id)->exists() ? 1 : 0;
        }
    }
    
    public function getIsSavedAttribute()
    {
        if(\request()->has('customer_id'))
        {
            return Wishlist::where('customer_id', \request()->input('customer_id'))->where('product_id', $this->id)->exists() ? 1 : 0;
        }
    }
    
    // public function getDiscountPercentAttribute($value)
    // {
    
    //      return \DB::table('products')->where('id', $this->id)->first()->discount_percent;
    // }
    
    public function getProductPriceAttribute($value)
    {
       if($this->type == 2)
       {
           if(\DB::table('m_variants_values')->where('product_id', $this->id)->whereNotNull('price')->exists()){
                return floatval(\DB::table('m_variants_values')->where('product_id', $this->id)->whereNotNull('price')->first()->price) ;
           }else{
               return 0;
           }
            
       }else {
	return $value;
	}
    }

 public function getAttributePriceAttribute()
{
return 0;
}
    
    public function getCustomtagsAttribute()
    {
        return json_decode($this->tags) ;
    }

    public function getTotalCommentAttribute()
	{
	return ProductReview::where('product_id', $this->id)->count();
	}



    protected static function booted()
    {
         static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('status', '=', 1);
        });    
     }
    
    
 }
