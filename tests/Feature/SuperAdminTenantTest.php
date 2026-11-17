<?php

namespace Tests\Feature;

use App\Mail\KredensialAdminSekolah;
use App\Models\School;
use App\Models\User;
use App\Modules\AdminSekolah\Http\Livewire\ManageUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class SuperAdminTenantTest extends TestCase
{
    use RefreshDatabase;

    private function buatSekolah(array $o = []): int
    {
        return DB::table('schools')->insertGetId(array_merge([
            'name' => 'SMA Contoh '.uniqid(), 'address' => 'Jl. Contoh',
            'contact_email' => uniqid().'@sekolah.test', 'phone' => '08123456789',
            'status' => 'active', 'max_users' => 10, 'max_classes' => 5,
            'created_at' => now(), 'updated_at' => now(),
        ], $o));
    }

    private function buatUser(string $role, ?int $schoolId = null): User
    {
        return User::forceCreate([
            'name' => ucfirst($role), 'email' => $role.uniqid().'@contoh.test',
            'password' => Hash::make('password'), 'role' => $role,
            'school_id' => $schoolId, 'is_active' => true, 'email_verified_at' => now(),
        ]);
    }

    public function test_status_berubah_dan_tercatat_di_audit_log(): void
    {
        $sa = $this->buatUser('super_admin');
        $id = $this->buatSekolah();

        $this->actingAs($sa)
            ->patch("/super-admin/tenants/$id/status", ['status' => 'suspended', 'alasan' => 'Menunggak langganan'])
            ->assertSessionHasNoErrors();

        $this->assertSame('suspended', DB::table('schools')->where('id', $id)->value('status'));
        $this->assertDatabaseHas('audit_logs', [
            'school_id' => $id, 'user_id' => $sa->id,
            'action' => 'tenant.status_changed', 'description' => 'Menunggak langganan',
        ]);
    }

    public function test_status_nonaktif_diterima(): void
    {
        $sa = $this->buatUser('super_admin');
        $id = $this->buatSekolah();

        $this->actingAs($sa)->patch("/super-admin/tenants/$id/status", ['status' => 'inactive', 'alasan' => 'Berhenti']);

        $this->assertSame('inactive', DB::table('schools')->where('id', $id)->value('status'));
    }

    public function test_status_tanpa_alasan_atau_nilai_salah_ditolak(): void
    {
        $sa = $this->buatUser('super_admin');
        $id = $this->buatSekolah();

        $this->actingAs($sa)->patch("/super-admin/tenants/$id/status", ['status' => 'suspended'])
            ->assertSessionHasErrors('alasan');
        $this->actingAs($sa)->patch("/super-admin/tenants/$id/status", ['status' => 'rusak', 'alasan' => 'x'])
            ->assertSessionHasErrors('status');

        $this->assertSame('active', DB::table('schools')->where('id', $id)->value('status'));
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_admin_sekolah_tidak_boleh_mengubah_status(): void
    {
        $id = $this->buatSekolah();
        $admin = $this->buatUser('school_admin', $id);

        $this->actingAs($admin)->patch("/super-admin/tenants/$id/status", ['status' => 'suspended', 'alasan' => 'x'])
            ->assertForbidden();
    }

    public function test_update_kuota_berhasil_dan_tercatat(): void
    {
        $sa = $this->buatUser('super_admin');
        $id = $this->buatSekolah(['name' => 'SMA Satu', 'contact_email' => 'satu@sekolah.test']);

        $this->actingAs($sa)->put("/super-admin/tenants/$id", [
            'name' => 'SMA Satu', 'address' => 'Jl. Baru', 'email' => 'satu@sekolah.test',
            'phone' => '08123456789', 'max_users' => 50, 'max_classes' => 20,
        ])->assertSessionHasNoErrors()->assertRedirect(route('super-admin.tenants.index'));

        $this->assertDatabaseHas('schools', ['id' => $id, 'max_users' => 50, 'max_classes' => 20, 'address' => 'Jl. Baru']);
        $this->assertDatabaseHas('audit_logs', ['school_id' => $id, 'action' => 'tenant.updated']);
    }

    public function test_kuota_tidak_boleh_di_bawah_pemakaian(): void
    {
        $sa = $this->buatUser('super_admin');
        $id = $this->buatSekolah(['name' => 'SMA Dua', 'contact_email' => 'dua@sekolah.test']);
        $this->buatUser('teacher', $id);
        $this->buatUser('student', $id);
        $this->buatUser('student', $id);

        $this->actingAs($sa)->put("/super-admin/tenants/$id", [
            'name' => 'SMA Dua', 'address' => 'Jl. A', 'email' => 'dua@sekolah.test',
            'phone' => '08123456789', 'max_users' => 2, 'max_classes' => 5,
        ])->assertSessionHasErrors('max_users');

        $this->assertSame(10, (int) DB::table('schools')->where('id', $id)->value('max_users'));
    }

    public function test_nama_sekolah_tidak_boleh_kembar_saat_edit(): void
    {
        $sa = $this->buatUser('super_admin');
        $this->buatSekolah(['name' => 'SMA Kembar']);
        $id = $this->buatSekolah(['name' => 'SMA Lain', 'contact_email' => 'lain@sekolah.test']);

        $this->actingAs($sa)->put("/super-admin/tenants/$id", [
            'name' => 'SMA Kembar', 'address' => 'Jl. A', 'email' => 'lain@sekolah.test',
            'phone' => '08123456789', 'max_users' => 10, 'max_classes' => 5,
        ])->assertSessionHasErrors('name');
    }

    public function test_halaman_detail_dan_edit_bisa_dibuka(): void
    {
        $sa = $this->buatUser('super_admin');
        $id = $this->buatSekolah(['name' => 'SMA Tampil']);

        $this->actingAs($sa)->get("/super-admin/tenants/$id")->assertOk()->assertSee('SMA Tampil');
        $this->actingAs($sa)->get("/super-admin/tenants/$id/edit")->assertOk()->assertSee('SMA Tampil');
        $this->actingAs($sa)->get('/super-admin/tenants/999999')->assertNotFound();
    }

    public function test_daftar_tenant_baru_membuat_admin_audit_dan_email(): void
    {
        Mail::fake();
        $sa = $this->buatUser('super_admin');

        $this->actingAs($sa)->post('/super-admin/tenants', [
            'name' => 'SMA Baru', 'address' => 'Jl. Baru 1', 'email' => 'baru@sekolah.test',
            'phone' => '08123456789', 'admin_name' => 'Admin Baru', 'admin_email' => 'admin@baru.test',
            'max_users' => 100, 'max_classes' => 10,
        ])->assertSessionHasNoErrors()->assertSessionHas('tenant_created');

        $this->assertDatabaseHas('schools', ['name' => 'SMA Baru', 'status' => 'active']);
        $this->assertDatabaseHas('users', ['email' => 'admin@baru.test', 'role' => 'school_admin']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'tenant.created']);
        Mail::assertSent(KredensialAdminSekolah::class, fn ($m) => $m->hasTo('admin@baru.test'));
    }

    public function test_sisa_kuota_user_dihitung_benar(): void
    {
        $id = $this->buatSekolah(['max_users' => 3]);
        $this->buatUser('school_admin', $id);
        $this->buatUser('teacher', $id);

        $this->assertSame(1, School::sisaKuotaUser($id));
    }

    public function test_admin_sekolah_tidak_bisa_tambah_siswa_jika_kuota_penuh(): void
    {
        $id = $this->buatSekolah(['max_users' => 1]);
        $admin = $this->buatUser('school_admin', $id); // kuota langsung penuh

        Livewire::actingAs($admin)
            ->test(ManageUsers::class, ['role' => 'student'])
            ->set('name', 'Budi')->set('email', 'budi@contoh.test')
            ->call('simpan')
            ->assertHasErrors(['email']);

        $this->assertDatabaseMissing('users', ['email' => 'budi@contoh.test']);
    }

    public function test_admin_sekolah_bisa_tambah_siswa_jika_kuota_masih_ada(): void
    {
        $id = $this->buatSekolah(['max_users' => 5]);
        $admin = $this->buatUser('school_admin', $id);

        Livewire::actingAs($admin)
            ->test(ManageUsers::class, ['role' => 'student'])
            ->set('name', 'Budi')->set('email', 'budi@contoh.test')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'budi@contoh.test', 'role' => 'student', 'school_id' => $id]);
    }

    public function test_pengguna_sekolah_ditangguhkan_otomatis_keluar(): void
    {
        $id = $this->buatSekolah(['status' => 'suspended']);
        $guru = $this->buatUser('teacher', $id);

        $this->actingAs($guru)->get('/guru/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_pengguna_sekolah_aktif_tidak_diblokir(): void
    {
        $id = $this->buatSekolah(['status' => 'active']);
        $guru = $this->buatUser('teacher', $id);

        $this->actingAs($guru)->get('/guru/dashboard')->assertOk();
    }

    public function test_ringkasan_dashboard_memakai_data_asli(): void
    {
        $sa = $this->buatUser('super_admin');
        $a = $this->buatSekolah();
        $this->buatSekolah(['status' => 'suspended']);
        $this->buatUser('teacher', $a);
        $this->buatUser('student', $a);

        $this->actingAs($sa)->get('/super-admin/ringkasan')
            ->assertOk()
            ->assertJsonPath('sekolah.total', 2)
            ->assertJsonPath('sekolah.aktif', 1)
            ->assertJsonPath('sekolah.ditangguhkan', 1)
            ->assertJsonPath('pengguna.guru', 1)
            ->assertJsonPath('pengguna.siswa', 1);
    }

    public function test_akun_nonaktif_tidak_bisa_login_web(): void {
     $sid = $this->buatSekolah();
     $u = $this->buatUser('teacher', $sid);
     DB::table('users')->where('id', $u->id)->update(['is_active' => false]);

     $this->post('/login', ['email' => $u->email, 'password' => 'password']);

     $this->assertGuest();
    }

   public function test_akun_yang_dinonaktifkan_saat_login_langsung_keluar(): void {
     $sid = $this->buatSekolah();
     $u = $this->buatUser('teacher', $sid);

     $this->actingAs($u);
     $u->forceFill(['is_active' => false])->save(); // objek yang sama dengan yang dipegang guard

     $this->get('/guru/dashboard')->assertRedirect('/login');
     $this->assertGuest();
    }
}
