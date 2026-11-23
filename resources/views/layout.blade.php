<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Beranda') | Room Availability Checker</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>

<body>
    <a class="skip-link" href="#main-content">
        Lewati ke konten
    </a>

    <header class="site-header">
        <div class="container header-content">
            <a class="brand" href="{{ route('home') }}">
                Backroom
            </a>

            <nav aria-label="Navigasi utama">
                <a
                    href="{{ route('classes') }}"
                    @if(request()->routeIs('home', 'classes'))
                    aria-current="page"
                    @endif
                    >
                    Jadwal kelas
                </a>

                <a
                    href="{{ route('rooms') }}"
                    @if(request()->routeIs('rooms'))
                    aria-current="page"
                    @endif
                    >
                    Ruang
                </a>

                <a
                    href="{{ route('lost-items.index') }}"
                    @if(request()->routeIs('lost-items.*'))
                    aria-current="page"
                    @endif
                    >
                    Barang hilang
                </a>

                @guest
                <a href="{{ route('login') }}">Masuk</a>
                @endguest

                @auth
<<<<<<< HEAD
                <a
                    href="{{ route('timetable.index') }}"
                    @if(request()->routeIs('timetable.*'))
                    aria-current="page"
                    @endif
                    >
                    Jadwal pribadi
                </a>
=======
                <a href="{{ route('timetable.index') }}">Jadwal pribadi</a>
                <a href="{{ route('reports.index') }}">Laporan</a>
>>>>>>> origin/main
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


    <main id="main-content" class="container main-content {{ request()->routeIs('login', 'register') ? 'auth-page' : '' }}" tabindex="-1">

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

        @unless(request()->routeIs('login', 'register'))
        <header class="page-heading">
            <h1>@yield('heading', 'Beranda')</h1>

            @hasSection('description')
            <p>@yield('description')</p>
            @endif
        </header>
        @endunless

        @yield('content')

    </main>
</body>

</html>