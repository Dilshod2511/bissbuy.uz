<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MProductOption extends Model
{
    use HasFactory;
    protected $table = 'm_product_options';
    protected $guarded = [];
    public $timestamps = false;
    public function option()
    {
        return $this->belongsTo(MOption::class, 'option_id');
    }
}
