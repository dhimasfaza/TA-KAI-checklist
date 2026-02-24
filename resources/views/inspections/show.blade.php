@extends('layouts.app')

@section('content')
@php
  $freq  = $inspection->movement_freq;
  $grade = null; $color = null;
  if (!is_null($freq)) {
    $f=(int)$freq;
    if ($f >= 4)      { $grade='Grade 1'; $color='#16a34a'; }
    elseif ($f >= 2)  { $grade='Grade 2'; $color='#f59e0b'; }
    else              { $grade='Grade 3'; $color='#dc2626'; }
  }
@endphp

<style>
.container{max-width:1000px;margin:0 auto;padding:16px}
.box{border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin:12px 0;background:#fff}
.badge{display:inline-block;padding:2px 8px;border-radius:999px;color:#fff;font-size:12px}
</style>

<div class="container">
  <h1 style="font-size:26px;font-weight:700">Detail Inspeksi</h1>

  <div class="box">
    <p><strong>Plant:</strong> {{ $inspection->location->code }} — {{ $inspection->location->name }}</p>
    <p><strong>SLOC:</strong> {{ $inspection->sloc ?? '-' }}</p>

    <p><strong>Periode:</strong>
      {{ $inspection->semester ? 'Semester '.$inspection->semester : '-' }}
      &nbsp;|&nbsp; <strong>Tahun:</strong> {{ $inspection->year ?? '-' }}
    </p>

    <p><strong>Saldo Awal (Rp):</strong> @if(!is_null($inspection->opening_balance)) Rp {{ number_format($inspection->opening_balance,0,',','.') }} @else - @endif</p>
    <p><strong>Pemasukan (Rp):</strong> @if(!is_null($inspection->income_total)) Rp {{ number_format($inspection->income_total,0,',','.') }} @else - @endif</p>
    <p><strong>Pengeluaran (Rp):</strong> @if(!is_null($inspection->expense_total)) Rp {{ number_format($inspection->expense_total,0,',','.') }} @else - @endif</p>
    <p><strong>Saldo Terakhir (Rp):</strong> @if(!is_null($inspection->closing_balance)) Rp {{ number_format($inspection->closing_balance,0,',','.') }} @else - @endif</p>

    <p><strong>Frekuensi Bergerak (per semester):</strong>
      {{ is_null($freq) ? '-' : $freq.'x' }}
      @if($grade) <span class="badge" style="background:{{ $color }};margin-left:6px">{{ $grade }}</span> @endif
    </p>

    <p><strong>Petugas:</strong> {{ $inspection->inspector_name }}</p>
    <p><strong>Skor Rata-rata:</strong> {{ $inspection->overall_score ?? '-' }}</p>
    <p><strong>Kesimpulan:</strong> {{ $inspection->overall_note ?? '-' }}</p>

    <p><a class="btn" href="{{ route('inspections.export',$inspection) }}">Download PDF</a></p>
  </div>

  <h3 style="margin:12px 0">Butir Penilaian</h3>
  @foreach ($inspection->items as $it)
    <div class="box">
      <p style="font-weight:700">{{ $it->item->title }}</p>
      <p>Nilai: {{ $it->rating ?? '-' }} | Catatan: {{ $it->note ?? '-' }}</p>
      @if ($it->photo_path_1 || $it->photo_path_2 || ($it->photos && $it->photos->count()))
        <div style="display:flex;gap:10px;flex-wrap:wrap">
          @if($it->photo_path_1)<img src="{{ asset('storage/'.$it->photo_path_1) }}" style="height:140px;border:1px solid #e5e7eb;border-radius:8px">@endif
          @if($it->photo_path_2)<img src="{{ asset('storage/'.$it->photo_path_2) }}" style="height:140px;border:1px solid #e5e7eb;border-radius:8px">@endif
          @foreach($it->photos ?? [] as $ph)
            <img src="{{ asset('storage/'.$ph->photo_path) }}" style="height:140px;border:1px solid #e5e7eb;border-radius:8px">
          @endforeach
        </div>
      @endif
    </div>
  @endforeach
</div>
@endsection
