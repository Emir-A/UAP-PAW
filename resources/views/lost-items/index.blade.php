@extends('layout')

@section('title', 'Laporan Barang Hilang')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/lost-items.css') }}">
@endpush

@section('content')
<div class="lost-page">
    <div class="lost-heading">
        <div>
            <p class="lost-eyebrow">GEDUNG F &amp; G · FILKOM</p>
            <h1>Laporan barang hilang</h1>
            <p class="lost-muted">
                Laporkan barang hilang atau tertinggal di sekitar ruang perkuliahan beserta lokasi dan ciri-cirinya.
            </p>
        </div>

        @auth
            <a class="button" href="{{ route('lost-items.create') }}">
                + Buat laporan
            </a>
        @else
            <a class="button" href="{{ route('login') }}">
                Masuk untuk melapor
            </a>
        @endauth
    </div>

    <section class="panel lost-filter-panel">
        <form method="GET" action="{{ route('lost-items.index') }}" class="lost-filters">
            <label class="lost-search">
                Cari barang / lokasi
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Nama barang, ciri-ciri, ruang, lantai…">
            </label>

            <label>
                Status
                <select name="status">
                    <option value="">Semua status</option>
                    <option value="hilang" @selected(request('status') === 'hilang')>Masih dicari</option>
                    <option value="ditemukan" @selected(request('status') === 'ditemukan')>Sudah ditemukan</option>
                </select>
            </label>

            <button type="submit" class="button">Terapkan</button>
            <a class="lost-reset" href="{{ route('lost-items.index') }}">Reset</a>
        </form>
    </section>

    <div class="lost-section-title">
        <h2>Daftar laporan</h2>
        <span class="lost-muted">{{ $items->count() }} laporan tercatat</span>
    </div>

    @forelse($items as $item)
        <article class="lost-card">
            @if($item->photo_path)
                <img
                    class="lost-photo"
                    src="{{ asset($item->photo_path) }}"
                    alt="Foto {{ $item->item_name }}">
            @endif

            <div class="lost-body">
                <div class="lost-top">
                    <span class="lost-badge {{ $item->status === 'ditemukan' ? 'found' : 'missing' }}">
                        {{ $item->status === 'ditemukan' ? '✓ Sudah ditemukan' : 'Masih dicari' }}
                    </span>

                    <span class="lost-location">
                        Lokasi: {{ $item->location }}
                    </span>

                    <span class="lost-muted lost-time">
                        Waktu hilang: {{ $item->lost_at->format('d M Y, H:i') }} WIB
                    </span>
                </div>

                <h3>{{ $item->item_name }}</h3>
                <p class="lost-desc">{{ $item->description }}</p>
                <small class="lost-muted">
                    Dilaporkan oleh {{ $item->user?->name ?? 'Mahasiswa' }}
                </small>
            </div>

            @auth
                <div class="lost-actions">
                    @if($item->status !== 'ditemukan')
                        <form method="POST" action="{{ route('lost-items.found', $item->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="button lost-success">
                                ✓ Tandai selesai
                            </button>
                        </form>
                    @endif

                    <a class="button lost-secondary" href="{{ route('lost-items.edit', $item->id) }}">
                        Ubah
                    </a>

                    <form method="POST" action="{{ route('lost-items.destroy', $item->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button lost-danger">
                            Hapus
                        </button>
                    </form>
                </div>
            @endauth
        </article>
    @empty
        <div class="panel lost-empty">
            <h2>Belum ada laporan barang hilang</h2>
            <p class="lost-muted">Jika Anda kehilangan atau menemukan barang tertinggal di ruang kelas, tambahkan laporan baru.</p>
            @auth
                <a class="button" href="{{ route('lost-items.create') }}">
                    + Buat laporan pertama
                </a>
            @endauth
        </div>
    @endforelse
</div>
@endsection