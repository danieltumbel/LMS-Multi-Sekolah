{{-- Komponen Livewire: satu elemen root --}}
<div>
<style>
.mp-cari{position:relative;margin-bottom:14px}
.mp-cari input{width:100%;height:44px;border:1px solid var(--line);background:#fff;border-radius:12px;padding:0 16px 0 42px;font:inherit;font-size:12.5px;color:var(--ink);box-shadow:0 2px 8px rgba(20,35,63,.04)}
.mp-cari input:focus-visible{outline:2px solid #2f6fe4}
.mp-cari svg{position:absolute;left:15px;top:50%;transform:translateY(-50%);width:17px;height:17px;color:#8a99b0;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round}
.mp-tabs{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap}
.mp-tab{border:1px solid var(--line);background:#fff;color:#41506b;font:inherit;font-size:12.5px;font-weight:600;border-radius:9px;padding:9px 26px;cursor:pointer}
.mp-tab.on{background:#1f8bff;border-color:#1f8bff;color:#fff;box-shadow:0 3px 8px rgba(31,139,255,.3)}
.mp-tab:focus-visible{outline:2px solid #1f6bff;outline-offset:2px}
.mp-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.mp-card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:18px;box-shadow:0 2px 8px rgba(20,35,63,.05)}
.mp-ic{width:38px;height:38px;border-radius:10px;display:grid;place-items:center;margin-bottom:14px}
.mp-ic .i{width:19px;height:19px}
.mp-ic.teal{background:#d9f3f6;color:#12939f}
.mp-card h3{margin:0;font-size:13px;font-weight:700}
.mp-card .kl{font-size:11px;color:var(--muted);margin:4px 0 12px}
.mp-bar{display:flex;align-items:center;gap:10px}
.mp-bar .tr2{flex:1;height:6px;border-radius:99px;background:#e6ecf5;overflow:hidden}
.mp-bar .tr2 i{display:block;height:100%;border-radius:99px}
.mp-bar b{font-size:11px;font-weight:600;color:#41506b}
.mp-card a.dt{display:inline-block;margin-top:14px;font-size:11.5px;font-weight:500;color:#2f56c8;text-decoration:none}
.mp-card a.dt:hover{text-decoration:underline}
.mp-card a.dt:focus-visible{outline:2px solid #1f6bff;outline-offset:2px;border-radius:4px}
.mp-kosong{background:#fff;border:1px solid var(--line);border-radius:14px;text-align:center;padding:40px 16px;color:var(--muted);font-size:12.5px}
.mp-kosong a{display:inline-block;margin-top:14px;background:#2f6fe4;color:#fff;font-weight:600;font-size:12px;padding:9px 18px;border-radius:9px;text-decoration:none}
@media (max-width:1200px){.mp-grid{grid-template-columns:repeat(3,1fr)}}
@media (max-width:900px){.mp-grid{grid-template-columns:repeat(2,1fr)}}
@media (max-width:560px){.mp-grid{grid-template-columns:1fr}}
</style>

    <label class="mp-cari">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.500-4.500"/></svg>
        <input type="search" wire:model.live.debounce.300ms="q" placeholder="Cari mata pelajaran..." aria-label="Cari mata pelajaran">
    </label>

    @if ($adaSemester)
        <div class="mp-tabs" role="tablist" aria-label="Filter semester">
            <button type="button" class="mp-tab {{ $semester === 'semua' ? 'on' : '' }}" wire:click="setSemester('semua')">Semua</button>
            <button type="button" class="mp-tab {{ $semester === 'ganjil' ? 'on' : '' }}" wire:click="setSemester('ganjil')">Semester 1</button>
            <button type="button" class="mp-tab {{ $semester === 'genap' ? 'on' : '' }}" wire:click="setSemester('genap')">Semester 2</button>
        </div>
    @endif

    @php $warna = ['blue' => '#2f6fe4', 'green' => '#1fae7a', 'orange' => '#f5a524', 'purple' => '#7c5cf0', 'teal' => '#19b5c5']; @endphp

    @if ($kartu->isEmpty())
        <div class="mp-kosong">
            @if (trim($q) !== '' || $semester !== 'semua')
                Tidak ada mata pelajaran yang cocok.
            @else
                Kamu belum mengikuti mata pelajaran apa pun.<br>
                <a href="{{ route('siswa.jelajahi') }}">Jelajahi Kelas</a>
            @endif
        </div>
    @else
        <div class="mp-grid">
            @foreach ($kartu as $m)
                <article class="mp-card" wire:key="mp-{{ $m['id'] }}">
                    <span class="mp-ic {{ $m['nada'] }}"><svg class="i"><use href="#{{ $m['ikon'] }}"/></svg></span>
                    <h3>{{ $m['mapel'] }}</h3>
                    <div class="kl">{{ $m['kelas'] }}</div>
                    <div class="mp-bar">
                        <div class="tr2"><i style="width:{{ $m['persen'] }}%;background:{{ $warna[$m['nada']] }}"></i></div>
                        <b>{{ $m['ada'] ? $m['persen'].'%' : '–' }}</b>
                    </div>
                    <a href="{{ route('siswa.mapel.show', $m['id']) }}" class="dt">Lihat Detail ›</a>
                </article>
            @endforeach
        </div>
    @endif
</div>