<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Gallery;

class LandingController extends Controller
{
    public function index()
    {
        $posts = Post::published()->where('type', 'article')->orderByDesc('published_at')->orderByDesc('id')
            ->limit(4)->get(['id', 'title', 'slug', 'excerpt', 'cover_url', 'published_at']);
        $galleries = Gallery::latest()->orderByDesc('id')->limit(4)->get(['id', 'title', 'caption', 'image_url']);

        return view('landingpage.home', ['posts' => $posts, 'galleries' => $galleries, 'isArticle' => true]);
    }

    public function posts()
    {
        $posts = Post::published()->where('type', 'reflection')->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(12, ['id', 'title', 'slug', 'excerpt', 'cover_url', 'published_at']);

        return view('landingpage.posts.index', ['posts' => $posts, 'isArticle' => false]);
    }

    public function show(string $slug)
    {
        $post = Post::published()->where('type', 'reflection')->where('slug', $slug)->firstOrFail();

        return view('landingpage.posts.show', ['post' => $post, 'isArticle' => false]);
    }

    public function articles()
    {
        $posts = Post::published()->where('type', 'article')->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(12, ['id', 'title', 'slug', 'excerpt', 'cover_url', 'published_at']);

        return view('landingpage.posts.index', ['posts' => $posts, 'isArticle' => true]);
    }

    public function article(string $slug)
    {
        $post = Post::published()->where('type', 'article')->where('slug', $slug)->firstOrFail();

        return view('landingpage.posts.show', ['post' => $post, 'isArticle' => true]);
    }

    public function gallery()
    {
        $galleries = Gallery::latest()->orderByDesc('id')->paginate(12, ['id', 'title', 'caption', 'image_url']);

        return view('landingpage.gallery', compact('galleries'));
    }
}
