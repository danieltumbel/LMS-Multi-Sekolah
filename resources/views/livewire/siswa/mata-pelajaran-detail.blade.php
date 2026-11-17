@php
    $tabs  = ['ringkasan' => 'Ringkasan', 'tugas' => 'Tugas', 'materi' => 'Materi', 'ujian' => 'Ujian', 'nilai' => 'Nilai'];
    $semLabel = ['Ganjil' => 'Semester 1 (Ganjil)', 'Genap' => 'Semester 2 (Genap)'][$k->semester ?? ''] ?? ($k->semester ?? null);
@endphp

{{-- Komponen Livewire: satu elemen root --}}
<div>
<style>
.md-bc{display:flex;gap:8px;align-items:center;font-size:12px;color:#41506b;margin-bottom:14px}
.md-bc a{color:#2f56c8;text-decoration:none}
.md-bc a:hover{text-decoration:underline}
.md-hd{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 8px rgba(20,35,63,.05);padding:18px 22px;display:flex;gap:18px;align-items:center;margin-bottom:14px}
.md-hd .ic{width:56px;height:56px;border-radius:14px;background:#1f8bff;color:#fff;display:grid;place-items:center;flex:none}
.md-hd .ic .i{width:28px;height:28px}
.md-hd .tx{flex:1;min-width:0}
.md-hd h2{margin:0;font-size:17px}
.md-hd .kl{font-size:12px;color:var(--muted);margin:3px 0 12px}
.md-pr{display:flex;align-items:center;gap:12px}
.md-pr .tr2{flex:1;height:7px;border-radius:99px;background:#e6ecf5;overflow:hidden}
.md-pr .tr2 i{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,#1fae7a,#2fc4a0)}
.md-pr b{font-size:12px;color:#41506b}
.md-tabs{display:flex;gap:4px;margin-bottom:14px;overflow-x:auto}
.md-tab{border:0;background:none;font:inherit;font-size:12.5px;font-weight:600;color:#41506b;padding:9px 20px;border-radius:9px;cursor:pointer;white-space:nowrap}
.md-tab:hover{background:#eaf0fb}
.md-tab.on{background:#fff;color:#1f6bff;box-shadow:0 2px 6px rgba(20,35,63,.08)}
.md-tab:focus-visible{outline:2px solid #1f6bff;outline-offset:-2px}
.md-2{display:grid;grid-template-columns:1.6fr 1fr;gap:14px;align-items:start}
.md-c{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 8px rgba(20,35,63,.05);padding:20px 22px}
.md-c h3{margin:0 0 10px;font-size:13.5px;font-weight:700}
.md-c p{margin:0 0 20px;font-size:12.5px;line-height:1.6;color:#41506b}
.md-g{display:flex;align-items:center;gap:14px;border:1px solid var(--line);border-radius:12px;padding:12px 14px}
.md-g .av{width:46px;height:46px;border-radius:50%;background:#e8eef8;display:grid;place-items:center;flex:none}
.md-g .av svg{width:26px;height:26px;color:#1a2f5a;fill:currentColor}
.md-g b{display:block;font-size:12.5px}
.md-g small{font-size:11px;color:var(--muted)}
.md-g a{margin-left:auto;width:34px;height:34px;border-radius:9px;background:#e4eeff;color:#2f56c8;display:grid;place-items:center;text-decoration:none}
.md-g a svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.md-i{display:flex;justify-content:space-between;gap:12px;padding:11px 0;border-top:1px solid var(--line);font-size:12px}
.md-i:first-of-type{border-top:0}
.md-i span{color:var(--muted)}.md-i b{font-weight:500;text-align:right}
.md-bt{display:block;width:100%;margin-top:14px;text-align:center;background:#1f8bff;color:#fff;border:0;font:inherit;font-size:12.5px;font-weight:600;border-radius:10px;padding:13px;cursor:pointer;box-shadow:0 3px 8px rgba(31,139,255,.3)}
.md-bt:focus-visible{outline:2px solid #1f6bff;outline-offset:2px}
.md-t{width:100%;border-collapse:collapse;font-size:12px}
.md-t th{background:#f0f4fa;text-align:left;font-weight:600;color:#41506b;padding:11px 14px;font-size:11.5px}
.md-t td{padding:13px 14px;border-bottom:1px solid var(--line)}
.md-t tr:last-child td{border-bottom:0}
.md-w{overflow-x:auto;margin:0 -22px -20px}
.md-bd{display:inline-block;padding:3px 12px;border-radius:999px;font-size:11px;font-weight:600}
.md-bd.merah{background:#ffe3e3;color:#d93b3b}.md-bd.biru{background:#e4eeff;color:#2f56c8}.md-bd.hijau{background:#dff5e8;color:#1b9a58}
.md-ab{display:inline-block;padding:6px 16px;border-radius:8px;background:#2f6fe4;color:#fff;font-size:11.5px;font-weight:600;text-decoration:none}
.md-ab.abu{background:#eef3fb;color:#41506b}
.md-em{text-align:center;color:var(--muted);padding:34px 10px;font-size:12.5px}
.md-ra{font-size:12.5px;margin-bottom:12px;color:#41506b}.md-ra b{font-size:15px;color:var(--ink)}
@media (max-width:900px){.md-2{grid-template-columns:1fr}}
</style>

    <nav class="md-bc" aria-label="Breadcrumb">
        <a href="{{ route('siswa.mapel.index') }}">‹ Mata Pelajaran</a>
        <span>›</span>
        <span>{{ $k->mata_pelajaran }}</span>
    </nav>

    <section class="md-hd">
        <span class="ic"><svg class="i"><use href="#i-monitor"/></svg></span>
        <div class="tx">
            <h2>{{ $k->mata_pelajaran }}</h2>
            <div class="kl">{{ $k->nama }}</div>
            <div class="md-pr">
                <div class="tr2"><i style="width:{{ $persen }}%"></i></div>
                <b>{{ $total > 0 ? $persen.'%' : '–' }}</b>
            </div>
        </div>
    </section>

    <div class="md-tabs" role="tablist" aria-label="Menu mata pelajaran">
        @foreach ($tabs as $kode => $label)
            <button type="button" role="tab" aria-selected="{{ $tab === $kode ? 'true' : 'false' }}"
                    class="md-tab {{ $tab === $kode ? 'on' : '' }}" wire:click="$set('tab', '{{ $kode }}')">{{ $label }}</button>
        @endforeach
    </div>

    {{-- ================= RINGKASAN ================= --}}
    @if ($tab === 'ringkasan')
        <div class="md-2">
            <section class="md-c">
                <h3>Deskripsi</h3>
                <p>{{ $k->deskripsi ?: 'Belum ada deskripsi untuk mata pelajaran ini.' }}</p>

                <h3>Guru Pengampu</h3>
                <div class="md-g">
                    <span class="av"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4.500"/><path d="M3.500 21c0-4.700 3.800-7.500 8.500-7.500s8.500 2.800 8.500 7.500v.5h-17V21Z"/></svg></span>
                    <div>
                        <b>{{ $k->guru ?: '–' }}</b>
                        @if ($k->guru_nip)<small>NIP {{ $k->guru_nip }}</small>@endif
                    </div>
                    @if ($k->guru_email)
                        <a href="mailto:{{ $k->guru_email }}" aria-label="Kirim email ke {{ $k->guru }}">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
                        </a>
                    @endif
                </div>
            </section>

            <div>
                <section class="md-c">
                    <h3>Informasi</h3>
                    <div class="md-i"><span>Kelas</span><b>{{ $k->nama }}</b></div>
                    <div class="md-i"><span>Semester</span><b>{{ $semLabel ?: '–' }}</b></div>
                    <div class="md-i"><span>Tahun Ajaran</span><b>{{ $k->tahun_ajaran ?? '–' }}</b></div>
                    <div class="md-i"><span>Tugas Terkumpul</span><b>{{ $kumpul }} / {{ $total }}</b></div>
                </section>
                <button type="button" class="md-bt" wire:click="$set('tab', 'materi')">Lihat Semua Materi →</button>
            </div>
        </div>

    {{-- ================= TUGAS ================= --}}
    @elseif ($tab === 'tugas')
        <section class="md-c">
            <h3>Tugas</h3>
            @if ($tugas->isEmpty())
                <div class="md-em">Belum ada tugas di mata pelajaran ini.</div>
            @else
                <div class="md-w">
                    <table class="md-t">
                        <thead><tr><th>Judul Tugas</th><th>Deadline</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @foreach ($tugas as $t)
                                <tr wire:key="t-{{ $t->id }}">
                                    <td>{{ $t->judul }}</td>
                                    <td>{{ \Carbon\Carbon::parse($t->deadline)->locale('id')->translatedFormat('j M Y, H:i') }}</td>
                                    <td>
                                        @if ($t->nilai !== null)
                                            <span class="md-bd hijau">Dinilai: {{ rtrim(rtrim(number_format($t->nilai, 1), '0'), '.') }}</span>
                                        @elseif ($t->sub_id)
                                            <span class="md-bd biru">Dikumpulkan</span>
                                        @else
                                            <span class="md-bd merah">Belum</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right"><a href="{{ route('siswa.tugas.kumpul', $t->id) }}" class="md-ab {{ $t->sub_id ? 'abu' : '' }}">{{ $t->sub_id ? 'Lihat' : 'Kerjakan' }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    {{-- ================= NILAI ================= --}}
    @elseif ($tab === 'nilai')
        <section class="md-c">
            <h3>Nilai</h3>
            @if ($nilai->isEmpty())
                <div class="md-em">Belum ada tugas yang dinilai.</div>
            @else
                <div class="md-ra">Rata-rata nilai: <b>{{ $rata ?? '–' }}</b></div>
                <div class="md-w">
                    <table class="md-t">
                        <thead><tr><th>Tugas</th><th>Nilai</th></tr></thead>
                        <tbody>
                            @foreach ($nilai as $n)
                                <tr wire:key="n-{{ $n->id }}">
                                    <td>{{ $n->judul }}</td>
                                    <td>{{ rtrim(rtrim(number_format($n->nilai, 1), '0'), '.') }} / {{ $n->nilai_maks }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    {{-- ================= MATERI / UJIAN ================= --}}
    @else
        <section class="md-c">
            <div class="md-em">Fitur {{ strtolower($tabs[$tab] ?? $tab) }} belum tersedia.</div>
        </section>
    @endif
</div>