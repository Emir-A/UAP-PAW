@extends('layout')

@section('title', 'Masuk')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<section class="auth-layout" aria-labelledby="login-title">
    <div class="auth-card">
        <h1 id="login-title">Masuk</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-field">
                <label for="identity">Email atau NIM</label>

                <input
                    id="identity"
                    name="identity"
                    type="text"
                    value="{{ old('identity') }}"
                    autocomplete="username"
                    maxlength="254"
                    required
                    @error('identity')
                    aria-invalid="true"
                    aria-describedby="identity-error"
                    @enderror>

                @error('identity')
                <p id="identity-error" class="field-error">
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
                    autocomplete="current-password"
                    maxlength="128"
                    required
                    @error('password')
                    aria-invalid="true"
                    aria-describedby="password-error"
                    @enderror>

                @error('password')
                <p id="password-error" class="field-error">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <button type="submit" class="button auth-submit">
                Masuk
            </button>
        </form>

        <p class="auth-switch">
            Belum punya akun?
            <a href="{{ route('register') }}">Daftar sekarang</a>
        </p>
    </div>
</section>
@endsection