<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>KAICek</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    :root{
      --kai-blue-1:#0b4a82; --kai-blue-2:#0a3a66;
      --kai-bg:#f3f4f6; --kai-text:#0f172a; --kai-muted:#6b7280;
      --kai-primary:#0ea5e9; --kai-danger:#ef4444;
    }
    html,body{background:var(--kai-bg);color:var(--kai-text)}
    .navbar{background:linear-gradient(120deg,var(--kai-blue-1),var(--kai-blue-2)); color:#fff; box-shadow:0 2px 6px rgba(0,0,0,.18)}
    .wrap{max-width:1120px;margin:0 auto;display:flex;align-items:center;gap:16px;padding:10px 16px}
    .brand{display:flex;align-items:center;gap:10px}
    .logo{width:28px;height:28px}
    .ttl{font-weight:800;letter-spacing:.5px}
    .sub{font-size:12px;opacity:.85;margin-top:-2px}
    .nav{display:flex;gap:6px;margin-left:auto}
    .nav a{color:#fff;text-decoration:none;padding:8px 12px;border-radius:10px}
    .nav a:hover{background:rgba(255,255,255,.12)}
    .nav a[aria-current="page"]{background:rgba(255,255,255,.22)}
    .account{display:flex;gap:8px;align-items:center;margin-left:8px}
    .btn{--bg:#fff;--bd:#d1d5db;--fg:#0f172a;
         background:var(--bg);border:1px solid var(--bd);color:var(--fg);
         padding:8px 12px;border-radius:10px;text-decoration:none;display:inline-flex;align-items:center;gap:8px}
    .btn.ghost{--bg:transparent;--bd:rgba(255,255,255,.35);--fg:#fff}
    .btn.danger{--bg:#ef4444;--bd:#ef4444;--fg:#fff}
    .container{max-width:1120px;margin:0 auto;padding:18px}
  </style>
</head>
<body>
  <header class="navbar">
    <div class="wrap">
      @php
        $logoSvg = public_path('images/kaicek-logo.svg');
        $logoPng = public_path('images/kaicek-logo.png');
      @endphp
      <div class="brand">
        @if (file_exists($logoSvg))
          <img src="{{ asset('images/kaicek-logo.svg') }}" class="logo" alt="KAICek">
        @elseif (file_exists($logoPng))
          <img src="{{ asset('images/kaicek-logo.png') }}" class="logo" alt="KAICek">
        @else
          <span class="ttl">K</span>
        @endif
        <div>
          <div class="ttl">KAICek</div>
          <div class="sub">Cek gudang sekejap</div>
        </div>
      </div>

      <nav class="nav">
        <a href="{{ route('inspections.create') }}" aria-current="{{ request()->routeIs('inspections.create') ? 'page':'false' }}">Inspeksi</a>
        <a href="{{ route('inspections.create_nophoto') }}" aria-current="{{ request()->routeIs('inspections.create_nophoto') ? 'page':'false' }}">Tanpa Foto</a>
        <a href="{{ route('inspections.drafts') }}" aria-current="{{ request()->routeIs('inspections.drafts') ? 'page':'false' }}">Draft</a>
        <a href="{{ route('inspections.blank') }}" aria-current="{{ request()->routeIs('inspections.blank') ? 'page':'false' }}">Form Mentah (PDF)</a>
      </nav>

      <div class="account">
        @auth
          <span>{{ auth()->user()->name }}</span>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn ghost" type="submit">Keluar</button>
          </form>
        @else
          <a class="btn ghost" href="{{ route('login') }}">Masuk</a>
          <a class="btn ghost" href="{{ route('register') }}">Daftar</a>
        @endauth
      </div>
    </div>
  </header>

  <main class="container">
    @yield('content')
  </main>
</body>
</html>
