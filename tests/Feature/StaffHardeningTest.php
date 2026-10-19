<?php

namespace Tests\Feature;

use App\Models\Complain;
use App\Models\Jenis;
use App\Models\Response;
use App\Models\Tujuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffHardeningTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    private User $adminA;

    private User $adminB;

    private User $user;

    private Tujuan $tujuan;

    private Jenis $jenis;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->adminA = User::factory()->create(['role' => 'admin']);
        $this->adminB = User::factory()->create(['role' => 'admin']);
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

    public function test_double_respond_is_rejected_with_single_response(): void
    {
        $complain = $this->makeComplain();

        $this->actingAs($this->adminA)->post("/admin/complains/{$complain->id}/respond", [
            'tanggapan' => 'Sudah diperbaiki',
        ])->assertRedirect('/admin/complains');

        $this->actingAs($this->adminB)->post("/admin/complains/{$complain->id}/respond", [
            'tanggapan' => 'Ikut menanggapi',
        ])->assertStatus(422);

        $this->assertSame(1, Response::where('complain_id', $complain->id)->count());
        $this->assertSame('Sudah diperbaiki', $complain->fresh()->response->tanggapan);
        $this->assertSame('selesai', $complain->fresh()->status);
    }

    public function test_admin_cannot_delete_another_admins_response(): void
    {
        $complain = $this->makeComplain();
        $this->actingAs($this->adminA)->post("/admin/complains/{$complain->id}/respond", [
            'tanggapan' => 'Sudah diperbaiki',
        ]);

        $this->actingAs($this->adminB)->delete("/admin/responses/{$complain->id}")->assertForbidden();
        $this->assertSame(1, Complain::count());

        // Pemilik tanggapan boleh menghapus.
        $this->actingAs($this->adminA)->delete("/admin/responses/{$complain->id}")->assertRedirect('/admin/responses');
        $this->assertSame(0, Complain::count());
    }

    public function test_admin_cannot_delete_selesai_via_complains_route(): void
    {
        $complain = $this->makeComplain();
        $this->actingAs($this->adminA)->post("/admin/complains/{$complain->id}/respond", [
            'tanggapan' => 'Sudah diperbaiki',
        ]);

        $this->actingAs($this->adminA)->delete("/admin/complains/{$complain->id}")->assertStatus(422);
        $this->assertSame(1, Complain::count());
    }

    public function test_deleting_admin_reverts_their_complains_to_proses(): void
    {
        $complain = $this->makeComplain();
        $this->actingAs($this->adminA)->post("/admin/complains/{$complain->id}/respond", [
            'tanggapan' => 'Sudah diperbaiki',
        ]);
        $this->assertSame('selesai', $complain->fresh()->status);

        $this->actingAs($this->superadmin)->delete("/superadmin/admins/{$this->adminA->id}")
            ->assertRedirect('/superadmin/admins');

        $this->assertSame('proses', $complain->fresh()->status);
        $this->assertNull($complain->fresh()->response);
    }

    public function test_cannot_delete_last_superadmin(): void
    {
        $only = User::factory()->create(['role' => 'superadmin']);
        User::where('role', 'superadmin')->where('id', '!=', $only->id)->delete();

        $this->actingAs($only)->delete("/superadmin/admins/{$only->id}")->assertStatus(422);
        $this->assertSame(1, User::where('role', 'superadmin')->count());
    }

    public function test_login_is_throttled_after_many_attempts(): void
    {
        $throttled = false;

        for ($i = 0; $i < 40; $i++) {
            $res = $this->post('/login', ['username' => 'takada', 'password' => 'salah']);
            if ($res->getStatusCode() === 429) {
                $throttled = true;
                break;
            }
        }

        $this->assertTrue($throttled, 'Login seharusnya kena 429 setelah banyak percobaan.');
    }
}
