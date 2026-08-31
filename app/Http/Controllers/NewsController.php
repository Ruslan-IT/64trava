<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;

class NewsController
{

    public function index(){



        $news = News::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->get();





        return view('news.index', compact('news'));

    }



    public function show(News $news){



        $previousNews = News::where('id', '<', $news->id)
            ->where('is_published', true)
            ->orderBy('id', 'desc')
            ->first();

        $nextNews = News::where('id', '>', $news->id)
            ->where('is_published', true)
            ->orderBy('id', 'asc')
            ->first();

        $newsBlock = News::all();

        $categories = Category::orderBy('name')->get();


        return view('news.show', compact(
            'news',
            'previousNews',
            'nextNews',
            'newsBlock',
            'categories',
        ));

    }

}
