<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\News;
use App\Models\Product;
use App\Models\Tag;

class HomeController
{

    public function index(){



        /*Один запрос в бд */
        $products = Product::all();
        $brands = Brand::all();
        $categories = Category::orderBy('name')->get();
        $tags = Tag::all();


        $newProducts = Product::whereHas('categories', function ($query) {
            $query->where('id', 7);//новинки
        })->get();

        $productPopular = Product::whereHas('categories', function ($query) {
            $query->where('id', 6);//популярные
        })
            ->get()
            ->take(4);

        $largePackProducts = Product::whereHas('categories', function ($query) {
            $query->where('id', 5);//крупные пачки
        })
            ->get()
            ->take(4);


        $discountProducts = $products
            ->where('is_on_sale', true)
            ->take(4);





        $saleProducts = $products->filter(function($product){
            return $product->is_sale;
        });

        $newsBlock = News::where('is_published', true)
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->take(5)
            ->get();





        return view('home', compact(
            'newProducts',
            'saleProducts',
            'brands',
            'newsBlock',
            'categories',
            'tags',
            'productPopular',
            'largePackProducts',
            'discountProducts',
        ));
    }

}
