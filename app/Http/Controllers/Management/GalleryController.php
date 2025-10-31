<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    // 🔹 Tampilkan semua galeri
    public function index()
    {
        $galleries = Gallery::latest()->paginate(9);
        return view('management.galeri.index', compact('galleries'));
    }

    // 🔹 Form tambah galeri
    public function create()
    {
        return view('management.galeri.create');
    }

    // 🔹 Simpan galeri baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'caption' => 'nullable|string',
            'image' => 'required|image|max:2048',
        ]);

        $path = $request->file('image')->store('galleries', 'public');

        Gallery::create([
            'title' => $request->title,
            'caption' => $request->caption,
            'image_url' => $path,
        ]);

        return redirect()->route('management.galeri.index')->with('success', 'Galeri berhasil ditambahkan.');
    }

    // 🔹 Update data galeri
    public function update(Request $request, Gallery $galeri)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'caption' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('title', 'caption');

        // kalau ada gambar baru, hapus lama dan upload baru
        if ($request->hasFile('image')) {
            if ($galeri->image_url && Storage::disk('public')->exists($galeri->image_url)) {
                Storage::disk('public')->delete($galeri->image_url);
            }

            $path = $request->file('image')->store('galleries', 'public');
            $data['image_url'] = $path;
        }

        $galeri->update($data);

        return redirect()->route('management.galeri.index')->with('success', 'Galeri berhasil diperbarui.');
    }

    // 🔹 Hapus galeri
    public function destroy(Gallery $galeri)
    {
        if ($galeri->image_url && Storage::disk('public')->exists($galeri->image_url)) {
            Storage::disk('public')->delete($galeri->image_url);
        }

        $galeri->delete();

        return redirect()->route('management.galeri.index')->with('success', 'Galeri berhasil dihapus.');
    }
}
