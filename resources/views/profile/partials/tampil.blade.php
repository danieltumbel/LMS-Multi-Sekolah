{{--
    Halaman profil (hanya baca) untuk Admin Sekolah, Guru, dan Siswa.
    Parameter: $user dan $info (dibuat oleh ProfileController::info()).
    $info['gaya'] = 'guru' (satu kartu) atau 'kartu' (dua kolom dengan ikon).
--}}
@php
    $fotoUrl = $user->foto ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->foto) : null;
    $ikon = [
        'id'    => '<rect x="4" y="5" width="16" height="14" rx="2"/><path d="M8 10h8M8 14h5"/>',
        'mail'  => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'phone' => '<path d="M5 4h4l2 5-2.500 1.500a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
        'pin'   => '<path d="M12 21s7-6.200 7-12a7 7 0 0 0-14 0c0 5.800 7 12 7 12z"/><circle cx="12" cy="9" r="2.500"/>',
    ];
    $guru = $info['gaya'] === 'guru';
@endphp

<style>
.tp-grid{display:grid;grid-template-columns:{{ $info['kanan'] ? '1.5fr 1fr' : '1fr' }};gap:16px;align-items:start}
.tp-card{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 8px rgba(20,35,63,.05);padding:24px 26px}
.tp-head{display:flex;align-items:center;gap:20px;flex-wrap:wrap}
.tp-av{width:92px;height:92px;border-radius:50%;background:#e8eef8;display:grid;place-items:center;overflow:hidden;flex:none}
.tp-av img{width:100%;height:100%;object-fit:cover}
.tp-av svg{width:52px;height:52px;color:#1a2f5a;fill:currentColor}
.tp-who{flex:1;min-width:170px}
.tp-who b{display:block;font-size:15px}
.tp-who span{display:block;font-size:12.5px;color:#41506b;margin-top:3px}
.tp-bd{display:inline-block;margin-top:8px;padding:3px 14px;border-radius:7px;font-size:11px;font-weight:600}
.tp-bd.ok{background:#dff5e8;color:#1b9a58}.tp-bd.off{background:#ffe3e3;color:#d93b3b}
.tp-btn{display:inline-block;text-decoration:none;font:inherit;font-size:12.5px;font-weight:600;border-radius:9px;padding:10px 26px;white-space:nowrap}
.tp-btn.sol{background:#1f6bff;color:#fff;box-shadow:0 3px 8px rgba(31,107,255,.3)}
.tp-btn.sft{background:#e4eeff;color:#2f56c8;border:1px solid #cfe0fb}
.tp-btn:focus-visible{outline:2px solid #1f6bff;outline-offset:2px}
.tp-h{margin:22px 0 8px;font-size:13px;font-weight:700}
.tp-g{display:grid;grid-template-columns:90px 12px 1fr;gap:12px 6px;font-size:12.5px;padding:4px 0 8px}
.tp-g dt{color:var(--muted);margin:0}.tp-g i{font-style:normal;color:var(--muted)}.tp-g dd{margin:0;word-break:break-word}
.tp-r{display:flex;align-items:center;gap:14px;padding:14px 4px;border-top:1px solid var(--line);font-size:12.5px;margin-top:0}
.tp-r:first-of-type{margin-top:18px}
.tp-r svg{width:18px;height:18px;flex:none;color:#8a99b0;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.tp-r b{width:96px;flex:none;font-weight:600}
.tp-r span{flex:1;word-break:break-word}
.tp-k h3{margin:0 0 6px;font-size:13px;font-weight:700}
.tp-kr{display:flex;justify-content:space-between;gap:14px;padding:12px 0;border-top:1px solid var(--line);font-size:12px}
.tp-kr:first-of-type{margin-top:10px}
.tp-kr span{color:var(--muted)}.tp-kr b{font-weight:500;text-align:right;word-break:break-word}
.tp-ft{display:flex;justify-content:flex-end;margin-top:14px}
@media (max-width:900px){.tp-grid{grid-template-columns:1fr}}
</style>

@if (session('status') === 'profile-updated')
    <div class="pf-ok" role="status" style="background:#e7f7ee;border:1px solid #9edbb8;color:#14633b;border-radius:12px;padding:12px 16px;margin-bottom:14px;font-size:12.5px">Profil berhasil diperbarui.</div>
@endif

<div class="tp-grid">
    <section class="tp-card">
        <div class="tp-head">
            <div class="tp-av">
                @if ($fotoUrl)
                    <img src="{{ $fotoUrl }}" alt="Foto profil">
                @else
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4.500"/><path d="M3.500 21c0-4.700 3.800-7.500 8.500-7.500s8.500 2.800 8.500 7.500v.5h-17V21Z"/></svg>
                @endif
            </div>
            <div class="tp-who">
                <b>{{ $user->name }}</b>
                <span>{{ $info['subjudul'] }}</span>
                @if ($info['badge'])<span class="tp-bd {{ $info['badge'][1] }}">{{ $info['badge'][0] }}</span>@endif
            </div>
            @unless ($guru)
                <a href="{{ route('profile.ubah') }}" class="tp-btn sft">{{ $info['tombol'] }}</a>
            @endunless
        </div>

        @if ($guru)
            <h3 class="tp-h">Informasi Pribadi</h3>
            <dl class="tp-g">
                @foreach ($info['rows'] as [$tipe, $label, $nilai])
                    <dt>{{ $label }}</dt><i>:</i><dd>{{ $nilai }}</dd>
                @endforeach
            </dl>
            <div class="tp-ft"><a href="{{ route('profile.ubah') }}" class="tp-btn sol">{{ $info['tombol'] }}</a></div>
        @else
            @foreach ($info['rows'] as [$tipe, $label, $nilai])
                <div class="tp-r">
                    <svg viewBox="0 0 24 24" aria-hidden="true">{!! $ikon[$tipe] !!}</svg>
                    <b>{{ $label }}</b>
                    <span>{{ $nilai }}</span>
                </div>
            @endforeach
        @endif
    </section>

    @if ($info['kanan'])
        <aside class="tp-card tp-k">
            <h3>{{ $info['kanan']['judul'] }}</h3>
            @foreach ($info['kanan']['rows'] as [$label, $nilai])
                <div class="tp-kr"><span>{{ $label }}</span><b>{{ $nilai }}</b></div>
            @endforeach
        </aside>
    @endif
</div>