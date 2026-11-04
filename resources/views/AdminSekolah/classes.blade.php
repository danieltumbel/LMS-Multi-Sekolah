@php
    $cur  = $kelas->currentPage();
    $last = $kelas->lastPage();
    $from = max(1, $cur - 1);
    $to   = min($last, $from + 2);
    $from = max(1, $to - 2);
@endphp

{{-- Komponen Livewire: satu elemen root --}}
<div>
<style>
.tl{display:flex;gap:12px;align-items:center;margin-bottom:14px;flex-wrap:wrap}
.sb3{flex:1;min-width:220px;position:relative}
.sb3 input{width:100%;height:40px;border:1px solid var(--line);background:#fff;border-radius:10px;padding:0 14px 0 38px;font:inherit;font-size:12.5px;color:var(--ink)}
.sb3 input:focus-visible{outline:2px solid #2f6fe4}
.sb3 svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#8a99b0;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round}
.bt5{background:#1f6bff;color:#fff;font-weight:600;font-size:12.5px;padding:10px 20px;border-radius:9px;text-decoration:none;white-space:nowrap;box-shadow:0 3px 8px rgba(31,107,255,.3)}
.bt5:focus-visible,.pb:focus-visible{outline:2px solid #1f6bff;outline-offset:2px}
.tcd{padding:0;overflow:visible}
.twr{overflow-x:auto}
.tg3{width:100%;border-collapse:collapse;font-size:12px;min-width:760px}
.tg3 th{background:#f0f4fa;text-align:left;font-weight:600;color:#41506b;padding:12px 14px;font-size:11.5px}
.tg3 td{padding:14px;border-bottom:1px solid var(--line)}
.tg3 tr:last-child td{border-bottom:0}
.tg3 .nm2{font-weight:600}
.tg3 .nm2 small{display:block;font-weight:400;color:var(--muted);font-size:10.5px}
.b-nonaktif{background:#eef1f5;border-color:#cfd7e3;color:#5b6a80}
.em3{text-align:center;color:var(--muted);padding:34px}
.pgr{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;padding:14px}
.pgl{display:flex;gap:6px}
.pb{min-width:30px;height:30px;display:grid;place-items:center;border:1px solid var(--line);background:#fff;border-radius:7px;font:inherit;font-size:12px;color:#41506b;cursor:pointer;padding:0}
.pb.on{background:#2f6fe4;border-color:#2f6fe4;color:#fff}
.pb:disabled{opacity:.4;cursor:default}
.inf{font-size:11.5px;color:var(--muted)}
.fls{background:#e7f7ee;border:1px solid #9edbb8;color:#14633b;border-radius:12px;padding:12px 16px;margin-bottom:14px;font-size:12.5px}
</style>

    @if (session('status'))
        <div class="fls" role="status">{{ session('status') }}</div>
    @endif

    <div class="tl">
        <label class="sb3">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.500-4.500"/></svg>
            <input type="search" wire:model.live.debounce.300ms="q" placeholder="Cari nama kelas, mata pelajaran, atau guru..." aria-label="Cari kelas">
        </label>
        <a href="{{ route('admin.classes.create') }}" class="bt5">+ Tambah Kelas</a>
    </div>

    <section class="card tcd">
        <div class="twr">
            <table class="tg3">
                <thead>
                    <tr><th>No</th><th>Nama Kelas</th><th>Periode</th><th>Wali Kelas</th><th>Guru Pengampu</th><th>Siswa</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse ($kelas as $k)
                        <tr wire:key="k-{{ $k->id }}">
                            <td>{{ $kelas->firstItem() + $loop->index }}</td>
                            <td class="nm2">{{ $k->nama }}<small>{{ $k->mata_pelajaran }}</small></td>
                            <td>{{ $k->tahun_ajaran ? $k->tahun_ajaran.' · '.$k->semester : '–' }}</td>
                            <td>{{ $k->wali ?: '–' }}</td>
                            <td>{{ $k->guru ?: '–' }}</td>
                            <td>{{ $k->jml_siswa }}{{ $k->kapasitas ? ' / '.$k->kapasitas : '' }}</td>
                            <td><span class="badge {{ $k->status === 'aktif' ? 'b-aktif' : 'b-nonaktif' }}">{{ $k->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="em3">
                            {{ trim($q) !== '' ? 'Tidak ada hasil untuk "'.$q.'".' : 'Belum ada kelas. Klik "+ Tambah Kelas" untuk membuat yang pertama.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pgr">
            <div class="pgl">
                <button type="button" class="pb" wire:click="previousPage" aria-label="Sebelumnya" {{ $kelas->onFirstPage() ? 'disabled' : '' }}>‹</button>
                @for ($p = $from; $p <= $to; $p++)
                    <button type="button" class="pb {{ $p === $cur ? 'on' : '' }}" wire:click="gotoPage({{ $p }})">{{ $p }}</button>
                @endfor
                <button type="button" class="pb" wire:click="nextPage" aria-label="Berikutnya" {{ $kelas->hasMorePages() ? '' : 'disabled' }}>›</button>
            </div>
            <span class="inf">
                @if ($kelas->total())
                    Menampilkan {{ $kelas->firstItem() }}-{{ $kelas->lastItem() }} dari {{ $kelas->total() }} data
                @else
                    Tidak ada data
                @endif
            </span>
        </div>
    </section>
</div>