<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_register_page(): void
    {
        $this->get('/register')->assertOk()
            ->assertSee('Daftar Akun', false)
            ->assertSee('pw-toggle', false)
            ->assertSee(route('login'), false);
    }

    public function test_login_page_links_register(): void
    {
        $this->get('/login')->assertOk()
            ->assertSee('Selamat Datang Kembali', false)
            ->assertSee('pw-toggle', false)
            ->assertSee(route('register'), false);
    }

    public function test_guest_can_register_and_is_logged_in_as_user(): void
    {
        $this->post('/register', [
            'name' => 'Siswa Baru',
            'username' => 'siswa',
            'password' => 'siswa123',
            'password_confirmation' => 'siswa123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        $user = User::where('username', 'siswa')->first();
        $this->assertNotNull($user);
        $this->assertSame('user', $user->role);
        $this->assertNotSame('siswa123', $user->password);
    }

    public function test_register_rejects_duplicate_username_and_mismatch(): void
    {
        User::factory()->create(['username' => 'siswa']);

        $this->post('/register', [
            'name' => 'Kembar',
            'username' => 'siswa',
            'password' => 'siswa123',
            'password_confirmation' => 'siswa123',
        ])->assertSessionHasErrors('username');
        $this->assertGuest();

        $this->post('/register', [
            'name' => 'Baru',
            'username' => 'baru',
            'password' => 'baru1234',
            'password_confirmation' => 'beda1234',
        ])->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_open_register(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/register')->assertRedirect('/dashboard');
    }
}
