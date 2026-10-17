<?php

namespace Tests\Feature;

use App\Models\{Role, User, Wilayah};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_navigation_exposes_named_controls_dialog_and_skip_link_without_dummy_search(): void
    {
        $user = User::factory()->create(['status' => 'active', 'wilayah_id' => Wilayah::create(['name' => 'Wilayah'])->id]);
        $user->roles()->attach(Role::firstOrCreate(['name' => 'admin']));
        $response = $this->actingAs($user)->get('/laporan')->assertOk()->assertDontSee('Search here...');
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $this->assertSame(1, $xpath->query('//button[@aria-controls="rf-mobile-drawer" and @aria-label="Buka navigasi" and @aria-expanded="false"]')->length);
        $this->assertSame(1, $xpath->query('//*[@id="rf-mobile-drawer" and @role="dialog" and @aria-modal="true" and @aria-label="Navigasi" and @aria-hidden="true"]')->length);
        $this->assertSame(1, $xpath->query('//*[@id="rf-mobile-drawer"]//button[@aria-label="Tutup navigasi"]')->length);
        $this->assertSame(1, $xpath->query('//button[@aria-controls="user-dropdown" and @aria-label="Menu akun" and @aria-expanded="false"]')->length);
        $this->assertSame(1, $xpath->query('//a[@href="#konten-utama"]')->length);
        $this->assertSame(1, $xpath->query('//main[@id="konten-utama"]')->length);
        $this->assertSame(0, $xpath->query('//main[contains(concat(" ", normalize-space(@class), " "), " -m-36 ")]')->length);
    }
}
