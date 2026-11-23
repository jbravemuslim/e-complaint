<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandPaletteTest extends TestCase
{
    use RefreshDatabase;

    public function test_palette_lists_commands_per_role(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user)->get('/dashboard')->assertOk()
            ->assertSee('hx-palette', false)
            ->assertSee('Riwayat Keluhan', false)
            ->assertDontSee('Kelola User', false);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/admin')->assertOk()
            ->assertSee('hx-palette', false)
            ->assertSee('Complain Proses', false)
            ->assertDontSee('Laporan', false);

        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($superadmin)->get('/superadmin')->assertOk()
            ->assertSee('hx-palette', false)
            ->assertSee('Kelola User', false)
            ->assertSee('Laporan', false);
    }
}
