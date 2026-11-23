<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_landing_page(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Ajukan Keluhan', false)
            ->assertSee('Cara Kerja', false)
            ->assertSee('hero-visual', false)
            ->assertSee('nav-actions', false)
            ->assertSee(route('login'), false)
            ->assertSee(route('register'), false);
    }

    public function test_authenticated_users_skip_landing(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $this->actingAs($user)->get('/')->assertRedirect('/dashboard');
        $this->actingAs($admin)->get('/')->assertRedirect('/admin');
        $this->actingAs($superadmin)->get('/')->assertRedirect('/superadmin');
    }
}
