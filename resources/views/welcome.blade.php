<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>KAICek</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    body{font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Arial; background:#f3f4f6; color:#0f172a;}
    .wrap{max-width:640px; margin:60px auto; text-align:center; background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:28px;}
    a{color:#0ea5e9; text-decoration:none}
    a:hover{text-decoration:underline}
  </style>
</head>
<body>
  <div class="wrap">
    <h1 style="font-size:28px; font-weight:800; margin:0 0 6px">KAICek</h1>
    <p style="margin:0 0 16px">Cek gudang sekejap</p>
    <p><a href="{{ route('login') }}">Masuk</a> · <a href="{{ route('register') }}">Daftar</a></p>
  </div>
</body>
</html>
