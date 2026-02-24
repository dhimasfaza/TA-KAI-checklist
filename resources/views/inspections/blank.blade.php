@extends('layouts.app')

@section('content')
<style>
  .container{max-width:900px;margin:0 auto}
  .h1{font-size:26px;font-weight:700;margin-bottom:12px}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  @media(max-width:900px){.grid{grid-template-columns:1fr}}
  .label{display:block;margin-bottom:6px;font-size:14px}
  .inp, select{width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px;background:#fff}
  .btn{border:1px solid #0ea5e9;background:#0ea5e9;color:#fff;border-radius:10px;padding:10px 14px}
  .muted{color:#6b7280;font-size:13px}
  .card{border:1px solid #e5e7eb;border-radius:12px;padding:16px;background:#fff}
</style>

<div class="container">
  <h1 class="h1">Download Formulir Mentahan</h1>
  <p class="muted" style="margin-bottom:12px">
    Formulir ini <strong>tanpa kolom upload foto</strong>. Cocok untuk dicetak lalu diisi manual di lapangan.
  </p>

  <div class="card">
    <form method="GET" action="{{ route('inspections.blank.export') }}">
      <div class="grid">
        <div>
          <label class="label">Plant (opsional)</label>
          <select name="location_id">
            <option value="">— pilih plant —</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}">{{ $loc->code }} — {{ $loc->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">SLOC (opsional)</label>
          <input type="text" name="sloc" class="inp" placeholder="contoh: 1101">
        </div>
        <div>
          <label class="label">Periode (opsional)</label>
          <select name="semester">
            <option value="">— pilih semester —</option>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
          </select>
        </div>
        <div>
          <label class="label">Tahun (opsional)</label>
          <input type="number" name="year" class="inp" min="2000" max="2100" placeholder="2025">
        </div>
        <div style="grid-column:1 / -1">
          <label class="label">Nama Petugas (opsional)</label>
          <input type="text" name="inspector_name" class="inp" placeholder="tuliskan nama petugas">
        </div>
      </div>

      <div style="margin-top:14px;display:flex;gap:10px;align-items:center">
        <button type="submit" class="btn">Download Formulir Mentahan (PDF)</button>
        <span class="muted">Checklist aktif: <strong>{{ $checklist->name }}</strong></span>
      </div>
    </form>
  </div>
</div>
@endsection
