<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::latest()->paginate(9);

        return view('management.galeri.index', compact('galleries'));
    }

    public function create()
    {
        return redirect()->route('management.galeri.index');
    }

    public function store(Request $request)
    {
        $request->merge(['_editing_id' => null]);
        $data = $this->validatedData($request, true);
        $path = null;
        try {
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('galleries', 'public');
                if ($path === false) {
                    throw new RuntimeException('Gambar gagal disimpan.');
                }
            }
            $data['image_url'] = $path;
            DB::transaction(function () use ($data) {
                if (! (new Gallery($data))->save()) {
                    throw new RuntimeException('Galeri gagal disimpan.');
                }
            });
        } catch (Throwable $exception) {
            $this->deleteImage($path ?: null);
            throw $exception;
        }

        return redirect()->route('management.galeri.index')->with('success', 'Galeri berhasil ditambahkan.');
    }

    public function update(Request $request, Gallery $galeri)
    {
        $request->merge(['_editing_id' => $galeri->id]);
        $data = $this->validatedData($request);
        $oldPath = $galeri->image_url;
        $path = null;
        try {
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('galleries', 'public');
                if ($path === false) {
                    throw new RuntimeException('Gambar gagal disimpan.');
                }
                $data['image_url'] = $path;
            }
            DB::transaction(function () use ($galeri, $data) {
                if (! $galeri->fill($data)->save()) {
                    throw new RuntimeException('Galeri gagal diperbarui.');
                }
            });
        } catch (Throwable $exception) {
            $this->deleteImage($path ?: null);
            throw $exception;
        }
        if ($path !== null) {
            $this->deleteImage($oldPath);
        }

        return redirect()->route('management.galeri.index')->with('success', 'Galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $galeri)
    {
        $path = $galeri->image_url;
        DB::transaction(function () use ($galeri) {
            if (! $galeri->delete()) {
                throw new RuntimeException('Galeri gagal dihapus.');
            }
        });
        $this->deleteImage($path);

        return redirect()->route('management.galeri.index')->with('success', 'Galeri berhasil dihapus.');
    }

    private function validatedData(Request $request, bool $creating = false): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'caption' => 'nullable|string',
            'image' => ($creating ? 'required' : 'nullable').'|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        unset($data['image']);

        return $data;
    }

    private function deleteImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'galleries/')) {
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
