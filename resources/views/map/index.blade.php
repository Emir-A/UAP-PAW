@extends('layout')

@section('title', 'Map Per Lantai')
@section('heading', 'Denah Visual & Peta Ruang Per Lantai')

@section('description')
    Pantau posisi ruang kosong dan ruang terpakai secara langsung melalui peta arsitektural tiap lantai Gedung F &amp; G FILKOM UB.
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/floor-map.css') }}">
@endpush

@section('content')
@php
    $days = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];
    $currentSummary = $floorSummaries[$floorKey];
@endphp

<div class="fmap-wrapper">
    {{-- Panel Kontrol Filter Hari, Jam, & Status --}}
    <section class="panel fmap-filter-panel" aria-label="Filter waktu dan status ruang">
        <form method="GET" action="{{ route('floor-map.index') }}" class="fmap-filter-form">
            <input type="hidden" name="floor" value="{{ $floorKey }}">

            <div class="fmap-field">
                <label for="filter-day">Hari Perkuliahan</label>
                <select id="filter-day" name="day">
                    @foreach($days as $dayNum => $dayName)
                        <option value="{{ $dayNum }}" @selected($day === $dayNum)>
                            {{ $dayName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="fmap-field">
                <label for="filter-time">Waktu Pantau (WIB)</label>
                <input
                    id="filter-time"
                    type="time"
                    name="time"
                    value="{{ $time }}"
                    required>
            </div>

            <div class="fmap-field">
                <label for="filter-status">Sorot Status Ruang</label>
                <select id="filter-status" name="status">
                    <option value="all" @selected($statusFilter === 'all')>Tampilkan Semua Ruang</option>
                    <option value="free" @selected($statusFilter === 'free')>Fokus Ruang Kosong</option>
                    <option value="busy" @selected($statusFilter === 'busy')>Fokus Ruang Terpakai</option>
                </select>
            </div>

            <div class="fmap-filter-actions">
                <button type="submit" class="button">
                    Terapkan Waktu
                </button>
                <a
                    href="{{ route('floor-map.index', ['floor' => $floorKey]) }}"
                    class="button fmap-btn-outline">
                    Waktu Sekarang
                </a>
            </div>
        </form>

        {{-- Preset Jam Kuliah Cepat --}}
        <div class="fmap-quick-slots" aria-label="Pilihan cepat jam sesi kuliah">
            <span class="fmap-quick-label">Jam Sesi Cepat:</span>
            @foreach(['07:30', '09:30', '11:15', '13:00', '15:30', '18:30'] as $presetTime)
                <a
                    href="{{ route('floor-map.index', ['floor' => $floorKey, 'day' => $day, 'time' => $presetTime, 'status' => $statusFilter]) }}"
                    class="fmap-slot-pill {{ $time === $presetTime ? 'is-active' : '' }}">
                    {{ $presetTime }}
                </a>
            @endforeach
        </div>
    </section>

    {{-- Navigasi Pilihan Lantai (Floor Switcher Tabs) --}}
    <nav class="fmap-floor-tabs" aria-label="Pilih lantai gedung">
        @foreach($floorSummaries as $key => $summary)
            <a
                href="{{ route('floor-map.index', ['floor' => $key, 'day' => $day, 'time' => $time, 'status' => $statusFilter]) }}"
                class="fmap-floor-tab {{ $floorKey === $key ? 'is-active' : '' }}"
                @if($floorKey === $key) aria-current="page" @endif>
                <div class="fmap-tab-top">
                    <span class="fmap-tab-badge">Gedung {{ $summary['building'] }}</span>
                    <span class="fmap-tab-count">{{ $summary['free'] }}/{{ $summary['total'] }} kosong</span>
                </div>
                <strong class="fmap-tab-title">Lantai {{ $summary['floor'] }}</strong>
                <div class="fmap-tab-meter" aria-hidden="true">
                    <span
                        class="fmap-tab-meter-fill"
                        style="width: {{ $summary['total'] > 0 ? round(($summary['free'] / $summary['total']) * 100) : 0 }}%">
                    </span>
                </div>
            </a>
        @endforeach
    </nav>

    {{-- Ringkasan Statistik Lantai Aktif --}}
    <section class="fmap-stats-bar" aria-label="Ringkasan ketersediaan lantai">
        <div class="fmap-stat-info">
            <span class="fmap-eyebrow">DENAH INTERAKTIF · {{ strtoupper($days[$day]) }}, {{ $time }} WIB</span>
            <h2>{{ $currentLayout['label'] }}</h2>
            <p class="text-muted">{{ $currentLayout['subtitle'] }}</p>
        </div>

        <div class="fmap-stat-cards">
            <div class="fmap-stat-card">
                <span class="fmap-stat-num">{{ $currentSummary['total'] }}</span>
                <span class="fmap-stat-label">Total Ruang</span>
            </div>
            <div class="fmap-stat-card is-free">
                <span class="fmap-stat-num">{{ $currentSummary['free'] }}</span>
                <span class="fmap-stat-label">Ruang Kosong</span>
            </div>
            <div class="fmap-stat-card is-busy">
                <span class="fmap-stat-num">{{ $currentSummary['busy'] }}</span>
                <span class="fmap-stat-label">Sedang Terpakai</span>
            </div>
        </div>
    </section>

    {{-- Layout Utama: Peta Blueprint di Kiri + Inspector Detail di Kanan --}}
    <div class="fmap-main-grid">
        {{-- Kanvas Peta Denah Visual Lantai --}}
        <section class="panel fmap-blueprint-panel" aria-label="Denah visual {{ $currentLayout['label'] }}">
            <div class="fmap-blueprint-header">
                <div class="fmap-legend">
                    <span class="fmap-legend-item">
                        <span class="fmap-dot is-free"></span> Ruang Kosong (Tersedia)
                    </span>
                    <span class="fmap-legend-item">
                        <span class="fmap-dot is-busy"></span> Sedang Dipakai Kuliah
                    </span>
                    <span class="fmap-legend-item">
                        <span class="fmap-dot is-tour"></span> Titik 360° Virtual Tour
                    </span>
                </div>

                <div class="fmap-compass" aria-label="Penunjuk arah mata angin Utara">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 19 21 12 17 5 21 12 2"/>
                    </svg>
                    <span>UTARA</span>
                </div>
            </div>

            <div class="fmap-blueprint-canvas" data-status-filter="{{ $statusFilter }}">
                {{-- Zona Sayap Utara --}}
                <div class="fmap-wing-label">
                    <span>SAYAP UTARA (NORTH WING)</span>
                    <small>{{ count($northWingRooms) }} Ruangan</small>
                </div>

                <div class="fmap-wing-grid" style="--wing-cols: {{ min(count($northWingRooms), 5) }}">
                    @foreach($northWingRooms as $room)
                        @php
                            $dimmed = ($statusFilter === 'free' && $room['is_busy']) || ($statusFilter === 'busy' && ! $room['is_busy']);
                        @endphp
                        <button
                            type="button"
                            class="fmap-room-node {{ $room['is_busy'] ? 'is-busy' : 'is-free' }} {{ $dimmed ? 'is-dimmed' : '' }}"
                            data-room-trigger="{{ $room['code'] }}"
                            aria-label="Ruang {{ $room['code'] }} - {{ $room['status_label'] }}">
                            <div class="fmap-node-top">
                                <strong class="fmap-node-code">{{ $room['code'] }}</strong>
                                @if($room['has_tour'])
                                    <span class="fmap-tour-badge" title="Tersedia di Virtual Tour 360°">360°</span>
                                @endif
                            </div>

                            <span class="fmap-node-status">
                                {{ $room['status_label'] }}
                            </span>

                            <small class="fmap-node-meta">
                                @if($room['active_session'])
                                    {{ \Illuminate\Support\Str::limit($room['active_session']->title, 26) }}
                                @else
                                    {{ $room['availability_note'] }}
                                @endif
                            </small>

                            <span class="fmap-door-marker is-bottom" aria-hidden="true"></span>
                        </button>
                    @endforeach
                </div>

                {{-- Koridor Tengah, Lift, & Tangga --}}
                <div class="fmap-corridor" aria-label="Koridor utama dan fasilitas lantai">
                    <div class="fmap-corridor-line">
                        <span class="fmap-corridor-title">KORIDOR UTAMA LANTAI {{ $currentLayout['floor'] }}</span>
                    </div>
                    <div class="fmap-facilities">
                        @foreach($currentLayout['facilities'] as $facility)
                            <span class="fmap-facility-chip">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                                    <path d="M9 12h6M12 9v6"/>
                                </svg>
                                {{ $facility }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Zona Sayap Selatan --}}
                <div class="fmap-wing-grid" style="--wing-cols: {{ min(count($southWingRooms), 5) }}">
                    @foreach($southWingRooms as $room)
                        @php
                            $dimmed = ($statusFilter === 'free' && $room['is_busy']) || ($statusFilter === 'busy' && ! $room['is_busy']);
                        @endphp
                        <button
                            type="button"
                            class="fmap-room-node {{ $room['is_busy'] ? 'is-busy' : 'is-free' }} {{ $dimmed ? 'is-dimmed' : '' }}"
                            data-room-trigger="{{ $room['code'] }}"
                            aria-label="Ruang {{ $room['code'] }} - {{ $room['status_label'] }}">
                            <span class="fmap-door-marker is-top" aria-hidden="true"></span>

                            <div class="fmap-node-top">
                                <strong class="fmap-node-code">{{ $room['code'] }}</strong>
                                @if($room['has_tour'])
                                    <span class="fmap-tour-badge" title="Tersedia di Virtual Tour 360°">360°</span>
                                @endif
                            </div>

                            <span class="fmap-node-status">
                                {{ $room['status_label'] }}
                            </span>

                            <small class="fmap-node-meta">
                                @if($room['active_session'])
                                    {{ \Illuminate\Support\Str::limit($room['active_session']->title, 26) }}
                                @else
                                    {{ $room['availability_note'] }}
                                @endif
                            </small>
                        </button>
                    @endforeach
                </div>

                <div class="fmap-wing-label is-south">
                    <span>SAYAP SELATAN (SOUTH WING)</span>
                    <small>{{ count($southWingRooms) }} Ruangan</small>
                </div>
            </div>

            <p class="fmap-blueprint-hint">
                💡 <strong>Tips:</strong> Klik salah satu kotak ruangan pada denah di atas untuk melihat rincian penggunaan ruang, jadwal harian, dan tautan panorama 360° di panel samping.
            </p>
        </section>

        {{-- Panel Samping: Inspector Detail Ruangan --}}
        <aside class="panel fmap-inspector-panel" aria-label="Detail informasi ruangan terpilih" aria-live="polite">
            @foreach($allFloorRooms as $index => $room)
                <div
                    class="fmap-inspector-card {{ $index === 0 ? 'is-active' : '' }}"
                    data-room-detail="{{ $room['code'] }}">
                    <div class="fmap-inspector-head">
                        <div>
                            <span class="fmap-eyebrow">GEDUNG {{ $room['building'] }} · LANTAI {{ $room['floor'] }}</span>
                            <h3 class="fmap-inspector-title">Ruang {{ $room['code'] }}</h3>
                        </div>
                        <span class="fmap-status-pill {{ $room['is_busy'] ? 'is-busy' : 'is-free' }}">
                            {{ $room['is_busy'] ? 'Sedang Terpakai' : 'Ruang Kosong' }}
                        </span>
                    </div>

                    <div class="fmap-availability-box {{ $room['is_busy'] ? 'is-busy' : 'is-free' }}">
                        <strong>Status Pukul {{ $time }} WIB:</strong>
                        <p>{{ $room['availability_note'] }}</p>
                    </div>

                    @if($room['active_session'])
                        <div class="fmap-session-highlight">
                            <span class="fmap-box-label">Sesi Perkuliahan Saat Ini</span>
                            <h4>{{ $room['active_session']->title }}</h4>
                            <p class="fmap-session-meta">
                                {{ $room['active_session']->start_time }} – {{ $room['active_session']->end_time }} WIB
                                · Kelas {{ $room['active_session']->class_group }}
                            </p>
                            <p class="fmap-session-sub">
                                {{ $room['active_session']->program }}<br>
                                <small>{{ $room['active_session']->lecturer }}</small>
                            </p>

                            @auth
                                <form method="POST" action="{{ route('timetable.store') }}" class="fmap-add-form">
                                    @csrf
                                    <input type="hidden" name="course_session_id" value="{{ $room['active_session']->id }}">
                                    <button type="submit" class="button fmap-btn-small">
                                        + Simpan ke Jadwal Pribadi
                                    </button>
                                </form>
                            @endauth
                        </div>
                    @elseif($room['next_session'])
                        <div class="fmap-session-next">
                            <span class="fmap-box-label">Sesi Berikutnya Hari Ini</span>
                            <h4>{{ $room['next_session']->title }}</h4>
                            <p class="fmap-session-meta">
                                Mulai pukul {{ $room['next_session']->start_time }} – {{ $room['next_session']->end_time }} WIB
                                · Kelas {{ $room['next_session']->class_group }}
                            </p>
                        </div>
                    @endif

                    {{-- Tautan Referensi Virtual Tour 360 FILKOM UB --}}
                    <div class="fmap-tour-box">
                        <div>
                            <strong>Panorama Virtual Tour FILKOM</strong>
                            <p>
                                @if($room['has_tour'])
                                    Ruang {{ $room['code'] }} tercatat memiliki titik kamera 360° pada Virtual Tour FILKOM UB.
                                @else
                                    Jelajahi koridor Lantai {{ $room['floor'] }} Gedung {{ $room['building'] }} melalui Virtual Tour 360° FILKOM UB.
                                @endif
                            </p>
                        </div>
                        <a
                            href="https://filkom.ub.ac.id/tour/"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="button fmap-btn-outline fmap-btn-small">
                            Buka Virtual Tour 360° ↗
                        </a>
                    </div>

                    {{-- Daftar Lengkap Jadwal Harian di Ruangan Ini --}}
                    <div class="fmap-timeline">
                        <div class="fmap-timeline-head">
                            <h4>Jadwal Hari {{ $days[$day] }}</h4>
                            <span>{{ $room['session_count'] }} sesi</span>
                        </div>

                        @if($room['daily_sessions']->isEmpty())
                            <p class="fmap-empty-schedule">
                                Tidak ada jadwal perkuliahan reguler di ruang {{ $room['code'] }} sepanjang hari {{ $days[$day] }}.
                            </p>
                        @else
                            <ul class="fmap-timeline-list">
                                @foreach($room['daily_sessions'] as $session)
                                    @php
                                        $isCurrent = $session->start_time <= $time && $session->end_time > $time;
                                    @endphp
                                    <li class="fmap-timeline-item {{ $isCurrent ? 'is-current' : '' }}">
                                        <div class="fmap-timeline-time">
                                            <strong>{{ $session->start_time }}</strong>
                                            <span>{{ $session->end_time }}</span>
                                        </div>
                                        <div class="fmap-timeline-body">
                                            <strong>{{ $session->title }}</strong>
                                            <span>{{ $session->program }} · Kelas {{ $session->class_group }}</span>
                                            <small>{{ $session->lecturer }}</small>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endforeach
        </aside>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const triggers = document.querySelectorAll('[data-room-trigger]');
    const details = document.querySelectorAll('[data-room-detail]');

    function selectRoom(roomCode) {
        triggers.forEach(function (btn) {
            const active = btn.getAttribute('data-room-trigger') === roomCode;
            btn.classList.toggle('is-selected', active);
            btn.setAttribute('aria-pressed', active ? 'true' : 'false');
        });

        details.forEach(function (card) {
            const active = card.getAttribute('data-room-detail') === roomCode;
            card.classList.toggle('is-active', active);
        });
    }

    if (triggers.length > 0) {
        const firstFree = Array.from(triggers).find(function (btn) {
            return btn.classList.contains('is-free') && !btn.classList.contains('is-dimmed');
        });
        const initialCode = (firstFree || triggers[0]).getAttribute('data-room-trigger');
        selectRoom(initialCode);

        triggers.forEach(function (btn) {
            btn.addEventListener('click', function () {
                selectRoom(btn.getAttribute('data-room-trigger'));
            });
        });
    }
});
</script>
@endsection