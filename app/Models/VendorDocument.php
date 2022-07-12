<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorDocument extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 'vendor_id','id_proof','id_proof_status','certificate','certificate_status'
    ];
}