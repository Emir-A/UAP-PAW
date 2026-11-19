@extends('layout')

@section('title', 'Daftar')
@section('heading', 'Daftar akun')

@section('description')
Isi data berikut untuk membuat akun mahasiswa.
@endsection

@section('content')
<section class="panel">
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-field">
            <label for="name">Nama lengkap</label>
            <input
                id="name"
                name="name"
                value="{{ old('name') }}"
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
                id="nim"
                name="nim"
                value="{{ old('nim') }}"
                inputmode="numeric"
                pattern="[0-9]+"
                maxlength="30">
            @error('nim')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="email">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
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
                id="program_studi"
                name="program_studi"
                value="{{ old('program_studi') }}"
                maxlength="100">
            @error('program_studi')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="password">Kata sandi</label>
            <input
                id="password"
                name="password"
                type="password"
                autocomplete="new-password"
                minlength="8"
                maxlength="128"
                required>
            <small>Minimal 8 karakter.</small>
            @error('password')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="button">Daftar</button>
    </form>

</section>
@endsection