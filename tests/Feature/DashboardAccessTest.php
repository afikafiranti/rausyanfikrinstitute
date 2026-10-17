<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    public function test_guest_is_redirected_to_login_before_accessing_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_unauthenticated_json_request_cannot_access_dashboard(): void
    {
        $this->getJson('/dashboard')->assertUnauthorized();
    }
}
