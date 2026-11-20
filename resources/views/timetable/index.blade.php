@extends('layout')

@section('title', 'Jadwal Pribadi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/timetable.css') }}">
@endpush

@section('content')
@php
$days = [
1 => 'Senin',
2 => 'Selasa',
3 => 'Rabu',
4 => 'Kamis',
5 => 'Jumat',
6 => 'Sabtu',
7 => 'Minggu',
];
@endphp

<div class="timetable-page">
    <div class="timetable-heading">
        <div>
            <h1>Jadwal pribadi</h1>
            <p class="timetable-muted">
                {{ $entries->count() }} sesi dalam jadwal mingguan Anda.
            </p>
        </div>

        <a class="button" href="{{ route('timetable.create') }}">
            + Tambah kelas
        </a>
    </div>

    @forelse($entries->groupBy(fn ($entry) => $entry->courseSession->weekday) as $day => $daily)
    <section aria-labelledby="day-{{ $day }}">
        <div class="timetable-day-heading">
            <h2 id="day-{{ $day }}">{{ $days[$day] }}</h2>
            <span class="timetable-muted">{{ $daily->count() }} sesi</span>
        </div>

        @foreach($daily as $entry)
        @php($class = $entry->courseSession)

        <article class="timetable-row">
            <div class="timetable-time">
                <strong>{{ $class->start_time }}</strong>
                <span>{{ $class->end_time }}</span>
            </div>

            <div class="timetable-course">
                <h3>{{ $class->title }}</h3>
                <p>{{ $class->program }} · Kelas {{ $class->class_group }}</p>
                <small class="timetable-muted">{{ $class->lecturer }}</small>
            </div>

            <span
                class="timetable-room"
                aria-label="Ruang {{ $class->room }}">
                {{ $class->room }}
            </span>

            <div class="timetable-actions">
                <a
                    class="button timetable-secondary"
                    href="{{ route('timetable.edit', $entry->id) }}"
                    aria-label="Ganti kelas {{ $class->title }}">
                    Ganti kelas
                </a>

                <form
                    method="POST"
                    action="{{ route('timetable.destroy', $entry->id) }}">
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="button timetable-danger"
                        aria-label="Hapus {{ $class->title }} dari jadwal">
                        Hapus dari jadwal
                    </button>
                </form>
            </div>
        </article>
        @endforeach
    </section>
    @empty
    <div class="timetable-empty">
        <svg
            aria-hidden="true"
            viewBox="0 0 24 24"
            width="20"
            height="20"
            fill="none"
            stroke="currentColor"
            stroke-width="1.6"
            stroke-linejoin="round"
            stroke-linecap="round">
            <path d="M5 3h14v18l-7-4-7 4z" />
        </svg>

        <h2>Mulai susun jadwal Anda</h2>

        <p>Tambahkan kelas anda di semester ini.</p>

        <a class="button" href="{{ route('timetable.create') }}">
            Buka jadwal kelas →
        </a>
    </div>
    @endforelse
</div>
@endsection