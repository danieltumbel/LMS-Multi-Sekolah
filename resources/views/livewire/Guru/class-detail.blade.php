@php
    $tabs = ['overview' => 'Overview', 'siswa' => 'Siswa', 'materi' => 'Materi', 'tugas' => 'Tugas', 'kuis' => 'Kuis', 'presensi' => 'Presensi'];
@endphp

{{-- Komponen Livewire: satu elemen root --}}
<div>
<style>
.cd-info{display:flex;gap:16px;flex-wrap:wrap;align-items:center;margin:-6px 0 14px;font-size:12px;color:#41506b}
.cd-info code{background:#eaf0fb;border-radius:6px;padding:2px 8px;font-size:12px;user-select:all}
.cd-tabs{display:flex;gap:6px;border-bottom:1px solid var(--line);margin-bottom:16px;overflow-x:auto}
.cd-tab{border:0;background:none;font:inherit;font-size:12.5px;font-weight:600;color:#41506b;padding:10px 20px;border-radius:9px 9px 0 0;cursor:pointer;white-space:nowrap;border-bottom:2px solid transparent}
.cd-tab:hover{background:#f0f4fa}
.cd-tab.on{color:#1f6bff;background:#eaf1ff;border-bottom-color:#1f6bff}
.cd-tab:focus-visible{outline:2px solid #1f6bff;outline-offset:-2px}
.cd-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:16px}
.cd-c{background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px;box-shadow:0 2px 8px rgba(20,35,63,.05)}
.cd-c .ic{width:34px;height:34px;border-radius:9px;display:grid;place-items:center;margin-bottom:10px}
.cd-c .ic .i{width:17px;height:17px}
.cd-c .lb{font-size:11.5px;color:#41506b}
.cd-c .vl{font-size:22px;font-weight:700;margin-top:2px}
.cd-c .na{font-size:10px;color:var(--muted);font-weight:400;display:block;margin-top:2px}
.cd-c .bar{margin-top:8px}
.cd-c .bar.b i{background:#2f6fe4}
.dtb{width:100%;border-collapse:collapse;font-size:12px}
.dtb th{background:#f0f4fa;text-align:left;font-weight:600;color:#41506b;padding:11px 14px;font-size:11.5px}
.dtb td{padding:13px 14px;border-bottom:1px solid var(--line)}
.dtb tr:last-child td{border-bottom:0}
.dtb td:last-child{text-align:right;width:60px}
.kh{display:inline-block;padding:3px 12px;border-radius:999px;background:#eef1f5;color:#5b6a80;font-size:11px;font-weight:600}
.cd-ov{overflow-x:auto;margin:0 -16px -16px}
.cd-ke{background:#e7f7ee;border:1px solid #9edbb8;color:#14633b;border-radius:12px;padding:12px 16px;margin-bottom:14px;font-size:12.5px}
.cd-kb{position:relative;display:inline-block}
.cd-kb summary{list-style:none;cursor:pointer;width:28px;height:28px;display:grid;place-items:center;border-radius:6px;color:#41506b;font-size:16px}
.cd-kb summary::-webkit-details-marker{display:none}
.cd-kb summary:hover{background:#eef3fb}
.cd-kb .dd{right:0;top:calc(100% + 4px);min-width:170px;text-align:left}
.cd-pg{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;padding:14px 0 0}
.cd-pl{display:flex;gap:6px}
.cd-pb{min-width:30px;height:30px;display:grid;place-items:center;border:1px solid var(--line);background:#fff;border-radius:7px;font:inherit;font-size:12px;color:#41506b;cursor:pointer;padding:0}
.cd-pb.on{background:#2f6fe4;border-color:#2f6fe4;color:#fff}
.cd-pb:disabled{opacity:.4;cursor:default}
.cd-em{text-align:center;color:var(--muted);padding:34px 10px;font-size:12.5px}
@media (max-width:1200px){.cd-grid{grid-template-columns:repeat(3,1fr)}}
@media (max-width:640px){.cd-grid{grid-template-columns:repeat(2,1fr)}}
</style>

    <div class="cd-info">
        <span>{{ $kelas->mata_pelajaran }}</span>
        <span>Kode undangan: <code>{{ $kelas->kode_undangan }}</code></span>
        @if ($kelas->status !== 'aktif')<span class="kh">Nonaktif</span>@endif
    </div>

    <div class="cd-tabs" role="tablist" aria-label="Menu kelas">
        @foreach ($tabs as $kode => $label)
            <button type="button" role="tab" aria-selected="{{ $tab === $kode ? 'true' : 'false' }}"
                    class="cd-tab {{ $tab === $kode ? 'on' : '' }}" wire:click="$set('tab', '{{ $kode }}')">{{ $label }}</button>
        @endforeach
    </div>

    @if ($pesan)
        <div class="cd-ke" role="status">{{ $pesan }}</div>
    @endif

    {{-- ================= OVERVIEW ================= --}}
    @if ($tab === 'overview')
        <section class="cd-grid">
            <div class="cd-c"><span class="ic blue"><svg class="i"><use href="#i-users"/></svg></span><div class="lb">Jumlah Siswa</div><div class="vl">{{ $jmlSiswa }}</div></div>
            <div class="cd-c"><span class="ic purple"><svg class="i"><use href="#i-folder"/></svg></span><div class="lb">Materi</div><div class="vl">–</div><small class="na">Belum tersedia</small></div>
            <div class="cd-c"><span class="ic orange"><svg class="i"><use href="#i-task"/></svg></span><div class="lb">Tugas</div><div class="vl">{{ $jmlTugas }}</div></div>
            <div class="cd-c"><span class="ic purple"><svg class="i"><use href="#i-exam"/></svg></span><div class="lb">Kuis</div><div class="vl">–</div><small class="na">Belum tersedia</small></div>
            <div class="cd-c"><span class="ic green"><svg class="i"><use href="#i-circlecheck"/></svg></span><div class="lb">Kehadiran</div><div class="vl">–</div><div class="bar g"><i style="width:0"></i></div><small class="na">Belum ada data presensi</small></div>
            <div class="cd-c"><span class="ic blue"><svg class="i"><use href="#i-chart"/></svg></span><div class="lb">Rata-rata Nilai</div><div class="vl">{{ $rata ?? '–' }}</div><div class="bar b"><i style="width:{{ $rata ?? 0 }}%"></i></div></div>
        </section>

        <section class="card panel">
            <div class="hd">
                <h3>Daftar Siswa <small style="font-weight:400;color:var(--muted)">(5 Terbaru)</small></h3>
                <a href="#" class="link" wire:click.prevent="$set('tab', 'siswa')">Lihat semua</a>
            </div>
            @if ($terbaru->isEmpty())
                <div class="cd-em">Belum ada siswa di kelas ini. Bagikan kode undangan <b>{{ $kelas->kode_undangan }}</b> kepada siswa.</div>
            @else
                <div class="cd-ov">
                    <table class="dtb">
                        <thead><tr><th>No</th><th>Nama Siswa</th><th>NIS</th><th>Kehadiran</th><th></th></tr></thead>
                        <tbody>
                            @foreach ($terbaru as $s)
                                <tr wire:key="ov-{{ $s->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $s->name }}</td>
                                    <td>{{ $s->nomor_induk ?: '–' }}</td>
                                    <td><span class="kh">–</span></td>
                                    <td>
                                        <details class="cd-kb">
                                            <summary aria-label="Menu aksi {{ $s->name }}">⋯</summary>
                                            <div class="dd">
                                                <button type="button" wire:click="keluarkan({{ $s->id }})" wire:confirm="Keluarkan {{ $s->name }} dari kelas ini?">Keluarkan dari kelas</button>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    {{-- ================= SISWA ================= --}}
    @elseif ($tab === 'siswa')
        <section class="card panel">
            <div class="hd"><h3>Daftar Siswa</h3></div>
            @if ($siswa->isEmpty())
                <div class="cd-em">Belum ada siswa di kelas ini. Bagikan kode undangan <b>{{ $kelas->kode_undangan }}</b> kepada siswa.</div>
            @else
                <div class="cd-ov">
                    <table class="dtb">
                        <thead><tr><th>No</th><th>Nama Siswa</th><th>NIS</th><th>Kehadiran</th><th></th></tr></thead>
                        <tbody>
                            @foreach ($siswa as $s)
                                <tr wire:key="sw-{{ $s->id }}">
                                    <td>{{ $siswa->firstItem() + $loop->index }}</td>
                                    <td>{{ $s->name }}</td>
                                    <td>{{ $s->nomor_induk ?: '–' }}</td>
                                    <td><span class="kh">–</span></td>
                                    <td>
                                        <details class="cd-kb">
                                            <summary aria-label="Menu aksi {{ $s->name }}">⋯</summary>
                                            <div class="dd">
                                                <button type="button" wire:click="keluarkan({{ $s->id }})" wire:confirm="Keluarkan {{ $s->name }} dari kelas ini?">Keluarkan dari kelas</button>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="cd-pg">
                    <div class="cd-pl">
                        <button type="button" class="cd-pb" wire:click="previousPage('ps')" aria-label="Sebelumnya" {{ $siswa->onFirstPage() ? 'disabled' : '' }}>‹</button>
                        @for ($p = 1; $p <= $siswa->lastPage(); $p++)
                            <button type="button" class="cd-pb {{ $p === $siswa->currentPage() ? 'on' : '' }}" wire:click="gotoPage({{ $p }}, 'ps')">{{ $p }}</button>
                        @endfor
                        <button type="button" class="cd-pb" wire:click="nextPage('ps')" aria-label="Berikutnya" {{ $siswa->hasMorePages() ? '' : 'disabled' }}>›</button>
                    </div>
                    <span style="font-size:11.5px;color:var(--muted)">Menampilkan {{ $siswa->firstItem() }}-{{ $siswa->lastItem() }} dari {{ $siswa->total() }} siswa</span>
                </div>
            @endif
        </section>

    {{-- ================= TUGAS ================= --}}
    @elseif ($tab === 'tugas')
        <section class="card panel">
            <div class="hd"><h3>Tugas Kelas</h3></div>
            @if ($tugas->isEmpty())
                <div class="cd-em">Belum ada tugas di kelas ini.</div>
            @else
                <div class="cd-ov">
                    <table class="dtb">
                        <thead><tr><th>Judul Tugas</th><th>Deadline</th><th>Terkumpul</th><th>Belum Dinilai</th></tr></thead>
                        <tbody>
                            @foreach ($tugas as $t)
                                <tr wire:key="tg-{{ $t->id }}">
                                    <td>{{ $t->judul }}</td>
                                    <td>{{ \Carbon\Carbon::parse($t->deadline)->locale('id')->translatedFormat('j M Y, H:i') }}</td>
                                    <td>{{ $t->terkumpul }} / {{ $jmlSiswa }}</td>
                                    <td style="text-align:left;{{ $t->belum_dinilai > 0 ? 'color:#dc3b3b;font-weight:600' : '' }}">{{ $t->belum_dinilai }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    {{-- ================= MATERI / KUIS / PRESENSI ================= --}}
    @else
        <section class="card panel">
            <div class="cd-em">Fitur {{ strtolower($tabs[$tab] ?? $tab) }} belum tersedia.</div>
        </section>
    @endif
</div>