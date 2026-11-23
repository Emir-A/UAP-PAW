@extends('layout')

@section('title', $item ? 'Ubah Laporan Barang Hilang' : 'Buat Laporan Barang Hilang')
@section('heading', $item ? 'Ubah laporan barang hilang' : 'Laporkan barang hilang')
@section('description', 'Cantumkan informasi barang, ciri-ciri, lokasi ruangan atau lantai, serta waktu kehilangan.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/lost-items.css') }}">
@endpush

@section('content')
<section class="panel lost-form-panel">
    <form
        method="POST"
        enctype="multipart/form-data"
        action="{{ $item ? route('lost-items.update', $item->id) : route('lost-items.store') }}">
        @csrf

        @if($item)
            @method('PUT')
        @endif

        <div class="form-field">
            <label for="item_name">Nama barang</label>
            <input
                id="item_name"
                type="text"
                name="item_name"
                value="{{ old('item_name', $item?->item_name) }}"
                placeholder="Contoh: Tumbler Corkcicle Hitam / Charger Laptop Asus"
                required>
            @error('item_name')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="location">Lokasi kehilangan (ruangan atau lantai)</label>
            <input
                id="location"
                type="text"
                name="location"
                list="room-options"
                value="{{ old('location', $item?->location) }}"
                placeholder="Contoh: Ruang F2.1 / Gedung F Lantai 3"
                required>
            <datalist id="room-options">
                @foreach($rooms as $room)
                    <option value="Ruang {{ $room->code }} (Gedung {{ $room->building }} Lantai {{ $room->floor }})"></option>
                @endforeach
                <option value="Gedung F Lantai 2"></option>
                <option value="Gedung F Lantai 3"></option>
                <option value="Gedung F Lantai 4"></option>
                <option value="Gedung F Lantai 11"></option>
                <option value="Gedung G Lantai 1"></option>
            </datalist>
            @error('location')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="lost_at">Waktu kehilangan</label>
            <input
                id="lost_at"
                type="datetime-local"
                name="lost_at"
                value="{{ old('lost_at', $item ? $item->lost_at->format('Y-m-d\TH:i') : now('Asia/Jakarta')->format('Y-m-d\TH:i')) }}"
                required>
            @error('lost_at')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="description">Ciri-ciri dan informasi barang</label>
            <textarea
                id="description"
                name="description"
                rows="4"
                placeholder="Jelaskan warna, merek, stiker, kondisi, atau nomor kontak yang dapat dihubungi…"
                required>{{ old('description', $item?->description) }}</textarea>
            @error('description')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        @if($item)
            <div class="form-field">
                <label for="status">Status laporan</label>
                <select id="status" name="status">
                    <option value="hilang" @selected(old('status', $item->status) === 'hilang')>Masih dicari</option>
                    <option value="ditemukan" @selected(old('status', $item->status) === 'ditemukan')>Sudah ditemukan (Selesai)</option>
                </select>
                @error('status')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="form-field">
            <label for="photo">Foto barang (opsional)</label>
            <input
                id="photo"
                type="file"
                name="photo"
                accept="image/*">
            @if($item?->photo_path)
                <p class="lost-muted" style="margin-top: 6px; font-size: 13px;">
                    Foto saat ini sudah tersimpan. Unggah foto baru jika ingin menggantinya.
                </p>
            @endif
            @error('photo')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="lost-form-actions">
            <button type="submit" class="button">
                {{ $item ? 'Simpan perubahan' : 'Kirim laporan' }}
            </button>
            <a class="button lost-secondary" href="{{ route('lost-items.index') }}">
                Batal
            </a>
        </div>
    </form>
</section>
@endsection