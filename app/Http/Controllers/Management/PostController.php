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
    public function index(Request $request)
    {
        $request->validate(['edit' => ['nullable', 'integer', 'min:1']]);
        $editingId = array_key_exists('_editing_id', $request->session()->getOldInput()) ? $request->old('_editing_id') : $request->query('edit');
        $editingPost = $editingId ? Post::findOrFail($editingId) : null;
        $posts = Post::latest()->paginate(10);

        return response()->view('management.refleksi.index', compact('posts', 'editingPost'))
            ->header('Cache-Control', 'private, no-store');
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
            $data['type'] ??= 'reflection';
            $data['published_at'] = ($data['publication_status'] ?? 'published') === 'published' ? now() : null;
            unset($data['publication_status']);
            DB::transaction(function () use ($data) {
                if (! (new Post($data))->save()) {
                    throw new RuntimeException('Artikel gagal disimpan.');
                }
            });
        } catch (Throwable $exception) {
            $this->deleteImage($path ?: null);
            throw $exception;
        }

        return redirect()->route('management.post.index')->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function update(Request $request, Post $post)
    {
        $request->merge(['_editing_id' => $post->id]);
        $data = $this->validatedData($request);
        if (isset($data['publication_status'])) {
            $data['published_at'] = $data['publication_status'] === 'draft'
                ? null
                : ($post->published_at && ! $post->published_at->isFuture() ? $post->published_at : now());
        }
        unset($data['publication_status']);
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
                    throw new RuntimeException('Artikel gagal diperbarui.');
                }
            });
        } catch (Throwable $exception) {
            $this->deleteImage($path ?: null);
            throw $exception;
        }
        if ($path !== null) {
            $this->deleteImage($oldPath);
        }

        return redirect()->route('management.post.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        $path = $post->cover_url;
        DB::transaction(function () use ($post) {
            if (! $post->delete()) {
                throw new RuntimeException('Artikel gagal dihapus.');
            }
        });
        $this->deleteImage($path);

        return redirect()->route('management.post.index')->with('success', 'Artikel berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'cover_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'type' => 'sometimes|required|in:reflection,article',
            'publication_status' => 'sometimes|nullable|in:draft,published',
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
