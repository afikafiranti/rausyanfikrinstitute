<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);

        return view('management.refleksi.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->merge(['_editing_id' => null]);
        $data = $this->validatedData($request);
        $path = null;
        try {
            if ($request->hasFile('cover_url')) {
                $path = $request->file('cover_url')->store('covers', 'public');
                if ($path === false) {
                    throw new RuntimeException('Gambar gagal disimpan.');
                }
            }
            $data['cover_url'] = $path;
            $data['published_at'] = now();
            DB::transaction(function () use ($data) {
                if (! (new Post($data))->save()) {
                    throw new RuntimeException('Refleksi gagal disimpan.');
                }
            });
        } catch (Throwable $exception) {
            $this->deleteImage($path ?: null);
            throw $exception;
        }

        return redirect()->route('management.post.index')->with('success', 'Refleksi berhasil ditambahkan.');
    }

    public function update(Request $request, Post $post)
    {
        $request->merge(['_editing_id' => $post->id]);
        $data = $this->validatedData($request);
        $oldPath = $post->cover_url;
        $path = null;
        try {
            if ($request->hasFile('cover_url')) {
                $path = $request->file('cover_url')->store('covers', 'public');
                if ($path === false) {
                    throw new RuntimeException('Gambar gagal disimpan.');
                }
                $data['cover_url'] = $path;
            }
            DB::transaction(function () use ($post, $data) {
                if (! $post->fill($data)->save()) {
                    throw new RuntimeException('Refleksi gagal diperbarui.');
                }
            });
        } catch (Throwable $exception) {
            $this->deleteImage($path ?: null);
            throw $exception;
        }
        if ($path !== null) {
            $this->deleteImage($oldPath);
        }

        return redirect()->route('management.post.index')->with('success', 'Refleksi berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        $path = $post->cover_url;
        DB::transaction(function () use ($post) {
            if (! $post->delete()) {
                throw new RuntimeException('Refleksi gagal dihapus.');
            }
        });
        $this->deleteImage($path);

        return redirect()->route('management.post.index')->with('success', 'Refleksi berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'cover_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        unset($data['cover_url']);

        return $data;
    }

    private function deleteImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'covers/')) {
            return;
        }
        try {
            if (! Storage::disk('public')->delete($path)) {
                report(new RuntimeException('Gambar lama gagal dibersihkan.'));
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
