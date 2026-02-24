@php
$logo = public_path('images/kaicek-logo.png'); // optional
function imgPath($rel){ return $rel ? public_path('storage/'.$rel) : null; }
@endphp
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Laporan Inspeksi Gudang</title>
<style>
  *{font-family: DejaVu Sans, Arial, Helvetica, sans-serif;}
  body{font-size:12px;color:#111}
  .head{display:flex;align-items:center;gap:10px;margin-bottom:10px}
  .brand{font-weight:700;font-size:16px}
  .muted{color:#666}
  .title{font-size:18px;font-weight:700;margin:8px 0 12px}
  table{border-collapse:collapse;width:100%}
  th,td{border:1px solid #ddd;padding:6px;vertical-align:top}
  .chip{display:inline-block;padding:2px 8px;border-radius:999px;color:#fff;font-size:11px}
  .chip-green{background:#16a34a}.chip-yellow{background:#f59e0b}.chip-red{background:#dc2626}.chip-blue{background:#0ea5e9}
  .section{margin-top:12px}
  .page-break{page-break-after: always;}
  .img{width:100%;height:auto;max-height:720px;object-fit:contain;border:1px solid #ddd;margin:6px 0}
</style>
</head>
<body>

<div class="head">
  @if(file_exists($logo)) <img src="{{ $logo }}" height="28"> @endif
  <div><div class="brand">KAICek</div><div class="muted">Cek gudang sekejap</div></div>
</div>

<div class="title">Laporan Inspeksi Gudang</div>

<table>
  <tr><td width="34%"><strong>Plant</strong></td><td>{{ $inspection->location->code ?? '-' }} — {{ $inspection->location->name ?? '-' }}</td></tr>
  <tr><td><strong>SLOC</strong></td><td>{{ $inspection->sloc ?? '-' }}</td></tr>

  <tr><td><strong>Periode</strong></td><td>{{ $inspection->semester ? 'Semester '.$inspection->semester : '-' }}</td></tr>
  <tr><td><strong>Tahun</strong></td><td>{{ $inspection->year ?? '-' }}</td></tr>

  <tr><td><strong>Saldo Awal (Rp)</strong></td><td>@if(!is_null($inspection->opening_balance)) Rp {{ number_format($inspection->opening_balance,0,',','.') }} @else - @endif</td></tr>
  <tr><td><strong>Pemasukan (Rp)</strong></td><td>@if(!is_null($inspection->income_total)) Rp {{ number_format($inspection->income_total,0,',','.') }} @else - @endif</td></tr>
  <tr><td><strong>Pengeluaran (Rp)</strong></td><td>@if(!is_null($inspection->expense_total)) Rp {{ number_format($inspection->expense_total,0,',','.') }} @else - @endif</td></tr>
  <tr><td><strong>Saldo Terakhir (Rp)</strong></td><td>@if(!is_null($inspection->closing_balance)) Rp {{ number_format($inspection->closing_balance,0,',','.') }} @else - @endif</td></tr>

  <tr>
    <td><strong>Frekuensi Bergerak (per semester)</strong></td>
    <td>
      @php
        $freq = $inspection->movement_freq;
        $label=null; $cls=null;
        if (!is_null($freq)) {
          $f=(int)$freq;
          if ($f >= 4)      { $label='Grade 1'; $cls='chip-green'; }
          elseif ($f >= 2)  { $label='Grade 2'; $cls='chip-yellow'; }
          else              { $label='Grade 3'; $cls='chip-red'; }
        }
      @endphp
      @if(is_null($freq)) - @else {{ $freq }}x @if($label) <span class="chip {{ $cls }}" style="margin-left:6px">{{ $label }}</span> @endif @endif
    </td>
  </tr>

  <tr><td><strong>Petugas</strong></td><td>{{ $inspection->inspector_name }}</td></tr>
  <tr>
    <td><strong>Skor Rata-rata</strong></td>
    <td>
      @if(!is_null($avgScore))
        {{ $avgScore }} ({{ $percentScore }}%)
        <span class="chip chip-blue">{{ $statusLabel }}</span>
      @else - @endif
    </td>
  </tr>
  <tr><td><strong>Kesimpulan</strong></td><td>{{ $inspection->overall_note ?? '-' }}</td></tr>
  <tr><td><strong>Dibuat</strong></td><td>{{ $generatedAt->format('d-m-Y H:i') }}</td></tr>
</table>

<div class="section">
  <h3 style="margin:8px 0">Hasil Penilaian</h3>
  <table>
    <thead><tr><th style="width:45%">Butir</th><th style="width:15%">Nilai</th><th>Catatan</th></tr></thead>
    <tbody>
      @foreach($inspection->items as $it)
        <tr>
          <td>{{ $it->item->title }}</td>
          <td>{{ $it->rating ?? '-' }} @if($it->rating) — {{ $ratingLabels[$it->rating] ?? '' }} @endif</td>
          <td>{{ $it->note ?? '-' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

@foreach($inspection->items as $it)
  @php
    $p1 = imgPath($it->photo_path_1);
    $p2 = imgPath($it->photo_path_2);
    $extras = $it->photos ?? collect();
  @endphp
  @if(($p1 && file_exists($p1)) || ($p2 && file_exists($p2)) || $extras->count())
    <div class="page-break"></div>
    <h3>Lampiran Foto — {{ $it->item->title }}</h3>

    @if($p1 && file_exists($p1)) <div><strong>Foto 1 (depan)</strong></div><img class="img" src="{{ $p1 }}"> @endif
    @if($p2 && file_exists($p2)) <div><strong>Foto 2 (samping)</strong></div><img class="img" src="{{ $p2 }}"> @endif

    @if($extras->count())
      <div><strong>Foto Tambahan</strong></div>
      @foreach($extras as $ph)
        @php $px = imgPath($ph->photo_path); @endphp
        @if($px && file_exists($px)) <img class="img" src="{{ $px }}"> @endif
      @endforeach
    @endif
  @endif
@endforeach

</body>
</html>
