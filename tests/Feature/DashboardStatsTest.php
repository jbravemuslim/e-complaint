<?php

namespace Tests\Feature;

use App\Models\Complain;
use App\Models\Jenis;
use App\Models\Tujuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_aggregates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $u1 = User::factory()->create(['name' => 'Budi Santoso', 'role' => 'user']);
        $u2 = User::factory()->create(['name' => 'Sari Melati', 'role' => 'user']);
        $tujuan = Tujuan::create(['nama_unit' => 'Kesiswaan']);
        $jenis1 = Jenis::create(['nama_jenis' => 'Fasilitas']);
        $jenis2 = Jenis::create(['nama_jenis' => 'Akademik']);

        Complain::create(['user_id' => $u1->id, 'keluhan' => 'AC kelas 7A mati', 'lampiran' => null, 'tgl_keluhan' => now()->toDateString(), 'tujuan_id' => $tujuan->id, 'jenis_id' => $jenis1->id, 'status' => 'proses']);
        Complain::create(['user_id' => $u1->id, 'keluhan' => 'Papan tulis pecah', 'lampiran' => null, 'tgl_keluhan' => now()->toDateString(), 'tujuan_id' => $tujuan->id, 'jenis_id' => $jenis2->id, 'status' => 'selesai']);
        Complain::create(['user_id' => $u2->id, 'keluhan' => 'Toilet mampet', 'lampiran' => null, 'tgl_keluhan' => now()->toDateString(), 'tujuan_id' => $tujuan->id, 'jenis_id' => $jenis1->id, 'status' => 'proses']);

        $this->actingAs($admin)->get('/admin')->assertOk()
            ->assertSee('Kinerja Penanganan')
            ->assertSee('Pelanggan Teraktif')
            ->assertSee('Budi Santoso')
            // Grafik per jenis: Fasilitas 2 proses/0 selesai, Akademik 0 proses/1 selesai (urut total menurun).
            ->assertSee('"proses":[2,0]', false)
            ->assertSee('"selesai":[0,1]', false);
    }

    public function test_user_dashboard_scopes_to_own_complains(): void
    {
        $u1 = User::factory()->create(['role' => 'user']);
        $u2 = User::factory()->create(['role' => 'user']);
        $tujuan = Tujuan::create(['nama_unit' => 'Kesiswaan']);
        $jenis = Jenis::create(['nama_jenis' => 'Fasilitas']);

        Complain::create(['user_id' => $u1->id, 'keluhan' => 'Kipas ruang guru mati', 'lampiran' => null, 'tgl_keluhan' => now()->toDateString(), 'tujuan_id' => $tujuan->id, 'jenis_id' => $jenis->id, 'status' => 'proses']);
        Complain::create(['user_id' => $u1->id, 'keluhan' => 'Kaca jendela retak', 'lampiran' => null, 'tgl_keluhan' => now()->toDateString(), 'tujuan_id' => $tujuan->id, 'jenis_id' => $jenis->id, 'status' => 'proses']);
        Complain::create(['user_id' => $u2->id, 'keluhan' => 'Lampi koridor mati', 'lampiran' => null, 'tgl_keluhan' => now()->toDateString(), 'tujuan_id' => $tujuan->id, 'jenis_id' => $jenis->id, 'status' => 'proses']);

        // Donut status hanya menghitung keluhan milik u1, bukan milik u2.
        $this->actingAs($u1)->get('/dashboard')->assertOk()->assertSee('data: [2, 0],', false);
    }

    public function test_superadmin_dashboard_shows_account_counts(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        User::factory()->count(3)->create(['role' => 'user']);
        User::factory()->create(['role' => 'admin']);

        $this->actingAs($superadmin)->get('/superadmin')->assertOk()
            ->assertSee('Total Pelanggan')
            ->assertSee('Admin Aktif');
    }
}
