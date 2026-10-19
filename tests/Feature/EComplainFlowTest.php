<?php

namespace Tests\Feature;

use App\Models\Complain;
use App\Models\Faq;
use App\Models\Jenis;
use App\Models\Tujuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EComplainFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    private User $admin;

    private User $user;

    private Tujuan $tujuan;

    private Jenis $jenis;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create(['username' => 'admin123', 'password' => 'admin123', 'role' => 'superadmin']);
        $this->admin = User::factory()->create(['username' => 'admin1', 'password' => 'admin1', 'role' => 'admin']);
        $this->user = User::factory()->create(['username' => 'rakadaffa', 'password' => 'raka123', 'role' => 'user']);
        $this->tujuan = Tujuan::create(['nama_unit' => 'Kesiswaan']);
        $this->jenis = Jenis::create(['nama_jenis' => 'Fasilitas']);
    }

    public function test_login_redirects_by_role(): void
    {
        $this->post('/login', ['username' => 'rakadaffa', 'password' => 'raka123'])->assertRedirect('/dashboard');
        $this->post('/logout');

        $this->post('/login', ['username' => 'admin1', 'password' => 'admin1'])->assertRedirect('/admin');
        $this->post('/logout');

        $this->post('/login', ['username' => 'admin123', 'password' => 'admin123'])->assertRedirect('/superadmin');
    }

    public function test_login_rejects_wrong_password(): void
    {
        $this->post('/login', ['username' => 'rakadaffa', 'password' => 'salah'])->assertRedirect();
        $this->assertGuest();
    }

    public function test_role_middleware_blocks_cross_access(): void
    {
        $this->actingAs($this->user)->get('/admin')->assertRedirect('/');
        $this->actingAs($this->user)->get('/superadmin')->assertRedirect('/');
        $this->actingAs($this->admin)->get('/dashboard')->assertRedirect('/');
        $this->actingAs($this->admin)->get('/superadmin')->assertRedirect('/');
    }

    public function test_user_pages_render(): void
    {
        $this->actingAs($this->user)->get('/dashboard')->assertOk()->assertSee('Ajukan Keluhan')->assertSee('panel-card', false);
        // Ornamen dekoratif pojok kanan atas sudah dihapus dari layout.
        $this->actingAs($this->user)->get('/dashboard')->assertOk()->assertDontSee('corner-ornament', false)->assertSee('chart-card', false);
        $this->actingAs($this->user)->get('/riwayat')->assertOk()->assertSee('Riwayat Keluhan', false)->assertSee('sub-menu', false);
        $this->actingAs($this->user)->get('/faq')->assertOk();
    }

    public function test_user_can_submit_and_delete_complain(): void
    {
        Storage::fake('public');

        // PNG 1x1 asli (tanpa butuh ekstensi GD).
        $tmp = tempnam(sys_get_temp_dir(), 'up').'.png';
        file_put_contents($tmp, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
        $lampiran = new UploadedFile($tmp, 'ac.png', 'image/png', null, true);

        $this->actingAs($this->user)->post('/complains', [
            'tujuan_id' => $this->tujuan->id,
            'jenis_id' => $this->jenis->id,
            'keluhan' => 'AC rusak',
            'lampiran' => $lampiran,
        ])->assertRedirect('/riwayat');

        $complain = Complain::first();
        $this->assertNotNull($complain);
        $this->assertSame('proses', $complain->status);
        Storage::disk('public')->assertExists('lampiran/'.$complain->lampiran);

        $this->actingAs($this->user)->get("/complains/{$complain->id}")->assertOk()->assertSee('AC rusak');

        $this->actingAs($this->user)->delete("/complains/{$complain->id}")->assertRedirect('/riwayat');
        $this->assertSame(0, Complain::count());
        Storage::disk('public')->assertMissing('lampiran/'.$complain->lampiran);
    }

    public function test_admin_can_respond_and_sees_own_responses(): void
    {
        $complain = Complain::create([
            'user_id' => $this->user->id, 'keluhan' => 'Toilet mampet', 'lampiran' => null,
            'tgl_keluhan' => now()->toDateString(), 'tujuan_id' => $this->tujuan->id,
            'jenis_id' => $this->jenis->id, 'status' => 'proses',
        ]);

        $this->actingAs($this->admin)->get('/admin')->assertOk()->assertSee('stat-card', false);
        $this->actingAs($this->admin)->get('/admin/complains')->assertOk()->assertSee('Toilet mampet')->assertSee('status-pill', false);

        $this->actingAs($this->admin)->post("/admin/complains/{$complain->id}/respond", [
            'tanggapan' => 'Sudah diperbaiki',
        ])->assertRedirect('/admin/complains');

        $this->assertSame('selesai', $complain->fresh()->status);
        $this->assertSame('Sudah diperbaiki', $complain->fresh()->response->tanggapan);

        $this->actingAs($this->admin)->get('/admin/responses')->assertOk()->assertSee('Sudah diperbaiki');
    }

    public function test_superadmin_pages_and_master_crud(): void
    {
        foreach (['/superadmin', '/superadmin/complains', '/superadmin/responses', '/superadmin/users',
            '/superadmin/admins', '/superadmin/tujuans', '/superadmin/jenis', '/superadmin/faqs',
            '/superadmin/laporan'] as $url) {
            $this->actingAs($this->superadmin)->get($url)->assertOk($url)->assertSee('page-head', false, $url);
        }
        $this->actingAs($this->superadmin)->get('/superadmin/laporan/cetak')->assertOk();

        $this->actingAs($this->superadmin)->post('/superadmin/tujuans', ['nama_unit' => 'TU'])
            ->assertRedirect('/superadmin/tujuans');
        $tujuan = Tujuan::where('nama_unit', 'TU')->first();
        $this->assertNotNull($tujuan);

        $this->actingAs($this->superadmin)->put("/superadmin/tujuans/{$tujuan->id}", ['nama_unit' => 'Tata Usaha'])
            ->assertRedirect('/superadmin/tujuans');
        $this->actingAs($this->superadmin)->delete("/superadmin/tujuans/{$tujuan->id}")
            ->assertRedirect('/superadmin/tujuans');

        $this->actingAs($this->superadmin)->post('/superadmin/users', [
            'name' => 'Budi', 'username' => 'budi', 'password' => 'budi123',
        ])->assertRedirect('/superadmin/users');
        $budi = User::where('username', 'budi')->first();
        $this->assertSame('user', $budi->role);
        $this->actingAs($this->superadmin)->delete("/superadmin/users/{$budi->id}")
            ->assertRedirect('/superadmin/users');

        Faq::create(['pertanyaan' => 'Q?', 'jawaban' => 'A.']);
        $this->actingAs($this->user)->get('/faq')->assertOk()->assertSee('Q?');
    }
}
