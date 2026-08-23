<?php

use Illuminate\Support\Facades\Route;

/*Route::get('/', function () {
    return view('welcome');
});*/


Route::get('/', function () {
    return view('home');
});


Route::get('/cart', function () {
    return view('cart');
});

Route::get('/cart-2', function () {
    return view('cart-2');
});

Route::get('/cart-3', function () {
    return view('cart-3');
});


Route::get('delivery', function () {
    return view('delivery');
});


Route::get('news', function () {
    return view('news');
});

Route::get('news-details', function () {
    return view('news-details');
});

Route::get('product', function () {
    return view('product');
});

Route::get('product-2', function () {
    return view('product-2');
});

Route::get('product-details', function () {
    return view('product-details');
});

Route::get('text', function () {
    return view('text');
});

