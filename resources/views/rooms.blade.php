@extends('layout')

@section('title', 'Ketersediaan Ruang')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/rooms.css') }}">
@endpush

@section('content')
@php($days = [1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu',7=>'Minggu'])
<div class="heading">
    <div>
        <p class="eyebrow">GEDUNG F & G</p>
        <h1>Ketersediaan ruang</h1>
        <p class="muted">Lihat penggunaan ruang berdasarkan jadwal kelas mingguan.</p>
    </div>
</div>

<section class="panel">
    <form method="get" class="filters">
        <label>Hari
            <select name="day">
                @foreach($days as $num => $label)
                    <option value="{{ $num }}" @selected($day == $num)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>Waktu (WIB)
            <input type="time" name="time" value="{{ $time }}" required>
        </label>
        <label>Gedung
            <select name="building">
                <option value="">Semua gedung</option>
                @foreach(['F', 'G'] as $b)
                    <option @selected(request('building') == $b)>{{ $b }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit" class="button">Lihat ruang</button>
    </form>
    <p class="filter-note">Jadwal reguler dapat berbeda saat libur atau ujian.</p>
</section>

<div class="section-title">
    <h2>{{ $days[$day] }}, {{ $time }} WIB</h2>
    <span class="muted">{{ $rooms->count() }} ruang tetap</span>
</div>

@forelse($rooms->groupBy(fn ($room) => "Gedung {$room->building} · Lantai ".($room->floor ?? '—')) as $floor => $floorRooms)
<section class="floor-directory">
    <h3>{{ $floor }}</h3>
    @foreach($floorRooms as $room)
        @php($daily = $sessions->where('room', $room->code))
        @php($active = $daily->first(fn ($s) => $s->start_time <= $time && $s->end_time > $time))
        <article class="directory-row">
            <h4>{{ $room->code }}</h4>
            <span class="badge {{ $active ? 'busy' : '' }}">{{ $active ? 'Terjadwal' : 'Tidak terjadwal' }}</span>
            @if($active)
                <div class="current-class">
                    {{ $active->title }}
                    <small>{{ $active->start_time }} – {{ $active->end_time }} · Kelas {{ $active->class_group }}</small>
                </div>
            @else
                <p class="muted">Tidak ada kelas pada waktu ini.</p>
            @endif
            @if($daily->isNotEmpty())
                <details>
                    <summary aria-label="Lihat {{ $daily->count() }} sesi di {{ $room->code }}">{{ $daily->count() }} sesi</summary>
                    @foreach($daily as $session)
                        <p class="room-session">
                            <strong>{{ $session->start_time }} – {{ $session->end_time }}</strong><br>
                            {{ $session->title }} · {{ $session->class_group }}
                        </p>
                    @endforeach
                </details>
            @else
                <small class="muted">0 sesi</small>
            @endif
        </article>
    @endforeach
</section>
@empty
<p class="empty">Tidak ada ruang untuk pilihan ini.</p>
@endforelse
@endsection