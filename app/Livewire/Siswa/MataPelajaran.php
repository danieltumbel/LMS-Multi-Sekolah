<?php

namespace App\Livewire\Siswa;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Daftar mata pelajaran (kelas aktif) yang diikuti siswa, lengkap dengan progres tugas.
 */
class MataPelajaran extends Component
{
    public string $q = '';
    public string $semester = 'semua'; // semua | ganjil | genap

    public function setSemester(string $s): void
    {
        $this->semester = in_array($s, ['semua', 'ganjil', 'genap'], true) ? $s : 'semua';
    }

    #[Layout('components.siswa-layout')]
    public function render()
    {
        $u = request()->user();
        abort_unless($u && $u->role === 'student', 403);

        $adaSemester = Schema::hasColumn('classes', 'semester');
        $q = trim($this->q);

        $baris = DB::table('enrollments as e')
            ->join('classes as c', 'c.id', '=', 'e.class_id')
            ->where('e.user_id', $u->id)
            ->where('e.status', 'aktif')
            ->where('c.status', 'aktif')
            ->when($q !== '', function ($b) use ($q) {
                $b->where(function ($w) use ($q) {
                    $w->where('c.mata_pelajaran', 'like', '%'.$q.'%')->orWhere('c.nama', 'like', '%'.$q.'%');
                });
            })
            ->when($adaSemester && $this->semester !== 'semua', function ($b) {
                $b->where('c.semester', $this->semester === 'ganjil' ? 'Ganjil' : 'Genap');
            })
            ->select('c.id', 'c.nama', 'c.mata_pelajaran')
            ->selectSub(
                DB::table('assignments as a')->selectRaw('COUNT(*)')->whereColumn('a.class_id', 'c.id'),
                'total_tugas'
            )
            ->selectSub(
                DB::table('submissions as s')
                    ->join('assignments as a2', 'a2.id', '=', 's.assignment_id')
                    ->selectRaw('COUNT(DISTINCT s.assignment_id)')
                    ->where('s.user_id', $u->id)
                    ->whereColumn('a2.class_id', 'c.id'),
                'dikumpulkan'
            )
            ->orderBy('c.mata_pelajaran')
            ->get();

        $nada  = ['blue', 'green', 'orange', 'purple', 'teal'];
        $ikon  = ['i-monitor', 'i-folder', 'i-chart', 'i-book', 'i-exam', 'i-task', 'i-school', 'i-class'];

        $kartu = $baris->values()->map(fn ($r, $i) => [
            'id'      => $r->id,
            'mapel'   => $r->mata_pelajaran,
            'kelas'   => $r->nama,
            'persen'  => $r->total_tugas > 0 ? min(100, (int) round($r->dikumpulkan / $r->total_tugas * 100)) : 0,
            'ada'     => $r->total_tugas > 0,
            'nada'    => $nada[$i % count($nada)],
            'ikon'    => $ikon[$i % count($ikon)],
        ]);

        return view('livewire.siswa.mata-pelajaran', [
            'kartu'       => $kartu,
            'adaSemester' => $adaSemester,
        ]);
    }
}