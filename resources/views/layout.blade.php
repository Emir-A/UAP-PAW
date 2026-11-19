<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Beranda') | Room Availability Checker</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <a class="skip-link" href="#main-content">
        Lewati ke konten
    </a>

    <header class="site-header">
        <div class="container header-content">
            <a class="brand" href="{{ route('home') }}">
                Room Availability Checker
            </a>

            <nav aria-label="Navigasi utama">
                <a
                    href="{{ route('home') }}"
                    @if(request()->routeIs('home'))
                    aria-current="page"
                    @endif
                    >
                    Beranda
                </a>

                @guest
                <a href="{{ route('login') }}">Masuk</a>
                <a href="{{ route('register') }}">Daftar</a>
                @endguest

                @auth
                <span>{{ auth()->user()->name }}</span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="button">
                        Keluar
                    </button>
                </form>
                @endauth
            </nav>
        </div>
    </header>

    <main id="main-content" class="container main-content" tabindex="-1">
        @if(session('status'))
        <div class="alert alert-success" role="status">
            {{ session('status') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-error" role="alert">
            <strong>Periksa kembali data yang dimasukkan.</strong>

            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <header class="page-heading">
            <h1>@yield('heading', 'Beranda')</h1>

            @hasSection('description')
            <p>@yield('description')</p>
            @endif
        </header>

        @yield('content')
    </main>
</body>

</html>