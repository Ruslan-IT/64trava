<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use App\Models\Product;

class ProductsController
{

    public function index(){


    }



    public function show($slug){

        $product = Product::where('slug', $slug)->firstOrFail();
        $categories = Category::orderBy('name')->get();

        return view('products.show', compact(
            'product',
            'categories',

        ));

    }

}
