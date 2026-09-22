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
use App\Services\BonusService;
use App\Services\CartService;
use App\Services\PromoCodeService;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*Route::get('/', function () {
    return view('welcome');
});*/


Route::get('/', [HomeController::class, 'index'])->name('home');


Route::get('/news', [NewsController::class, 'index'])->name('news.index');

Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');


Route::get('/cat', [CatalogController::class, 'index'])->name('catalog.index');

Route::get('/cat/{category}', [CatalogController::class, 'index'])->name('catalog.category');

Route::get('/brands/{brand}', [CatalogController::class, 'brand'])->name('catalog.brand');


Route::get('/cat/tag/{tag}', [CatalogController::class, 'tag'])->name('catalog.tag');


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



Route::get('/bonus-test', function (BonusService $bonusService) {
    $cart = session('cart', []);

    return response()->json(
        $bonusService->calculate($cart)
    );



});


Route::post('/cart/bonuses', function (\Illuminate\Http\Request $request) {
    session()->put('bonuses', $request->input('bonuses', []));

    // Если клиент выбрал бонусные семена —
    // ранее выбранный промокод больше не действует в этой корзине.
    session()->forget('promo_code');

    return response()->json([
        'success' => true,
    ]);
})->name('cart.bonuses');


Route::get('/clear-cart', function () {
    session()->forget('cart');

    return redirect('/cart');
});







Route::get('/telegram-test', function () {
    $token = config('services.telegram.bot_token');
    $chatId = config('services.telegram.manager_chat_id');

    $response = Http::timeout(10)->post(
        'https://api.telegram.org/bot' . $token . '/sendMessage',
        [
            'chat_id' => $chatId,
            'text' => '🔔 Тестовое сообщение от Laravel',
        ]
    );

    return [
        'status' => $response->status(),
        'body' => $response->json(),
    ];
});



Route::get('/cart/promo-amount', function (PromoCodeService $promoCodeService) {
    $cart = app(\App\Services\CartService::class)->get();

    $selectedIds = session('cart_selected');

    if (is_array($selectedIds)) {
        $selectedIds = array_map('intval', $selectedIds);

        $cart = array_filter(
            $cart,
            fn ($item, $variantId) => in_array(
                (int) $variantId,
                $selectedIds,
                true
            ),
            ARRAY_FILTER_USE_BOTH
        );
    }

    $amount = $promoCodeService->calculate($cart);

    return response()->json([
        'success' => true,
        'amount' => $amount,
    ]);
})->name('cart.promo-amount');


Route::post('/cart/promo-code', function (
    \Illuminate\Http\Request $request,
    \App\Services\PromoCodeService $promoCodeService
) {
    $cart = app(\App\Services\CartService::class)->get();

    $selectedIds = session('cart_selected');

    if (is_array($selectedIds)) {
        $selectedIds = array_map('intval', $selectedIds);

        $cart = array_filter(
            $cart,
            fn ($item, $variantId) => in_array(
                (int) $variantId,
                $selectedIds,
                true
            ),
            ARRAY_FILTER_USE_BOTH
        );
    }

    if (empty($cart)) {
        return response()->json([
            'success' => false,
            'message' => 'Выберите хотя бы один товар.',
        ], 422);
    }

    // Если клиент уже выбрал бонусные семена,
    // промокод получить нельзя.
    $bonuses = session('bonuses', []);

    if (is_array($bonuses) && count($bonuses) > 0) {
        return response()->json([
            'success' => false,
            'message' => 'Вы уже выбрали бонусные семена.',
        ], 422);
    }

    // Если промокод уже был получен для этой корзины,
    // повторно новый не создаём.
    $existingPromo = session('promo_code');

    if (!empty($existingPromo)) {
        return response()->json([
            'success' => true,
            'code' => $existingPromo['code'],
            'amount' => $existingPromo['amount'],
        ]);
    }

    // Создаём новый промокод только для выбранных товаров.
    $promoCode = $promoCodeService->create($cart);

    // Промокод и бонусные семена — взаимоисключающие варианты.
    session()->forget('bonuses');

    session()->put('promo_code', [
        'id' => $promoCode->id,
        'code' => $promoCode->code,
        'amount' => (float) $promoCode->amount,
    ]);

    return response()->json([
        'success' => true,
        'code' => $promoCode->code,
        'amount' => (float) $promoCode->amount,
    ]);
})->name('cart.promo-code');


Route::get('/clear-cart-choice', function () {
    session()->forget('bonuses');
    session()->forget('promo_code');

    return redirect()->route('cart.index');
});

Route::get('/debug-cart-session', function () {
    return response()->json([
        'bonuses' => session('bonuses'),
        'promo_code' => session('promo_code'),
    ]);
});


Route::post('/checkout/apply-promo', [
    \App\Http\Controllers\CheckoutController::class,
    'applyPromo',
])->name('checkout.apply-promo');



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



/*Route::get('product-2', function () {
    return view('product-2');
});

Route::get('product-details', function () {
    return view('product-details');
});*/

Route::get('text', function () {
    return view('text');
});

