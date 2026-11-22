@extends('layout')
@section('title', 'Laporan Saya')
@section('heading', 'Laporan ruangan saya')
@section('description')
    Riwayat laporan yang pernah Anda kirim.
@endsection
@section('content')
<section class="panel">
    <p>
        <a class="button" href="{{ route('reports.create') }}">
            + Buat laporan
        </a>
    </p>
    @forelse($reports as $report)
    <article class="report-item">
        <h2>
            <a href="{{ route('reports.show', $report->id) }}">
                {{ $report->title }}
            </a>
        </h2>
        <p class="text-muted">
            {{ $report->room }} · {{ $report->categoryLabel() }}
            · {{ $report->created_at->format('d M Y, H:i') }}
        </p>
        <p>
            Status: <strong>{{ $report->statusLabel() }}</strong>
        </p>
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
        </p>
    </article>
    @empty
    <p class="text-muted">Belum ada laporan yang dikirim.</p>
    @endforelse
</section>
@endsection