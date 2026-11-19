@extends('layout')

@section('title', 'Masuk')
@section('heading', 'Masuk')

@section('description')
Gunakan email atau NIM yang sudah terdaftar.
@endsection

@section('content')
<section class="panel">
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
                required>
        </div>

        <button type="submit" class="button">Masuk</button>
    </form>

</section>
@endsection