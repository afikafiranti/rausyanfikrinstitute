<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $admin = User::factory()->create(['status' => 'active']);
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin']));
        $this->actingAs($admin);
    }

    public static function contentTypes(): array
    {
        return [
            'post' => [Post::class, 'post', 'cover_url', 'cover_url', 'covers'],
            'gallery' => [Gallery::class, 'galeri', 'image', 'image_url', 'galleries'],
        ];
    }

    #[DataProvider('contentTypes')]
    public function test_upload_and_replacement_only_keep_the_current_image(string $model, string $resource, string $upload, string $column, string $folder): void
    {
        $this->from('/management/'.$resource)->post('/management/'.$resource, [
            'title' => 'Gambar Pertama', $upload => UploadedFile::fake()->image('first.jpg'),
        ])->assertRedirect('/management/'.$resource)->assertSessionHas('success');
        $record = $model::firstOrFail();
        $old = $record->$column;
        Storage::disk('public')->assertExists($old);

        $this->put('/management/'.$resource.'/'.$record->id, [
            'title' => 'Gambar Baru', $upload => UploadedFile::fake()->image('new.png'),
        ])->assertRedirect('/management/'.$resource)->assertSessionHas('success');

        $record->refresh();
        $this->assertSame('Gambar Baru', $record->title);
        $this->assertNotSame($old, $record->$column);
        Storage::disk('public')->assertExists($record->$column);
        Storage::disk('public')->assertMissing($old);
        $this->assertCount(1, Storage::disk('public')->allFiles($folder));
    }

    #[DataProvider('contentTypes')]
    public function test_failed_insert_rolls_back_the_record_and_removes_uploaded_image(string $model, string $resource, string $upload, string $column, string $folder): void
    {
        $model::created(fn () => throw new RuntimeException('Simulated post-insert failure'));
        $this->post('/management/'.$resource, [
            'title' => 'Gagal Disimpan', $upload => UploadedFile::fake()->image('new.jpg'),
        ])->assertServerError();

        $this->assertSame(0, $model::count());
        $this->assertSame([], Storage::disk('public')->allFiles($folder));
    }

    #[DataProvider('contentTypes')]
    public function test_failed_update_preserves_original_record_and_image_and_cleans_new_upload(string $model, string $resource, string $upload, string $column, string $folder): void
    {
        $old = $folder.'/old.jpg';
        Storage::disk('public')->put($old, 'original image');
        $record = $model::create(['title' => 'Judul Lama', $column => $old]);
        $model::updated(fn () => throw new RuntimeException('Simulated post-update failure'));

        $this->put('/management/'.$resource.'/'.$record->id, [
            'title' => 'Judul Baru', $upload => UploadedFile::fake()->image('new.jpg'),
        ])->assertServerError();

        $record->refresh();
        $this->assertSame('Judul Lama', $record->title);
        $this->assertSame($old, $record->$column);
        Storage::disk('public')->assertExists($old);
        $this->assertSame([$old], Storage::disk('public')->allFiles($folder));
    }

    #[DataProvider('contentTypes')]
    public function test_failed_delete_preserves_record_and_image(string $model, string $resource, string $upload, string $column, string $folder): void
    {
        $old = $folder.'/old.jpg';
        Storage::disk('public')->put($old, 'original image');
        $record = $model::create(['title' => 'Tetap Ada', $column => $old]);
        $model::deleted(fn () => throw new RuntimeException('Simulated post-delete failure'));

        $this->delete('/management/'.$resource.'/'.$record->id)->assertServerError();
        $this->assertNotNull($record->fresh());
        Storage::disk('public')->assertExists($old);
    }

    #[DataProvider('contentTypes')]
    public function test_successful_delete_removes_record_and_image(string $model, string $resource, string $upload, string $column, string $folder): void
    {
        $old = $folder.'/old.jpg';
        Storage::disk('public')->put($old, 'original image');
        $record = $model::create(['title' => 'Hapus', $column => $old]);
        $this->from('/management/'.$resource)->delete('/management/'.$resource.'/'.$record->id)
            ->assertRedirect('/management/'.$resource)->assertSessionHas('success');
        $this->assertNull($record->fresh());
        Storage::disk('public')->assertMissing($old);
    }

    #[DataProvider('contentTypes')]
    public function test_invalid_and_oversized_images_are_rejected_without_creating_files(string $model, string $resource, string $upload, string $column, string $folder): void
    {
        $fake = UploadedFile::fake()->createWithContent('fake.jpg', '<script>bad</script>');
        $invalid = new UploadedFile($fake->getPathname(), 'fake.jpg', 'image/jpeg', null, true);
        foreach ([$invalid, UploadedFile::fake()->image('large.jpg')->size(2049), UploadedFile::fake()->image('animation.gif')] as $file) {
            $this->from('/management/'.$resource)->post('/management/'.$resource, ['title' => 'Input Dipertahankan', $upload => $file])
                ->assertRedirect('/management/'.$resource)->assertSessionHasErrors($upload);
        }
        $this->assertSame(0, $model::count());
        $this->assertSame([], Storage::disk('public')->allFiles($folder));
        $this->get('/management/'.$resource)->assertOk()->assertSee('Input Dipertahankan')->assertSee('role="alert"', false);
    }

    #[DataProvider('contentTypes')]
    public function test_disk_write_failure_preserves_the_existing_record_and_image(string $model, string $resource, string $upload, string $column, string $folder): void
    {
        $old = $folder.'/old.jpg';
        $disk = Storage::disk('public');
        $disk->put($old, 'original image');
        $record = $model::create(['title' => 'Judul Lama', $column => $old]);
        $failingDisk = \Mockery::mock($disk);
        $failingDisk->shouldReceive('putFileAs')->andReturn(false);
        Storage::set('public', $failingDisk);

        $this->put('/management/'.$resource.'/'.$record->id, [
            'title' => 'Judul Baru', $upload => UploadedFile::fake()->image('new.jpg'),
        ])->assertServerError();
        $this->assertSame('Judul Lama', $record->refresh()->title);
        $this->assertSame($old, $record->$column);
        $disk->assertExists($old);
        $this->assertSame([$old], $disk->allFiles($folder));
    }

    #[DataProvider('contentTypes')]
    public function test_cancelled_save_does_not_report_success_or_leave_new_files(string $model, string $resource, string $upload, string $column, string $folder): void
    {
        $model::saving(fn () => false);
        $this->post('/management/'.$resource, ['title' => 'Dibatalkan', $upload => UploadedFile::fake()->image('new.jpg')])
            ->assertServerError()->assertSessionMissing('success');
        $this->assertSame(0, $model::count());
        $this->assertSame([], Storage::disk('public')->allFiles($folder));
    }

    #[DataProvider('contentTypes')]
    public function test_text_only_update_preserves_image_and_failed_validation_keeps_edit_context(string $model, string $resource, string $upload, string $column, string $folder): void
    {
        $old = $folder.'/old.jpg';
        Storage::disk('public')->put($old, 'original image');
        $record = $model::create(['title' => 'Judul Lama', $column => $old]);
        $this->put('/management/'.$resource.'/'.$record->id, ['title' => 'Judul Baru'])
            ->assertRedirect('/management/'.$resource)->assertSessionHas('success');
        $this->assertSame($old, $record->refresh()->$column);
        Storage::disk('public')->assertExists($old);

        $this->from('/management/'.$resource)->put('/management/'.$resource.'/'.$record->id, ['title' => '', '_editing_id' => '999999'])
            ->assertRedirect('/management/'.$resource)->assertSessionHasErrors('title')->assertSessionHasInput('_editing_id', $record->id);
        $response = $this->get('/management/'.$resource)->assertOk()->assertSee('Periksa kembali isian formulir.');
        $dom = new \DOMDocument;
        @$dom->loadHTML($response->getContent());
        $xpath = new \DOMXPath($dom);
        $form = $xpath->query('//form[@id="editForm"]')->item(0);
        $this->assertSame(route('management.'.$resource.'.update', $record->id), $form->getAttribute('action'));
        $this->assertSame('', $xpath->query('//input[@id="editTitle"]')->item(0)->getAttribute('value'));
    }

    public function test_post_edit_content_is_escaped_data_instead_of_inline_javascript(): void
    {
        $payload = '`);window.injected=true;// ${alert(1)} </script><script>alert(1)</script> "quoted"';
        Post::create(['title' => 'Konten Kutipan', 'content' => $payload]);
        $response = $this->get('/management/post')->assertOk()->assertDontSee('<script>alert(1)</script>', false);
        $dom = new \DOMDocument;
        @$dom->loadHTML($response->getContent());
        $button = (new \DOMXPath($dom))->query('//button[@data-content]')->item(0);
        $this->assertNotNull($button);
        $this->assertFalse($button->hasAttribute('onclick'));
        $this->assertSame($payload, $button->getAttribute('data-content'));
    }

    public function test_posts_with_colliding_or_non_slug_titles_have_unique_bounded_slugs(): void
    {
        foreach (['Judul Sama', 'Judul Sama', '!!!', '???', str_repeat('a', 255), str_repeat('a', 255)] as $title) {
            $this->from('/management/post')->post('/management/post', ['title' => $title])->assertRedirect('/management/post')->assertSessionHasNoErrors();
        }
        $slugs = Post::pluck('slug');
        $this->assertCount(6, $slugs->unique());
        foreach ($slugs as $slug) {
            $this->assertNotEmpty($slug);
            $this->assertLessThanOrEqual(255, strlen($slug));
        }

        $post = Post::latest('id')->first();
        $this->from('/management/post')->put('/management/post/'.$post->id, ['title' => 'Judul Sama'])->assertRedirect('/management/post');
        $this->assertCount(6, Post::pluck('slug')->unique());
        $slug = $post->refresh()->slug;
        $this->put('/management/post/'.$post->id, ['title' => 'Judul Sama', 'excerpt' => 'Ringkasan baru'])->assertRedirect('/management/post');
        $this->assertSame($slug, $post->refresh()->slug);
    }

    public function test_same_title_insert_can_finish_between_slug_allocation_and_insert(): void
    {
        $inserted = false;
        Post::creating(function (Post $post) use (&$inserted) {
            if (! $inserted) {
                $inserted = true;
                Post::create(['title' => $post->title]);
            }
        });

        $this->from('/management/post')->post('/management/post', ['title' => 'Judul Serentak'])
            ->assertRedirect('/management/post')->assertSessionHas('success');
        $this->assertSame(2, Post::count());
        $this->assertCount(2, Post::pluck('slug')->unique());
    }

    public function test_unimplemented_resource_actions_are_not_exposed(): void
    {
        foreach (['post', 'galeri'] as $resource) {
            foreach (['create', '1', '1/edit'] as $path) {
                $this->get('/management/'.$resource.'/'.$path)->assertStatus($path === '1/edit' ? 404 : 405);
            }
        }
    }
}
