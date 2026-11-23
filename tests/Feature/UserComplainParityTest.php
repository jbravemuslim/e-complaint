<?php

namespace Tests\Feature;

use App\Models\Complain;
use App\Models\Jenis;
use App\Models\Tujuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserComplainParityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $other;

    private Tujuan $tujuan;

    private Jenis $jenis;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'user']);
        $this->other = User::factory()->create(['role' => 'user']);
        $this->tujuan = Tujuan::create(['nama_unit' => 'Kesiswaan']);
        $this->jenis = Jenis::create(['nama_jenis' => 'Fasilitas']);
    }

    public function test_store_rejects_invalid_master_and_oversize_file(): void
    {
        Storage::fake('public');

        // tujuan tidak valid
        $this->actingAs($this->user)->post('/complains', [
            'tujuan_id' => 9999,
            'jenis_id' => $this->jenis->id,
            'keluhan' => 'AC rusak',
        ])->assertSessionHasErrors('tujuan_id');

        // file bukan gambar -> pesan legacy
        $txt = UploadedFile::fake()->create('dok.txt', 10, 'text/plain');
        $this->actingAs($this->user)->post('/complains', [
            'tujuan_id' => $this->tujuan->id,
            'jenis_id' => $this->jenis->id,
            'keluhan' => 'AC rusak',
            'lampiran' => $txt,
        ])->assertSessionHasErrors('lampiran');

        // file > 1MB -> pesan legacy (PNG asli 1x1 lalu di-pad hingga >1MB)
        $tmpBig = tempnam(sys_get_temp_dir(), 'big').'.jpg';
        file_put_contents(
            $tmpBig,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')
            .str_repeat("\0", 2 * 1024 * 1024)
        );
        $big = new UploadedFile($tmpBig, 'besar.jpg', 'image/jpeg', null, true);
        $this->actingAs($this->user)->post('/complains', [
            'tujuan_id' => $this->tujuan->id,
            'jenis_id' => $this->jenis->id,
            'keluhan' => 'AC rusak',
            'lampiran' => $big,
        ])->assertSessionHasErrors('lampiran');

        $this->assertSame(0, Complain::count());
    }

    public function test_user_cannot_view_or_delete_other_users_complain(): void
    {
        $complain = Complain::create([
            'user_id' => $this->other->id, 'keluhan' => 'Milik orang',
            'lampiran' => null, 'tgl_keluhan' => now()->toDateString(),
            'tujuan_id' => $this->tujuan->id, 'jenis_id' => $this->jenis->id,
            'status' => 'proses',
        ]);

        $this->actingAs($this->user)->get("/complains/{$complain->id}")->assertForbidden();
        $this->actingAs($this->user)->delete("/complains/{$complain->id}")->assertForbidden();
        $this->assertSame(1, Complain::count());
    }

    public function test_riwayat_paginated_and_show_has_full_detail(): void
    {
        foreach (range(1, 12) as $i) {
            Complain::create([
                'user_id' => $this->user->id, 'keluhan' => "Keluhan {$i}",
                'lampiran' => null, 'tgl_keluhan' => now()->toDateString(),
                'tujuan_id' => $this->tujuan->id, 'jenis_id' => $this->jenis->id,
                'status' => 'proses',
            ]);
        }

        $res = $this->actingAs($this->user)->get('/riwayat')->assertOk();
        // total 12 tampil di eyebrow, pagination muncul (halaman 1 = 10 baris)
        $res->assertSee('Riwayat 12 Keluhan', false);
        $res->assertSee('page=2', false);
        $res->assertSee('Showing', false);

        $this->actingAs($this->user)->get('/riwayat?page=2')->assertOk()->assertSee('page=1', false);

        $first = Complain::where('user_id', $this->user->id)->latest('id')->first();
        $this->actingAs($this->user)->get("/complains/{$first->id}")
            ->assertOk()
            ->assertSee('No Antrian', false)
            ->assertSee('Kesiswaan', false)
            ->assertSee('Fasilitas', false)
            ->assertSee('noImage.svg', false);
    }

    public function test_keluhan_is_escaped(): void
    {
        $complain = Complain::create([
            'user_id' => $this->user->id, 'keluhan' => '<script>alert(1)</script>',
            'lampiran' => null, 'tgl_keluhan' => now()->toDateString(),
            'tujuan_id' => $this->tujuan->id, 'jenis_id' => $this->jenis->id,
            'status' => 'proses',
        ]);

        $this->actingAs($this->user)->get("/complains/{$complain->id}")
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }
}
