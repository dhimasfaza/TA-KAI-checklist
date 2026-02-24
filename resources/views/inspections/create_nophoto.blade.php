@extends('layouts.app')

@section('content')
@php
  $isEdit   = isset($inspection) && $inspection;
  $existing = $existing ?? collect();
  $ratingLabels = [1=>'Sangat Tidak Sesuai',2=>'Tidak Sesuai',3=>'Cukup Sesuai',4=>'Sesuai',5=>'Sangat Sesuai'];
@endphp

<style>
.container{max-width:1100px;margin:0 auto;padding:16px}
.h1{font-size:26px;font-weight:700;margin-bottom:8px}
.row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
@media(max-width:900px){.row{grid-template-columns:1fr}}
.label{font-size:14px;margin-bottom:6px;display:block}
.inp, select, textarea{width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px;background:#fff}
.box{border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin:16px 0;background:#fff}
.badge{display:inline-block;background:#111827;color:#fff;padding:2px 8px;border-radius:999px;font-size:12px;margin-bottom:8px}
.item{border:1px solid #e5e7eb;border-radius:12px;padding:12px;margin-top:12px}
.grid3{display:grid;grid-template-columns:5fr 2fr 3fr;gap:12px;align-items:start}
.legend{font-size:12px;color:#6b7280;margin-top:4px}
.actions{display:flex;gap:10px;flex-wrap:wrap}
.btn{border:1px solid #d1d5db;background:#fff;border-radius:10px;padding:10px 14px}
.btn.primary{background:#0ea5e9;color:#fff;border-color:#0ea5e9}
.btn.danger{background:#ef4444;color:#fff;border-color:#ef4444}
.sticky{position:sticky;bottom:0;background:#ffffffd9;backdrop-filter:saturate(180%) blur(6px);padding:10px;border-top:1px solid #e5e7eb;margin-top:8px}
.notice{background:#e0f2fe;border:1px solid #bae6fd;color:#075985;border-radius:10px;padding:10px;margin-bottom:10px}
</style>

<div class="container">
  <h1 class="h1">{{ $isEdit ? 'Edit Inspeksi (Tanpa Foto)' : 'Form Inspeksi (Tanpa Foto)' }}</h1>

  <div class="notice">Mode ini tidak memiliki kolom upload foto. Kamu tetap bisa simpan draft atau kirim & unduh PDF.</div>

  <div class="actions" style="margin-bottom:10px">
    <a class="btn" href="{{ route('inspections.drafts') }}">Daftar Draft</a>
    @if($isEdit)
      <a class="btn primary" href="{{ route('inspections.export', $inspection) }}" target="_blank" rel="noopener">Download PDF (Draft)</a>
      <form action="{{ route('inspections.destroy',$inspection) }}" method="POST" onsubmit="return confirm('Hapus draft ini?')" style="display:inline">
        @csrf @method('DELETE')
        <button type="submit" class="btn danger">Hapus Draft</button>
      </form>
    @endif
  </div>

  <form method="POST"
        action="{{ $isEdit ? route('inspections.update_nophoto',$inspection) : route('inspections.store_nophoto') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="row">
      <div>
        <label class="label">Plant</label>
        <select name="location_id" required>
          <option value="">-- pilih plant --</option>
          @foreach ($locations as $loc)
            <option value="{{ $loc->id }}" {{ $isEdit && $inspection->location_id==$loc->id ? 'selected':'' }}>
              {{ $loc->code }} — {{ $loc->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">SLOC</label>
        <input type="text" name="sloc" class="inp" placeholder="contoh: 1101"
               value="{{ $isEdit ? $inspection->sloc : '' }}">
      </div>
      <div>
        <label class="label">Periode</label>
        <select name="semester" required>
          <option value="">-- pilih semester --</option>
          <option value="1" {{ $isEdit && $inspection->semester==1 ? 'selected':'' }}>Semester 1</option>
          <option value="2" {{ $isEdit && $inspection->semester==2 ? 'selected':'' }}>Semester 2</option>
        </select>
      </div>
      <div>
        <label class="label">Tahun</label>
        <input type="number" name="year" class="inp" min="2000" max="2100" placeholder="2025"
               value="{{ $isEdit ? $inspection->year : '' }}" required>
      </div>
    </div>

    <div class="row" style="margin-top:10px">
      <div>
        <label class="label">Saldo Awal (Rp)</label>
        <input type="number" name="opening_balance" class="inp" min="0" value="{{ $isEdit ? $inspection->opening_balance : '' }}">
      </div>
      <div>
        <label class="label">Pemasukan (Rp)</label>
        <input type="number" name="income_total" class="inp" min="0" value="{{ $isEdit ? $inspection->income_total : '' }}">
      </div>
      <div>
        <label class="label">Pengeluaran (Rp)</label>
        <input type="number" name="expense_total" class="inp" min="0" value="{{ $isEdit ? $inspection->expense_total : '' }}">
      </div>
      <div>
        <label class="label">Saldo Terakhir (Rp)</label>
        <input type="number" name="closing_balance" class="inp" min="0" value="{{ $isEdit ? $inspection->closing_balance : '' }}">
      </div>
    </div>

    <div class="row" style="margin-top:10px">
      <div>
        <label class="label">Frekuensi Bergerak (per semester)</label>
        <input type="number" name="movement_freq" class="inp" min="0" placeholder="mis. 3"
               value="{{ $isEdit ? $inspection->movement_freq : '' }}">
        <div class="legend">≥4x = Grade 1 (Hijau) • 2–3x = Grade 2 (Kuning) • 0–1x = Grade 3 (Merah)</div>
      </div>
      <div>
        <label class="label">Nama Petugas</label>
        <input type="text" name="inspector_name" class="inp" required value="{{ $isEdit ? $inspection->inspector_name : '' }}">
      </div>
      <div style="grid-column: span 2">
        <label class="label">Kesimpulan</label>
        <textarea name="overall_note" rows="3" class="inp">{{ $isEdit ? $inspection->overall_note : '' }}</textarea>
      </div>
    </div>

    @foreach ($checklist->categories as $cat)
      <div class="box">
        <span class="badge">{{ $cat->title }}</span>

        @foreach ($cat->items as $it)
          @php $cur = $existing[$it->id] ?? null; @endphp
          <div class="item">
            <div class="grid3">
              <div>
                <p style="font-weight:600">{{ $it->title }}</p>
                @if ($it->hint ?? false)
                  <p class="legend">{{ $it->hint }}</p>
                @endif
              </div>

              <div>
                <label class="label">Nilai (1–5)</label>
                <select name="items[{{ $it->id }}][rating]">
                  <option value="">-</option>
                  @for ($i=1; $i<=5; $i++)
                    <option value="{{ $i }}" {{ ($cur && $cur->rating==$i)?'selected':'' }}>
                      {{ $i }} — {{ $ratingLabels[$i] }}
                    </option>
                  @endfor
                </select>
                <div class="legend">1: Sangat Tidak Sesuai · 2: Tidak Sesuai · 3: Cukup Sesuai · 4: Sesuai · 5: Sangat Sesuai</div>
              </div>

              <div>
                <label class="label">Catatan</label>
                <input type="text" name="items[{{ $it->id }}][note]" class="inp" value="{{ $cur->note ?? '' }}">
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endforeach

    <div class="sticky actions">
      <button type="submit" name="save" class="btn">{{ $isEdit ? 'Simpan Perubahan (Draft)' : 'Simpan Draft' }}</button>
      <button type="submit" name="submit" class="btn primary">Kirim & Download PDF</button>
    </div>
  </form>
</div>
@endsection
