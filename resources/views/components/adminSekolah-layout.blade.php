@props(['title' => 'Dashboard', 'subtitle' => null, 'tagline' => null, 'sekolah' => null])
@php
    $user = request()->user();
    $namaUser = $user?->name ?? 'Admin Sekolah';

    // Nama sekolah diambil dari data sekolah milik admin
    $namaSekolah = $sekolah ?: ($user?->school_id
        ? \Illuminate\Support\Facades\DB::table('schools')->where('id', $user->school_id)->value('name')
        : null) ?: 'LMS Multi Tenant';

    // Dashboard (komponen Livewire) tidak mengirim sapaan lewat parameter, jadi diisi di sini
    if (request()->routeIs('admin.dashboard')) {
        $subtitle = $subtitle ?? 'Selamat datang, '.$namaUser;
        $tagline  = $tagline  ?? $namaSekolah;
    }

    // Judul halaman manajemen pengguna diisi dari nama route
    if (request()->routeIs('admin.teachers.create')) {
        $title    = 'Tambah Guru';
        $subtitle = null;
    } elseif (request()->routeIs('admin.teachers.*')) {
        $title    = 'Manajemen Pengguna - Guru';
        $subtitle = 'Kelola data guru di sekolah Anda.';
    } elseif (request()->routeIs('admin.students.*')) {
        $title    = 'Manajemen Pengguna - Siswa';
        $subtitle = 'Kelola data siswa di sekolah Anda.';
    }

    if (request()->routeIs('admin.classes.create')) {
        $title    = 'Tambah Kelas / Rombel';
        $subtitle = null;
    } elseif (request()->routeIs('admin.classes.*')) {
        $title    = 'Kelas / Rombel';
        $subtitle = 'Kelola kelas dan rombongan belajar di sekolah Anda.';
    }

    // Nama route di bawah adalah usulan; menu tampil "#" sampai route-nya dibuat.
    $r  = fn ($name) => $name && \Illuminate\Support\Facades\Route::has($name) ? route($name) : '#';
    $on = fn (...$pola) => request()->routeIs(...$pola) ? 'on' : '';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{{ $title }} – SIKESMA</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  color-scheme:light;
  --navy:#0b2a5c; --navy2:#0a2250; --active:#1f56a8;
  --bg:#f2f5fa; --card:#fff; --line:#e4eaf3; --ink:#14233f; --muted:#6b7a93; --link:#2563eb;
  --blue:#2f6fe4; --green:#1fae7a; --purple:#7c5cf0;
  box-sizing:border-box;
}
*{box-sizing:border-box}
html{scroll-padding-top:env(safe-area-inset-top,0px)}
body{margin:0;font-family:'Inter',system-ui,sans-serif;background:var(--bg);color:var(--ink);font-size:13px;
  padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}
.app{display:flex;min-height:100vh}
.i{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex:none}

