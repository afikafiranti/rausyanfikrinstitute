<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RemovedMemberReflectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_private_reflection_routes_and_table_are_removed(): void
    {
        $this->assertFalse(Schema::hasTable('member_reflections'));

        $requests = [
            ['get', '/refleksi-saya'],
            ['get', '/refleksi-saya/create'],
            ['get', '/refleksi-saya/1'],
            ['get', '/refleksi-saya/1/edit'],
            ['post', '/refleksi-saya'],
            ['patch', '/refleksi-saya/1'],
            ['delete', '/refleksi-saya/1'],
            ['post', '/refleksi-saya/1/kirim'],
            ['get', '/tinjau-refleksi'],
            ['get', '/tinjau-refleksi/1'],
            ['patch', '/tinjau-refleksi/1'],
        ];

        foreach ([null, 'alumni', 'koorda', 'admin', 'super_admin'] as $role) {
            if ($role !== null) {
                $user = User::factory()->create(['status' => User::STATUS_ACTIVE]);
                $user->roles()->attach(Role::firstOrCreate(['name' => $role]));
                $this->actingAs($user);
            }

            foreach ($requests as [$method, $path]) {
                $this->{$method}($path)->assertNotFound();
            }
        }
    }

    public function test_private_reflection_links_are_gone_while_public_reflections_still_work(): void
    {
        $reflection = Post::create(['title' => 'Refleksi publik', 'type' => 'reflection', 'published_at' => now()]);
        $this->get('/refleksi/'.$reflection->slug)->assertOk();

        $alumni = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $alumni->roles()->attach(Role::firstOrCreate(['name' => 'alumni']));
        $this->actingAs($alumni)->get('/dashboard')->assertOk()
            ->assertDontSee('/refleksi-saya')->assertDontSee('/tinjau-refleksi');

        $superAdmin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $superAdmin->roles()->attach(Role::firstOrCreate(['name' => 'super_admin']));
        $page = $this->actingAs($superAdmin)->get('/management/post')->assertOk()
            ->assertDontSee('/refleksi-saya')->assertDontSee('/tinjau-refleksi');
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$page->getContent());
        $links = (new \DOMXPath($dom))->query('//a[contains(@href, "/management/post")]');
        $this->assertCount(2, $links);
        foreach ($links as $link) {
            $this->assertSame('Artikel', trim($link->textContent));
        }
    }
}
