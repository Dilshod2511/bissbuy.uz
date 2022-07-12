<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->get('get_option_value', 'GeneralController@GetOptionValue');
    $router->resource('badges', BadgeController::class);
    $router->resource('brands', BrandController::class);
    $router->resource('categories', CategoryController::class);
    $router->resource('statuses', StatusController::class);
    $router->resource('cancellation-and-return-policies', CancellationAndReturnPolicyController::class);
    $router->resource('category-banners', CategoryBannerController::class);
    $router->resource('category-sliders', CategorySliderController::class);
    $router->resource('customers', CustomerController::class);
    $router->resource('customer-addresses', CustomerAddressController::class);
    $router->resource('customer-wallet-histories', CustomerWalletHistoryController::class);
    $router->resource('delivery-partners', DeliveryPartnerController::class);
    $router->resource('faqs', FaqController::class);
    $router->resource('fcm-notifications', FcmNotificationController::class);
    $router->resource('home-sliders', HomeSliderController::class);
    $router->resource('payment-modes', PaymentModeController::class);
    $router->resource('privacy-policies', PrivacyPolicyController::class);
    $router->resource('products', ProductController::class);
    $router->resource('vendors', VendorController::class);
    $router->resource('customer-favourites', CustomerFavouriteController::class);
    $router->resource('orders', OrderController::class);
    $router->resource('order-payment-details', OrderPaymentDetailController::class);
    $router->resource('order-products', OrderProductController::class);
    $router->resource('order-statuses', OrderStatusController::class);
    $router->resource('product-attributes', ProductAttributeController::class);
    $router->resource('product-highlights', ProductHighlightController::class);
    $router->resource('product-images', ProductImageController::class);
    $router->resource('product-options', ProductOptionController::class);
    $router->resource('product-option-values', ProductOptionValueController::class);
    $router->resource('product-reviews', ProductReviewController::class);
    $router->resource('promo-codes', PromoCodeController::class);
    $router->resource('shipping-settings', ShippingSettingController::class);
    $router->resource('taxes', TaxController::class);
    $router->resource('vendor-earnings', VendorEarningController::class);
    $router->resource('vendor-wallet-histories', VendorWalletHistoryController::class);
    $router->resource('app-settings', AppSettingController::class);
    $router->resource('addresses', AddressController::class);
    $router->resource('vendor-withdrawals', VendorWithdrawalController::class);
    $router->resource('customer-favourite-products', CustomerFavouriteProductController::class);
    $router->resource('customer-shared-products', CustomerSharedProductController::class);
    $router->resource('customer-viewed-products', CustomerViewedProductController::class);
    $router->resource('order-product-attributes', OrderProductAttributeController::class);
    $router->resource('vendor-documents', VendorDocumentController::class);
    $router->resource('vendor-banners', VendorBannerController::class);
    $router->resource('statistics', StatisticController::class);
    $router->resource('delivery-statistics', DeliveryStatisticController::class);
});
