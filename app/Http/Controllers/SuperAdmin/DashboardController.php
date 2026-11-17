<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\School;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $fmt = fn ($n) => number_format($n, 0, ',', '.');

        // ---- Kartu statistik ----
        $totalSekolah = School::count();
        $aktif        = School::where('status', 'active')->count();
        $suspended    = School::where('status', 'suspended')->count();
        $nonaktif     = School::where('status', 'inactive')->count();
        $guru         = User::where('role', 'teacher')->count();
        $siswa        = User::where('role', 'student')->count();
        $totalKelas   = Kelas::withoutGlobalScopes()->count();

        $stats = [
            ['label' => 'Total Sekolah',  'value' => $fmt($totalSekolah), 'tone' => 'blue',   'icon' => 'school',
             'note' => "$aktif Aktif | $suspended Ditangguhkan | $nonaktif Nonaktif"],
            ['label' => 'Sekolah Aktif',  'value' => $fmt($aktif),        'tone' => 'green',  'icon' => 'check',
             'note' => ($totalSekolah ? round($aktif / $totalSekolah * 100) : 0) . '% dari total'],
            ['label' => 'Total Pengguna', 'value' => $fmt(User::count()), 'tone' => 'orange', 'icon' => 'users',
             'note' => 'Guru ' . $fmt($guru) . ' | Siswa ' . $fmt($siswa)],
            ['label' => 'Total Kelas',    'value' => $fmt($totalKelas),   'tone' => 'purple', 'icon' => 'class',
             'note' => "dari $totalSekolah sekolah"],
        ];

        // ---- Grafik: jumlah kumulatif 9 bulan terakhir ----
        $chart = ['labels' => [], 'pengguna' => [], 'kelas' => [], 'sekolah' => []];
        for ($i = 8; $i >= 0; $i--) {
            $end = now()->subMonths($i)->endOfMonth();
            $chart['labels'][]   = $end->copy()->locale('id')->isoFormat('MMM');
            $chart['pengguna'][] = User::where('created_at', '<=', $end)->count();
            $chart['kelas'][]    = Kelas::withoutGlobalScopes()->where('created_at', '<=', $end)->count();
            $chart['sekolah'][]  = School::where('created_at', '<=', $end)->count();
        }

        // ---- Sekolah terbaru ----
        $label = ['active' => 'Aktif', 'suspended' => 'Ditangguhkan', 'inactive' => 'Nonaktif'];
        $badge = ['active' => 'aktif', 'suspended' => 'tangguh',      'inactive' => 'nonaktif'];

        $schools = School::withCount('users')->latest()->take(5)->get()->map(fn ($s) => [
            'nama'     => $s->name,
            'status'   => $label[$s->status] ?? ucfirst($s->status),
            'badge'    => $badge[$s->status] ?? 'nonaktif',
            'pengguna' => $s->users_count,
            'tgl'      => $s->created_at->locale('id')->translatedFormat('d M Y'),
        ]);

        // ---- Aktivitas terbaru (diturunkan dari data sekolah) ----
        // Belum ada tabel audit log, jadi aktivitas dibaca dari perubahan data sekolah.
        $activities = School::latest('updated_at')->take(5)->get()->map(function ($s) {
            if ($s->status === 'suspended') {
                $text = "Status tenant {$s->name} diubah menjadi Ditangguhkan"; $tone = 'red';
            } elseif ($s->updated_at->gt($s->created_at->copy()->addMinute())) {
                $text = "Data/kuota sekolah {$s->name} diperbarui"; $tone = 'blue';
            } else {
                $text = "Sekolah {$s->name} baru didaftarkan"; $tone = 'orange';
            }
            return ['text' => $text, 'tone' => $tone,
                    'time' => $s->updated_at->locale('id')->translatedFormat('d M Y H:i')];
        });

        return view('SuperAdmin.dashboard', compact('stats', 'chart', 'schools', 'activities'));
    }

    public function ringkasan()
{
    $perStatus = DB::table('schools')->select('status', DB::raw('count(*) as total'))
        ->groupBy('status')->pluck('total', 'status');
    $perPeran = DB::table('users')->select('role', DB::raw('count(*) as total'))
        ->groupBy('role')->pluck('total', 'role');

    $aktivitas = DB::table('audit_logs as l')
        ->leftJoin('schools as s', 's.id', '=', 'l.school_id')
        ->orderByDesc('l.id')->limit(5)
        ->get(['l.action', 'l.description', 'l.created_at', 's.name as sekolah']);

    return response()->json([
        'sekolah' => [
            'total'        => (int) $perStatus->sum(),
            'aktif'        => (int) ($perStatus['active'] ?? 0),
            'ditangguhkan' => (int) ($perStatus['suspended'] ?? 0),
            'nonaktif'     => (int) ($perStatus['inactive'] ?? 0),
        ],
        'pengguna' => [
            'total'         => (int) $perPeran->sum(),
            'admin_sekolah' => (int) ($perPeran['school_admin'] ?? 0),
            'guru'          => (int) ($perPeran['teacher'] ?? 0),
            'siswa'         => (int) ($perPeran['student'] ?? 0),
        ],
        'kelas' => DB::table('classes')->count(),
        'penggunaan' => [
            'pengguna_baru_30_hari' => DB::table('users')->where('created_at', '>=', now()->subDays(30))->count(),
            'tugas_dikumpul_7_hari' => DB::table('submissions')->where('submitted_at', '>=', now()->subDays(7))->count(),
            'tenant_hampir_penuh'   => DB::table('schools')
                ->whereRaw('(select count(*) from users where users.school_id = schools.id) >= schools.max_users * 0.9')
                ->count(),
        ],
        'aktivitas_terbaru' => $aktivitas,
    ]);
}
}
