@extends('layout')

@section('title', 'Jadwal Kelas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/catalog.css') }}">
@endpush

@section('content')
@php($days = [1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu',7=>'Minggu'])
<header class="hero">
    <div>
        <p class="kicker">Fakultas Ilmu Komputer · Gedung F &amp; G</p>
        <h1>{{ $replacement ? 'Ganti pilihan kelas' : 'Jadwal perkuliahan' }}</h1>
        <p>Cari mata kuliah dan tambahkan sesi pilihan ke jadwal pribadi Anda.</p>
    </div>
</header>

@if($replacement)
    <div class="notice">
        Pilih kelas pengganti. Hanya pilihan pribadi yang berubah.
        <a href="{{ route('timetable.index') }}">Batal mengganti</a>
    </div>
@endif

<section class="panel">
    <form class="filters" method="get" action="{{ route('classes') }}">
        @if($replacement)
            <input type="hidden" name="replace" value="{{ $replacement->id }}">
        @endif
        <label class="search">Cari kelas
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Mata kuliah, ruang, kelas, dosen…" maxlength="100">
        </label>
        <label>Hari
            <select name="day">
                <option value="">Semua hari</option>
                @foreach($days as $num => $label)
                    <option value="{{ $num }}" @selected(request('day') == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>Gedung
            <select name="building">
                <option value="">Semua gedung</option>
                @foreach(['F', 'G'] as $b)
                    <option @selected(request('building') == $b)>{{ $b }}</option>
                @endforeach
            </select>
        </label>
        <label>Program studi
            <select name="program">
                <option value="">Semua prodi</option>
                @foreach($programs as $program)
                    <option @selected(request('program') == $program)>{{ $program }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit" class="button">Terapkan</button>
        <a class="link" href="{{ route('classes', $replacement ? ['replace' => $replacement->id] : []) }}">Reset</a>
    </form>
</section>

<div class="section-title">
    <h2>Daftar kelas</h2>
    <span class="muted">{{ number_format($classes->total(), 0, ',', '.') }} sesi ditemukan</span>
</div>

<div class="catalog-list">
    <div class="column-head" aria-hidden="true">
        <span>Hari &amp; waktu (WIB)</span>
        <span>Mata kuliah</span>
        <span>Ruang / kelas</span>
        <span>Jadwal pribadi</span>
    </div>
    @forelse($classes as $class)
        <article class="catalog-row card">
            <div class="slot">
                <strong>{{ $days[$class->weekday] }}</strong>
                <span>{{ $class->start_time }}–{{ $class->end_time }}</span>
            </div>
            <div class="subject">
                <h3>{{ $class->title }}</h3>
                <p>{{ $class->program }}</p>
                <small>{{ $class->lecturer }}</small>
            </div>
            <div class="location">
                <strong>{{ $class->room }}</strong>
                <small>Kelas {{ $class->class_group }}</small>
            </div>
            @auth
                @if(in_array($class->id, $selected) && (!$replacement || $replacement->course_session_id !== $class->id))
                    <span class="saved">✓ Sudah ditambahkan</span>
                @else
                    <form method="post" action="{{ $replacement ? route('timetable.update', $replacement->id) : route('timetable.store') }}">
                        @csrf
                        @if($replacement)
                            @method('PUT')
                        @endif
                        <input type="hidden" name="course_session_id" value="{{ $class->id }}">
                        <button class="button secondary wide" type="submit">
                            {{ $replacement ? 'Pilih sebagai pengganti' : '+ Tambah ke jadwal' }}
                        </button>
                    </form>
                @endif
            @else
                <a class="button secondary wide" href="{{ route('login') }}">Masuk untuk tambah</a>
            @endauth
        </article>
    @empty
        <div class="empty">
            <h3>Tidak ada kelas yang cocok</h3>
            <p>Coba kata kunci lain atau reset filter.</p>
        </div>
    @endforelse
</div>

<nav class="pagination" aria-label="Halaman daftar kelas">
    @if($classes->previousPageUrl())
        <a class="button secondary" href="{{ $classes->previousPageUrl() }}">← Sebelumnya</a>
    @endif
    <span>Halaman {{ $classes->currentPage() }} / {{ $classes->lastPage() }}</span>
    @if($classes->hasMorePages())
        <a class="button secondary" href="{{ $classes->nextPageUrl() }}">Berikutnya →</a>
    @endif
</nav>

<p class="footnote">Sumber: jadwal yang diberikan pada project DPSI. Tampilan mengasumsikan pengulangan mingguan; hari libur, ujian, dan perubahan semester belum diterapkan.</p>
@endsection