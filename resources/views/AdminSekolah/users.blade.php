@php
    $cur  = $users->currentPage();
    $last = $users->lastPage();
    $from = max(1, $cur - 1);
    $to   = min($last, $from + 2);
    $from = max(1, $to - 2);

    $noInduk = $guru ? 'NIP' : 'NIS';
    $kol4    = $guru ? 'Mata Pelajaran' : 'Kelas Diikuti';
    $isi4    = fn ($v) => $guru ? ($v ?: '–') : ((int) $v).' kelas';
@endphp

{{-- Komponen Livewire: satu elemen root --}}
<div>
<style>
.tabs2{display:flex;gap:10px;margin-bottom:14px}
.tb2{padding:9px 34px;border-radius:10px;font-size:12.5px;font-weight:600;text-decoration:none;background:#eaf0fb;color:#41506b;border:1px solid transparent}
.tb2.on{background:#1f6bff;color:#fff;box-shadow:0 3px 8px rgba(31,107,255,.3)}
.tools3{display:flex;gap:12px;align-items:center;margin-bottom:14px;flex-wrap:wrap}
.sbox2{flex:1;min-width:220px;position:relative}
.sbox2 input{width:100%;height:40px;border:1px solid var(--line);background:#fff;border-radius:10px;padding:0 14px 0 38px;font:inherit;font-size:12.5px;color:var(--ink)}
.sbox2 input:focus-visible{outline:2px solid #2f6fe4}
.sbox2 svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#8a99b0;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round}
.bt3{font:inherit;font-size:12.5px;font-weight:600;border-radius:9px;padding:10px 18px;cursor:pointer;white-space:nowrap;border:1px solid transparent}
.bt3.pri{background:#1f6bff;color:#fff;box-shadow:0 3px 8px rgba(31,107,255,.3)}
.bt3.out{background:#fff;color:#2f56c8;border-color:#bcd0f5}
.bt3:disabled{opacity:.55;cursor:not-allowed}
.bt3:focus-visible,.pg-b:focus-visible,.lihat2:focus-visible{outline:2px solid #1f6bff;outline-offset:2px}
.tcard2{padding:0;overflow:visible}
.tw2{overflow-x:auto}
.tg2{width:100%;border-collapse:collapse;font-size:12px;min-width:680px}
.tg2 th{background:#f0f4fa;text-align:left;font-weight:600;color:#41506b;padding:12px 14px;font-size:11.5px}
.tg2 td{padding:14px;border-bottom:1px solid var(--line)}
.tg2 tr:last-child td{border-bottom:0}
.b-off{background:#ffe3e3;border-color:#f3b4b4;color:#d93b3b}
.lihat2{display:inline-block;padding:6px 20px;border:1px solid #2f6fe4;color:#2f6fe4;background:#fff;border-radius:8px;font:inherit;font-size:11.5px;font-weight:600;cursor:pointer}
.lihat2:hover{background:#eaf1ff}
.aksi2{display:flex;align-items:center;gap:10px}
.kb2{position:relative}
.kb2 summary{list-style:none;cursor:pointer;width:24px;height:28px;display:grid;place-items:center;border-radius:6px;color:#41506b;font-size:16px}
.kb2 summary::-webkit-details-marker{display:none}
.kb2 summary:hover{background:#eef3fb}
.kb2 .dd{right:0;top:calc(100% + 4px);min-width:150px}
.empty2{text-align:center;color:var(--muted);padding:34px}
.pager2{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;padding:14px}
.pgs2{display:flex;gap:6px}
.pg-b{min-width:30px;height:30px;display:grid;place-items:center;border:1px solid var(--line);background:#fff;border-radius:7px;font:inherit;font-size:12px;color:#41506b;cursor:pointer;padding:0}
.pg-b.on{background:#2f6fe4;border-color:#2f6fe4;color:#fff}
.pg-b:disabled{opacity:.4;cursor:default}
.info2{font-size:11.5px;color:var(--muted)}
.flash2{background:#e7f7ee;border:1px solid #9edbb8;color:#14633b;border-radius:12px;padding:12px 16px;margin-bottom:14px;font-size:12.5px;line-height:1.6}
.flash2 code{background:#fff;border:1px solid #bfe6d0;border-radius:6px;padding:2px 8px;font-size:12px;user-select:all}
.flash2 button{float:right;border:0;background:none;color:#14633b;font-size:16px;cursor:pointer}
.ov{position:fixed;inset:0;background:rgba(10,25,50,.45);display:grid;place-items:center;z-index:50;padding:16px}
.mdl{background:#fff;border-radius:14px;padding:22px;width:100%;max-width:440px;box-shadow:0 20px 50px rgba(0,0,0,.3)}
.mdl h3{margin:0 0 14px;font-size:14px}
.mdl label{display:block;font-size:11.5px;color:#41506b;margin:12px 0 5px}
.mdl label b{color:#e5484d}
.mdl input{width:100%;height:38px;border:1px solid #d6e2f4;border-radius:8px;padding:0 12px;font:inherit;font-size:12px;color:var(--ink)}
.mdl input:focus-visible{outline:2px solid #2f6fe4;border-color:#2f6fe4}
.err2{display:block;color:#d93b3b;font-size:11px;margin-top:4px}
.mact{display:flex;justify-content:flex-end;gap:10px;margin-top:18px}
.dl2{display:grid;grid-template-columns:120px 1fr;gap:8px 12px;font-size:12.5px}
.dl2 dt{color:var(--muted)}.dl2 dd{margin:0;font-weight:500;word-break:break-word}
</style>

    @if ($kredensial)
        <div class="flash2" role="status">
            <button type="button" wire:click="tutupKredensial" aria-label="Tutup">×</button>
            <b>{{ $kredensial['nama'] }} berhasil ditambahkan.</b><br>
            Berikan akun ini kepada yang bersangkutan (password hanya ditampilkan sekali):<br>
            Email: <code>{{ $kredensial['email'] }}</code> &nbsp; Password sementara: <code>{{ $kredensial['password'] }}</code>
        </div>
    @endif

    @if ($pesan)
        <div class="flash2" role="status">{{ $pesan }}</div>
    @endif

    <div class="tabs2">
        <a href="{{ route('admin.teachers.index') }}" class="tb2 {{ $guru ? 'on' : '' }}">Guru</a>
        <a href="{{ route('admin.students.index') }}" class="tb2 {{ $guru ? '' : 'on' }}">Siswa</a>
    </div>

    <div class="tools3">
        <label class="sbox2">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.500-4.500"/></svg>
            <input type="search" wire:model.live.debounce.300ms="q" placeholder="Cari nama, {{ $noInduk }}, atau email..." aria-label="Cari pengguna">
        </label>
        <button type="button" class="bt3 out" disabled title="Fitur import segera hadir">Import CSV/Excel</button>
        <button type="button" class="bt3 pri" wire:click="buka">+ Tambah {{ $guru ? 'Guru' : 'Siswa' }}</button>
    </div>

    <section class="card tcard2">
        <div class="tw2">
            <table class="tg2">
                <thead>
                    <tr><th>No</th><th>Nama {{ $guru ? 'Guru' : 'Siswa' }}</th><th>{{ $noInduk }}</th><th>{{ $kol4 }}</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr wire:key="u-{{ $u->id }}">
                            <td>{{ $users->firstItem() + $loop->index }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->nomor_induk ?: '–' }}</td>
                            <td>{{ $isi4($u->kolom4) }}</td>
                            <td><span class="badge {{ $u->is_active ? 'b-aktif' : 'b-off' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td>
                                <div class="aksi2">
                                    <button type="button" class="lihat2" wire:click="lihat({{ $u->id }})">Lihat</button>
                                    <details class="kb2">
                                        <summary aria-label="Menu aksi {{ $u->name }}">⋮</summary>
                                        <div class="dd">
                                            <button type="button" wire:click="toggle({{ $u->id }})" wire:confirm="{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $u->name }}?">{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                        </div>
                                    </details>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty2">
                            {{ trim($q) !== '' ? 'Tidak ada hasil untuk "'.$q.'".' : 'Belum ada data '.($guru ? 'guru' : 'siswa').'.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pager2">
            <div class="pgs2">
                <button type="button" class="pg-b" wire:click="previousPage" aria-label="Sebelumnya" {{ $users->onFirstPage() ? 'disabled' : '' }}>‹</button>
                @for ($p = $from; $p <= $to; $p++)
                    <button type="button" class="pg-b {{ $p === $cur ? 'on' : '' }}" wire:click="gotoPage({{ $p }})">{{ $p }}</button>
                @endfor
                <button type="button" class="pg-b" wire:click="nextPage" aria-label="Berikutnya" {{ $users->hasMorePages() ? '' : 'disabled' }}>›</button>
            </div>
            <span class="info2">
                @if ($users->total())
                    Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }} data
                @else
                    Tidak ada data
                @endif
            </span>
        </div>
    </section>

    {{-- Modal tambah --}}
    @if ($showForm)
        <div class="ov" wire:click.self="tutupForm">
            <form class="mdl" wire:submit="simpan" role="dialog" aria-modal="true" aria-label="Tambah {{ $guru ? 'guru' : 'siswa' }}">
                <h3>Tambah {{ $guru ? 'Guru' : 'Siswa' }}</h3>

                <label for="f-name">Nama {{ $guru ? 'Guru' : 'Siswa' }} <b>*</b></label>
                <input id="f-name" type="text" wire:model="name" maxlength="255">
                @error('name')<small class="err2">{{ $message }}</small>@enderror

                <label for="f-email">Email <b>*</b></label>
                <input id="f-email" type="email" wire:model="email" maxlength="255">
                @error('email')<small class="err2">{{ $message }}</small>@enderror

                <label for="f-ni">{{ $noInduk }}</label>
                <input id="f-ni" type="text" wire:model="nomor_induk" maxlength="30">
                @error('nomor_induk')<small class="err2">{{ $message }}</small>@enderror

                <div class="mact">
                    <button type="button" class="bt3 out" wire:click="tutupForm">Batal</button>
                    <button type="submit" class="bt3 pri" wire:loading.attr="disabled">Simpan</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Modal detail --}}
    @if ($detail)
        <div class="ov" wire:click.self="tutupDetail">
            <div class="mdl" role="dialog" aria-modal="true" aria-label="Detail {{ $guru ? 'guru' : 'siswa' }}">
                <h3>Detail {{ $guru ? 'Guru' : 'Siswa' }}</h3>
                <dl class="dl2">
                    <dt>Nama</dt><dd>{{ $detail->name }}</dd>
                    <dt>Email</dt><dd>{{ $detail->email }}</dd>
                    <dt>{{ $noInduk }}</dt><dd>{{ $detail->nomor_induk ?: '–' }}</dd>
                    <dt>{{ $kol4 }}</dt><dd>{{ $isi4($detail->kolom4) }}</dd>
                    <dt>Status</dt><dd>{{ $detail->is_active ? 'Aktif' : 'Nonaktif' }}</dd>
                    <dt>Terdaftar</dt><dd>{{ \Carbon\Carbon::parse($detail->created_at)->locale('id')->translatedFormat('j F Y') }}</dd>
                </dl>
                <div class="mact"><button type="button" class="bt3 out" wire:click="tutupDetail">Tutup</button></div>
            </div>
        </div>
    @endif
</div>