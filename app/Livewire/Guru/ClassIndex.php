<?php

namespace App\Livewire\Guru;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Daftar kelas milik guru yang sedang login + pembuatan kelas baru.
 */
class ClassIndex extends Component
{
    use WithPagination;

    public string $q = '';

    // Form "Buat Kelas"
    public bool $showForm = false;
    public string $nama = '';
    public string $mata_pelajaran = '';
    public string $deskripsi = '';
    public bool $perlu_persetujuan = false;

    // Detail (tombol "Lihat")
    public ?int $detailId = null;

    public ?string $pesan = null;

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    // Livewire tidak selalu menjalankan ulang middleware route, jadi akses dicek lagi di sini
    private function guru()
    {
        $u = request()->user();
        abort_unless($u && $u->role === 'teacher' && $u->school_id, 403);

        return $u;
    }

    public function buka(): void
    {
        $this->guru();
        $this->resetValidation();
        $this->reset('nama', 'mata_pelajaran', 'deskripsi', 'perlu_persetujuan');
        $this->pesan = null;
        $this->showForm = true;
    }

    public function tutupForm(): void
    {
        $this->showForm = false;
    }

    public function simpan(): void
    {
        $guru = $this->guru();

        $data = $this->validate([
            'nama'              => ['required', 'string', 'max:255'],
            'mata_pelajaran'    => ['required', 'string', 'max:255'],
            'deskripsi'         => ['nullable', 'string', 'max:2000'],
            'perlu_persetujuan' => ['boolean'],
        ], [
            'nama.required'           => 'Nama kelas wajib diisi.',
            'mata_pelajaran.required' => 'Mata pelajaran wajib diisi.',
        ]);

        // Batas jumlah kelas per sekolah (diatur Super Admin)
        $batas = (int) DB::table('schools')->where('id', $guru->school_id)->value('max_classes');
        $aktif = DB::table('classes')->where('school_id', $guru->school_id)->where('status', 'aktif')->count();
        if ($batas > 0 && $aktif >= $batas) {
            $this->addError('nama', 'Batas jumlah kelas sekolah ('.$batas.') sudah tercapai. Hubungi admin sekolah.');

            return;
        }

        $dobel = DB::table('classes')
            ->where('school_id', $guru->school_id)->where('guru_id', $guru->id)
            ->where('nama', $data['nama'])->where('mata_pelajaran', $data['mata_pelajaran'])->exists();
        if ($dobel) {
            $this->addError('nama', 'Kamu sudah punya kelas dengan nama dan mata pelajaran yang sama.');

            return;
        }

        $mulai = now()->month >= 7 ? now()->year : now()->year - 1;

        $baris = [
            'school_id'         => $guru->school_id,
            'guru_id'           => $guru->id,
            'nama'              => $data['nama'],
            'mata_pelajaran'    => $data['mata_pelajaran'],
            'deskripsi'         => $data['deskripsi'] !== '' ? $data['deskripsi'] : null,
            'kode_undangan'     => $this->kodeUnik(),
            'perlu_persetujuan' => $data['perlu_persetujuan'],
            'status'            => 'aktif',
            'tahun_ajaran'      => $mulai.'/'.($mulai + 1),
            'semester'          => now()->month >= 7 ? 'Ganjil' : 'Genap',
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
        // Kolom tahun ajaran / semester hanya ada kalau migration rombel sudah dijalankan
        $baris = array_filter($baris, fn ($k) => Schema::hasColumn('classes', $k), ARRAY_FILTER_USE_KEY);

        DB::table('classes')->insert($baris);

        $this->showForm = false;
        $this->reset('nama', 'mata_pelajaran', 'deskripsi', 'perlu_persetujuan');
        $this->resetPage();
        $this->pesan = 'Kelas "'.$data['nama'].'" berhasil dibuat.';
    }

    public function toggle(int $id): void
    {
        $guru = $this->guru();

        $k = DB::table('classes')
            ->where('id', $id)->where('guru_id', $guru->id)->where('school_id', $guru->school_id)->first();
        if (! $k) {
            return;
        }

        $baru = $k->status === 'aktif' ? 'nonaktif' : 'aktif';
        DB::table('classes')->where('id', $id)->update(['status' => $baru, 'updated_at' => now()]);

        $this->pesan = 'Kelas "'.$k->nama.'" '.($baru === 'aktif' ? 'diaktifkan.' : 'dinonaktifkan.');
    }

    public function lihat(int $id): void
    {
        $this->detailId = $id;
    }

    public function tutupDetail(): void
    {
        $this->detailId = null;
    }

    private function kodeUnik(): string
    {
        do {
            $kode = strtoupper(Str::random(8));
        } while (DB::table('classes')->where('kode_undangan', $kode)->exists());

        return $kode;
    }

    private function dasar(int $guruId, int $schoolId)
    {
        return DB::table('classes as c')
            ->where('c.guru_id', $guruId)
            ->where('c.school_id', $schoolId)
            ->select('c.id', 'c.nama', 'c.mata_pelajaran', 'c.status', 'c.kode_undangan', 'c.deskripsi', 'c.perlu_persetujuan')
            ->selectSub(
                DB::table('enrollments as e')->selectRaw('COUNT(*)')
                    ->whereColumn('e.class_id', 'c.id')->where('e.status', 'aktif'),
                'jml_siswa'
            );
    }

    #[Layout('components.guru-layout')]
    public function render()
    {
        $guru = $this->guru();
        $q = trim($this->q);

        $kelas = $this->dasar($guru->id, $guru->school_id)
            ->when($q !== '', function ($b) use ($q) {
                $b->where(function ($w) use ($q) {
                    $w->where('c.nama', 'like', '%'.$q.'%')->orWhere('c.mata_pelajaran', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('c.nama')
            ->paginate(5);

        $detail = $this->detailId
            ? $this->dasar($guru->id, $guru->school_id)->where('c.id', $this->detailId)->first()
            : null;

        return view('livewire.guru.classes', compact('kelas', 'detail'));
    }
}