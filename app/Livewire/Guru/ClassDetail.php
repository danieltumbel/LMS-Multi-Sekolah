<?php

namespace App\Livewire\Guru;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Detail satu kelas milik guru: ringkasan, daftar siswa, dan tugas.
 */
class ClassDetail extends Component
{
    use WithPagination;

    #[Locked]
    public int $classId = 0;

    #[Url(as: 'tab')]
    public string $tab = 'overview';

    public ?string $pesan = null;

    public function mount(int $kelas): void
    {
        $this->classId = $kelas;
        $this->kelasMilikGuru(); // 404 jika bukan kelas milik guru ini
    }

    public function updatedTab(): void
    {
        if (! in_array($this->tab, ['overview', 'siswa', 'materi', 'tugas', 'kuis', 'presensi'], true)) {
            $this->tab = 'overview';
        }
        $this->resetPage('ps');
        $this->pesan = null;
    }

    // Livewire tidak selalu menjalankan ulang middleware route, jadi akses dicek lagi di sini
    private function kelasMilikGuru(): object
    {
        $u = request()->user();
        abort_unless($u && $u->role === 'teacher' && $u->school_id, 403);

        $k = DB::table('classes')
            ->where('id', $this->classId)
            ->where('guru_id', $u->id)
            ->where('school_id', $u->school_id)
            ->first();
        abort_if(! $k, 404);

        return $k;
    }

    public function keluarkan(int $userId): void
    {
        $this->kelasMilikGuru();

        $nama = DB::table('users')->where('id', $userId)->value('name');

        DB::table('enrollments')
            ->where('class_id', $this->classId)->where('user_id', $userId)->where('status', 'aktif')
            ->update(['status' => 'nonaktif', 'updated_at' => now()]);

        $this->pesan = ($nama ?: 'Siswa').' dikeluarkan dari kelas.';
    }

    private function siswaQuery()
    {
        return DB::table('enrollments as e')
            ->join('users as u', 'u.id', '=', 'e.user_id')
            ->where('e.class_id', $this->classId)
            ->where('e.status', 'aktif')
            ->select('u.id', 'u.name', 'u.nomor_induk', 'e.created_at as bergabung');
    }

    #[Layout('components.guru-layout')]
    public function render()
    {
        $kelas = $this->kelasMilikGuru();
        $id    = $this->classId;

        $jmlSiswa = DB::table('enrollments')->where('class_id', $id)->where('status', 'aktif')->count();
        $jmlTugas = DB::table('assignments')->where('class_id', $id)->count();

        $rata = DB::table('submissions as s')
            ->join('assignments as a', 'a.id', '=', 's.assignment_id')
            ->where('a.class_id', $id)
            ->whereNotNull('s.nilai')->where('a.nilai_maks', '>', 0)
            ->selectRaw('AVG(s.nilai / a.nilai_maks * 100) as r')
            ->value('r');

        $data = [
            'kelas'    => $kelas,
            'jmlSiswa' => $jmlSiswa,
            'jmlTugas' => $jmlTugas,
            'rata'     => $rata !== null ? (int) round($rata) : null,
            'terbaru'  => collect(),
            'siswa'    => null,
            'tugas'    => collect(),
        ];

        if ($this->tab === 'overview') {
            $data['terbaru'] = $this->siswaQuery()->orderByDesc('e.id')->limit(5)->get();
        } elseif ($this->tab === 'siswa') {
            $data['siswa'] = $this->siswaQuery()->orderBy('u.name')->paginate(10, ['*'], 'ps');
        } elseif ($this->tab === 'tugas') {
            $data['tugas'] = DB::table('assignments as a')
                ->where('a.class_id', $id)
                ->orderByDesc('a.deadline')
                ->limit(20)
                ->select('a.id', 'a.judul', 'a.deadline')
                ->selectSub(
                    DB::table('submissions as s')->selectRaw('COUNT(*)')->whereColumn('s.assignment_id', 'a.id'),
                    'terkumpul'
                )
                ->selectSub(
                    DB::table('submissions as s')->selectRaw('COUNT(*)')
                        ->whereColumn('s.assignment_id', 'a.id')->whereNull('s.nilai'),
                    'belum_dinilai'
                )
                ->get();
        }

        return view('livewire.guru.class-detail', $data);
    }
}