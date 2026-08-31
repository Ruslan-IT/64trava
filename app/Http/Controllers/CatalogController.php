<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\News;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;

class CatalogController
{

    public function index(Request $request, ?string $category = null)
    {



        $query = Product::query()
            ->with(['brand', 'categories', 'tags']);

        /*
       |--------------------------------------------------------------------------
       | ПОИСК
       |--------------------------------------------------------------------------
       */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('brand', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });

            });
        }

        /*
        |--------------------------------------------------------------------------
        | ФИЛЬТРЫ
        |--------------------------------------------------------------------------
        */

        // Новинки
        if ($request->filter === 'new') {
            $query->where('is_new', true);
        }

        // Популярные
        if ($request->filter === 'popular') {
            $query->where('is_popular', true);
        }

        // Тип семян
        if ($request->filled('seed_type')) {
            $query->where('seed_type', $request->seed_type);
        }

        /*
        |--------------------------------------------------------------------------
        | КАТЕГОРИЯ
        |--------------------------------------------------------------------------
        */

        $currentCategory = $category
            ? Category::where('slug', $category)->firstOrFail()
            : null;

        if ($currentCategory) {
            $query->whereHas('categories', function ($q) use ($currentCategory) {
                $q->where('categories.id', $currentCategory->id);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | БРЕНД
        |--------------------------------------------------------------------------
        */

        $brand = null;

        if ($request->filled('brand')) {

            $brand = Brand::where('slug', $request->brand)->firstOrFail();

            $query->where('brand_id', $brand->id);
        }

        /* |--------------------------------------------------------------------------
        | ТЕГ |--------------------------------------------------------------------------
        */
        $tag = null;
        if ($request->filled('tag')) {
            $tag = Tag::where('slug', $request->tag)->firstOrFail();
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('tags.id', $tag->id); });
        }


        /*
        |--------------------------------------------------------------------------
        | ТОВАРЫ
        |--------------------------------------------------------------------------
        */

        $products = $query
            ->latest()
            ->paginate(24)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | КАТЕГОРИИ И БРЕНДЫ
        |--------------------------------------------------------------------------
        */

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $brands = Brand::query()
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ИНФОРМАЦИЯ ДЛЯ БЛОКА
        |--------------------------------------------------------------------------
        */

        $catalogInfo = $brand ?? $currentCategory;

        return view('category.index', compact(
            'products',
            'categories',
            'brands',
            'currentCategory',
            'brand',
            'tag',
            'catalogInfo'
        ));
    }


    public function brand(Brand $brand)
    {

        $tag = null;

        $products = Product::query()
            ->with(['brand', 'categories', 'tags'])
            ->where('brand_id', $brand->id)
            ->latest()
            ->paginate(24)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $brands = Brand::query()
            ->orderBy('name')
            ->get();

        $currentCategory = null;
        $tag = null;

        $catalogInfo = $brand;

        return view('category.index', compact(
            'products',
            'categories',
            'brands',
            'currentCategory',
            'brand',
            'tag',
            'catalogInfo'
        ));
    }


    public function tag(Tag $tag)
    {


        $products = Product::query()
            ->with(['brand', 'categories', 'tags'])
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('tags.id', $tag->id);
            })
            ->latest()
            ->paginate(24)
            ->withQueryString();



        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $brands = Brand::query()
            ->orderBy('name')
            ->get();

        $catalogInfo = $tag;

        $currentCategory = null;
        $brand = null;

        return view('category.index', compact(
            'products',
            'categories',
            'brands',
            'currentCategory',
            'brand',
            'tag',
            'catalogInfo'
        ));
    }

}
