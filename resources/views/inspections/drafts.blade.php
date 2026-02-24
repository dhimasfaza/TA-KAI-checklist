@extends('layouts.app')

@section('content')
<div class="container">
  <h1 style="font-size:24px;font-weight:700;margin-bottom:12px">Daftar Draft</h1>

  @if ($drafts->isEmpty())
    <p>Belum ada draft.</p>
  @else
    <div class="table-responsive">
      <table border="1" cellspacing="0" cellpadding="8" style="width:100%;border-collapse:collapse;background:#fff">
        <tr style="background:#f3f4f6">
          <th style="width:70px">ID</th>
          <th>Lokasi</th>
          <th style="width:120px">Tanggal</th>
          <th style="width:160px">Di-update</th>
          <th style="min-width:280px">Aksi</th>
        </tr>
        @foreach ($drafts as $d)
          <tr>
            <td>#{{ $d->id }}</td>
            <td>{{ $d->location->name ?? '-' }}</td>
            <td>{{ $d->visited_at?->format('d-m-Y') }}</td>
            <td>{{ $d->updated_at->format('d-m-Y H:i') }}</td>
            <td>
              <a class="btn" href="{{ route('inspections.edit',$d) }}">Lanjutkan Edit</a>
              <a class="btn" href="{{ route('inspections.export',$d) }}">Print (PDF)</a>
              <form action="{{ route('inspections.destroy',$d) }}" method="POST" style="display:inline"
                    onsubmit="return confirm('Hapus draft #{{ $d->id }}? Tindakan ini tidak bisa dibatalkan.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn danger">Hapus</button>
              </form>
            </td>
          </tr>
        @endforeach
      </table>
    </div>
  @endif

  <a class="btn" href="{{ route('inspections.create') }}" style="margin-top:14px">Buat Draft Baru</a>
</div>
@endsection
