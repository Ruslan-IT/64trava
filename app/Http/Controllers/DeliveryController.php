<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\DeliveryPage;

class DeliveryController
{

    public  function index()
    {

        $page  = DeliveryPage::first();
        $categories = Category::orderBy('name')->get();


        return view('delivery.index', compact('page',  'categories'));
    }

}
