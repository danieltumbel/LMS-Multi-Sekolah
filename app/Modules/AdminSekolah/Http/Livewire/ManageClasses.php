<?php

namespace App\Modules\AdminSekolah\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Daftar Kelas / Rombel sekolah.
 */
class ManageClasses extends Component
{
    use WithPagination;

    public string $q = '';

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    #[Layout('components.adminsekolah-layout')]
    public function render()
    {
        $u = request()->user();
        abort_unless($u && $u->role === 'school_admin' && $u->school_id, 403);

        $q = trim($this->q);

        $kelas = DB::table('classes as c')
            ->leftJoin('users as g', 'g.id', '=', 'c.guru_id')
            ->leftJoin('users as w', 'w.id', '=', 'c.wali_kelas_id')
            ->where('c.school_id', $u->school_id)
            ->when($q !== '', function ($b) use ($q) {
                $b->where(function ($w) use ($q) {
                    $w->where('c.nama', 'like', '%'.$q.'%')
                      ->orWhere('c.mata_pelajaran', 'like', '%'.$q.'%')
                      ->orWhere('g.name', 'like', '%'.$q.'%');
                });
            })
            ->select('c.id', 'c.nama', 'c.mata_pelajaran', 'c.tahun_ajaran', 'c.semester', 'c.kapasitas', 'c.status',
                     'g.name as guru', 'w.name as wali')
            ->selectSub(
                DB::table('enrollments as e')->selectRaw('COUNT(*)')
                    ->whereColumn('e.class_id', 'c.id')->where('e.status', 'aktif'),
                'jml_siswa'
            )
            ->orderByDesc('c.id')
            ->paginate(8);

        return view('AdminSekolah.classes', compact('kelas'));
    }
}