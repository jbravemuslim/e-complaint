<?php

namespace Tests\Feature;

use App\Models\Complain;
use App\Models\Jenis;
use App\Models\Response;
use App\Models\Tujuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_dashboard_shows_monthly_chart(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $tujuan = Tujuan::create(['nama_unit' => 'Kesiswaan']);
        $jenis = Jenis::create(['nama_jenis' => 'Fasilitas']);

        $c = Complain::create([
            'user_id' => $user->id, 'keluhan' => 'AC rusak',
            'lampiran' => null, 'tgl_keluhan' => now()->toDateString(),
            'tujuan_id' => $tujuan->id, 'jenis_id' => $jenis->id,
            'status' => 'selesai',
        ]);
        Response::create([
            'complain_id' => $c->id, 'admin_id' => $admin->id,
            'tanggapan' => 'OK', 'tgl_tanggapan' => now()->toDateString(),
        ]);

        $this->actingAs($superadmin)->get('/superadmin')->assertOk()
            ->assertSee('hx-chart', false)
            ->assertSee('Masuk vs Ditanggapi', false)
            ->assertSee(now()->translatedFormat('M Y'), false);
    }
}
