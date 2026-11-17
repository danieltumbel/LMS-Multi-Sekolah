<?php

namespace App\Livewire\Siswa;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Detail satu mata pelajaran (kelas) yang diikuti siswa.
 */
class MataPelajaranDetail extends Component
{
    #[Locked]
    public int $classId = 0;

    #[Url(as: 'tab')]
    public string $tab = 'ringkasan';

    public function mount(int $kelas): void
    {
        $this->classId = $kelas;
        $this->kelasSiswa(); // 404 jika siswa tidak terdaftar di kelas ini
    }

    public function updatedTab(): void
    {
        if (! in_array($this->tab, ['ringkasan', 'tugas', 'materi', 'ujian', 'nilai'], true)) {
            $this->tab = 'ringkasan';
        }
    }

    private function kelasSiswa(): object
    {
        $u = request()->user();
        abort_unless($u && $u->role === 'student', 403);

        $kolom = ['c.id', 'c.nama', 'c.mata_pelajaran', 'c.deskripsi',
                  'g.name as guru', 'g.email as guru_email', 'g.nomor_induk as guru_nip'];
        foreach (['tahun_ajaran', 'semester'] as $k) {
            if (Schema::hasColumn('classes', $k)) {
                $kolom[] = 'c.'.$k;
            }
        }

        $k = DB::table('classes as c')
            ->join('enrollments as e', function ($j) use ($u) {
                $j->on('e.class_id', '=', 'c.id')->where('e.user_id', $u->id)->where('e.status', 'aktif');
            })
            ->leftJoin('users as g', 'g.id', '=', 'c.guru_id')
            ->where('c.id', $this->classId)
            ->where('c.status', 'aktif')
            ->first($kolom);

        abort_if(! $k, 404);

        return $k;
    }

    #[Layout('components.siswa-layout')]
    public function render()
    {
        $u = request()->user();
        $k = $this->kelasSiswa();

        $total = DB::table('assignments')->where('class_id', $k->id)->count();
        $kumpul = DB::table('submissions as s')
            ->join('assignments as a', 'a.id', '=', 's.assignment_id')
            ->where('s.user_id', $u->id)->where('a.class_id', $k->id)
            ->distinct()->count('s.assignment_id');

        $persen = $total > 0 ? min(100, (int) round($kumpul / $total * 100)) : 0;

        $tugas = collect();
        $nilai = collect();
        $rata  = null;

        if (in_array($this->tab, ['tugas', 'nilai'], true)) {
            $semua = DB::table('assignments as a')
                ->leftJoin('submissions as s', function ($j) use ($u) {
                    $j->on('s.assignment_id', '=', 'a.id')->where('s.user_id', $u->id);
                })
                ->where('a.class_id', $k->id)
                ->orderBy('a.deadline')
                ->get(['a.id', 'a.judul', 'a.deadline', 'a.nilai_maks', 's.id as sub_id', 's.nilai']);

            if ($this->tab === 'tugas') {
                $tugas = $semua;
            } else {
                $nilai = $semua->filter(fn ($r) => $r->nilai !== null)->values();
                $persenNilai = $nilai->filter(fn ($r) => $r->nilai_maks > 0)
                    ->map(fn ($r) => $r->nilai / $r->nilai_maks * 100);
                $rata = $persenNilai->isNotEmpty() ? (int) round($persenNilai->avg()) : null;
            }
        }

        return view('livewire.siswa.mata-pelajaran-detail', [
            'k'      => $k,
            'persen' => $persen,
            'total'  => $total,
            'kumpul' => $kumpul,
            'tugas'  => $tugas,
            'nilai'  => $nilai,
            'rata'   => $rata,
        ]);
    }
}