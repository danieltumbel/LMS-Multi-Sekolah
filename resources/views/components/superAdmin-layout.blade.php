@props(['title' => 'Super Admin', 'subtitle' => null])
@php
    $namaUser = request()->user()?->name ?? 'Super Admin';

    // [label, nama route (null = belum ada), pola route aktif, ikon]
    $menu = [
        ['Dashboard',         'dashboard',            'dashboard',         'i-home'],
        ['Manajemen Sekolah', 'super-admin.tenants.index', 'super-admin.tenants.*', 'i-school'],
        ['Pengguna',          null,                   null,                'i-users'],
        ['Monitoring Sistem', null,                   null,                'i-monitor'],
        ['Manajemen Kuota',   null,                   null,                'i-quota'],
        ['Audit Log',         null,                   null,                'i-audit'],
        ['Profil',            'profile.edit',         'profile.*',         'i-users'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{{ $title }} – LMS</title>
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
.fcard{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 8px rgba(20,35,63,.05);padding:20px 22px;margin-bottom:14px}
.fcard h3{margin:0 0 14px;font-size:13px;font-weight:700}
.fgrid{display:grid;grid-template-columns:1fr 1fr;gap:16px 22px}
.fld label{display:block;font-size:11.5px;color:#41506b;margin-bottom:6px}
.fld label b{color:#e5484d;font-weight:600}
.fld input{width:100%;height:38px;border:1px solid #d6e2f4;border-radius:8px;background:#fff;padding:0 12px;font:inherit;font-size:12px;color:var(--ink)}
.fld input::placeholder{color:#a3b2c8}
.fld input:focus{outline:2px solid #2f6fe4;outline-offset:0;border-color:#2f6fe4}
.fld input.bad{border-color:#e5484d}
.err{display:block;color:#d93b3b;font-size:11px;margin-top:4px}
.factions{display:flex;justify-content:flex-end;gap:12px;margin-top:6px}
.bt{font:inherit;font-size:12.5px;font-weight:600;border-radius:9px;padding:10px 26px;cursor:pointer;text-decoration:none;display:inline-block}
.bt.ghost{background:#fff;color:#2a3956;border:1px solid #d6e2f4}
.bt.pri{background:#1f6bff;color:#fff;border:1px solid #1f6bff;box-shadow:0 3px 8px rgba(31,107,255,.3)}
.bt:disabled{opacity:.6;cursor:wait}
.bt:focus-visible{outline:2px solid #1f6bff;outline-offset:2px}
.ok{background:#e7f7ee;border:1px solid #9edbb8;color:#14633b;border-radius:12px;padding:14px 18px;margin-bottom:14px;font-size:12.5px;line-height:1.6}
.ok code{background:#fff;border:1px solid #bfe6d0;border-radius:6px;padding:2px 8px;font-size:12px;user-select:all}
@media (max-width:700px){.fgrid{grid-template-columns:1fr}}
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
  </defs>
</svg>

<div class="app">
  <aside class="side">
    <div class="brand">
      <span class="logo"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4 3 8.500l9 4.500 9-4.500z" fill="#fff"/><path d="M7 11v4c0 1.500 2.200 3 5 3s5-1.500 5-3v-4"/></svg></span>
      <div><b>Sistem Informasi</b><small>LMS Multi Tenant</small></div>
    </div>
    <nav class="menu">
      @foreach ($menu as [$label, $route, $pola, $icon])
        @php $ada = $route && \Illuminate\Support\Facades\Route::has($route); @endphp
        <a href="{{ $ada ? route($route) : '#' }}" class="{{ $pola && request()->routeIs($pola) ? 'on' : '' }}"><svg class="i"><use href="#{{ $icon }}"/></svg>{{ $label }}</a>
      @endforeach
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
        @if ($subtitle)<p>{{ $subtitle }}</p>@endif
      </div>
      <details class="usermenu">
        <summary class="user">
          <span class="avatar"><svg class="i"><use href="#i-users"/></svg></span>
          {{ $namaUser }}
          <svg class="i chev"><use href="#i-chev"/></svg>
        </summary>
        <div class="dd">
          <a href="{{ route('profile.edit') }}">Profil</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
          </form>
        </div>
      </details>
    </header>

    {{ $slot }}
  </main>
</div>
</body>
</html>