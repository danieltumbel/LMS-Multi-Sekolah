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
.gt{display:flex;gap:14px;align-items:center;margin-bottom:14px;flex-wrap:wrap}
.gs{flex:1;min-width:220px;position:relative}
.gs input{width:100%;height:40px;border:1px solid var(--line);background:#fff;border-radius:10px;padding:0 14px 0 38px;font:inherit;font-size:12.5px;color:var(--ink)}
.gs input:focus-visible{outline:2px solid #2f6fe4}
.gs svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#8a99b0;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round}
.gb{font:inherit;font-size:12.5px;font-weight:600;border-radius:9px;padding:10px 22px;cursor:pointer;white-space:nowrap;border:1px solid transparent;text-decoration:none;display:inline-block}
.gb.pr{background:#1f6bff;color:#fff;box-shadow:0 3px 8px rgba(31,107,255,.3)}
.gb.gh{background:#fff;color:#2a3956;border-color:#d6e2f4}
.gb:focus-visible,.gp:focus-visible,.lh:focus-visible{outline:2px solid #1f6bff;outline-offset:2px}
.gcard{padding:0;overflow:visible}
.gw{overflow-x:auto}
.gtb{width:100%;border-collapse:collapse;font-size:12px;min-width:620px}
.gtb th{background:#f0f4fa;text-align:center;font-weight:600;color:#41506b;padding:13px 14px;font-size:11.5px}
.gtb th:first-child,.gtb td:first-child{text-align:left}
.gtb td{padding:15px 14px;border-bottom:1px solid var(--line);text-align:center}
.gtb tr:last-child td{border-bottom:0}
.gtb td:first-child{font-weight:600}
.gtb .off{opacity:.55}
.gtb .tag{display:inline-block;margin-left:8px;padding:1px 8px;border-radius:6px;background:#eef1f5;color:#5b6a80;font-size:10px;font-weight:600}
.lh{display:inline-block;min-width:70px;padding:8px 18px;border:0;border-radius:8px;background:#2f6fe4;color:#fff;font:inherit;font-size:11.5px;font-weight:600;cursor:pointer}
.lh:hover{background:#2459c4}
.ak3{display:flex;align-items:center;justify-content:center;gap:12px}
.kb3{position:relative}
.kb3 summary{list-style:none;cursor:pointer;width:24px;height:28px;display:grid;place-items:center;border-radius:6px;color:#41506b;font-size:16px}
.kb3 summary::-webkit-details-marker{display:none}
.kb3 summary:hover{background:#eef3fb}
.kb3 .dd{right:0;top:calc(100% + 4px);min-width:150px;text-align:left}
.ge{text-align:center;color:var(--muted);padding:34px}
.gpg{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;padding:14px}
.gpl{display:flex;gap:6px}
.gp{min-width:30px;height:30px;display:grid;place-items:center;border:1px solid var(--line);background:#fff;border-radius:7px;font:inherit;font-size:12px;color:#41506b;cursor:pointer;padding:0}
.gp.on{background:#2f6fe4;border-color:#2f6fe4;color:#fff}
.gp:disabled{opacity:.4;cursor:default}
.gi{font-size:11.5px;color:var(--muted)}
.gf{background:#e7f7ee;border:1px solid #9edbb8;color:#14633b;border-radius:12px;padding:12px 16px;margin-bottom:14px;font-size:12.5px}
.ov2{position:fixed;inset:0;background:rgba(10,25,50,.45);display:grid;place-items:center;z-index:50;padding:16px}
.md{background:#fff;border-radius:14px;padding:22px;width:100%;max-width:440px;box-shadow:0 20px 50px rgba(0,0,0,.3)}
.md h3{margin:0 0 14px;font-size:14px}
.md label.l{display:block;font-size:11.5px;color:#41506b;margin:12px 0 5px}
.md label.l b{color:#e5484d}
.md input[type=text],.md textarea{width:100%;border:1px solid #d6e2f4;border-radius:8px;padding:0 12px;font:inherit;font-size:12px;color:var(--ink)}
.md input[type=text]{height:38px}
.md textarea{padding:10px 12px;min-height:80px;resize:vertical}
.md input:focus-visible,.md textarea:focus-visible{outline:2px solid #2f6fe4;border-color:#2f6fe4}
.md .ck{display:flex;align-items:center;gap:8px;margin-top:14px;font-size:12px;color:var(--ink)}
.md .ck input{accent-color:#1f6bff;width:16px;height:16px}
.er3{display:block;color:#d93b3b;font-size:11px;margin-top:4px}
.ma{display:flex;justify-content:flex-end;gap:10px;margin-top:18px}
.dd3{display:grid;grid-template-columns:130px 1fr;gap:8px 12px;font-size:12.5px}
.dd3 dt{color:var(--muted)}.dd3 dd{margin:0;font-weight:500;word-break:break-word}
.dd3 code{background:#f0f4fa;border-radius:6px;padding:2px 8px;font-size:12px;user-select:all}
</style>

    @if ($pesan)
        <div class="gf" role="status">{{ $pesan }}</div>
    @endif

    <div class="gt">
        <label class="gs">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.500-4.500"/></svg>
            <input type="search" wire:model.live.debounce.300ms="q" placeholder="Cari kelas..." aria-label="Cari kelas">
        </label>
        <button type="button" class="gb pr" wire:click="buka">+ Buat Kelas</button>
    </div>

    <section class="card gcard">
        <div class="gw">
            <table class="gtb">
                <thead>
                    <tr><th>Nama Kelas</th><th>Mata Pelajaran</th><th>Jumlah Siswa</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($kelas as $k)
                        <tr wire:key="k-{{ $k->id }}" class="{{ $k->status === 'aktif' ? '' : 'off' }}">
                            <td>{{ $k->nama }}@if ($k->status !== 'aktif')<span class="tag">Nonaktif</span>@endif</td>
                            <td>{{ $k->mata_pelajaran }}</td>
                            <td>{{ $k->jml_siswa }}</td>
                            <td>
                                <div class="ak3">
                                    <a href="{{ route('guru.classes.show', $k->id) }}" class="lh" style="text-decoration:none">Lihat</a>
                                    <details class="kb3">
                                        <summary aria-label="Menu aksi {{ $k->nama }}">⋮</summary>
                                        <div class="dd">
                                            <button type="button" wire:click="toggle({{ $k->id }})" wire:confirm="{{ $k->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }} kelas {{ $k->nama }}?">{{ $k->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                        </div>
                                    </details>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="ge">
                            {{ trim($q) !== '' ? 'Tidak ada kelas yang cocok dengan "'.$q.'".' : 'Kamu belum punya kelas. Klik "+ Buat Kelas" untuk membuat yang pertama.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="gpg">
            <div class="gpl">
                <button type="button" class="gp" wire:click="previousPage" aria-label="Sebelumnya" {{ $kelas->onFirstPage() ? 'disabled' : '' }}>‹</button>
                @for ($p = $from; $p <= $to; $p++)
                    <button type="button" class="gp {{ $p === $cur ? 'on' : '' }}" wire:click="gotoPage({{ $p }})">{{ $p }}</button>
                @endfor
                <button type="button" class="gp" wire:click="nextPage" aria-label="Berikutnya" {{ $kelas->hasMorePages() ? '' : 'disabled' }}>›</button>
            </div>
            <span class="gi">
                @if ($kelas->total())
                    Menampilkan {{ $kelas->firstItem() }}-{{ $kelas->lastItem() }} dari {{ $kelas->total() }} kelas
                @else
                    Tidak ada data
                @endif
            </span>
        </div>
    </section>

    {{-- Modal buat kelas --}}
    @if ($showForm)
        <div class="ov2" wire:click.self="tutupForm">
            <form class="md" wire:submit="simpan" role="dialog" aria-modal="true" aria-label="Buat kelas">
                <h3>Buat Kelas</h3>

                <label class="l" for="bk-nama">Nama Kelas <b>*</b></label>
                <input id="bk-nama" type="text" wire:model="nama" placeholder="Contoh: X MIPA 1" maxlength="255">
                @error('nama')<small class="er3">{{ $message }}</small>@enderror

                <label class="l" for="bk-mapel">Mata Pelajaran <b>*</b></label>
                <input id="bk-mapel" type="text" wire:model="mata_pelajaran" placeholder="Contoh: Matematika" maxlength="255">
                @error('mata_pelajaran')<small class="er3">{{ $message }}</small>@enderror

                <label class="l" for="bk-desk">Deskripsi</label>
                <textarea id="bk-desk" wire:model="deskripsi" maxlength="2000" placeholder="Opsional"></textarea>
                @error('deskripsi')<small class="er3">{{ $message }}</small>@enderror

                <label class="ck"><input type="checkbox" wire:model="perlu_persetujuan"> Siswa perlu persetujuan guru untuk bergabung</label>

                <div class="ma">
                    <button type="button" class="gb gh" wire:click="tutupForm">Batal</button>
                    <button type="submit" class="gb pr" wire:loading.attr="disabled">Simpan</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Modal detail --}}
    @if ($detail)
        <div class="ov2" wire:click.self="tutupDetail">
            <div class="md" role="dialog" aria-modal="true" aria-label="Detail kelas">
                <h3>Detail Kelas</h3>
                <dl class="dd3">
                    <dt>Nama Kelas</dt><dd>{{ $detail->nama }}</dd>
                    <dt>Mata Pelajaran</dt><dd>{{ $detail->mata_pelajaran }}</dd>
                    <dt>Jumlah Siswa</dt><dd>{{ $detail->jml_siswa }}</dd>
                    <dt>Status</dt><dd>{{ $detail->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</dd>
                    <dt>Kode Undangan</dt><dd><code>{{ $detail->kode_undangan }}</code></dd>
                    <dt>Persetujuan</dt><dd>{{ $detail->perlu_persetujuan ? 'Siswa perlu disetujui guru' : 'Siswa langsung bergabung' }}</dd>
                    <dt>Deskripsi</dt><dd>{{ $detail->deskripsi ?: '–' }}</dd>
                </dl>
                <div class="ma"><button type="button" class="gb gh" wire:click="tutupDetail">Tutup</button></div>
            </div>
        </div>
    @endif
</div>