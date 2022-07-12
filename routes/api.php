<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*//
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/*General API */
Route::get('app_setting', 'App\Http\Controllers\AppSettingController@index');
Route::get('payment_modes', 'App\Http\Controllers\OrderController@payment_modes');

/* Customer */
Route::post('customer/register', 'App\Http\Controllers\CustomerController@register');
Route::post('customer/login', 'App\Http\Controllers\CustomerController@login');
Route::post('customer/profile_update', 'App\Http\Controllers\CustomerController@profile_update');
Route::post('customer/profile_picture', 'App\Http\Controllers\CustomerController@profile_picture');
Route::post('customer/get_profile', 'App\Http\Controllers\CustomerController@get_profile');
Route::post('customer/forgot_password', 'App\Http\Controllers\CustomerController@forgot_password');
Route::post('customer/reset_password', 'App\Http\Controllers\CustomerController@reset_password');
Route::post('customer/check_phone', 'App\Http\Controllers\CustomerController@check_phone');
Route::get('customer/privacy_policy', 'App\Http\Controllers\PrivacyPolicyController@customer_policy');
Route::get('customer/faq', 'App\Http\Controllers\FaqController@customer_faq');
Route::get('home_banners', 'App\Http\Controllers\CustomerController@home_banners');
Route::post('add_address', 'App\Http\Controllers\AddressController@add_address');
Route::post('address/all', 'App\Http\Controllers\AddressController@all_address');
Route::post('address/delete', 'App\Http\Controllers\AddressController@delete_address');
Route::post('address/get', 'App\Http\Controllers\AddressController@get_address');
Route::post('address/update', 'App\Http\Controllers\AddressController@update_address');
Route::get('categories/{category_name?}', 'App\Http\Controllers\CustomerController@get_categories');
Route::post('get_product_detail', 'App\Http\Controllers\CustomerController@get_product_detail');
Route::post('get_products', 'App\Http\Controllers\CustomerController@get_products');
Route::get('get_payment_mode', 'App\Http\Controllers\CustomerController@get_payment_mode');
Route::get('get_promo_code', 'App\Http\Controllers\CustomerController@get_promo_code');
Route::post('add_customer_favourite_product', 'App\Http\Controllers\CustomerController@customer_favourite_product');
Route::post('add_customer_viewed_product', 'App\Http\Controllers\CustomerController@customer_viewed_product');
Route::post('add_customer_shared_product', 'App\Http\Controllers\CustomerController@customer_shared_product');
Route::post('customer/vendor_detail', 'App\Http\Controllers\CustomerController@vendor_detail');
Route::get('customer/get_favourite_products', 'App\Http\Controllers\CustomerController@get_favourite_products');
Route::post('add_rating', 'App\Http\Controllers\CustomerController@add_rating');
Route::post('get_rating', 'App\Http\Controllers\CustomerController@get_rating');
Route::post('place_order', 'App\Http\Controllers\OrderController@place_order');
Route::post('get_order_products', 'App\Http\Controllers\OrderController@get_order_products');
Route::post('customer/get_wallet', 'App\Http\Controllers\CustomerController@get_wallet');
Route::post('customer_top_product', 'App\Http\Controllers\CustomerController@customer_top_product');
Route::post('get_my_orders', 'App\Http\Controllers\CustomerController@get_my_orders');
Route::post('search_products', 'App\Http\Controllers\CustomerController@search_products');
Route::post('add_app_review', 'App\Http\Controllers\CustomerController@add_app_review');
Route::post('get_user_details', 'App\Http\Controllers\CustomerController@get_user_details');
Route::post('check_liked', 'App\Http\Controllers\CustomerController@check_is_liked');
Route::post('logout', 'App\Http\Controllers\CustomerController@logout');
Route::post('add-to-wishlist', 'App\Http\Controllers\CustomerController@addToWishlist');
Route::post('check-wishlist', 'App\Http\Controllers\CustomerController@checkWishlist');
Route::post('get-wishlist', 'App\Http\Controllers\CustomerController@getWishlist');
Route::post('reset-password', 'App\Http\Controllers\CustomerController@resetPassword');

