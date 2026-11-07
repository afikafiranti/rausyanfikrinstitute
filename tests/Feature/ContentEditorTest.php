<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContentEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $actor = User::factory()->create(['status' => 'active']);
        $actor->roles()->attach(Role::firstOrCreate(['name' => 'super_admin']));
        $this->actingAs($actor);
    }

    public static function resources(): array
    {
        return [
            [Post::class, 'post', 'editingPost', 'excerpt', 'cover_url'],
            [Gallery::class, 'galeri', 'editingGallery', 'caption', 'image'],
        ];
    }

    #[DataProvider('resources')]
    public function test_one_form_supports_create_and_scoped_edit_without_an_edit_modal(string $model, string $resource, string $variable, string $text, string $file): void
    {
        $record = $model::factory()->create(['title' => "Judul O'Rahman", $text => 'Isi lama']);
        $index = '/management/'.$resource;
        $response = $this->get($index.'?edit='.$record->id)->assertOk()
            ->assertViewHas($variable, fn ($item) => $item->is($record))
            ->assertHeader('Cache-Control', 'no-store, private')->assertDontSee('id="editModal"', false);
        $xpath = $this->xpath($response->getContent());
        $this->assertSame(1, $xpath->query('//form[@data-editor-form]')->length);
        $this->assertSame(url($index.'/'.$record->id), $xpath->query('//form[@data-editor-form]')->item(0)->getAttribute('action'));
        $this->assertSame(0, $xpath->query('//input[@name="_method" and @disabled]')->length);
        $this->assertSame(0, $xpath->query('//input[@name="'.$file.'" and @required]')->length);

        $page = $this->get($index)->assertOk()->assertViewHas($variable, fn ($item) => $item === null);
        $xpath = $this->xpath($page->getContent());
        $this->assertSame(url($index), $xpath->query('//form[@data-editor-form]')->item(0)->getAttribute('action'));
        $this->assertSame(1, $xpath->query('//input[@name="_method" and @disabled]')->length);
        $this->assertSame($resource === 'galeri' ? 1 : 0, $xpath->query('//input[@name="'.$file.'" and @required]')->length);

        $actor = User::factory()->create(['status' => 'active']);
        $actor->roles()->attach(Role::firstOrCreate(['name' => 'admin']));
        $this->actingAs($actor)->get($index.'?edit='.$record->id)->assertForbidden();
    }

    #[DataProvider('resources')]
    public function test_failed_edit_restores_the_target_and_submitted_fields_in_the_side_form(string $model, string $resource, string $variable, string $text, string $file): void
    {
        $record = $model::factory()->create(['title' => 'Judul asli', $text => 'Isi asli']);
        $index = '/management/'.$resource;
        $url = $index.'?edit='.$record->id;
        $this->from($url)->put($index.'/'.$record->id, ['title' => '', $text => 'Isian belum tersimpan'])
            ->assertRedirect($url)->assertSessionHasErrors('title');
        $response = $this->get($url)->assertOk()->assertViewHas($variable, fn ($item) => $item->is($record))
            ->assertSee('Isian belum tersimpan')->assertSee('data-has-errors="true"', false);
        $xpath = $this->xpath($response->getContent());
        $this->assertSame('', $xpath->query('//input[@name="title"]')->item(0)->getAttribute('value'));
        $this->assertSame(url($index.'/'.$record->id), $xpath->query('//form[@data-editor-form]')->item(0)->getAttribute('action'));
        $this->assertSame('Judul asli', $record->fresh()->title);
    }

    #[DataProvider('resources')]
    public function test_failed_create_does_not_reopen_the_record_from_an_old_edit_url(string $model, string $resource, string $variable, string $text, string $file): void
    {
        $record = $model::factory()->create(['title' => 'Jangan diubah', $text => 'Isi asli']);
        $index = '/management/'.$resource;
        $url = $index.'?edit='.$record->id;
        $this->from($url)->post($index, ['_editing_id' => $record->id, 'title' => '', $text => 'Draf data baru'])
            ->assertRedirect($url)->assertSessionHasErrors('title');
        $response = $this->get($url)->assertOk()->assertViewHas($variable, fn ($item) => $item === null)->assertSee('Draf data baru');
        $xpath = $this->xpath($response->getContent());
        $this->assertSame(url($index), $xpath->query('//form[@data-editor-form]')->item(0)->getAttribute('action'));
        $this->assertSame(1, $xpath->query('//input[@name="_method" and @disabled]')->length);
        $this->assertSame('Jangan diubah', $record->fresh()->title);
        $this->assertSame(1, $model::count());
    }

    private function xpath(string $html): \DOMXPath
    {
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        return new \DOMXPath($dom);
    }
}
