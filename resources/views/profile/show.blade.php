@extends('layout')

@section('title', 'Profil')
@section('heading', 'Profil akun')

@section('description')
    Informasi pribadi dan pengaturan akun Anda.
@endsection

@section('content')
<section class="panel">
    <dl>
        <div>
            <dt class="text-muted">Nama lengkap</dt>
            <dd>{{ $user->name }}</dd>
        </div>

        <div>
            <dt class="text-muted">NIM</dt>
            <dd>{{ $user->nim ?: '-' }}</dd>
        </div>

        <div>
            <dt class="text-muted">Email</dt>
            <dd>{{ $user->email }}</dd>
        </div>

        <div>
            <dt class="text-muted">Program studi</dt>
            <dd>{{ $user->program_studi ?: '-' }}</dd>
        </div>
    </dl>

    <p>
        <a class="button" href="{{ route('profile.edit') }}">
            Ubah profil
        </a>
    </p>
</section>
@endsection
