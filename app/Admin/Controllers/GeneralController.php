<?php

namespace App\Admin\Controllers;
use App\Models\ProductOptionValue;
use App\Http\Controllers\Controller;
//use App\Http\Controllers\ProductOption;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Controllers\ModelForm;

class GeneralController extends Controller
{
    use ModelForm;

    public function GetOptionValue()
    {
        return ProductOptionValue::where('product_option_id', $_GET['q'])->get(['id', DB::raw('product_option_value')]);
    }
}
