<?php

namespace App\Http\Controllers;

use App\Models\Category;

class MainController extends Controller
{
    /**
     * Display the main/home page with categories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('main', [
            'categories' => Category::orderByDesc('name')->where('id', '!=', 1)->get(),
        ]);
    }
}