<?php

use App\Filament\Pages\Orders;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SeedbanksController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*Route::get('/', function () {
    return view('welcome');
});*/


Route::get('/', [HomeController::class, 'index'])->name('home');


Route::get('/news', [NewsController::class, 'index'])->name('news.index');

Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');


Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');

Route::get('/catalog/{category}', [CatalogController::class, 'index'])->name('catalog.category');

Route::get('/brands/{brand}', [CatalogController::class, 'brand'])->name('catalog.brand');


Route::get('/catalog/tag/{tag}', [CatalogController::class, 'tag'])->name('catalog.tag');


Route::get('product/{slug}', [ProductsController::class, 'show'])->name('product.show');






Route::get('/seedbanks', [SeedbanksController::class, 'index'])->name('seedbanks.index');

Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery.index');




Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

Route::post('/cart/add', [CartController::class, 'add'])
    ->name('cart.add');

Route::post('/cart/update', [CartController::class, 'update'])
    ->name('cart.update');

Route::delete('/cart/remove/{variantId}', [CartController::class, 'remove'])
    ->name('cart.remove');

Route::delete('/cart/clear', [CartController::class, 'clear'])
    ->name('cart.clear');

Route::get('/cart/count', [CartController::class, 'count'])
    ->name('cart.count');

Route::get('/cart/popup', [CartController::class, 'popup'])
    ->name('cart.popup');




Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');

Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/order/success', [CheckoutController::class, 'success'])->name('order.success');



/*Route::get('/admin/orders/{number}', [AdminOrderController::class, 'show'])
    ->name('admin.orders.show');*/

Route::get('/admin/orders/{number}/download', [AdminOrderController::class, 'download'])
    ->name('admin.orders.download');



Route::get('/cart-2', function () {return view('cart-2');});



Route::get('/test-cart', function () {
    dd(session()->get('cart', []));
});













/*Route::get('/cart-1', function () {
    return view('cart');
});

Route::get('/cart-2', function () {
    return view('cart-2');
});

Route::get('/cart-3', function () {
    return view('cart-3');
});*/



/*Route::get('news', function () {
    return view('news');
});

Route::get('news-details', function () {
    return view('news-details');
});*/



Route::get('product-2', function () {
    return view('product-2');
});

Route::get('product-details', function () {
    return view('product-details');
});

Route::get('text', function () {
    return view('text');
});