Route::get('customer_completed_order/{customer_id}', 'App\Http\Controllers\CustomerController@customer_completed_order');
Route::get('customer_cancelled_order/{customer_id}', 'App\Http\Controllers\CustomerController@customer_cancelled_order');

Route::get('waiting_orders/{customer_id}', 'App\Http\Controllers\CustomerController@waiting_orders');
Route::get('received_orders/{customer_id}', 'App\Http\Controllers\CustomerController@received_orders');
Route::get('shipping_orders/{customer_id}', 'App\Http\Controllers\CustomerController@shipping_orders');
Route::get('delivered_orders/{customer_id}', 'App\Http\Controllers\CustomerController@delivered_orders');
Route::get('cancelled_orders/{customer_id}', 'App\Http\Controllers\CustomerController@cancelled_orders');


Route::get('customer_pending_order/{customer_id}', 'App\Http\Controllers\CustomerController@customer_pending_order');

Route::get('cards/{customer_id}', 'App\Http\Controllers\CustomerController@cards');
Route::post('add_card', 'App\Http\Controllers\CustomerController@add_card');
Route::post('create_order', 'App\Http\Controllers\CustomerController@create_order');
Route::post('delete_order', 'App\Http\Controllers\CustomerController@delete_order');


Route::post('set_location', 'App\Http\Controllers\CustomerController@set_location');
Route::get('get_location/{customer_id}', 'App\Http\Controllers\CustomerController@get_location');

Route::post('cancel_order', 'App\Http\Controllers\CustomerController@cancel_order');


Route::post('add_comment', 'App\Http\Controllers\CustomerController@add_comment');
Route::get('get_comments/{product_id}', 'App\Http\Controllers\CustomerController@get_comments');

Route::get('customer_business/{customer_id}', 'App\Http\Controllers\CustomerController@customer_business');




/*vendor */
Route::post('vendor/register', 'App\Http\Controllers\VendorController@register');
Route::get('vendor/privacy_policy', 'App\Http\Controllers\PrivacyPolicyController@vendor_policy');
Route::get('vendor/faq', 'App\Http\Controllers\FaqController@vendor_faq');
Route::post('vendor/earning', 'App\Http\Controllers\VendorController@vendor_earning');
Route::post('vendor/wallet', 'App\Http\Controllers\VendorController@vendor_wallet');
Route::post('vendor/withdrawal_request', 'App\Http\Controllers\VendorController@vendor_withdrawal_request');
Route::post('vendor/withdrawal_history', 'App\Http\Controllers\VendorController@vendor_withdrawal_history');
Route::post('vendor/get_profile', 'App\Http\Controllers\VendorController@get_profile');
Route::post('vendor/profile_update', 'App\Http\Controllers\VendorController@profile_update');
Route::post('vendor/check_phone', 'App\Http\Controllers\VendorController@check_phone');
Route::post('vendor/login', 'App\Http\Controllers\VendorController@login');
Route::post('vendor/forgot_password', 'App\Http\Controllers\VendorController@forgot_password');
Route::post('vendor/reset_password', 'App\Http\Controllers\VendorController@reset_password');
Route::post('vendor/address', 'App\Http\Controllers\VendorController@vendor_address');
Route::post('vendor/details', 'App\Http\Controllers\VendorController@details');
Route::post('vendor/upload', 'App\Http\Controllers\VendorController@upload');
Route::post('vendor/document_upload', 'App\Http\Controllers\VendorController@document_upload');
Route::post('vendor/document_update', 'App\Http\Controllers\VendorController@document_update');
Route::post('vendor/document_details', 'App\Http\Controllers\VendorController@document_details');
Route::post('vendor/get_orders', 'App\Http\Controllers\OrderController@getVendorOrders');
Route::post('vendor/dashboard_details', 'App\Http\Controllers\VendorController@dashboard_details');
Route::post('vendor/dashboard', 'App\Http\Controllers\VendorController@dashboard');
Route::post('vendor/order_accept', 'App\Http\Controllers\VendorController@order_accept');
Route::post('vendor/change_online_status', 'App\Http\Controllers\VendorController@change_online_status');
Route::post('order_status_update', 'App\Http\Controllers\OrderController@order_status_update');
Route::post('vendor/get_status', 'App\Http\Controllers\VendorController@get_vendor_status');
Route::get('vendor_banners', 'App\Http\Controllers\VendorController@vendor_banners');
Route::post('products_by_vendor_categroy', 'App\Http\Controllers\VendorController@products_by_vendor_categroy');