/* Sidebar */
.side{width:232px;flex:none;background:linear-gradient(180deg,var(--navy),var(--navy2));color:#fff;display:flex;flex-direction:column;padding:18px 10px;position:sticky;top:0;height:100vh}
.brand{display:flex;align-items:center;gap:10px;padding:2px 8px 22px}
.brand .logo{width:34px;height:34px;border-radius:50%;background:radial-gradient(circle at 30% 25%,#5b9bff,#2457b8);display:grid;place-items:center}
.brand .logo svg{width:18px;height:18px}
.brand b{display:block;font-size:13px;font-weight:600}
.brand small{font-size:10px;opacity:.75}
.menu{display:flex;flex-direction:column;gap:6px;flex:1}
.menu a{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:8px;color:#e6eefc;text-decoration:none;font-size:12.5px;font-weight:500}
.menu a:hover{background:rgba(255,255,255,.08)}
.menu a.on{background:var(--active);color:#fff;font-weight:600}
.logout{margin:0}
.logout button{all:unset;box-sizing:border-box;width:100%;display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:8px;color:#e6eefc;font-size:12.5px;font-weight:500;cursor:pointer}
.logout button:hover{background:rgba(255,255,255,.08)}
.logout button:focus-visible,.menu a:focus-visible{outline:2px solid #9cc2ff}

/* Main */
.main{flex:1;min-width:0;padding:22px 26px 30px}
.top{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:16px}
.top h1{margin:0 0 6px;font-size:21px;font-weight:700}
.top p{margin:0;color:#41506b;font-size:12px}
.user{display:flex;align-items:center;gap:8px;background:#fff;border:1px solid var(--line);padding:5px 10px 5px 6px;border-radius:999px;font-size:12px;font-weight:500;white-space:nowrap}
.avatar{width:26px;height:26px;border-radius:50%;background:var(--navy);color:#fff;display:grid;place-items:center}
.avatar svg{width:15px;height:15px}
.user .chev{width:12px;height:12px;stroke-width:2.5}

.usermenu{position:relative}
.usermenu summary{list-style:none;cursor:pointer}
.usermenu summary::-webkit-details-marker{display:none}
.dd{position:absolute;right:0;top:calc(100% + 6px);min-width:150px;background:#fff;border:1px solid var(--line);border-radius:10px;box-shadow:0 8px 20px rgba(20,35,63,.12);padding:6px;z-index:20}
.dd a,.dd button{display:block;width:100%;text-align:left;padding:8px 10px;border-radius:6px;font:inherit;font-size:12px;color:var(--ink);background:none;border:0;text-decoration:none;cursor:pointer}
.dd a:hover,.dd button:hover{background:#f0f4fa}
.cards{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:14px}
.card{background:var(--card);border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 8px rgba(20,35,63,.05)}
.stat{padding:16px 16px 12px}
.stat .row{display:flex;align-items:center;gap:12px}
.ico{width:40px;height:40px;border-radius:10px;display:grid;place-items:center;flex:none}
.ico .i{width:20px;height:20px}
.blue{background:#e4edff;color:#2f6fe4}.green{background:#dcf3e8;color:#1f9d63}.orange{background:#ffeed6;color:#e58a1f}.purple{background:#e9e4ff;color:#6a4fe0}
.stat .lb{font-size:11.5px;color:#41506b;font-weight:500}
.stat .val{font-size:26px;font-weight:700;line-height:1.1;margin-top:2px}
.stat .foot{margin-top:12px;padding-top:9px;border-top:1px solid var(--line);text-align:center;font-size:10.5px;color:var(--muted)}

.mid{display:grid;grid-template-columns:1.12fr 1fr;gap:14px;margin-bottom:14px}
.panel{padding:16px}
.panel h3{margin:0;font-size:13px;font-weight:700}
.panel .hd{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;gap:10px;flex-wrap:wrap}
.link{color:var(--link);font-size:11px;font-weight:600;text-decoration:none}
.legend{display:flex;gap:14px;font-size:11px;color:#41506b;flex-wrap:wrap}
.legend span{display:inline-flex;align-items:center;gap:5px}
.legend i{width:8px;height:8px;border-radius:50%;display:inline-block}
#chart{width:100%;height:auto;display:block}
#chart text{font-family:inherit;font-size:10px;fill:#6b7a93}

.tbl{width:100%;border-collapse:collapse;font-size:11.5px}
.tbl th{background:#f0f4fa;text-align:left;font-weight:600;color:#41506b;padding:9px 12px;font-size:11px}
.tbl td{padding:11px 12px;border-bottom:1px solid var(--line)}
.tbl tr:last-child td{border-bottom:0}
.tbl td:first-child{font-weight:500}
.tblwrap{overflow-x:auto;margin:0 -16px -16px}
.badge{display:inline-block;padding:2px 9px;border-radius:6px;font-size:10.5px;font-weight:600;border:1px solid}
.b-aktif{background:#e7f7ee;border-color:#9edbb8;color:#1b9a58}
.b-tangguh{background:#fff1dc;border-color:#f4c27d;color:#d8801f}

.act{display:flex;align-items:center;gap:12px;padding:8px 0}
.act .dot{width:28px;height:28px;border-radius:50%;display:grid;place-items:center;flex:none}
.act .dot .i{width:14px;height:14px}
.warn{background:#fff1dc;color:#e58a1f}.info{background:#dcebff;color:#2f6fe4}.danger{background:#ffe1e1;color:#dc3b3b}
.act .t{flex:1;color:#2a3956;font-size:12px}
.act .w{color:var(--muted);font-size:11px;white-space:pre}

@media (max-width:1100px){.cards{grid-template-columns:repeat(2,1fr)}.mid{grid-template-columns:1fr}}
@media (max-width:820px){
  .app{flex-direction:column}
  .side{width:100%;height:auto;position:static;flex-direction:row;align-items:center;overflow-x:auto;padding:10px;gap:8px}
  .brand{padding:0 8px 0 0}.brand small{display:none}
  .menu{flex-direction:row;flex:none}.menu a{white-space:nowrap}
  .logout{flex:none}.logout button{white-space:nowrap}
  .main{padding:18px 14px 26px}
  .top{flex-direction:column}
  .act{flex-wrap:wrap}.act .w{white-space:normal;width:100%;padding-left:40px}
}
@media (max-width:520px){.cards{grid-template-columns:1fr}}
.main{background:linear-gradient(180deg,#f5f8ff,#eef3fb)}
.top .sub{font-weight:600;color:#14233f;font-size:12.5px}
.top .tag{color:#3b7be0;margin-top:4px;font-size:12px}
.tr{display:flex;align-items:center;gap:12px}
.bell{position:relative;width:34px;height:34px;display:grid;place-items:center;border-radius:50%;background:#fff;border:1px solid var(--line);color:#41506b}
.bell .i{width:16px;height:16px}
.bell i{position:absolute;top:6px;right:7px;width:7px;height:7px;background:#e5484d;border-radius:50%}
.user .nm{display:flex;flex-direction:column;line-height:1.2}
.user .nm small{font-size:10px;color:var(--muted);font-weight:400}
.cards.c4 .stat{padding:16px}
.cards .t-blue{background:linear-gradient(180deg,#fff,#eef4ff);border-color:#d9e6fb}
.cards .t-green{background:linear-gradient(180deg,#fff,#edf9f2);border-color:#d3eedf}
.cards .t-orange{background:linear-gradient(180deg,#fff,#fff3e3);border-color:#f8dfbc}
.cards .t-purple{background:linear-gradient(180deg,#fff,#f1edff);border-color:#e0d8fb}
.stat .delta{margin-top:10px;font-size:10.5px;color:#1b9a58;font-weight:500}
.mid.m32{grid-template-columns:1.65fr 1fr}
.sub-date{font-size:11.5px;color:#41506b;margin:-6px 0 10px}
.jd{background:#f6f9ff;border:1px solid var(--line);border-radius:10px;padding:4px 10px}
.jd .r{display:grid;grid-template-columns:96px 1.5fr 70px 64px;align-items:center;gap:8px;padding:11px 2px;border-bottom:1px solid var(--line);font-size:11.5px}
.jd .r:last-child{border-bottom:0}
.jd .tm{color:#41506b}
.jd .mp{display:flex;align-items:center;gap:8px;font-weight:600}
.mpi{width:22px;height:22px;border-radius:6px;display:grid;place-items:center;flex:none}
.mpi .i{width:12px;height:12px}
.jd .kl,.jd .rg{color:#41506b}
.quote{margin:14px 0 0;display:flex;align-items:center;gap:8px;font-size:11px;color:var(--muted);font-style:italic}
.pg{padding:0;margin:0;list-style:none}
.pg li{display:flex;gap:12px;padding:11px 0;border-bottom:1px solid var(--line)}
.pg li:last-child{border-bottom:0}
.pg .dt{width:8px;height:8px;border-radius:50%;margin-top:5px;flex:none}
.pg b{display:block;font-size:12px;font-weight:600}
.pg small{font-size:10.5px;color:var(--muted)}
.hd h3 .i{width:15px;height:15px;vertical-align:-2px;margin-right:6px;color:#e5484d}
@media (max-width:1100px){.mid.m32{grid-template-columns:1fr}}
@media (max-width:560px){.jd .r{grid-template-columns:1fr 1fr}.cards.c4{grid-template-columns:1fr}}
.menu .grp>summary{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:8px;color:#e6eefc;font-size:12.5px;font-weight:500;cursor:pointer;list-style:none}
.menu .grp>summary::-webkit-details-marker{display:none}
.menu .grp>summary:hover{background:rgba(255,255,255,.08)}
.menu .grp>summary .chev2{margin-left:auto;width:12px;height:12px;transition:transform .15s}
.menu .grp[open]>summary .chev2{transform:rotate(180deg)}
.menu .subm{display:flex;flex-direction:column;gap:4px;margin:4px 0 2px}
.menu .subm a{padding:8px 12px 8px 44px;font-size:12px}
.pt{width:100%;border-collapse:collapse;font-size:12px}
.pt th{text-align:left;font-weight:600;color:#41506b;padding:10px 12px;font-size:11.5px;background:#f0f4fa}
.pt td{padding:13px 12px;border-bottom:1px solid var(--line);vertical-align:middle}
.pt tr:last-child td{border-bottom:0}
.pt .nm{font-weight:600}
.pt .nm small{display:block;font-weight:400;color:var(--muted);font-size:10.5px}
.cell{min-width:110px}
.cell span{display:block;font-size:11.5px;margin-bottom:4px}
.bar{height:6px;border-radius:99px;background:#e6ecf5;overflow:hidden}
.bar i{display:block;height:100%;border-radius:99px}
.bar.g i{background:#22b07d}.bar.o i{background:#f5a524}
.pw{margin:0 -16px -16px;overflow-x:auto}
.ak{display:flex;align-items:center;gap:12px;padding:11px 0;border-bottom:1px solid var(--line)}
.ak:last-child{border-bottom:0}
.ak .dot{width:30px;height:30px;border-radius:9px;display:grid;place-items:center;flex:none}
.ak .dot .i{width:15px;height:15px}
.ak .t{flex:1;font-size:12px;color:#2a3956}
.ak .w{font-size:11px;color:var(--muted);white-space:nowrap}
.kosong{text-align:center;padding:26px 10px;color:var(--muted);font-size:12.5px}
.tag2{color:#3b7be0;margin-top:4px;font-size:12px}

/* ---- Form dua kolom (Tambah Guru, Tambah Kelas) ---- */
.tf{display:grid;grid-template-columns:1.1fr 1fr;gap:16px;align-items:start}
.tc{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 8px rgba(20,35,63,.05);padding:20px 22px}
.tc h3{margin:0 0 6px;font-size:13px;font-weight:700}
.fl{margin-top:14px}
.fl label,.fl .lbl{display:block;font-size:11.5px;color:#41506b;margin-bottom:6px}
.fl label b{color:#e5484d;font-weight:600}
.fl input[type=text],.fl input[type=email],.fl input[type=tel],.fl input[type=number],.fl select{width:100%;height:40px;border:1px solid #d6e2f4;border-radius:8px;background:#fff;padding:0 12px;font:inherit;font-size:12px;color:var(--ink)}
.fl input::placeholder{color:#a3b2c8}
.fl input:focus-visible,.fl select:focus-visible{outline:2px solid #2f6fe4;border-color:#2f6fe4}
.fl .bad{border-color:#e5484d}
.er{display:block;color:#d93b3b;font-size:11px;margin-top:4px}
.hint{display:block;color:var(--muted);font-size:11px;margin-top:5px}
.fa{display:flex;justify-content:flex-end;gap:12px;margin-top:18px}
.bt4{font:inherit;font-size:12.5px;font-weight:600;border-radius:9px;padding:10px 30px;cursor:pointer;text-decoration:none;display:inline-block;border:1px solid transparent}
.bt4.gh{background:#fff;color:#2a3956;border-color:#d6e2f4}
.bt4.pr{background:#1f6bff;color:#fff;box-shadow:0 3px 8px rgba(31,107,255,.3)}
.bt4:disabled{opacity:.6;cursor:wait}
.bt4:focus-visible{outline:2px solid #1f6bff;outline-offset:2px}
@media (max-width:900px){.tf{grid-template-columns:1fr}}
</style>
</head>
<body>

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <symbol id="i-home" viewBox="0 0 24 24"><path d="M3 11l9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></symbol>
    <symbol id="i-school" viewBox="0 0 24 24"><path d="M4 21V6l8-3 8 3v15M2 21h20M9 21v-5h6v5M9 9h2M13 9h2M9 12.500h2M13 12.500h2"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.500"/><path d="M5 20c0-3.800 3-6 7-6s7 2.200 7 6"/></symbol>
    <symbol id="i-monitor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8M12 16v4"/></symbol>
    <symbol id="i-quota" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 12l3.500-3.500M12 7v1.500M7 12h1.500"/></symbol>
    <symbol id="i-audit" viewBox="0 0 24 24"><path d="M6 3h9l4 4v14H6z"/><path d="M9 12h7M9 16h7"/></symbol>
    <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h4M16 17l5-5-5-5M21 12H9"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="M4 21V6l8-3 8 3v15M2 21h20"/><path d="M8.500 12.500l2.500 2.500 4.500-5"/></symbol>
    <symbol id="i-class" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M7 9h4M7 13h10M8 21h8"/></symbol>
    <symbol id="i-alert" viewBox="0 0 24 24"><path d="M12 3l10 18H2z"/><path d="M12 10v5M12 18v.5"/></symbol>
    <symbol id="i-chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></symbol>
      <symbol id="i-bell" viewBox="0 0 24 24"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 0 0 4 0"/></symbol>
    <symbol id="i-book" viewBox="0 0 24 24"><path d="M12 6c-2-1.500-5-2-8-2v14c3 0 6 .5 8 2 2-1.500 5-2 8-2V4c-3 0-6 .5-8 2zM12 6v14"/></symbol>
    <symbol id="i-task" viewBox="0 0 24 24"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4h6v3H9zM9 14l2 2 4-4"/></symbol>
    <symbol id="i-exam" viewBox="0 0 24 24"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h3"/></symbol>
    <symbol id="i-folder" viewBox="0 0 24 24"><path d="M3 6a1 1 0 0 1 1-1h5l2 2h9a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/></symbol>
    <symbol id="i-chart" viewBox="0 0 24 24"><path d="M4 20V10M10 20V4M16 20v-8M22 20H2"/></symbol>
    <symbol id="i-circlecheck" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/></symbol>
    <symbol id="i-megaphone" viewBox="0 0 24 24"><path d="M3 11v2a1 1 0 0 0 1 1h3l8 5V5L7 10H4a1 1 0 0 0-1 1zM19 9a4 4 0 0 1 0 6"/></symbol>
    <symbol id="i-userc" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="10" r="3"/><path d="M6.500 18c1-2.500 3-3.500 5.500-3.500s4.500 1 5.500 3.500"/></symbol>
    <symbol id="i-pin" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></symbol>
      <symbol id="i-calendar" viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="16" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></symbol>
    <symbol id="i-person" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.500-6 8-6s8 2 8 6"/></symbol>
  </defs>
</svg>

<div class="app">
  <aside class="side">
    <div class="brand">
      <span class="logo"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4 3 8.500l9 4.500 9-4.500z" fill="#fff"/><path d="M7 11v4c0 1.500 2.200 3 5 3s5-1.500 5-3v-4"/></svg></span>
      <div><b>Sistem Informasi</b><small>{{ $namaSekolah }}</small></div>
    </div>
    <nav class="menu">
      <a href="{{ $r('admin.dashboard') }}" class="{{ $on('admin.dashboard') }}"><svg class="i"><use href="#i-home"/></svg>Dashboard</a>

      <details class="grp" @if (request()->routeIs('admin.teachers.*', 'admin.students.*', 'admin.users.*')) open @endif>
        <summary><svg class="i"><use href="#i-users"/></svg>Pengguna<svg class="i chev2"><use href="#i-chev"/></svg></summary>
        <div class="subm">
          <a href="{{ $r('admin.teachers.index') }}" class="{{ $on('admin.teachers.*') }}"><svg class="i"><use href="#i-person"/></svg>Guru</a>
          <a href="{{ $r('admin.students.index') }}" class="{{ $on('admin.students.*') }}"><svg class="i"><use href="#i-person"/></svg>Siswa</a>
        </div>
      </details>

      <a href="{{ $r('admin.classes.index') }}" class="{{ $on('admin.classes.*') }}"><svg class="i"><use href="#i-school"/></svg>Kelas / Rombel</a>
      <a href="{{ $r('admin.calendar.index') }}" class="{{ $on('admin.calendar.*') }}"><svg class="i"><use href="#i-calendar"/></svg>Kalender Akademik</a>
      <a href="{{ $r('admin.progress.index') }}" class="{{ $on('admin.progress.*') }}"><svg class="i"><use href="#i-chart"/></svg>Progres Belajar</a>
      <a href="{{ $r('profile.edit') }}" class="{{ $on('profile.*') }}"><svg class="i"><use href="#i-userc"/></svg>Profil</a>
    </nav>
    <form class="logout" method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"><svg class="i"><use href="#i-logout"/></svg>Logout</button>
    </form>
  </aside>

  <main class="main">
    <header class="top">
      <div>
        <h1>{{ $title }}</h1>
        @if ($subtitle)<p class="sub">{{ $subtitle }}</p>@endif
        @if ($tagline)<p class="tag2">{{ $tagline }}</p>@endif
      </div>
      <div class="tr">
        <span class="bell" aria-label="Notifikasi"><svg class="i"><use href="#i-bell"/></svg><i></i></span>
        <details class="usermenu">
          <summary class="user">
            <span class="avatar"><svg class="i"><use href="#i-users"/></svg></span>
            <span class="nm">{{ $namaUser }}<small>{{ $user?->email }}</small></span>
            <svg class="i chev"><use href="#i-chev"/></svg>
          </summary>
          <div class="dd">
            <a href="{{ $r('profile.edit') }}">Profil</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit">Logout</button>
            </form>
          </div>
        </details>
      </div>
    </header>

    {{ $slot }}
  </main>
</div>
</body>
</html>