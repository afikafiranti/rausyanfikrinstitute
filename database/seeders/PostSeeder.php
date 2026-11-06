<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['article' => 'Artikel', 'reflection' => 'Refleksi publik'] as $type => $label) {
            foreach (['published' => true, 'draft' => false] as $state => $published) {
                Post::query()->firstOrCreate(
                    ['slug' => "contoh-{$type}-{$state}"],
                    [
                        'title' => "Contoh {$label} {$state}",
                        'type' => $type,
                        'excerpt' => "Konten contoh untuk menguji alur {$label}.",
                        'content' => "Konten contoh untuk pengembangan {$label}.",
                        'published_at' => $published ? now()->subDay() : null,
                    ]
                );
            }
        }
    }
}
