<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOption extends Model
{
    use HasFactory;

    protected $table = 'm_options';
    protected $guarded = [];
    public $timestamps = false;
}
