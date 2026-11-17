<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>LMS – Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --olive:#2d5aa0; --olive-dark:#3a66ad; --olive-light:#7aa5de;
  --field:#e3eefb; --ink:#2d5aa0; --muted:#5f7390;
  color-scheme:light; --card:#ffffff; --bg:#ececec; --swirl:#d4d4d4; --placeholder:#6c82a3;
  box-sizing:border-box;
  padding-top:env(safe-area-inset-top,0px); padding-bottom:env(safe-area-inset-bottom,0px);
}
html{scroll-padding-top:env(safe-area-inset-top,0px)}
*{box-sizing:border-box}
body{
  margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;
  font-family:'Poppins',system-ui,sans-serif;background-color:var(--bg);
  background-image:
    radial-gradient(circle at 20% 30%,transparent 38px,var(--swirl) 39px,var(--swirl) 46px,transparent 47px),
    radial-gradient(circle at 70% 70%,transparent 50px,var(--swirl) 51px,var(--swirl) 60px,transparent 61px),
    radial-gradient(circle at 90% 20%,transparent 26px,var(--swirl) 27px,var(--swirl) 33px,transparent 34px);
  background-size:180px 180px,260px 260px,140px 140px;
  padding:48px 16px;overflow-x:hidden;
}
.stage{position:relative;width:100%;max-width:960px;display:grid;grid-template-columns:1.5fr 1fr}
.card{background:var(--card);border-radius:28px;padding:64px 40px 52px;text-align:center;box-shadow:0 8px 24px rgba(0,0,0,.12);position:relative;z-index:2}
.panel{background:var(--olive-dark);border-radius:0 28px 28px 0;color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:48px 28px;position:relative;box-shadow:0 8px 24px rgba(0,0,0,.12)}
.panel h2{font-size:2.1rem;margin:0 0 8px;font-weight:600}
.panel p{margin:0 0 22px;font-size:.85rem;max-width:230px;line-height:1.5}
.panel > *{position:relative;z-index:3}
h1{color:var(--ink);font-size:2rem;font-weight:600;margin:0 0 18px}
.circle{position:absolute;border-radius:50%;background:radial-gradient(circle at 30% 25%,#a9c8f0,var(--olive-light) 45%,#4a7bc0);box-shadow:0 6px 14px rgba(0,0,0,.25);z-index:5}
.c1{width:123px;height:123px;left:34px;top:-4px}
.c2{width:61px;height:61px;left:136px;top:-26px;z-index:4}
.c3{width:130px;height:130px;left:-65px;bottom:3px}
.c4{width:123px;height:123px;left:34px;bottom:-56px}
.c5{width:65px;height:65px;left:128px;bottom:-36px}
.logo{position:absolute;left:60%;top:-52px;transform:translateX(-50%);width:120px;height:120px;border-radius:50%;z-index:6;
  background:radial-gradient(circle at 30% 25%,#7aa5de,var(--olive));box-shadow:0 8px 18px rgba(0,0,0,.3);
  display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff}
.logo svg{width:56px;height:46px;margin-top:4px}
.logo b{font-size:1.05rem;letter-spacing:.5px;line-height:1.1;margin-top:2px}
.logo small{font-size:.4rem;line-height:1;margin-top:1px}
.social{display:flex;justify-content:center;gap:18px;margin-bottom:6px}
.social button{width:34px;height:34px;border:0;background:none;color:var(--ink);cursor:pointer;padding:0}
.social svg{width:100%;height:100%}
.hint{font-size:.68rem;color:var(--muted);margin:0 0 4px}
.or{display:flex;align-items:center;gap:10px;font-size:.6rem;color:var(--muted);margin:0 auto 18px;max-width:230px}
.or::before,.or::after{content:"";flex:1;height:1px;background:var(--olive-light)}
form{width:100%;max-width:320px;margin:0 auto}
.row{display:flex;align-items:center;margin-bottom:14px}
.ico{width:48px;height:38px;background:var(--olive-dark);border-radius:14px 0 0 14px;display:grid;place-items:center;color:#fff;flex:none}
.ico svg{width:24px;height:24px}
.field{flex:1;position:relative}
.field input{width:100%;height:38px;border:0;background:var(--field);border-radius:0 19px 19px 0;padding:0 40px 0 14px;font:inherit;font-size:.8rem;color:var(--ink)}
.field input::placeholder{color:var(--placeholder)}
.field input:focus-visible{outline:2px solid var(--olive-dark);outline-offset:1px}
.eye{position:absolute;right:8px;top:50%;transform:translateY(-50%);width:26px;height:20px;border:0;border-radius:8px;background:var(--olive-dark);color:#fff;cursor:pointer;padding:0;display:grid;place-items:center}
.eye svg{width:16px;height:16px}
.btn{font:inherit;font-weight:600;font-size:.85rem;letter-spacing:.5px;border-radius:999px;cursor:pointer;text-transform:uppercase}
.signin{background:var(--olive-dark);color:#fff;border:0;padding:9px 34px;margin-top:4px;box-shadow:0 3px 8px rgba(0,0,0,.25);transition:transform .15s}
.signin:active{transform:scale(.97)}
.register{background:transparent;color:#fff;border:2px solid #fff;padding:7px 28px}
.register:hover{background:rgba(255,255,255,.15)}
.btn:focus-visible{outline:3px solid #b9d3f5;outline-offset:2px}
.msg{min-height:1.2em;font-size:.75rem;margin:12px 0 0;color:#b3261e}
.msg.ok{color:var(--ink)}
@media (max-width:760px){
  .stage{grid-template-columns:1fr;margin-top:50px}
  .card{border-radius:28px 28px 0 0;padding:76px 24px 36px}
  .panel{border-radius:0 0 28px 28px;overflow:hidden}
  .logo{left:50%}
  .c1,.c2,.c3,.c4,.c5{display:none}
}
.register{display:inline-block;text-decoration:none}
</style>
</head>
<body>
<main class="stage">
  <div class="logo" aria-label="LMS Logo">
    <svg viewBox="0 0 64 52" fill="none" stroke="#fff" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" aria-hidden="true">
      <path d="M32 4 6 15l26 11 26-11L32 4Z" fill="#fff"/>
      <path d="M16 22v8c0 4 7 7 16 7s16-3 16-7v-8"/>
      <path d="M32 46c-7-5-11-8-11-12a6 6 0 0 1 11-3 6 6 0 0 1 11 3c0 4-4 7-11 12Z" fill="#7aa5de"/>
    </svg>
    <b>LMS</b><small>Learning Management System</small>
  </div>

  <section class="card">
    <h1>Login to Your Account</h1>
    <div class="social">
      <button type="button" aria-label="Login dengan Facebook"><svg viewBox="0 0 34 34" fill="none" stroke="currentColor" stroke-width="2"><circle cx="17" cy="17" r="14"/><path d="M18.5 28V18h3.2l.5-3.6h-3.7v-2.2c0-1 .4-1.7 1.8-1.7h2V7.4c-.4 0-1.500-.2-2.800-.2-2.800 0-4.600 1.700-4.600 4.800v2.400h-3.200V18h3.200v10" fill="currentColor" stroke="none"/></svg></button>
      <button type="button" aria-label="Login dengan Google"><svg viewBox="0 0 34 34" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round"><path d="M26 11a10 10 0 1 0 2 8h-10"/></svg></button>
      <button type="button" aria-label="Login dengan Threads"><svg viewBox="0 0 34 34" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="17" cy="17" r="14"/><path d="M22 14c-.6-2.500-2.400-3.800-5-3.800-3.200 0-5 2.200-5 6.800s1.800 6.800 5 6.800c2.600 0 4.400-1.200 4.400-3.300 0-2.300-2-3.200-4.400-3.200-1.800 0-3 .8-3 2s1 1.800 2.200 1.800c2 0 3.200-1.500 3.200-4.300"/></svg></button>
    </div>
    <p class="hint">Login using social networks</p>
    <div class="or">OR</div>

    <form id="loginForm" method="POST" action="{{ route('login.store') }}" novalidate>
      @csrf
      <div class="row">
        <span class="ico"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M2 5h20v14H2z" opacity=".0"/><path d="M3 6h18a1 1 0 0 1 1 1v.4l-10 6.100L2 7.400V7a1 1 0 0 1 1-1Zm-1 3.700 10 6.100 10-6.100V18a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V9.700Z"/></svg></span>
        <div class="field"><input id="email" name="email" type="email" placeholder="Email" value="{{ old('email') }}" autocomplete="email" aria-label="Email" required></div>
      </div>
      <div class="row">
        <span class="ico"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 10V7a5 5 0 0 1 10 0v3h1a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h1Zm2 0h6V7a3 3 0 0 0-6 0v3Zm3 4a1.500 1.500 0 0 0-.8 2.800V19h1.600v-2.200A1.500 1.500 0 0 0 12 14Z"/></svg></span>
        <div class="field">
          <input id="password" name="password" type="password" placeholder="Password" autocomplete="current-password" aria-label="Password" required>
          <button type="button" class="eye" id="toggle" aria-label="Tampilkan password"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button>
        </div>
      </div>
      <button type="submit" class="btn signin">Sign In</button>
      <p class="msg" id="msg" role="status" aria-live="polite">{{ $errors->first() }}</p>
    </form>
  </section>

  <aside class="panel">
    <span class="circle c1"></span><span class="circle c2"></span>
    <span class="circle c3"></span><span class="circle c4"></span><span class="circle c5"></span>
    <h2>New Here?</h2>
    <p>Sign up and discover a great amount of new opportunities!</p>
    {{-- <a href="{{ route('register') }}" class="btn register">Register</a> --}}
  </aside>
</main>

<script>
(function () {
  var form = document.getElementById('loginForm'),
      email = document.getElementById('email'),
      pass = document.getElementById('password'),
      msg = document.getElementById('msg'),
      toggle = document.getElementById('toggle');

  toggle.addEventListener('click', function () {
    var show = pass.type === 'password';
    pass.type = show ? 'text' : 'password';
    toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
  });

  form.addEventListener('submit', function (e) {
    var em = email.value.trim();
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) {
      e.preventDefault(); msg.textContent = 'Masukkan alamat email yang valid.'; email.focus(); return;
    }
    if (pass.value.length < 6) {
      e.preventDefault(); msg.textContent = 'Password minimal 6 karakter.'; pass.focus(); return;
    }
    // Validasi lolos: form dikirim ke server (route login.store)
  });
})();
</script>
</body>
</html>
