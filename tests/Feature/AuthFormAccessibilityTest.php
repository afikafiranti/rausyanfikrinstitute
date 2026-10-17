<?php

namespace Tests\Feature;

use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFormAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_fields_have_associated_labels_and_autocomplete(): void
    {
        foreach (['/login', '/register', '/forgot-password', '/reset-password/example?email=alumni@example.test'] as $url) {
            $response = $this->get($url)->assertOk();
            $document = new DOMDocument();
            @$document->loadHTML($response->getContent(), LIBXML_NONET);
            $xpath = new DOMXPath($document);

            foreach ($xpath->query('//input[not(@type="hidden")]') as $input) {
                $id = $input->getAttribute('id');
                $this->assertNotSame('', $id, "Missing input ID on $url");
                $this->assertSame(1, $xpath->query('//label[@for="'.$id.'"]')->length, "Missing label on $url");
                if ($input->getAttribute('type') !== 'checkbox') {
                    $this->assertNotSame('', $input->getAttribute('autocomplete'), "Missing autocomplete on $url");
                }
            }
        }
    }

    public function test_password_visibility_control_has_an_accessible_name_and_state(): void
    {
        $this->get('/login')->assertOk()
            ->assertSee('aria-label="Tampilkan password"', false)
            ->assertSee('aria-controls="password"', false)
            ->assertSee('aria-pressed="false"', false);
    }

    public function test_invalid_reset_token_error_is_visible_on_reset_form(): void
    {
        $user = User::factory()->create();
        $url = '/reset-password/invalid?email='.urlencode($user->email);

        $this->from($url)->post('/reset-password', [
            'token' => 'invalid', 'email' => $user->email,
            'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123',
        ])->assertSessionHasErrors('email')->assertRedirect($url);

        $this->get($url)->assertOk()->assertSee(__('passwords.token'))
            ->assertSee('role="alert"', false);
    }

    public function test_verification_mail_failure_is_visible_and_offers_retry(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->withSession(['status' => 'verification-link-failed'])
            ->get(route('verification.notice'))->assertOk()
            ->assertSee('Email verifikasi belum dapat dikirim.')
            ->assertSee('role="alert"', false)
            ->assertSee(route('verification.send'), false)
            ->assertSee(route('profile.edit'), false);
    }
}
