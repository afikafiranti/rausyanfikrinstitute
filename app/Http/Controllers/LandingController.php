<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;
use App\Models\Gallery;

class LandingController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->take(3)->get();
        $galleries = Gallery::latest()->take(3)->get();

        return view('layouts/landing', compact('posts', 'galleries'));
    }
}
