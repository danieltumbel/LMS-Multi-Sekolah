<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create(); // peran bawaan factory: student

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('siswa.dashboard', absolute: false));
    }

    public static function peranDanTujuan(): array
    {
        return [
            'super admin'   => ['super_admin', 'super-admin.dashboard'],
            'siswa'         => ['student', 'siswa.dashboard'],
            'admin sekolah' => ['school_admin', 'admin.dashboard'],
            'guru'          => ['teacher', 'dashboard'], // diteruskan ke /dashboard, lalu ke dashboard guru
        ];
    }

    #[DataProvider('peranDanTujuan')]
    public function test_login_diarahkan_sesuai_peran(string $role, string $namaRute): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route($namaRute, absolute: false));

        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_registrasi_mandiri_tidak_tersedia(): void
    {
        $this->get('/register')->assertNotFound();

        $this->post('/register', [
        'name' => 'Penyusup', 'email' => 'x@contoh.test',
        'password' => 'password', 'password_confirmation' => 'password',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'x@contoh.test']);
    }
}
