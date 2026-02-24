@php
  $logo = public_path('images/kaicek-logo.png'); // kalau ada logo
@endphp
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Formulir Inspeksi (Mentah)</title>
  <style>
    *{font-family: DejaVu Sans, Arial, Helvetica, sans-serif;}
    body{font-size:12px;color:#111}
    .head{display:flex;align-items:center;gap:10px;margin-bottom:10px}
    .brand{font-weight:700;font-size:16px}
    .muted{color:#666}
    .title{font-size:18px;font-weight:700;margin:8px 0 12px}
    table{border-collapse:collapse;width:100%}
    th,td{border:1px solid #ddd;padding:6px;vertical-align:top}
    .box{border:1px solid #ddd;height:16px;display:inline-block;width:16px;margin-right:6px}
    .note{border:1px solid #ddd;height:38px;margin-top:6px}
    .section{margin-top:12px}
  </style>
</head>
<body>

<div class="head">
  @if(file_exists($logo)) <img src="{{ $logo }}" height="28"> @endif
  <div>
    <div class="brand">KAICek</div>
    <div class="muted">Formulir Inspeksi Gudang — Mentahan</div>
  </div>
</div>

<div class="title">Informasi Umum</div>
<table>
  <tr><td width="34%"><strong>Plant</strong></td><td>{{ $meta['plant'] }}</td></tr>
  <tr><td><strong>SLOC</strong></td><td>{{ $meta['sloc'] }}</td></tr>
  <tr><td><strong>Periode</strong></td><td>{{ $meta['semester'] }}</td></tr>
  <tr><td><strong>Tahun</strong></td><td>{{ $meta['year'] }}</td></tr>
  <tr><td><strong>Nama Petugas</strong></td><td>{{ $meta['inspector'] }}</td></tr>
  <tr><td><strong>Dibuat</strong></td><td>{{ $meta['generatedAt']->format('d-m-Y H:i') }}</td></tr>
</table>

<div class="section">
  <div class="title" style="margin-top:6px">Butir Penilaian</div>
  <table>
    <thead>
      <tr>
        <th style="width:45%">Butir</th>
        <th style="width:22%">Nilai (pilih salah satu)</th>
        <th>Catatan</th>
      </tr>
    </thead>
    <tbody>
      @foreach($checklist->categories as $cat)
        <tr>
          <td colspan="3" style="background:#f8fafc;font-weight:700">{{ $cat->title }}</td>
        </tr>
        @foreach($cat->items as $it)
          <tr>
            <td>{{ $it->title }}</td>
            <td>
              <span class="box"></span>1
              <span class="box"></span>2
              <span class="box"></span>3
              <span class="box"></span>4
              <span class="box"></span>5
              <div class="muted" style="margin-top:4px">
                1: Sangat Tidak Sesuai · 2: Tidak Sesuai · 3: Cukup Sesuai · 4: Sesuai · 5: Sangat Sesuai
              </div>
            </td>
            <td>
              <div class="note"></div>
            </td>
          </tr>
        @endforeach
      @endforeach
    </tbody>
  </table>
</div>

<div class="section" style="margin-top:14px">
  <table>
    <tr>
      <td style="height:70px;width:50%">
        <strong>Tanda Tangan Petugas</strong>
        <div class="note" style="height:60px"></div>
      </td>
      <td style="height:70px">
        <strong>Catatan Tambahan</strong>
        <div class="note" style="height:60px"></div>
      </td>
    </tr>
  </table>
</div>

</body>
</html>
