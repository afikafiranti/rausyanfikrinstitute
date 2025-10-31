<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);
        return view('management.berita.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'cover_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_url')) {
            $coverPath = $request->file('cover_url')->store('covers', 'public');
        }

        Post::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'cover_url' => $coverPath,
            'published_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Berita berhasil ditambahkan!');
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'cover_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['title', 'excerpt', 'content']);
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('cover_url')) {
            if ($post->cover_url && Storage::disk('public')->exists($post->cover_url)) {
                Storage::disk('public')->delete($post->cover_url);
            }
            $data['cover_url'] = $request->file('cover_url')->store('covers', 'public');
        }

        $post->update($data);
        return redirect()->back()->with('success', 'Berita berhasil diperbarui!');
    }

    public function destroy(Post $post)
    {
        if ($post->cover_url && Storage::disk('public')->exists($post->cover_url)) {
            Storage::disk('public')->delete($post->cover_url);
        }
        $post->delete();
        return redirect()->back()->with('success', 'Berita berhasil dihapus!');
    }
}
