<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_only_shows_the_latest_published_posts_and_real_gallery(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 16)->setTime(12, 0));
        for ($i = 1; $i <= 4; $i++) {
            Post::create(['title' => 'Refleksi Terbit '.$i, 'excerpt' => 'Ringkasan '.$i, 'published_at' => now()->subDays($i)]);
        }
        Post::create(['title' => 'Rahasia Draft', 'published_at' => null]);
        Post::create(['title' => 'Rahasia Terjadwal', 'published_at' => now()->addSecond()]);
        Gallery::create(['title' => 'Dokumentasi Kajian', 'caption' => 'Kegiatan bersama', 'image_url' => 'galleries/kajian.jpg']);

        $this->get(route('landing_page'))->assertOk()->assertViewIs('landingpage.home')
            ->assertSeeInOrder(['Refleksi Terbit 1', 'Refleksi Terbit 2', 'Refleksi Terbit 3'])
            ->assertDontSee('Refleksi Terbit 4')->assertDontSee('Rahasia Draft')->assertDontSee('Rahasia Terjadwal')
            ->assertSee('Dokumentasi Kajian')->assertSee('Kegiatan bersama')
            ->assertSee('/storage/galleries/kajian.jpg', false)
            ->assertViewHas('publishedPostCount', 4)->assertViewHas('galleryCount', 1);
    }

    public function test_public_detail_rejects_draft_future_and_missing_slugs_and_accepts_publication_boundary(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 16)->setTime(12, 0));
        $draft = Post::create(['title' => 'Draft', 'published_at' => null]);
        $future = Post::create(['title' => 'Belum Terbit', 'published_at' => now()->addSecond()]);
        $published = Post::create(['title' => 'Terbit Sekarang', 'content' => 'Isi refleksi yang dapat dibaca.', 'published_at' => now()]);

        foreach ([$draft->slug, $future->slug, 'tidak-ada'] as $slug) {
            $this->get('/refleksi/'.$slug)->assertNotFound();
        }
        $this->get('/refleksi/'.$published->slug)->assertOk()->assertSee('Terbit Sekarang')->assertSee('Isi refleksi yang dapat dibaca.');
    }

    public function test_empty_homepage_and_public_lists_have_honest_empty_states_and_no_demo_links(): void
    {
        $response = $this->get('/')->assertOk()->assertSee('Belum ada refleksi yang diterbitkan.')->assertSee('Belum ada foto galeri.');
        foreach (['Lorem Ipsum', 'Our Blogs', 'Testimonials', 'blog-details.html', 'javascript:void(0)', 'info@yourdomain.com'] as $demo) {
            $response->assertDontSee($demo, false);
        }
        $dom = new \DOMDocument;
        @$dom->loadHTML($response->getContent());
        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//a[@href]') as $link) {
            $href = $link->getAttribute('href');
            $this->assertNotSame('#', $href);
            if (str_starts_with($href, '#')) {
                $this->assertNotNull($dom->getElementById(substr($href, 1)), 'Missing section '.$href);
            }
        }
        $this->get('/refleksi')->assertOk()->assertSee('Belum ada refleksi yang diterbitkan.');
        $this->get('/galeri')->assertOk()->assertSee('Belum ada foto galeri.');
    }

    public function test_public_lists_paginate_in_a_stable_order_without_leaking_drafts(): void
    {
        for ($i = 1; $i <= 13; $i++) {
            Post::create(['title' => 'Publik '.$i, 'published_at' => '2026-01-01']);
            Gallery::create(['title' => 'Foto '.$i, 'image_url' => 'galleries/'.$i.'.jpg']);
        }
        Post::create(['title' => 'Tidak Boleh Tampil', 'published_at' => null]);
        $this->get('/refleksi')->assertOk()->assertViewHas('posts', fn ($posts) => $posts->count() === 12 && $posts->first()->title === 'Publik 13')->assertDontSee('Tidak Boleh Tampil');
        $this->get('/refleksi?page=2')->assertOk()->assertViewHas('posts', fn ($posts) => $posts->count() === 1 && $posts->first()->title === 'Publik 1');
        $this->get('/galeri')->assertOk()->assertViewHas('galleries', fn ($items) => $items->count() === 12 && $items->first()->title === 'Foto 13');
        $this->get('/galeri?page=2')->assertOk()->assertViewHas('galleries', fn ($items) => $items->count() === 1 && $items->first()->title === 'Foto 1');
    }

    public function test_public_content_is_escaped_and_unsafe_image_urls_are_not_rendered(): void
    {
        $payload = '<script>window.injected=true</script>';
        $post = Post::create(['title' => 'Judul '.$payload, 'excerpt' => $payload, 'content' => $payload."\nBaris kedua", 'cover_url' => 'javascript:alert(1)', 'published_at' => now()]);
        Gallery::create(['title' => 'Galeri '.$payload, 'caption' => $payload, 'image_url' => 'data:image/svg+xml,bad']);
        foreach (['/', '/refleksi', '/refleksi/'.$post->slug, '/galeri'] as $url) {
            $this->get($url)->assertOk()->assertDontSee($payload, false)->assertDontSee('javascript:alert(1)', false)->assertDontSee('data:image/svg+xml', false);
        }
        $this->get('/refleksi/'.$post->slug)->assertSee($payload)->assertSee('Baris kedua');
    }

    public function test_content_saved_by_admin_is_available_to_guests_with_its_uploaded_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['status' => 'active']);
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin']));
        $this->actingAs($admin)->post('/management/post', ['title' => 'Refleksi Dari Pengelola', 'content' => 'Isi baru.', 'cover_url' => UploadedFile::fake()->image('cover.jpg')])->assertRedirect();
        $this->post('/management/galeri', ['title' => 'Galeri Dari Pengelola', 'image' => UploadedFile::fake()->image('foto.png')])->assertRedirect();
        $this->app['auth']->forgetGuards();
        $this->assertGuest();
        $post = Post::firstOrFail();
        $gallery = Gallery::firstOrFail();

        $this->get('/')->assertOk()->assertSee('Refleksi Dari Pengelola')->assertSee('Galeri Dari Pengelola')
            ->assertSee(Storage::disk('public')->url($post->cover_url), false)->assertSee(Storage::disk('public')->url($gallery->image_url), false);
        $this->get('/refleksi/'.$post->slug)->assertOk()->assertSee('Isi baru.');
    }

    public function test_existing_https_images_are_preserved_and_publication_can_be_withdrawn(): void
    {
        $post = Post::create(['title' => 'Terbit Sementara', 'cover_url' => 'https://images.example.test/cover.jpg', 'published_at' => now()]);
        Gallery::create(['title' => 'Foto HTTPS', 'image_url' => 'https://images.example.test/foto.jpg']);
        $this->get('/')->assertOk()->assertSee('https://images.example.test/cover.jpg', false)->assertSee('https://images.example.test/foto.jpg', false);
        $this->get('/refleksi/'.$post->slug)->assertOk();
        $post->update(['published_at' => null]);
        $this->get('/refleksi/'.$post->slug)->assertNotFound();
        $this->get('/')->assertDontSee('Terbit Sementara');
    }
}