/*vendor new apis*/
Route::post('set_vendor_details', 'App\Http\Controllers\VendorController@set_vendor_details');
Route::get('get_vendor_orders/{id}', 'App\Http\Controllers\VendorController@get_vendor_orders');
Route::get('get_vendor_order_info/{id}', 'App\Http\Controllers\VendorController@get_vendor_order_info');
Route::get('get_vendor_archive_orders/{id}', 'App\Http\Controllers\VendorController@get_vendor_archive_orders');
Route::post('create_product', 'App\Http\Controllers\VendorController@create_product');
Route::post('set-options', 'App\Http\Controllers\VendorController@setOptions');
Route::post('change-product-status', 'App\Http\Controllers\VendorController@change_product_status');



Route::post('edit_product', 'App\Http\Controllers\VendorController@edit_product');
Route::post('change_status', 'App\Http\Controllers\VendorController@changeStatus');
Route::get('get-product-options', 'App\Http\Controllers\VendorController@get_product_options');
Route::get('get-product-options-values/{id}', 'App\Http\Controllers\VendorController@get_product_options_values');
Route::get('vendor_statistic/{vendor_id}', 'App\Http\Controllers\VendorController@vendor_statistic');
Route::get('vendor_order_statistics/{vendor_id}', 'App\Http\Controllers\VendorController@vendor_order_statistics');
Route::get('vendor_categories/{vendor_id}', 'App\Http\Controllers\VendorController@vendor_categories');




/*partner*/
Route::get('partner/privacy_policy', 'App\Http\Controllers\PrivacyPolicyController@partner_policy');
Route::get('partner/faq', 'App\Http\Controllers\FaqController@partner_faq');
Route::post('partner/profile_picture', 'App\Http\Controllers\DeliveryPartnerController@profile_picture');
Route::post('partner/login', 'App\Http\Controllers\DeliveryPartnerController@login');
Route::post('partner/forgot_password', 'App\Http\Controllers\DeliveryPartnerController@forgot_password');
Route::post('partner/reset_password', 'App\Http\Controllers\DeliveryPartnerController@reset_password');
Route::resource('partner', 'App\Http\Controllers\DeliveryPartnerController');
Route::post('partner/order_accept', 'App\Http\Controllers\OrderController@order_accept');
Route::post('partner/order_reject', 'App\Http\Controllers\OrderController@order_reject');
Route::post('order_status_change', 'App\Http\Controllers\OrderController@order_status_change');
Route::post('partner/change_online_status', 'App\Http\Controllers\DeliveryPartnerController@change_online_status');
Route::post('partner/dashboard_details', 'App\Http\Controllers\DeliveryPartnerController@dashboard_details');
Route::post('partner/dashboard', 'App\Http\Controllers\DeliveryPartnerController@dashboard');
Route::post('partner/get_my_orders', 'App\Http\Controllers\DeliveryPartnerController@get_my_orders');
Route::post('delivery_statistics/{deliveryman_id}', 'App\Http\Controllers\DeliveryPartnerController@delivery_statistics');
Route::get('delivery-info/{id}', 'App\Http\Controllers\DeliveryPartnerController@delivery_info');
Route::post('driver/logout', 'App\Http\Controllers\DeliveryPartnerController@logout');




