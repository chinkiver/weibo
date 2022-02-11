<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class StaticPagesController extends Controller
{
    public function home()
    {
        $feedItems = [];

        // 如果用户已登录
        if (Auth::check()) {
            $feedItems = Auth::user()->feed()->paginate(5);
        }

        return view('static_pages.home', compact('feedItems'));
    }

    public function help()
    {
        return view('static_pages.help');
    }

    public function about()
    {
        return view('static_pages.about');
    }
}
