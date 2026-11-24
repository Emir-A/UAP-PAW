@extends('layout')

@section('title', 'Ubah Profil')
@section('heading', 'Ubah profil')

@section('description')
    Perbarui informasi akun atau ganti kata sandi Anda.
@endsection

@section('content')
<section class="panel">
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <div class="form-field">
            <label for="name">Nama lengkap</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $user->name) }}"
                autocomplete="name"
                maxlength="100"
                required>

            @error('name')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="nim">NIM</label>
            <input
                type="text"
                id="nim"
                value="{{ $user->nim ?: '-' }}"
                disabled>
            <small class="text-muted">NIM tidak dapat diubah.</small>
        </div>

        <div class="form-field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                autocomplete="email"
                maxlength="254"
                required>

            @error('email')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="program_studi">Program studi</label>
            <input
                type="text"
                id="program_studi"
                name="program_studi"
                value="{{ old('program_studi', $user->program_studi) }}"
                maxlength="100">

            @error('program_studi')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <hr>

        <h2>Ubah kata sandi (opsional)</h2>
        <p class="text-muted">Kosongkan bagian ini jika tidak ingin mengganti kata sandi.</p>

        <div class="form-field">
            <label for="current_password">Kata sandi saat ini</label>
            <input
                type="password"
                id="current_password"
                name="current_password"
                autocomplete="current-password"
                maxlength="128">

            @error('current_password')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="password">Kata sandi baru</label>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="new-password"
                minlength="8"
                maxlength="128">
            <small class="text-muted">Minimal 8 karakter.</small>

            @error('password')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="password_confirmation">Konfirmasi kata sandi baru</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                autocomplete="new-password"
                minlength="8"
                maxlength="128">
        </div>

        <button type="submit" class="button">
            Simpan perubahan
        </button>
    </form>

    <p>
        <a href="{{ route('profile.show') }}">Batal</a>
    </p>
</section>
@endsection
