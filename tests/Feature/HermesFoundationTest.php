<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HermesFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_load_hermes_assets(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('css/hermes.css', false)
            ->assertSee('js/hermes.js', false)
            ->assertSee('Keluhanmu', false)
            ->assertDontSee('hx-marquee', false)
            ->assertDontSee('data-hx-scramble', false)
            ->assertDontSee('data-hx-rotate', false);

        $this->get('/login')->assertOk()
            ->assertSee('css/hermes.css', false)
            ->assertSee('js/hermes.js', false);
    }

    public function test_dashboard_loads_hermes_assets(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/dashboard')->assertOk()
            ->assertSee('css/hermes.css', false)
            ->assertSee('js/hermes.js', false);
    }
}
