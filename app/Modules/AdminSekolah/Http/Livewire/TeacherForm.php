<?php

namespace App\Modules\AdminSekolah\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Form "Tambah Guru". Guru ditugaskan ke satu kelas dengan mengisi kolom guru_id pada tabel classes.
 */
class TeacherForm extends Component
{
    public string $name = '';
    public string $nomor_induk = '';
    public string $email = '';
    public string $no_telepon = '';
    public string $mapel = '';
    public string $class_id = '';
    public string $status = 'aktif';

    private function admin()
    {
        $u = request()->user();
        abort_unless($u && $u->role === 'school_admin' && $u->school_id, 403);

        return $u;
    }

    // Ganti mata pelajaran -> pilihan kelas diulang
    public function updatedMapel(): void
    {
        $this->class_id = '';
    }

    private function daftarMapel(int $schoolId)
    {
        return DB::table('classes')
            ->where('school_id', $schoolId)->where('status', 'aktif')
            ->distinct()->orderBy('mata_pelajaran')->pluck('mata_pelajaran');
    }

    public function simpan()
    {
        $admin  = $this->admin();
        $wajibKelas = $this->daftarMapel($admin->school_id)->isNotEmpty();

        $data = $this->validate([
            'name'        => ['required', 'string', 'max:255'],
            'nomor_induk' => ['required', 'string', 'max:30',
                Rule::unique('users', 'nomor_induk')->where('school_id', $admin->school_id)],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_telepon'  => ['nullable', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'status'      => ['required', 'in:aktif,nonaktif'],
            'mapel'       => [$wajibKelas ? 'required' : 'nullable', 'string', 'max:255'],
            'class_id'    => [$wajibKelas ? 'required' : 'nullable', 'integer'],
        ], [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'nomor_induk.required' => 'NIP wajib diisi.',
            'nomor_induk.unique'   => 'NIP sudah terdaftar di sekolah ini.',
            'email.required'       => 'Email wajib diisi.',
            'email.email'          => 'Format email tidak valid.',
            'email.unique'         => 'Email sudah dipakai akun lain.',
            'no_telepon.regex'     => 'No. telepon tidak valid (8–20 digit, boleh diawali +).',
            'mapel.required'       => 'Pilih mata pelajaran.',
            'class_id.required'    => 'Pilih kelas pengampu.',
        ]);

        // Kelas harus milik sekolah ini dan sesuai mata pelajaran yang dipilih
        $kelasId = $data['class_id'] !== '' ? (int) $data['class_id'] : null;
        if ($kelasId) {
            $ada = DB::table('classes')
                ->where('id', $kelasId)->where('school_id', $admin->school_id)
                ->where('mata_pelajaran', $data['mapel'])->where('status', 'aktif')->exists();

            if (! $ada) {
                $this->addError('class_id', 'Kelas tidak valid untuk mata pelajaran tersebut.');

                return;
            }
        }

        $password = Str::random(12);

        DB::transaction(function () use ($data, $admin, $kelasId, $password) {
            $guru = User::forceCreate([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'nomor_induk'       => $data['nomor_induk'],
                'no_telepon'        => $data['no_telepon'] !== '' ? $data['no_telepon'] : null,
                'password'          => Hash::make($password),
                'role'              => 'teacher',
                'school_id'         => $admin->school_id,
                'is_active'         => $data['status'] === 'aktif',
                'email_verified_at' => now(),
            ]);

            if ($kelasId) {
                DB::table('classes')->where('id', $kelasId)
                    ->update(['guru_id' => $guru->id, 'updated_at' => now()]);
            }
        });

        session()->flash('kredensial', ['nama' => $data['name'], 'email' => $data['email'], 'password' => $password]);

        return redirect()->route('admin.teachers.index');
    }

    #[Layout('components.adminsekolah-layout')]
    public function render()
    {
        $admin = $this->admin();

        $mapelList = $this->daftarMapel($admin->school_id);

        $kelasList = $this->mapel !== ''
            ? DB::table('classes as c')
                ->leftJoin('users as g', 'g.id', '=', 'c.guru_id')
                ->where('c.school_id', $admin->school_id)
                ->where('c.status', 'aktif')
                ->where('c.mata_pelajaran', $this->mapel)
                ->orderBy('c.nama')
                ->get(['c.id', 'c.nama', 'g.name as guru'])
            : collect();

        return view('AdminSekolah.teacher-form', compact('mapelList', 'kelasList'));
    }
}