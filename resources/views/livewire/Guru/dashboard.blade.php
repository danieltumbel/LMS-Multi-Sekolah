{{-- Komponen Livewire: satu elemen root. Layout dipasang dari class Dashboard. --}}
<div>
    <section class="cards c4">
        @foreach ($stats as $s)
            <article class="card sc sc-{{ $s['tone'] }}">
                <span class="ib"><svg class="i"><use href="#{{ $s['icon'] }}"/></svg></span>
                <div>
                    <div class="lb2">{{ $s['label'] }}</div>
                    <div class="v2">{{ $s['value'] }}</div>
                    @if ($s['note'])<small>{{ $s['note'] }}</small>@endif
                </div>
            </article>
        @endforeach
    </section>

    <section class="mid m32b">
        <article class="card panel">
            <div class="hd"><h3>Progres Kelas</h3></div>
            @if ($progres->isEmpty())
                <div class="kosong">Kamu belum mengampu kelas apa pun.</div>
            @else
                <div class="pw">
                    <table class="pt">
                        <thead><tr><th>Kelas</th><th>Kehadiran</th><th>Tugas Terkumpul</th><th>Rata-rata Nilai</th></tr></thead>
                        <tbody>
                            @foreach ($progres as $p)
                                <tr>
                                    <td class="nm">{{ $p['nama'] }}<small>{{ $p['mapel'] }}</small></td>
                                    <td class="cell"><span>–</span><div class="bar g"><i style="width:0"></i></div></td>
                                    <td class="cell">
                                        <span>{{ $p['persen'] !== null ? $p['persen'].'%' : '–' }}</span>
                                        <div class="bar o"><i style="width:{{ $p['persen'] ?? 0 }}%"></i></div>
                                    </td>
                                    <td>{{ $p['rata'] ?? '–' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </article>

        <div>
            <article class="card panel">
                <div class="hd"><h3>Tugas Terbaru</h3></div>
                @forelse ($tugasBaru as $t)
                    <div class="tb">
                        <span class="fi2"><svg class="i"><use href="#i-task"/></svg></span>
                        <div>
                            <b>{{ $t['judul'] }}</b>
                            <small class="{{ $t['belum'] > 0 ? 'red' : '' }}">{{ $t['belum'] }} belum dinilai</small>
                        </div>
                        <span class="w2">{{ $t['waktu'] }}</span>
                    </div>
                @empty
                    <div class="kosong">Belum ada tugas.</div>
                @endforelse
            </article>

            <article class="card panel sec2" style="margin-top:14px">
                <div class="hd"><h3>Kuis Terbaru</h3></div>
                {{-- Belum ada tabel kuis --}}
                <div class="kosong">Fitur kuis belum tersedia.</div>
            </article>
        </div>
    </section>
</div>