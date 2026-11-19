@extends('layout')

@section('title', 'Daftar Akun')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<section class="auth-layout" aria-labelledby="register-title">
    <div class="auth-card">
        <h1 id="register-title">Buat akun</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-field">
                <label for="name">Nama lengkap</label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    autocomplete="name"
                    maxlength="100"
                    required
                    @error('name')
                    aria-invalid="true"
                    aria-describedby="name-error"
                    @enderror>

                @error('name')
                <p id="name-error" class="field-error">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="form-field">
                <label for="nim">NIM</label>

                <input
                    id="nim"
                    name="nim"
                    type="text"
                    value="{{ old('nim') }}"
                    inputmode="numeric"
                    pattern="[0-9]+"
                    maxlength="30"
                    required
                    @error('nim')
                    aria-invalid="true"
                    aria-describedby="nim-error"
                    @enderror>

                @error('nim')
                <p id="nim-error" class="field-error">
                    {{ $message }}
                </p>
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
                    required
                    @error('email')
                    aria-invalid="true"
                    aria-describedby="email-error"
                    @enderror>

                @error('email')
                <p id="email-error" class="field-error">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="form-field">
                <label for="program_studi">Program studi</label>

                <input
                    id="program_studi"
                    name="program_studi"
                    type="text"
                    value="{{ old('program_studi') }}"
                    maxlength="100"
                    required
                    @error('program_studi')
                    aria-invalid="true"
                    aria-describedby="program-error"
                    @enderror>

                @error('program_studi')
                <p id="program-error" class="field-error">
                    {{ $message }}
                </p>
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
                    required
                    aria-describedby="password-hint @error('password') password-error @enderror"
                    @error('password')
                    aria-invalid="true"
                    @enderror>

                <small id="password-hint" class="text-muted">
                    Minimal 8 karakter.
                </small>

                @error('password')
                <p id="password-error" class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="button auth-submit">
                Daftar
            </button>
        </form>

        <p class="auth-switch">
            Sudah punya akun?
            <a href="{{ route('login') }}">Masuk</a>
        </p>
    </div>
</section>
@endsection