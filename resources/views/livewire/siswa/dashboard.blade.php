@php
    $nama = request()->user()?->name ?? 'Siswa';
    $hari = now()->locale('id')->translatedFormat('l, j F Y');
    $ikon = ['blue' => 'i-monitor', 'green' => 'i-folder', 'orange' => 'i-chart', 'purple' => 'i-book'];
    $nada = ['blue', 'green', 'orange', 'purple'];
@endphp

<x-siswa-layout title="Dashboard Siswa"
                :subtitle="'Selamat datang kembali, '.$nama.'!'"
                tagline="Semangat belajar dan raih prestasimu! 🚀">

    <style>
        .jd.k4 .r{grid-template-columns:1.6fr 1.2fr 1.2fr 56px}
        .jd .go{color:#2f56c8;font-weight:600;text-decoration:none;text-align:right}
        .kosong{text-align:center;padding:26px 10px;color:var(--muted);font-size:12.5px}
        .kosong a{display:inline-block;margin-top:12px;background:#2f6fe4;color:#fff;font-weight:600;font-size:12px;padding:9px 18px;border-radius:9px;text-decoration:none}
    </style>

    <section class="cards c4">
        @foreach ($stats as $s)
            <article class="card stat t-{{ $s['tone'] }}">
                <div class="row">
                    <span class="ico {{ $s['tone'] }}"><svg class="i"><use href="#{{ $s['icon'] }}"/></svg></span>
                    <div><div class="lb">{{ $s['label'] }}</div><div class="val">{{ $s['value'] }}</div></div>
                </div>
                <div class="delta">{{ $s['delta'] }}</div>
            </article>
        @endforeach
    </section>

    <section class="mid m32">
        <article class="card panel">
            <div class="hd"><h3>Kelas Saya</h3><a href="{{ route('siswa.mapel.index') }}" class="link">Lihat Semua ›</a></div>
            <p class="sub-date">{{ $hari }}</p>

            @if ($kelas->isEmpty())
                <div class="kosong">
                    Kamu belum mengikuti kelas apa pun.<br>
                    <a href="{{ route('siswa.jelajahi') }}">Jelajahi Kelas</a>
                </div>
            @else
                <div class="jd k4">
                    @foreach ($kelas->take(6) as $k)
                        <div class="r">
                            <span class="mp"><span class="mpi {{ $nada[$loop->index % 4] }}"><svg class="i"><use href="#{{ $ikon[$nada[$loop->index % 4]] }}"/></svg></span>{{ $k->nama }}</span>
                            <span class="kl">{{ $k->mata_pelajaran }}</span>
                            <span class="rg">{{ $k->guru }}</span>
                            <a href="{{ route('siswa.mapel.index') }}" class="go">Buka</a>
                        </div>
                    @endforeach
                </div>
            @endif

            <p class="quote"><svg class="i" style="width:14px;height:14px"><use href="#i-circlecheck"/></svg>"Ilmu hari ini, kesuksesan esok hari."</p>
        </article>

        <article class="card panel">
            <div class="hd"><h3><svg class="i"><use href="#i-task"/></svg>Tenggat Tugas</h3><a href="{{ route('siswa.tugas') }}" class="link">Lihat Semua</a></div>
            @if ($tenggat->isEmpty())
                <div class="kosong">Tidak ada tugas yang menunggu.</div>
            @else
                <ul class="pg">
                    @foreach ($tenggat as $t)
                        <li>
                            <span class="dt" style="background:{{ $t['warna'] }}"></span>
                            <div>
                                <b><a href="{{ $t['url'] }}" style="color:inherit;text-decoration:none">{{ $t['judul'] }}</a></b>
                                <small>{{ $t['mapel'] }} · {{ $t['waktu'] }}</small>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>
    </section>

</x-siswa-layout>