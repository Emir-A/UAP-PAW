@extends('layout')

@section('title', 'Ubah Laporan Ruangan')
@section('heading', 'Ubah laporan ruangan')

@section('description')
    Perbarui detail kondisi ruangan atau status penanganan laporan.
@endsection

@section('content')
<section class="panel">
    <form
        method="POST"
        action="{{ route('reports.update', $report->id) }}"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-field">
            <label for="room">Ruangan</label>
            <input
                type="text"
                id="room"
                name="room"
                list="room-options"
                value="{{ old('room', $report->room) }}"
                maxlength="50"
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
                @foreach($categories as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $report->category) === $value)>
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
                value="{{ old('title', $report->title) }}"
                maxlength="150"
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
                required>{{ old('description', $report->description) }}</textarea>

            @error('description')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="status">Status laporan</label>
            <select id="status" name="status" required>
                @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $report->status) === $value)>
                    {{ $label }}
                </option>
                @endforeach
            </select>

            @error('status')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="photo">Foto bukti baru (opsional)</label>
            <input
                type="file"
                id="photo"
                name="photo"
                accept="image/jpeg,image/png,image/webp">
            <small class="text-muted">
                @if($report->photo_path)
                Foto saat ini sudah tersimpan. Unggah foto baru jika ingin menggantinya.
                @else
                JPG, PNG, atau WEBP. Maksimal 2 MB.
                @endif
            </small>

            @error('photo')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="button">
            Simpan perubahan
        </button>
    </form>

    <p>
        <a href="{{ route('reports.index') }}">Batal</a>
    </p>
</section>
@endsection
