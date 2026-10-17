<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Gallery;

class LandingController extends Controller
{
    public function index()
    {
        $posts = Post::published()->orderByDesc('published_at')->orderByDesc('id')
            ->limit(3)->get(['id', 'title', 'slug', 'excerpt', 'cover_url', 'published_at']);
        $galleries = Gallery::latest()->orderByDesc('id')->limit(6)->get(['id', 'title', 'caption', 'image_url']);
        $publishedPostCount = Post::published()->count();
        $galleryCount = Gallery::count();

        return view('landingpage.home', compact('posts', 'galleries', 'publishedPostCount', 'galleryCount'));
    }

    public function posts()
    {
        $posts = Post::published()->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(12, ['id', 'title', 'slug', 'excerpt', 'cover_url', 'published_at']);

        return view('landingpage.posts.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        return view('landingpage.posts.show', compact('post'));
    }

    public function gallery()
    {
        $galleries = Gallery::latest()->orderByDesc('id')->paginate(12, ['id', 'title', 'caption', 'image_url']);

        return view('landingpage.gallery', compact('galleries'));
    }
}
