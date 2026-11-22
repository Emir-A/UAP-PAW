@extends('layout')
@section('title', 'Detail Laporan')
@section('heading', $report->title)
@section('content')
<section class="panel">
    <p class="text-muted">
        {{ $report->room }} · {{ $report->categoryLabel() }}
        · {{ $report->created_at->format('d M Y, H:i') }}
    </p>
    <p>Status: <strong>{{ $report->statusLabel() }}</strong></p>
    <p>{!! nl2br(e($report->description)) !!}</p>
    @if($report->photo_path)
    <img src="{{ asset('storage/'.$report->photo_path) }}" alt="Foto bukti laporan" style="max-width:100%">
    @endif
    <p>
        <a href="{{ route('reports.edit', $report->id) }}">Ubah</a>
        ·
        <form
            method="POST"
            action="{{ route('reports.destroy', $report->id) }}"
            style="display:inline"
            onsubmit="return confirm('Hapus laporan ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="button">Hapus</button>
        </form>
        ·
        <a href="{{ route('reports.index') }}">Kembali</a>
    </p>
</section>
@endsection