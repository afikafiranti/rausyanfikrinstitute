<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileFormStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_has_one_set_of_fields_and_keeps_old_input(): void
    {
        $user = User::factory()->create(['name' => 'Nama Awal']);
        $page = $this->actingAs($user)
            ->withSession(['_old_input' => ['name' => 'Nama Koreksi']])
            ->get('/profile')->assertOk();

        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$page->getContent());
        $xpath = new \DOMXPath($dom);
        $profileForms = $xpath->query('//form[@action="'.route('profile.update').'"]');
        $passwordForms = $xpath->query('//form[@action="'.route('profile.updatePassword').'"]');
        $this->assertCount(1, $profileForms);
        $this->assertCount(1, $passwordForms);

        $profile = $profileForms->item(0);
        foreach (['photo', 'name', 'email', 'phone', 'tempat_lahir', 'tanggal_lahir', 'pekerjaan',
            'pendidikan_terakhir', 'kampus', 'status_pernikahan', 'ab', 'angkatan', 'wilayah_id'] as $field) {
            $this->assertCount(1, $xpath->query('.//*[@name="'.$field.'"]', $profile), $field);
        }
        $this->assertSame('Nama Koreksi', $xpath->query('.//*[@name="name"]', $profile)->item(0)->getAttribute('value'));

        foreach (['current_password', 'password', 'password_confirmation'] as $field) {
            $this->assertCount(1, $xpath->query('.//*[@name="'.$field.'"]', $passwordForms->item(0)), $field);
        }

        $ids = [];
        foreach ($xpath->query('.//*[@id]', $profile) as $node) {
            $ids[] = $node->getAttribute('id');
        }
        $this->assertSame($ids, array_values(array_unique($ids)));
    }
}
