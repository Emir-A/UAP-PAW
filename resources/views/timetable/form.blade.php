@extends('layout')

@section('title', $entry ? 'Ganti Kelas' : 'Tambah Kelas')
@section('heading', $entry ? 'Ganti pilihan kelas' : 'Tambah kelas')

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

<section class="panel">
    @if($classes->isEmpty())
    <p>Data kelas belum tersedia. Hubungi tim pengelola data.</p>
    @else
    <form
        method="POST"
        action="{{ $entry
                    ? route('timetable.update', $entry->id)
                    : route('timetable.store') }}">
        @csrf

        @if($entry)
        @method('PUT')
        @endif

        <div class="form-field">
            <label for="course_session_id">Sesi kelas</label>

            <select
                id="course_session_id"
                name="course_session_id"
                required>
                <option value="">Pilih sesi kelas</option>

                @foreach($classes as $class)
                <option
                    value="{{ $class->id }}"
                    @selected(
                    old( 'course_session_id' ,
                    $entry?->course_session_id
                    ) == $class->id
                    )
                    >
                    {{ $class->title }} —
                    {{ $class->class_group }} —
                    {{ $days[$class->weekday] }}
                    {{ $class->start_time }}–{{ $class->end_time }}
                    — {{ $class->room }}
                </option>
                @endforeach
            </select>

            @error('course_session_id')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="button">
            Simpan
        </button>
    </form>
    @endif

    <p>
        <a href="{{ route('timetable.index') }}">Kembali</a>
    </p>
</section>
@endsection