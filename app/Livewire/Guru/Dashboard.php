<?php

namespace App\Livewire\Guru;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('components.guru-layout')]
    public function render()
    {
        $user = request()->user();
        abort_unless($user && $user->role === 'teacher' && $user->school_id, 403);

        // Kelas aktif yang diampu guru ini
        $kelas = DB::table('classes')
            ->where('guru_id', $user->id)
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'mata_pelajaran']);

        $ids = $kelas->pluck('id')->all();

        $totalSiswa = $ids
            ? DB::table('enrollments')->whereIn('class_id', $ids)->where('status', 'aktif')->distinct()->count('user_id')
            : 0;

        $tugasAktif = $ids
            ? DB::table('assignments')->whereIn('class_id', $ids)->where('deadline', '>=', now())->count()
            : 0;

        $stats = [
            ['label' => 'Total Kelas', 'value' => $kelas->count(), 'note' => null, 'icon' => 'i-folder', 'tone' => 'blue'],
            ['label' => 'Total Siswa', 'value' => $totalSiswa,     'note' => null, 'icon' => 'i-users',  'tone' => 'green'],
            ['label' => 'Tugas Aktif', 'value' => $tugasAktif,     'note' => null, 'icon' => 'i-task',   'tone' => 'orange'],
            // Belum ada tabel kuis di database
            ['label' => 'Kuis Aktif',  'value' => '–', 'note' => 'Fitur kuis belum tersedia', 'icon' => 'i-exam', 'tone' => 'purple'],
        ];

        // ---- Progres kelas ----
        $siswaPer = $ids
            ? DB::table('enrollments')->whereIn('class_id', $ids)->where('status', 'aktif')
                ->groupBy('class_id')->selectRaw('class_id, COUNT(*) as n')->pluck('n', 'class_id')
            : collect();

        $tugasPer = $ids
            ? DB::table('assignments')->whereIn('class_id', $ids)
                ->groupBy('class_id')->selectRaw('class_id, COUNT(*) as n')->pluck('n', 'class_id')
            : collect();

        $kumpulPer = $ids
            ? DB::table('submissions as s')
                ->join('assignments as a', 'a.id', '=', 's.assignment_id')
                ->whereIn('a.class_id', $ids)
                ->groupBy('a.class_id')
                ->selectRaw('a.class_id, COUNT(*) as n, AVG(CASE WHEN s.nilai IS NOT NULL AND a.nilai_maks > 0 THEN s.nilai / a.nilai_maks * 100 END) as rata')
                ->get()->keyBy('class_id')
            : collect();

        $progres = $kelas->map(function ($c) use ($siswaPer, $tugasPer, $kumpulPer) {
            $s = (int) ($siswaPer[$c->id] ?? 0);
            $t = (int) ($tugasPer[$c->id] ?? 0);
            $k = $kumpulPer[$c->id] ?? null;

            return [
                'nama'   => $c->nama,
                'mapel'  => $c->mata_pelajaran,
                'persen' => ($s * $t) > 0 ? min(100, (int) round(($k->n ?? 0) / ($s * $t) * 100)) : null,
                'rata'   => ($k && $k->rata !== null) ? number_format($k->rata, 0) : null,
            ];
        });

        // ---- Tugas terbaru (3) beserta jumlah yang belum dinilai ----
        $tugasBaru = collect();
        if ($ids) {
            $tugasBaru = DB::table('assignments as a')
                ->join('classes as c', 'c.id', '=', 'a.class_id')
                ->whereIn('a.class_id', $ids)
                ->orderByDesc('a.created_at')
                ->limit(3)
                ->select('a.id', 'a.judul', 'a.created_at', 'c.mata_pelajaran')
                ->selectSub(
                    DB::table('submissions as s')->selectRaw('COUNT(*)')
                        ->whereColumn('s.assignment_id', 'a.id')->whereNull('s.nilai'),
                    'belum_dinilai'
                )
                ->get()
                ->map(fn ($t) => [
                    'judul'  => $t->mata_pelajaran.' - '.$t->judul,
                    'belum'  => (int) $t->belum_dinilai,
                    'waktu'  => $this->labelWaktu(Carbon::parse($t->created_at)),
                ]);
        }

        return view('livewire.guru.dashboard', compact('stats', 'progres', 'tugasBaru'));
    }

    private function labelWaktu(Carbon $d): string
    {
        $d = $d->locale('id');

        if ($d->isToday())     return 'Hari ini, '.$d->format('H:i');
        if ($d->isYesterday()) return 'Kemarin, '.$d->format('H:i');

        return $d->translatedFormat('j M Y');
    }
}