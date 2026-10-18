<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticlePublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_uses_only_four_latest_published_articles_and_four_gallery_photos(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Post::create(['title' => 'Artikel '.$i, 'type' => 'article', 'published_at' => now()->subDays($i)]);
            Gallery::create(['title' => 'Foto '.$i, 'image_url' => 'galleries/'.$i.'.jpg']);
        }
        Post::create(['title' => 'Refleksi lama', 'published_at' => now()]);
        Post::create(['title' => 'Artikel draf', 'type' => 'article']);
        Post::create(['title' => 'Artikel besok', 'type' => 'article', 'published_at' => now()->addDay()]);

        $this->get('/')->assertOk()
            ->assertViewHas('posts', fn ($posts) => $posts->count() === 4
                && $posts->pluck('title')->all() === ['Artikel 1', 'Artikel 2', 'Artikel 3', 'Artikel 4'])
            ->assertViewHas('galleries', fn ($galleries) => $galleries->count() === 4);
    }

    public function test_article_and_reflection_urls_are_separate_and_hide_unpublished_content(): void
    {
        $article = Post::create(['title' => 'Artikel terbit', 'type' => 'article', 'published_at' => now()]);
        $reflection = Post::create(['title' => 'Refleksi terbit', 'published_at' => now()]);
        $draft = Post::create(['title' => 'Artikel draf', 'type' => 'article']);
        $future = Post::create(['title' => 'Artikel nanti', 'type' => 'article', 'published_at' => now()->addDay()]);

        $this->get('/artikel')->assertOk()->assertViewHas('isArticle', true)
            ->assertViewHas('posts', fn ($posts) => $posts->pluck('id')->all() === [$article->id]);
        $this->get('/refleksi')->assertOk()->assertViewHas('isArticle', false)
            ->assertViewHas('posts', fn ($posts) => $posts->pluck('id')->all() === [$reflection->id]);
        $this->get('/artikel/'.$article->slug)->assertOk()->assertViewHas('isArticle', true);
        $this->get('/refleksi/'.$reflection->slug)->assertOk()->assertViewHas('isArticle', false);
        foreach (['/artikel/'.$reflection->slug, '/refleksi/'.$article->slug, '/artikel/'.$draft->slug, '/artikel/'.$future->slug] as $url) {
            $this->get($url)->assertNotFound();
        }
    }

    public function test_super_admin_can_draft_publish_and_preserve_post_publication_state_on_edit(): void
    {
        $this->asRole('super_admin');
        $this->post('/management/post', ['title' => 'Draf artikel', 'type' => 'article', 'publication_status' => 'draft'])
            ->assertRedirect();
        $post = Post::firstOrFail();
        $this->assertSame('article', $post->type);
        $this->assertNull($post->published_at);
        $this->get('/artikel/'.$post->slug)->assertNotFound();

        $this->put('/management/post/'.$post->id, ['title' => 'Artikel terbit', 'publication_status' => 'published'])
            ->assertRedirect();
        $publishedAt = $post->refresh()->published_at;
        $this->assertNotNull($publishedAt);
        $this->assertSame('article', $post->type);
        $this->get('/artikel/'.$post->slug)->assertOk();

        $this->put('/management/post/'.$post->id, ['title' => 'Artikel diperbarui'])->assertRedirect();
        $this->assertSame($publishedAt->toDateTimeString(), $post->refresh()->published_at->toDateTimeString());
        $this->put('/management/post/'.$post->id, ['title' => 'Artikel ditarik', 'publication_status' => 'draft'])->assertRedirect();
        $this->assertNull($post->refresh()->published_at);
        $this->get('/artikel/'.$post->slug)->assertNotFound();
    }

    public function test_legacy_post_submission_defaults_to_published_reflection_and_invalid_state_is_rejected(): void
    {
        $this->asRole('super_admin');
        $this->post('/management/post', ['title' => 'Refleksi lama'])->assertRedirect();
        $post = Post::firstOrFail();
        $this->assertSame('reflection', $post->type);
        $this->assertNotNull($post->published_at);
        $this->get('/refleksi/'.$post->slug)->assertOk();

        $this->from('/management/post')->post('/management/post', [
            'title' => 'Jenis salah', 'type' => 'page', 'publication_status' => 'live',
        ])->assertSessionHasErrors(['type', 'publication_status']);
        $this->assertSame(1, Post::count());
    }

    public function test_explicit_publish_releases_a_scheduled_article_but_omitted_status_keeps_schedule(): void
    {
        $this->asRole('super_admin');
        $post = Post::create([
            'title' => 'Artikel mendatang', 'type' => 'article', 'published_at' => now()->addDay(),
        ]);
        $this->get('/artikel/'.$post->slug)->assertNotFound();

        $this->put('/management/post/'.$post->id, ['title' => 'Jadwal dipertahankan'])->assertRedirect();
        $this->assertTrue($post->refresh()->published_at->isFuture());
        $this->get('/artikel/'.$post->slug)->assertNotFound();

        $this->put('/management/post/'.$post->id, [
            'title' => 'Jadwal tetap', 'publication_status' => '',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertTrue($post->refresh()->published_at->isFuture());
        $this->get('/artikel/'.$post->slug)->assertNotFound();

        $this->put('/management/post/'.$post->id, [
            'title' => 'Terbit sekarang', 'publication_status' => 'published',
        ])->assertRedirect();
        $this->assertTrue($post->refresh()->published_at->lessThanOrEqualTo(now()));
        $this->get('/artikel/'.$post->slug)->assertOk();
    }

    public function test_only_active_super_admin_can_use_every_post_and_gallery_management_endpoint(): void
    {
        $post = Post::create(['title' => 'Existing post']);
        $gallery = Gallery::create(['title' => 'Existing photo', 'image_url' => 'galleries/photo.jpg']);
        foreach (['admin', 'koorda', 'alumni'] as $role) {
            $this->asRole($role);
            foreach (['post', 'galeri'] as $resource) {
                $this->get('/management/'.$resource)->assertForbidden();
                $this->post('/management/'.$resource, ['title' => 'Forbidden'])->assertForbidden();
                $id = $resource === 'post' ? $post->id : $gallery->id;
                $this->put('/management/'.$resource.'/'.$id, ['title' => 'Forbidden'])->assertForbidden();
                $this->delete('/management/'.$resource.'/'.$id)->assertForbidden();
            }
        }
        $this->asRole('super_admin', 'suspended');
        $this->get('/management/post')->assertForbidden();
        $this->asRole('super_admin', 'pending');
        $this->get('/management/galeri')->assertForbidden();
        $this->asRole('admin', 'active', true);
        $this->get('/management/post')->assertOk();
        $this->get('/management/galeri')->assertOk();
        $this->assertSame('Existing post', Post::firstOrFail()->title);
        $this->assertSame('Existing photo', Gallery::firstOrFail()->title);
    }

    private function asRole(string $role, string $status = 'active', bool $alsoSuperAdmin = false): void
    {
        $user = User::factory()->create(['status' => $status]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));
        if ($alsoSuperAdmin) {
            $user->roles()->attach(Role::firstOrCreate(['name' => 'super_admin']));
        }
        $this->actingAs($user);
    }
}
