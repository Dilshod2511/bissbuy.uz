<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    public $timestamps = false;
    protected $table = 'wishlist';
    use HasFactory;
    protected $guarded = [];
} 