Route::get('delivery_ready_orders', 'App\Http\Controllers\DeliveryPartnerController@delivery_ready_orders');
Route::get('delivery_archive_orders/{partner_id}', 'App\Http\Controllers\DeliveryPartnerController@delivery_archive_orders');
Route::get('delivery_received_orders/{partner_id}', 'App\Http\Controllers\DeliveryPartnerController@delivery_received_orders');
Route::post('receive_order', 'App\Http\Controllers\DeliveryPartnerController@receive_order');
Route::post('complete_order', 'App\Http\Controllers\DeliveryPartnerController@complete_order');







Route::get('categories', 'App\Http\Controllers\DeliveryPartnerController@categories');
Route::get('categories/{search}', 'App\Http\Controllers\DeliveryPartnerController@categories_search');
Route::post('change_credentials', 'App\Http\Controllers\DeliveryPartnerController@change_credentials');
Route::get('products/{category_id}', 'App\Http\Controllers\OrderController@products');
Route::get('products_search/{name?}', 'App\Http\Controllers\OrderController@products_search');


Route::get('top_products', 'App\Http\Controllers\CustomerController@get_top_products');
Route::get('subcategories/{text?}', 'App\Http\Controllers\OrderController@subcategories');
Route::get('vendor_products/{vendor_id}', 'App\Http\Controllers\OrderController@vendor_products');
Route::get('vendor_products_with_discount/{vendor_id}', 'App\Http\Controllers\OrderController@vendor_products_with_discount');


Route::get('vendor_statistic_page/{vendor_id}', 'App\Http\Controllers\CustomerController@vendor_statistic_page');

Route::get('top_like', 'App\Http\Controllers\CustomerController@top_like');
Route::get('top_view', 'App\Http\Controllers\CustomerController@top_view');
Route::get('top_share', 'App\Http\Controllers\CustomerController@top_share');
Route::get('top_sale', 'App\Http\Controllers\CustomerController@top_sale');





Route::get('option_vals', 'App\Http\Controllers\VendorController@option_vals');
Route::get('subcats', 'App\Http\Controllers\VendorController@subcats');



Route::post('send_notify', 'App\Http\Controllers\VendorController@send_notify');
Route::get('send_notification_fcm', 'App\Http\Controllers\VendorController@send_notification_FCM');


Route::get('brands/{text?}', 'App\Http\Controllers\VendorController@brands');




// NEW PRODUCT
Route::get('get-product/{id}', 'App\Http\Controllers\OrderController@getProduct');

//Route::get('get-product-variants/{product_id}', 'App\Http\Controllers\CustomerController@getProductVariants');
Route::get('get-product-variants/{product_id}', 'App\Http\Controllers\CustomerController@getAttributesBySku');
Route::get('get-second-attributes/{product_id}/{option_id}/{value_id}', 'App\Http\Controllers\CustomerController@getSecondAttributes');


Route::get('search-page/{text?}', 'App\Http\Controllers\CustomerController@searchPage');
Route::get('get-search-history/{user_id}', 'App\Http\Controllers\CustomerController@getSearchHistory');
Route::post('set-search-history', 'App\Http\Controllers\CustomerController@setSearchHistory');
Route::post('remove-search-history', 'App\Http\Controllers\CustomerController@removeSearchHistory');
Route::get('search-input/{text}', 'App\Http\Controllers\CustomerController@searchInput');


Route::get('search-result/{text}', 'App\Http\Controllers\CustomerController@searchResult');

Route::post('customer-cancel-order', 'App\Http\Controllers\OrderController@cancel_order');




Route::get('categories-list', 'App\Http\Controllers\CustomerController@categoriesList');
Route::get('subcategories-list/{category_id}', 'App\Http\Controllers\CustomerController@subcategoriesList');
Route::get('brands-list', 'App\Http\Controllers\CustomerController@brandsList');



Route::post('vendor/create-product', 'App\Http\Controllers\VendorController@createProduct');


Route::post('/create-product-update',[\App\Http\Controllers\VendorController::class,'create_product_update']);

Route::post('/create-product-update',[\App\Http\Controllers\VendorController::class,'create_product_update']);
Route::post('/get-Imag-Product',[App\Http\Controllers\VendorController::class,'update']);

