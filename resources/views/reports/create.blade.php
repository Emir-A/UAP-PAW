@extends('layout')
@section('title', 'Laporan Ruangan')
@section('heading', 'Buat laporan ruangan')
@section('description')
    Laporkan kerusakan atau masalah pada ruang perkuliahan.
@endsection
@section('content')
<section class="panel">
    <form
        method="POST"
        action="{{ route('reports.store') }}"
        enctype="multipart/form-data">
        @csrf
        <div class="form-field">
            <label for="room">Ruangan</label>
            <input
                type="text"
                id="room"
                name="room"
                list="room-options"
                value="{{ old('room') }}"
                maxlength="50"
                placeholder="Contoh: GKB 1.1"
                required>
            <datalist id="room-options">
                @foreach($rooms as $room)
                <option value="{{ $room }}"></option>
                @endforeach
            </datalist>
            @error('room')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-field">
            <label for="category">Jenis masalah</label>
            <select id="category" name="category" required>
                <option value="">Pilih jenis masalah</option>
                @foreach($categories as $value => $label)
                <option value="{{ $value }}" @selected(old('category') === $value)>
                    {{ $label }}
                </option>
                @endforeach
            </select>
            @error('category')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-field">
            <label for="title">Judul laporan</label>
            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title') }}"
                maxlength="150"
                placeholder="Contoh: Item tidak berfungsi"
                required>
            @error('title')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-field">
            <label for="description">Deskripsi</label>
            <textarea
                id="description"
                name="description"
                rows="5"
                maxlength="2000"
                required>{{ old('description') }}</textarea>
            @error('description')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-field">
            <label for="photo">Foto bukti (opsional)</label>
            <input
                type="file"
                id="photo"
                name="photo"
                accept="image/jpeg,image/png,image/webp">
            <small class="text-muted">JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
            @error('photo')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="button">
            Kirim laporan
        </button>
    </form>
    <p>
        <a href="{{ route('reports.index') }}">Kembali ke daftar laporan</a>
    </p>
</section>
@endsection