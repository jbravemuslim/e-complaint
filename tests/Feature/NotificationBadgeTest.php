<?php

namespace Tests\Feature;

use App\Models\Complain;
use App\Models\Jenis;
use App\Models\Tujuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationBadgeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $user;

    private Tujuan $tujuan;

    private Jenis $jenis;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
        $this->tujuan = Tujuan::create(['nama_unit' => 'Kesiswaan']);
        $this->jenis = Jenis::create(['nama_jenis' => 'Fasilitas']);
    }

    private function makeComplain(string $status = 'proses'): Complain
    {
        return Complain::create([
            'user_id' => $this->user->id, 'keluhan' => 'AC rusak',
            'lampiran' => null, 'tgl_keluhan' => now()->toDateString(),
            'tujuan_id' => $this->tujuan->id, 'jenis_id' => $this->jenis->id,
            'status' => $status,
        ]);
    }

    public function test_user_badge_clears_after_opening_detail(): void
    {
        $complain = $this->makeComplain('selesai');

        $this->actingAs($this->user)->get('/dashboard')->assertOk()
            ->assertSee('hx-badge', false);

        $this->actingAs($this->user)->get("/complains/{$complain->id}")->assertOk();
        $this->assertNotNull($complain->fresh()->user_seen_at);

        $this->actingAs($this->user)->get('/dashboard')->assertOk()
            ->assertDontSee('hx-badge', false);
    }

    public function test_admin_badge_shows_proses_count(): void
    {
        $this->makeComplain('proses');
        $this->makeComplain('proses');
        $this->makeComplain('selesai');

        $this->actingAs($this->admin)->get('/admin')->assertOk()
            ->assertSee('hx-badge', false)
            ->assertSee('<span class="hx-badge">2</span>', false);
    }
}
