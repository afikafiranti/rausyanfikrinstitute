<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gallery;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        Gallery::query()->firstOrCreate(
            ['title' => 'Contoh Galeri Pengembangan'],
            ['image_url' => 'https://placehold.co/800x600?text=Contoh+Galeri', 'caption' => 'Gambar contoh untuk pengembangan.']
        );
    }
}
