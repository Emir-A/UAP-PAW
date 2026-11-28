@extends('layout')

@section('title', $assignment ? 'Ubah Tugas Kuliah' : 'Tambah Tugas Kuliah')
@section('heading', $assignment ? 'Ubah tugas kuliah' : 'Tambah tugas kuliah')
@section('description', 'Isi judul tugas, mata kuliah, deskripsi, tenggat waktu, serta status pengerjaannya.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/assignments.css') }}">
@endpush

@section('content')
<section class="panel task-form-panel">
    <form
        method="POST"
        action="{{ $assignment ? route('assignments.update', $assignment->id) : route('assignments.store') }}">
        @csrf

        @if($assignment)
            @method('PUT')
        @endif

        <div class="form-field">
            <label for="title">Judul tugas</label>
            <input
                id="title"
                type="text"
                name="title"
                value="{{ old('title', $assignment?->title) }}"
                placeholder="Contoh: Laporan Praktikum Modul 4 / Proyek Akhir CRUD"
                maxlength="150"
                required>
            @error('title')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="course_name">Mata kuliah</label>
            <input
                id="course_name"
                type="text"
                name="course_name"
                list="course-options"
                value="{{ old('course_name', $assignment?->course_name) }}"
                placeholder="Contoh: Pengembangan Aplikasi Web"
                maxlength="120"
                required>
            @if($courses->isNotEmpty())
                <datalist id="course-options">
                    @foreach($courses as $course)
                        <option value="{{ $course }}"></option>
                    @endforeach
                </datalist>
            @endif
            @error('course_name')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="due_at">Tenggat waktu</label>
            <input
                id="due_at"
                type="datetime-local"
                name="due_at"
                value="{{ old('due_at', $assignment ? $assignment->due_at->format('Y-m-d\TH:i') : now('Asia/Jakarta')->addDays(3)->setTime(23, 59)->format('Y-m-d\TH:i')) }}"
                required>
            @error('due_at')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="status">Status pengerjaan</label>
            <select id="status" name="status" required>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $assignment?->status ?? 'belum_dikerjakan') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="description">Deskripsi tugas (opsional)</label>
            <textarea
                id="description"
                name="description"
                rows="4"
                maxlength="2000"
                placeholder="Catat ketentuan pengumpulan, tautan materi, atau pembagian tugas kelompok…">{{ old('description', $assignment?->description) }}</textarea>
            @error('description')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="task-form-actions">
            <button type="submit" class="button">
                {{ $assignment ? 'Simpan perubahan' : 'Simpan tugas' }}
            </button>
            <a class="button task-secondary" href="{{ route('assignments.index') }}">
                Batal
            </a>
        </div>
    </form>
</section>
@endsection
