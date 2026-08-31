<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;

class SeedbanksController
{

    public  function index()
    {
        $categories = Category::orderBy('name')->get();

        $seedbanks = Brand::all();

        return view('seedbanks.index', compact('seedbanks', 'categories'));
    }

}
