<?php

namespace App\Modules\AdminSekolah\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Manajemen pengguna sekolah: satu komponen untuk tab Guru (teacher) dan Siswa (student).
 */
class ManageUsers extends Component
{
    use WithPagination;

    #[Locked]
    public string $role = 'teacher';

    public string $q = '';

    // Form tambah pengguna
    public bool $showForm = false;
    public string $name = '';
    public string $email = '';
    public string $nomor_induk = '';

    // Detail (tombol "Lihat")
    public ?int $detailId = null;

    // Pesan sukses dan password sementara (ditampilkan sekali)
    public ?string $pesan = null;
    public ?array $kredensial = null;

    public function mount(string $role = 'teacher'): void
    {
        $this->role = $role === 'student' ? 'student' : 'teacher';
    }

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    // Livewire tidak selalu menjalankan ulang middleware route, jadi akses dicek lagi di sini
    private function admin()
    {
        $u = request()->user();
        abort_unless($u && $u->role === 'school_admin' && $u->school_id, 403);

        return $u;
    }

    public function buka(): void
    {
        $this->admin();
        $this->resetValidation();
        $this->reset('name', 'email', 'nomor_induk');
        $this->showForm = true;
    }

    public function tutupForm(): void
    {
        $this->showForm = false;
    }

    public function simpan(): void
    {
        $admin = $this->admin();
        $label = $this->role === 'teacher' ? 'NIP' : 'NIS';

        $data = $this->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'nomor_induk' => ['nullable', 'string', 'max:30',
                Rule::unique('users', 'nomor_induk')->where('school_id', $admin->school_id)],
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah dipakai akun lain.',
            'nomor_induk.unique' => $label.' sudah terdaftar di sekolah ini.',
        ]);

        $password = $data['nomor_induk'] !== '' ? $data['nomor_induk'] : Str::random(12);

        // forceCreate: role, school_id, dan is_active sengaja tidak ada di $fillable
        User::forceCreate([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'nomor_induk'       => $data['nomor_induk'] !== '' ? $data['nomor_induk'] : null,
            'password'          => Hash::make($password),
            'role'              => $this->role,
            'school_id'         => $admin->school_id,
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        $this->kredensial = ['nama' => $data['name'], 'email' => $data['email'], 'password' => $password];
        $this->pesan      = null;
        $this->showForm   = false;
        $this->reset('name', 'email', 'nomor_induk');
        $this->resetPage();
    }

    public function toggle(int $id): void
    {
        $admin = $this->admin();

        $u = DB::table('users')
            ->where('id', $id)
            ->where('school_id', $admin->school_id)
            ->where('role', $this->role)
            ->first();

        if (! $u || $u->id === $admin->id) {
            return;
        }

        DB::table('users')->where('id', $id)->update(['is_active' => ! $u->is_active, 'updated_at' => now()]);

        $this->kredensial = null;
        $this->pesan = $u->name.($u->is_active ? ' dinonaktifkan.' : ' diaktifkan.');
    }

    public function lihat(int $id): void
    {
        $this->detailId = $id;
    }

    public function tutupDetail(): void
    {
        $this->detailId = null;
    }

    public function tutupKredensial(): void
    {
        $this->kredensial = null;
    }

    private function dasar(int $schoolId)
    {
        // Kolom ke-4: guru = mata pelajaran yang diampu; siswa = jumlah kelas aktif yang diikuti
        $kolom4 = $this->role === 'teacher'
            ? DB::table('classes as c')
                ->selectRaw("GROUP_CONCAT(DISTINCT c.mata_pelajaran ORDER BY c.mata_pelajaran SEPARATOR ', ')")
                ->whereColumn('c.guru_id', 'u.id')
            : DB::table('enrollments as e')
                ->selectRaw('COUNT(*)')
                ->whereColumn('e.user_id', 'u.id')
                ->where('e.status', 'aktif');

        return DB::table('users as u')
            ->where('u.school_id', $schoolId)
            ->where('u.role', $this->role)
            ->select('u.id', 'u.name', 'u.email', 'u.nomor_induk', 'u.is_active', 'u.created_at')
            ->selectSub($kolom4, 'kolom4');
    }

    #[Layout('components.adminsekolah-layout')]
    public function render()
    {
        $admin = $this->admin();
        $q = trim($this->q);

        $users = $this->dasar($admin->school_id)
            ->when($q !== '', function ($b) use ($q) {
                $b->where(function ($w) use ($q) {
                    $w->where('u.name', 'like', '%'.$q.'%')
                      ->orWhere('u.email', 'like', '%'.$q.'%')
                      ->orWhere('u.nomor_induk', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('u.name')
            ->paginate(5);

        $detail = $this->detailId
            ? $this->dasar($admin->school_id)->where('u.id', $this->detailId)->first()
            : null;

        return view('AdminSekolah.users', [
            'users'  => $users,
            'detail' => $detail,
            'guru'   => $this->role === 'teacher',
        ]);
    }
}