<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', 'App\Http\Controllers\WebController@index')->name('home');
Route::get('/home', 'App\Http\Controllers\WebController@index')->name('home');
Route::get('/shop/', 'App\Http\Controllers\WebController@shop');
Route::get('/cart', 'App\Http\Controllers\WebController@cart');
Route::get('/shop/{id}', 'App\Http\Controllers\WebController@shop_by_category');
Route::get('/shop/product_by_category/{id}', 'App\Http\Controllers\WebController@shop_by_subcategory');

Route::get('/login', 'App\Http\Controllers\WebController@showLogin');
Route::post('/login', 'App\Http\Controllers\WebController@doLogin');
Route::post('/register', 'App\Http\Controllers\WebController@doRegister');
Route::get('/register', 'App\Http\Controllers\WebController@showRegister');
Route::get('/logout', 'App\Http\Controllers\WebController@doLogout');
Route::get('/detail/{id}', 'App\Http\Controllers\WebController@product_details');
Route::post('/add_to_cart', 'App\Http\Controllers\WebController@add_to_cart');
Route::post('/add_item_to_cart', 'App\Http\Controllers\WebController@add_item_to_cart');
Route::post('/remove_from_cart', 'App\Http\Controllers\WebController@remove_from_cart');
Route::post('/apply_promo', 'App\Http\Controllers\WebController@apply_promo');
Route::post('/remove_promo', 'App\Http\Controllers\WebController@remove_promo');
Route::get('/checkout', 'App\Http\Controllers\WebController@checkout_page');
Route::post('/place_order', 'App\Http\Controllers\WebController@place_order');
Route::get('/order', 'App\Http\Controllers\WebController@order');
Route::get('/order_detail/{id}', 'App\Http\Controllers\WebController@order_detail');
Route::post('/save_address', 'App\Http\Controllers\WebController@save_address');
Route::post('/edit_address', 'App\Http\Controllers\WebController@edit_address');
Route::post('/address_delete', 'App\Http\Controllers\WebController@address_delete');
Route::get('/profile', 'App\Http\Controllers\WebController@profile');
Route::post('/profile_update', 'App\Http\Controllers\WebController@profile_update');
Route::post('/profile_image', 'App\Http\Controllers\WebController@profile_image');
Route::get('/thankyou', function () {
    return view('thankyou');
});


Route::get('cache', function(){
   \Artisan::call('cache:clear');
   \Artisan::call('optimize:clear'); 
    \Artisan::call('route:clear'); 
   return 'r success';
});

Route::get('change_img', function () {

    $products = Product::get();

    foreach ($products as $product) {
        $images = \DB::table('product_images')->where('product_id', $product->id)->pluck('product_image')->toArray();
        if (count($images) > 0 && $product->gallery == null) {
            $product->gallery = $images;
            $product->save();
        }
        // dump($images);
    }
});

Route::get('test', function () {

    $products = \App\Models\Product::whereNull('variants')->get();
    foreach ($products as $product) {
        $images = [];
        $recors = \DB::table('product_images')->where('product_id', $product->id)->get();
        foreach ($recors as $image) {
            $images[] = [
                'path' => $image->product_image,
                'mime_type' => 'image/jpeg'
            ];
        }

      

        $variants[] = [
            'description' => $product->short_description,
            'price' => $product->product_price,
            'images' => $images,
            'options' => []
        ];

       // $keywords = $product->key_words;
        //$imploded = explode(',', $keywords);

        $product->variants = json_encode($variants);
       // $product->tags = $imploded;
        $product->save();
        echo $product->id . "/n/n";
    }

});



