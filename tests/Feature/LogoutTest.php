<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_logout_returns_to_login_and_ends_authentication(): void
    {
        $this->actingAs(new User(['name' => 'HeaderUsernameExample']));
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
        $this->get('/login')->assertOk()->assertSee('Masuk');
    }

    public function test_header_hides_username_and_keeps_logout_button(): void
    {
        $user = new User(['name' => 'HeaderUsernameExample']);
        $this->actingAs($user)->get('/')->assertOk()
            ->assertSee('Keluar')->assertDontSeeText($user->name);
    }
}
