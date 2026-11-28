@extends('layout')

@section('title', 'Tugas Kuliah')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/assignments.css') }}">
@endpush

@section('content')
<div class="task-page">
    <div class="task-heading">
        <div>
            <p class="task-eyebrow">MANAJEMEN PERKULIAHAN · PRIBADI</p>
            <h1>Tugas kuliah</h1>
            <p class="task-muted">
                Catat tugas kuliah, tenggat waktu, dan status pengerjaannya. Tugas diurutkan otomatis dari tenggat terdekat.
            </p>
        </div>

        <a class="button" href="{{ route('assignments.create') }}">
            + Tambah tugas
        </a>
    </div>

    <section class="task-summary" aria-label="Ringkasan status tugas">
        <div class="task-stat">
            <span class="task-stat-label">Total tugas</span>
            <strong>{{ $counts['total'] }}</strong>
        </div>
        <div class="task-stat">
            <span class="task-stat-label">Belum dikerjakan</span>
            <strong>{{ $counts['belum_dikerjakan'] }}</strong>
        </div>
        <div class="task-stat">
            <span class="task-stat-label">Dikerjakan</span>
            <strong>{{ $counts['dikerjakan'] }}</strong>
        </div>
        <div class="task-stat">
            <span class="task-stat-label">Selesai</span>
            <strong>{{ $counts['selesai'] }}</strong>
        </div>
    </section>

    <section class="panel task-filter-panel">
        <form method="GET" action="{{ route('assignments.index') }}" class="task-filters">
            <label class="task-search">
                Cari tugas / mata kuliah
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Judul tugas, mata kuliah, atau deskripsi…">
            </label>

            <label>
                Status pengerjaan
                <select name="status">
                    <option value="">Semua status</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <button type="submit" class="button">Terapkan</button>
            <a class="task-reset" href="{{ route('assignments.index') }}">Reset</a>
        </form>
    </section>

    <div class="task-section-title">
        <h2>Daftar tugas</h2>
        <span class="task-muted">{{ $assignments->count() }} tugas ditampilkan · urut tenggat terdekat</span>
    </div>

    @forelse($assignments as $assignment)
        <article class="task-card {{ $assignment->isOverdue() ? 'is-overdue' : '' }}">
            <div class="task-body">
                <div class="task-top">
                    <span class="task-badge status-{{ $assignment->status }}">
                        {{ $assignment->statusLabel() }}
                    </span>

                    <span class="task-course">
                        {{ $assignment->course_name }}
                    </span>

                    <span class="task-deadline {{ $assignment->isOverdue() ? 'overdue' : '' }}">
                        Tenggat: {{ $assignment->due_at->format('d M Y, H:i') }} WIB
                        @if($assignment->isOverdue())
                            · Terlambat
                        @endif
                    </span>
                </div>

                <h3>{{ $assignment->title }}</h3>

                @if($assignment->description)
                    <p class="task-desc">{{ $assignment->description }}</p>
                @endif
            </div>

            <div class="task-actions">
                <form
                    method="POST"
                    action="{{ route('assignments.status', $assignment->id) }}"
                    class="task-status-form">
                    @csrf
                    @method('PATCH')

                    <label class="sr-only" for="status-{{ $assignment->id }}">Ubah status {{ $assignment->title }}</label>
                    <select
                        id="status-{{ $assignment->id }}"
                        name="status"
                        onchange="this.form.submit()"
                        aria-label="Status tugas {{ $assignment->title }}">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($assignment->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <noscript>
                        <button type="submit" class="button task-secondary">Simpan status</button>
                    </noscript>
                </form>

                <a
                    class="button task-secondary"
                    href="{{ route('assignments.edit', $assignment->id) }}"
                    aria-label="Ubah tugas {{ $assignment->title }}">
                    Ubah
                </a>

                <form
                    method="POST"
                    action="{{ route('assignments.destroy', $assignment->id) }}"
                    onsubmit="return confirm('Hapus tugas ini secara permanen?');">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="button task-danger"
                        aria-label="Hapus tugas {{ $assignment->title }}">
                        Hapus
                    </button>
                </form>
            </div>
        </article>
    @empty
        <div class="panel task-empty">
            <h2>
                @if(request()->filled('q') || request()->filled('status'))
                    Tidak ada tugas yang cocok
                @else
                    Belum ada tugas kuliah
                @endif
            </h2>
            <p class="task-muted">
                @if(request()->filled('q') || request()->filled('status'))
                    Coba ubah kata kunci pencarian atau filter status untuk melihat tugas lainnya.
                @else
                    Tambahkan tugas kuliah pertama Anda agar tenggat waktu dan progres pengerjaan lebih terpantau.
                @endif
            </p>
            @if(request()->filled('q') || request()->filled('status'))
                <a class="button task-secondary" href="{{ route('assignments.index') }}">
                    Tampilkan semua tugas
                </a>
            @else
                <a class="button" href="{{ route('assignments.create') }}">
                    + Tambah tugas pertama
                </a>
            @endif
        </div>
    @endforelse
</div>
@endsection
