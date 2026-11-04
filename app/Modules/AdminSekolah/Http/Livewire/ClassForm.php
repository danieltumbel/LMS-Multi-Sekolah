<?php

namespace App\Modules\AdminSekolah\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Form "Tambah Kelas / Rombel".
 * Guru Pengampu disimpan di classes.guru_id, Wali Kelas di classes.wali_kelas_id.
 */
class ClassForm extends Component
{
    public string $nama = '';
    public string $mata_pelajaran = '';
    public string $tahun_ajaran = '';
    public string $semester = '';
    public string $wali_kelas_id = '';
    public string $guru_id = '';
    public string $kapasitas = '';

    public function mount(): void
    {
        $mulai = now()->month >= 7 ? now()->year : now()->year - 1;

        $this->tahun_ajaran = $mulai.'/'.($mulai + 1);
        $this->semester     = now()->month >= 7 ? 'Ganjil' : 'Genap';
    }

    private function admin()
    {
        $u = request()->user();
        abort_unless($u && $u->role === 'school_admin' && $u->school_id, 403);

        return $u;
    }

    private function opsiTahun(): array
    {
        $mulai = now()->month >= 7 ? now()->year : now()->year - 1;

        return collect([$mulai - 1, $mulai, $mulai + 1])->map(fn ($y) => $y.'/'.($y + 1))->all();
    }

    private function daftarGuru(int $schoolId)
    {
        return DB::table('users')
            ->where('school_id', $schoolId)->where('role', 'teacher')->where('is_active', true)
            ->orderBy('name')->get(['id', 'name']);
    }

    public function simpan()
    {
        $admin = $this->admin();

        $guruValid = Rule::exists('users', 'id')
            ->where('school_id', $admin->school_id)->where('role', 'teacher');

        $data = $this->validate([
            'nama'           => ['required', 'string', 'max:255'],
            'mata_pelajaran' => ['required', 'string', 'max:255'],
            'tahun_ajaran'   => ['required', Rule::in($this->opsiTahun())],
            'semester'       => ['required', 'in:Ganjil,Genap'],
            'wali_kelas_id'  => ['required', 'integer', $guruValid],
            'guru_id'        => ['required', 'integer', $guruValid],
            'kapasitas'      => ['required', 'integer', 'min:1', 'max:500'],
        ], [
            'nama.required'           => 'Nama kelas wajib diisi.',
            'mata_pelajaran.required' => 'Mata pelajaran wajib diisi.',
            'tahun_ajaran.required'   => 'Pilih tahun ajaran.',
            'tahun_ajaran.in'         => 'Tahun ajaran tidak valid.',
            'semester.required'       => 'Pilih semester.',
            'wali_kelas_id.required'  => 'Pilih wali kelas.',
            'wali_kelas_id.exists'    => 'Wali kelas tidak valid.',
            'guru_id.required'        => 'Pilih guru pengampu.',
            'guru_id.exists'          => 'Guru pengampu tidak valid.',
            'kapasitas.required'      => 'Jumlah siswa maksimal wajib diisi.',
            'kapasitas.integer'       => 'Jumlah siswa harus berupa angka.',
            'kapasitas.min'           => 'Minimal 1 siswa.',
            'kapasitas.max'           => 'Maksimal 500 siswa.',
        ]);

        // Hindari kelas dobel di sekolah, tahun ajaran, dan semester yang sama
        $dobel = DB::table('classes')
            ->where('school_id', $admin->school_id)
            ->where('nama', $data['nama'])->where('mata_pelajaran', $data['mata_pelajaran'])
            ->where('tahun_ajaran', $data['tahun_ajaran'])->where('semester', $data['semester'])
            ->exists();

        if ($dobel) {
            $this->addError('nama', 'Kelas dengan nama dan mata pelajaran ini sudah ada pada periode tersebut.');

            return;
        }

        DB::table('classes')->insert([
            'school_id'       => $admin->school_id,
            'guru_id'         => (int) $data['guru_id'],
            'wali_kelas_id'   => (int) $data['wali_kelas_id'],
            'nama'            => $data['nama'],
            'mata_pelajaran'  => $data['mata_pelajaran'],
            'tahun_ajaran'    => $data['tahun_ajaran'],
            'semester'        => $data['semester'],
            'kapasitas'       => (int) $data['kapasitas'],
            'kode_undangan'   => $this->kodeUnik(),
            'perlu_persetujuan' => false,
            'status'          => 'aktif',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        session()->flash('status', 'Kelas "'.$data['nama'].'" berhasil ditambahkan.');

        return redirect()->route('admin.classes.index');
    }

    private function kodeUnik(): string
    {
        do {
            $kode = strtoupper(Str::random(8));
        } while (DB::table('classes')->where('kode_undangan', $kode)->exists());

        return $kode;
    }

    #[Layout('components.adminsekolah-layout')]
    public function render()
    {
        $admin = $this->admin();

        return view('AdminSekolah.class-form', [
            'guruList'  => $this->daftarGuru($admin->school_id),
            'tahunList' => $this->opsiTahun(),
        ]);
    }
}