@extends('layout')

@section('title', 'Virtual Tour 360° Map Per Lantai')
@section('heading', 'Virtual Tour 360° & Denah Ruang Per Lantai')

@section('description')
    Jelajahi panorama bola 360° (WebGL Spherical Tour) tiap lantai untuk melihat posisi pintu ruang kosong dan masuk ke dalam kelas secara langsung.
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pannellum.css') }}">
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
    $activeFloor = $tourFloors[$initialFloor];
@endphp

<div class="vt360-shell" id="vt360-app" data-initial-floor="{{ $initialFloor }}">
    {{-- Filter Hari & Jam Kuliah di Atas Viewer 360 --}}
    <form method="GET" action="{{ route('floor-map.index') }}" class="vt360-timebar" aria-label="Filter waktu pantau 360">
        <input type="hidden" name="floor" id="vt360-floor-input" value="{{ $initialFloor }}">

        <div class="vt360-timebar-group">
            <label for="vt-day">Hari:</label>
            <select id="vt-day" name="day">
                @foreach($days as $num => $label)
                    <option value="{{ $num }}" @selected($day === $num)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="vt360-timebar-group">
            <label for="vt-time">Waktu (WIB):</label>
            <input id="vt-time" type="time" name="time" value="{{ $time }}" required>
        </div>

        <button type="submit" class="button vt360-btn-apply">Perbarui Panorama 360°</button>

        <div class="vt360-presets">
            <span class="vt360-preset-label">Jam Sesi:</span>
            @foreach(['07:30', '09:30', '11:15', '13:00', '15:30'] as $preset)
                <a
                    href="{{ route('floor-map.index', ['floor' => $initialFloor, 'day' => $day, 'time' => $preset]) }}"
                    class="vt360-preset-pill {{ $time === $preset ? 'is-active' : '' }}">
                    {{ $preset }}
                </a>
            @endforeach
        </div>
    </form>

    {{-- VIEWPORT UTAMA WEBGL 360 SPHERICAL VIRTUAL TOUR --}}
    <section class="vt360-stage" id="vt360-stage" aria-label="Viewer Panorama Bola 360 Derajat">

        {{-- 1. Container WebGL Pannellum 360° --}}
        <div id="vt360-pano-viewer" class="vt360-pano-viewer"></div>

        {{-- 2. Top-Left Campus Branding Overlay (Gaya Virtual Tour, Dimodifikasi untuk Backroom UAP-PAW) --}}
        <div class="vt360-hud-brand">
            <div class="vt360-brand-emblem">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <ellipse cx="12" cy="12" rx="4" ry="10"/>
                    <path d="M2 12h20"/>
                </svg>
            </div>
            <div>
                <div class="vt360-brand-faculty">FACULTY OF COMPUTER SCIENCE · BRAWIJAYA UNIVERSITY</div>
                <div class="vt360-brand-sub">BACKROOM 360° FLOOR TOUR · {{ strtoupper($days[$day]) }}, {{ $time }} WIB</div>
            </div>
        </div>

        {{-- 3. Top-Right Interactive Radar Floor Plan Overlay --}}
        <div class="vt360-hud-radar" id="vt360-radar-box">
            <div class="vt360-radar-head">
                <span id="vt360-radar-title">{{ $activeFloor['label'] }}</span>
                <span class="vt360-compass-deg" id="vt360-compass-deg">0° UTARA</span>
            </div>

            <div class="vt360-minimap">
                <div class="vt360-minimap-wing" id="vt360-minimap-north"></div>
                <div class="vt360-minimap-corridor">
                    <span>KORIDOR UTAMA</span>
                    <div class="vt360-fov-cone" id="vt360-fov-cone"></div>
                </div>
                <div class="vt360-minimap-wing" id="vt360-minimap-south"></div>
            </div>

            <div class="vt360-radar-legend">
                <span><i class="dot-free"></i> <b id="vt360-free-count">{{ $activeFloor['free_count'] }}</b> Kosong</span>
                <span><i class="dot-busy"></i> <b id="vt360-busy-count">{{ $activeFloor['busy_count'] }}</b> Terpakai</span>
            </div>
        </div>

        {{-- 4. Bottom-Left Active Scene Title & Building/Floor Subtitle --}}
        <div class="vt360-hud-location">
            <span class="vt360-mode-pill" id="vt360-scene-mode">360° CORRIDOR</span>
            <h2 id="vt360-scene-title">{{ $activeFloor['corridor_title'] }}</h2>
            <p id="vt360-scene-subtitle">{{ $activeFloor['corridor_subtitle'] }}</p>
        </div>

        {{-- 5. Horizontal Thumbnail Carousel: "Main Floors" (Muncul Saat Ikon Gedung Diklik) --}}
        <div class="vt360-carousel-drawer is-hidden" id="vt360-floor-drawer">
            <div class="vt360-drawer-head">
                <strong>Main Floors · Pilih Lantai Gedung</strong>
                <span>Klik thumbnail lantai untuk berpindah panorama 360°</span>
            </div>
            <div class="vt360-floor-cards">
                @foreach($tourFloors as $fKey => $fData)
                    <button
                        type="button"
                        class="vt360-floor-card {{ $fKey === $initialFloor ? 'is-active' : '' }}"
                        data-switch-floor="{{ $fKey }}">
                        <span class="vt360-fcard-tag">Gedung {{ $fData['building'] }}</span>
                        <strong>Lantai {{ $fData['floor'] }}</strong>
                        <small>{{ $fData['free_count'] }}/{{ $fData['total_rooms'] }} Ruang Kosong</small>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- 6. Full Grid Modal: "All Locations" (Muncul Saat Ikon 3x3 Grid Diklik) --}}
        <div class="vt360-modal-overlay" id="vt360-all-modal" hidden>
            <div class="vt360-modal-box">
                <div class="vt360-modal-header">
                    <div>
                        <h3>All Locations · Direktori Panorama Ruang ({{ collect($tourFloors)->sum('total_rooms') }} Titik Ruang)</h3>
                        <p>Pilih titik ruangan untuk langsung masuk atau menyorot pintu ruangan di dalam panorama 360°.</p>
                    </div>
                    <button type="button" class="vt360-modal-close" id="vt360-close-modal" aria-label="Tutup semua lokasi">✕</button>
                </div>

                <div class="vt360-modal-filters">
                    <input type="search" id="vt360-modal-search" placeholder="Cari ruangan (misal: F2.2, F3.1, F4.1, G1.2)...">
                    <button type="button" class="vt360-filter-chip is-active" data-modal-filter="all">Semua Lokasi</button>
                    <button type="button" class="vt360-filter-chip" data-modal-filter="free">Ruang Kosong</button>
                    <button type="button" class="vt360-filter-chip" data-modal-filter="busy">Sedang Terpakai</button>
                </div>

                <div class="vt360-modal-grid" id="vt360-modal-grid">
                    @foreach($tourFloors as $fKey => $fData)
                        @foreach($fData['rooms'] as $r)
                            <button
                                type="button"
                                class="vt360-loc-card {{ $r['is_busy'] ? 'is-busy' : 'is-free' }}"
                                data-teleport-floor="{{ $fKey }}"
                                data-teleport-room="{{ $r['code'] }}"
                                data-room-status="{{ $r['is_busy'] ? 'busy' : 'free' }}"
                                data-room-search="{{ strtolower($r['code'].' '.$fData['label'].' '.$r['board_title']) }}">
                                <div class="vt360-loc-preview">
                                    <span class="vt360-loc-floor">{{ $fData['label'] }}</span>
                                    <strong class="vt360-loc-code">Classroom {{ $r['code'] }}</strong>
                                    <span class="vt360-loc-badge">{{ $r['status_label'] }}</span>
                                </div>
                                <div class="vt360-loc-meta">
                                    <strong>{{ \Illuminate\Support\Str::limit($r['board_title'], 34) }}</strong>
                                    <small>{{ $r['status_note'] }}</small>
                                </div>
                            </button>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 7. Info Hotspot (`i`) Drawer Detail Ruangan --}}
        <aside class="vt360-info-drawer" id="vt360-info-drawer" hidden>
            <div class="vt360-info-head">
                <div>
                    <span class="vt360-info-eyebrow" id="vt360-info-wing">SAYAP UTARA</span>
                    <h3 id="vt360-info-code">Ruang F2.1</h3>
                </div>
                <button type="button" class="vt360-info-close" id="vt360-info-close" aria-label="Tutup detail ruang">✕</button>
            </div>

            <div class="vt360-info-status" id="vt360-info-status-box">
                <strong id="vt360-info-status-label">RUANG KOSONG</strong>
                <p id="vt360-info-status-note">Kosong sepanjang sisa hari ini</p>
            </div>

            <div class="vt360-info-actions">
                <button type="button" class="button vt360-btn-enter" id="vt360-btn-enter-toggle">
                    Masuk ke Interior Ruang 360° →
                </button>

                @auth
                    <form method="POST" action="{{ route('timetable.store') }}" id="vt360-save-form" hidden>
                        @csrf
                        <input type="hidden" name="course_session_id" id="vt360-save-session-id" value="">
                        <button type="submit" class="button vt360-btn-outline">
                            + Simpan Kelas Ini ke Jadwal Pribadi
                        </button>
                    </form>
                @endauth
            </div>

            <div class="vt360-info-schedule">
                <h4>Jadwal Ruang Hari {{ $days[$day] }}</h4>
                <ul class="vt360-session-list" id="vt360-session-list"></ul>
            </div>
        </aside>

        {{-- 8. Bottom-Center 5 Floating Icon Controls (Terinspirasi 3DVista Control Bar + Modifikasi Filter Kosong) --}}
        <div class="vt360-control-dock" role="toolbar" aria-label="Kontrol Virtual Tour 360">
            <button type="button" class="vt360-dock-btn" id="vt360-btn-floors" title="Main Floors (Pilih Lantai)">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="M4 21V6l8-3 8 3v15M2 21h20M9 10h6M9 14h6M9 18h6"/>
                </svg>
                <span class="vt360-dock-tooltip">Main Floors</span>
            </button>

            <button type="button" class="vt360-dock-btn" id="vt360-btn-all" title="All Locations (Semua Ruangan)">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.9">
                    <rect x="3" y="3" width="5" height="5" rx="1"/>
                    <rect x="9.5" y="3" width="5" height="5" rx="1"/>
                    <rect x="16" y="3" width="5" height="5" rx="1"/>
                    <rect x="3" y="9.5" width="5" height="5" rx="1"/>
                    <rect x="9.5" y="9.5" width="5" height="5" rx="1"/>
                    <rect x="16" y="9.5" width="5" height="5" rx="1"/>
                    <rect x="3" y="16" width="5" height="5" rx="1"/>
                    <rect x="9.5" y="16" width="5" height="5" rx="1"/>
                    <rect x="16" y="16" width="5" height="5" rx="1"/>
                </svg>
                <span class="vt360-dock-tooltip">All Locations</span>
            </button>

            <button type="button" class="vt360-dock-btn" id="vt360-btn-filter-free" title="Sorot Ruang Kosong Saja">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span class="vt360-dock-tooltip">Filter Ruang Kosong</span>
            </button>

            <button type="button" class="vt360-dock-btn" id="vt360-btn-hotspots" title="Show/Hide Hotspots">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.9">
                    <circle cx="12" cy="12" r="9"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <span class="vt360-dock-tooltip">Show/Hide Hotspots</span>
            </button>

            <button type="button" class="vt360-dock-btn is-active" id="vt360-btn-autorotate" title="Auto-Rotate 360°">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                </svg>
                <span class="vt360-dock-tooltip">Auto-Rotate 360°</span>
            </button>

            <button type="button" class="vt360-dock-btn" id="vt360-btn-fullscreen" title="Toggle Fullscreen">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                </svg>
                <span class="vt360-dock-tooltip">Toggle Fullscreen</span>
            </button>
        </div>
    </section>

    {{-- Baris Pintasan Pintu Ruangan di Bawah Viewer --}}
    <section class="vt360-quick-strip" aria-label="Pintasan kamera ke pintu ruangan">
        <div class="vt360-quick-head">
            <strong id="vt360-quick-title">Pintasan Kamera Pintu Ruangan · {{ $activeFloor['label'] }}</strong>
            <span>Klik kode ruang untuk mengarahkan kamera 360° langsung ke pintu ruangan (atau klik 2x untuk masuk):</span>
        </div>
        <div class="vt360-quick-pills" id="vt360-quick-pills">
            @foreach($activeFloor['rooms'] as $room)
                <button
                    type="button"
                    class="vt360-qpill {{ $room['is_busy'] ? 'is-busy' : 'is-free' }}"
                    data-quick-room="{{ $room['code'] }}">
                    <strong>{{ $room['code'] }}</strong>
                    <span>{{ $room['is_busy'] ? 'Terpakai' : 'Kosong' }}</span>
                </button>
            @endforeach
        </div>
    </section>
</div>

<script src="{{ asset('js/pannellum.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const TOUR_DATA = @json($tourFloors);

    let currentFloorKey = document.getElementById('vt360-app').dataset.initialFloor || 'F2';
    let sceneMode = 'corridor'; // 'corridor' atau 'interior'
    let activeRoomCode = null;
    let onlyFreeHighlight = false;
    let hideHotspots = false;
    let autoRotateEnabled = true;
    let viewer = null;
    const panoCache = {};

    const stage = document.getElementById('vt360-stage');
    const floorInput = document.getElementById('vt360-floor-input');
    const sceneModeEl = document.getElementById('vt360-scene-mode');
    const sceneTitleEl = document.getElementById('vt360-scene-title');
    const sceneSubEl = document.getElementById('vt360-scene-subtitle');
    const radarTitleEl = document.getElementById('vt360-radar-title');
    const compassDegEl = document.getElementById('vt360-compass-deg');
    const fovConeEl = document.getElementById('vt360-fov-cone');
    const freeCountEl = document.getElementById('vt360-free-count');
    const busyCountEl = document.getElementById('vt360-busy-count');
    const minimapNorth = document.getElementById('vt360-minimap-north');
    const minimapSouth = document.getElementById('vt360-minimap-south');
    const quickTitle = document.getElementById('vt360-quick-title');
    const quickPills = document.getElementById('vt360-quick-pills');

    const infoDrawer = document.getElementById('vt360-info-drawer');
    const infoWing = document.getElementById('vt360-info-wing');
    const infoCode = document.getElementById('vt360-info-code');
    const infoStatusBox = document.getElementById('vt360-info-status-box');
    const infoStatusLabel = document.getElementById('vt360-info-status-label');
    const infoStatusNote = document.getElementById('vt360-info-status-note');
    const infoEnterBtn = document.getElementById('vt360-btn-enter-toggle');
    const sessionListEl = document.getElementById('vt360-session-list');
    const saveForm = document.getElementById('vt360-save-form');
    const saveSessionInput = document.getElementById('vt360-save-session-id');

    /**
     * Konversi derajat Yaw (-180..180) ke koordinat X pada kanvas Equirectangular (0..W)
     */
    function yawToCanvasX(yawDeg, width) {
        let norm = ((yawDeg + 180) % 360);
        if (norm < 0) norm += 360;
        return (norm / 360) * width;
    }

    /**
     * Generator Tekstur Equirectangular 360° (2:1) untuk Koridor Lantai
     * Menghasilkan panorama 3D yang bisa diputar 360 derajat penuh di WebGL Pannellum
     */
    function buildCorridorPanorama(floorKey) {
        const cacheKey = 'corridor_' + floorKey + '_' + (onlyFreeHighlight ? 'free' : 'all');
        if (panoCache[cacheKey]) return panoCache[cacheKey];

        const fData = TOUR_DATA[floorKey];
        const W = 2048;
        const H = 1024;
        const canvas = document.createElement('canvas');
        canvas.width = W;
        canvas.height = H;
        const ctx = canvas.getContext('2d');

        // 1. Langit-langit (Ceiling: y = 0 .. 310)
        const ceilGrad = ctx.createLinearGradient(0, 0, 0, 310);
        ceilGrad.addColorStop(0, '#c2cfc6');
        ceilGrad.addColorStop(1, fData.theme.ceiling);
        ctx.fillStyle = ceilGrad;
        ctx.fillRect(0, 0, W, 310);

        // Garis panel akustik & lampu LED plafon
        ctx.strokeStyle = 'rgba(25, 78, 64, 0.14)';
        ctx.lineWidth = 2;
        for (let x = 0; x < W; x += 128) {
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, 310);
            ctx.stroke();

            // Lampu LED Plafon
            ctx.fillStyle = 'rgba(255, 255, 255, 0.88)';
            ctx.fillRect(x + 28, 175, 72, 22);
        }

        // 2. Dinding Koridor 360 (Walls: y = 310 .. 720)
        const wallGrad = ctx.createLinearGradient(0, 310, 0, 720);
        wallGrad.addColorStop(0, fData.theme.wallTop);
        wallGrad.addColorStop(1, fData.theme.wallBottom);
        ctx.fillStyle = wallGrad;
        ctx.fillRect(0, 310, W, 410);

        // Lis Sabuk Dinding Arsitektural
        ctx.fillStyle = fData.theme.strip;
        ctx.fillRect(0, 310, W, 14);
        ctx.fillRect(0, 708, W, 12);

        // Pilar kolom gedung setiap 256px
        ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
        for (let x = 0; x < W; x += 256) {
            ctx.fillRect(x, 324, 18, 384);
        }

        // 3. Lantai Keramik Koridor (Floor: y = 720 .. 1024)
        const floorGrad = ctx.createLinearGradient(0, 720, 0, H);
        floorGrad.addColorStop(0, fData.theme.floor);
        floorGrad.addColorStop(1, '#7a8f82');
        ctx.fillStyle = floorGrad;
        ctx.fillRect(0, 720, W, H - 720);

        // Garis perspektif nat keramik lantai
        ctx.strokeStyle = 'rgba(25, 78, 64, 0.16)';
        ctx.lineWidth = 2;
        for (let y = 740; y < H; y += 36) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(W, y);
            ctx.stroke();
        }
        for (let x = 0; x < W; x += 64) {
            ctx.beginPath();
            ctx.moveTo(x, 720);
            ctx.lineTo(x, H);
            ctx.stroke();
        }

        // 4. Penanda Zona Arah Mata Angin di Dinding Atas
        const directions = [
            { yaw: 0, text: 'SAYAP UTARA · ' + fData.label.toUpperCase() },
            { yaw: 90, text: 'SAYAP TIMUR · LIFT & TANGGA UTAMA' },
            { yaw: 180, text: 'SAYAP SELATAN · ' + fData.label.toUpperCase() },
            { yaw: -90, text: 'SAYAP BARAT · LOUNGE & BALKON' },
        ];
        directions.forEach(function (d) {
            const cx = yawToCanvasX(d.yaw, W);
            ctx.fillStyle = 'rgba(17, 41, 34, 0.82)';
            ctx.fillRect(cx - 170, 336, 340, 30);
            ctx.fillStyle = '#d9f5e5';
            ctx.font = 'bold 14px "Segoe UI", Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(d.text, cx, 356);
        });

        // Gambar Lift & Fasilitas di Timur (Yaw 90) & Lounge di Barat (Yaw -90)
        const eastX = yawToCanvasX(90, W);
        ctx.fillStyle = '#2d3e37';
        ctx.fillRect(eastX - 75, 420, 150, 288);
        ctx.fillStyle = '#8fa89d';
        ctx.fillRect(eastX - 63, 440, 58, 268);
        ctx.fillRect(eastX + 5, 440, 58, 268);
        ctx.fillStyle = '#0f241d';
        ctx.fillRect(eastX - 55, 390, 110, 24);
        ctx.fillStyle = '#5df2a8';
        ctx.font = 'bold 13px Arial';
        ctx.fillText('LIFT ▲▼ LT.' + fData.floor, eastX, 407);

        // 5. Gambar Pintu-Pintu Ruang Kuliah di Sepanjang Dinding Utara & Selatan
        fData.rooms.forEach(function (room) {
            const cx = yawToCanvasX(room.yaw, W);
            const doorW = 92;
            const doorH = 265;
            const doorX = cx - (doorW / 2);
            const doorY = 708 - doorH;

            const isDimmed = onlyFreeHighlight && room.is_busy;
            ctx.save();
            if (isDimmed) {
                ctx.globalAlpha = 0.25;
            }

            // Kusen Pintu
            ctx.fillStyle = room.is_busy ? '#7d3c16' : '#1b5e41';
            ctx.fillRect(doorX - 6, doorY - 6, doorW + 12, doorH + 6);

            // Daun Pintu Kayu/Aluminium
            ctx.fillStyle = room.is_busy ? '#a85523' : '#2a825c';
            ctx.fillRect(doorX, doorY, doorW, doorH);

            // Jendela Kaca Pintu
            ctx.fillStyle = room.is_busy ? '#fff3e6' : '#e8faef';
            ctx.fillRect(doorX + 12, doorY + 46, doorW - 24, 105);

            // Gagang Pintu
            ctx.fillStyle = '#e4ede8';
            ctx.fillRect(doorX + doorW - 18, doorY + 165, 10, 5);

            // Papan LED Kode Ruang di Atas Pintu
            ctx.fillStyle = '#10261f';
            ctx.fillRect(doorX - 10, doorY - 46, doorW + 20, 36);
            ctx.fillStyle = room.is_busy ? '#ff7b29' : '#2ee892';
            ctx.fillRect(doorX - 10, doorY - 46, 6, 36);

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 17px "Segoe UI", Arial, sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText(room.code, doorX + 2, doorY - 22);

            // Badge Status LED
            ctx.fillStyle = room.is_busy ? '#d95614' : '#1a8755';
            ctx.fillRect(doorX + 50, doorY - 38, 46, 20);
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 9px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(room.is_busy ? 'PAKAI' : 'KOSONG', doorX + 73, doorY - 25);

            ctx.restore();
        });

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        panoCache[cacheKey] = dataUrl;
        return dataUrl;
    }

    /**
     * Generator Tekstur Equirectangular 360° (2:1) untuk INTERIOR DALAM RUANG KULIAH
     */
    function buildInteriorPanorama(floorKey, roomCode) {
        const cacheKey = 'interior_' + floorKey + '_' + roomCode;
        if (panoCache[cacheKey]) return panoCache[cacheKey];

        const fData = TOUR_DATA[floorKey];
        const room = fData.rooms.find(r => r.code === roomCode);
        const W = 2048;
        const H = 1024;
        const canvas = document.createElement('canvas');
        canvas.width = W;
        canvas.height = H;
        const ctx = canvas.getContext('2d');

        // Plafon Kelas
        ctx.fillStyle = '#e5ece8';
        ctx.fillRect(0, 0, W, 300);

        // Dinding Kelas
        const wallGrad = ctx.createLinearGradient(0, 300, 0, 720);
        wallGrad.addColorStop(0, '#f7faf8');
        wallGrad.addColorStop(1, '#dce6df');
        ctx.fillStyle = wallGrad;
        ctx.fillRect(0, 300, W, 420);

        // Lantai Kelas
        ctx.fillStyle = '#b9c9bf';
        ctx.fillRect(0, 720, W, H - 720);

        // Dinding Depan Kelas (Yaw 0 -> X = 1024): Papan Tulis & Layar Proyektor Lebar
        const frontX = yawToCanvasX(0, W);
        ctx.fillStyle = '#1f332c';
        ctx.fillRect(frontX - 270, 360, 540, 230);
        ctx.fillStyle = room.is_busy ? '#fff8f0' : '#f0fbf4';
        ctx.fillRect(frontX - 258, 372, 516, 206);

        // Header Layar Proyektor
        ctx.fillStyle = room.is_busy ? '#c95716' : '#1b7a50';
        ctx.fillRect(frontX - 258, 372, 516, 40);
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 16px "Segoe UI", Arial';
        ctx.textAlign = 'center';
        ctx.fillText('SMART CLASSROOM ' + room.code + ' · STATUS: ' + room.status_label, frontX, 398);

        // Isi Layar Proyektor (Jadwal / Status Kosong)
        ctx.fillStyle = '#142b23';
        ctx.font = 'bold 24px "Segoe UI", Arial';
        ctx.fillText(room.board_title.substring(0, 40), frontX, 452);

        ctx.fillStyle = '#335247';
        ctx.font = 'bold 17px "Segoe UI", Arial';
        ctx.fillText(room.board_meta, frontX, 492);

        ctx.fillStyle = '#536e64';
        ctx.font = '15px "Segoe UI", Arial';
        ctx.fillText(room.board_lecturer, frontX, 530);

        // Jendela Kaca Samping (Yaw -90 -> X = 512)
        const leftX = yawToCanvasX(-90, W);
        for (let i = -1; i <= 1; i++) {
            ctx.fillStyle = '#486b5d';
            ctx.fillRect(leftX + (i * 120) - 48, 390, 96, 190);
            ctx.fillStyle = '#cbe6f5';
            ctx.fillRect(leftX + (i * 120) - 40, 400, 80, 170);
        }

        // Deretan Meja Kursi Mahasiswa (Yaw 90 & lantai tengah)
        const rightX = yawToCanvasX(90, W);
        ctx.fillStyle = '#755638';
        for (let r = 0; r < 3; r++) {
            ctx.fillRect(rightX - 180 + (r * 130), 620, 100, 75);
        }

        // Pintu Keluar Kelas di Dinding Belakang (Yaw 180 -> X = 0 dan X = 2048)
        [30, W - 110].forEach(function (doorX) {
            ctx.fillStyle = '#295444';
            ctx.fillRect(doorX, 430, 85, 280);
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 13px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('KELUAR', doorX + 42, 420);
        });

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        panoCache[cacheKey] = dataUrl;
        return dataUrl;
    }

    /**
     * Custom Renderer untuk Hotspot di dalam Bola 360 Pannellum
     */
    function createDoorHotspotElement(hotSpotDiv, args) {
        hotSpotDiv.classList.add('vt360-pnlm-hotspot');
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'vt360-pnlm-door-btn ' + (args.is_busy ? 'is-busy' : 'is-free');
        btn.innerHTML = `
            <span class="vt360-pnlm-code">${args.code}</span>
            <span class="vt360-pnlm-state">${args.is_busy ? 'Terpakai' : 'Kosong'}</span>
        `;
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            loadInterior360(args.code);
        });
        hotSpotDiv.appendChild(btn);
    }

    function createInfoHotspotElement(hotSpotDiv, args) {
        hotSpotDiv.classList.add('vt360-pnlm-hotspot');
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'vt360-pnlm-info-btn';
        btn.title = 'Lihat Jadwal & Detail Ruang ' + args.code;
        btn.textContent = 'i';
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            openInfoDrawer(args.code);
        });
        hotSpotDiv.appendChild(btn);
    }

    function createChevronHotspotElement(hotSpotDiv, args) {
        hotSpotDiv.classList.add('vt360-pnlm-hotspot');
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'vt360-pnlm-chevron-btn';
        btn.title = args.label;
        btn.innerHTML = '⌃';
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (args.action === 'exit') {
                loadCorridor360(currentFloorKey, args.targetYaw || 0);
            } else if (viewer) {
                viewer.lookAt(0, args.targetYaw, 100, true);
            }
        });
        hotSpotDiv.appendChild(btn);
    }

    /**
     * Muat Panorama 360° Koridor Lantai ke dalam WebGL Pannellum
     */
    function loadCorridor360(floorKey, initialYaw) {
        currentFloorKey = floorKey;
        sceneMode = 'corridor';
        const fData = TOUR_DATA[floorKey];
        if (!fData) return;

        sceneModeEl.textContent = '360° CORRIDOR';
        sceneTitleEl.textContent = fData.corridor_title;
        sceneSubEl.textContent = fData.corridor_subtitle;
        radarTitleEl.textContent = fData.label;
        freeCountEl.textContent = fData.free_count;
        busyCountEl.textContent = fData.busy_count;
        if (floorInput) floorInput.value = floorKey;

        const hotSpots = [];

        // Tambahkan Door Hotspot & Info Hotspot (`i`) pada setiap pintu ruangan
        fData.rooms.forEach(function (room) {
            if (onlyFreeHighlight && room.is_busy) return;

            hotSpots.push({
                pitch: -6,
                yaw: room.yaw,
                type: 'custom',
                createTooltipFunc: createDoorHotspotElement,
                createTooltipArgs: room,
            });

            hotSpots.push({
                pitch: 8,
                yaw: room.yaw,
                type: 'custom',
                createTooltipFunc: createInfoHotspotElement,
                createTooltipArgs: room,
            });
        });

        // Tambahkan Floor Chevron Arrows di Lantai Koridor
        [-55, 0, 55, 180].forEach(function (navYaw) {
            hotSpots.push({
                pitch: -22,
                yaw: navYaw,
                type: 'custom',
                createTooltipFunc: createChevronHotspotElement,
                createTooltipArgs: {
                    action: 'pan',
                    targetYaw: navYaw,
                    label: 'Arahkan pandangan ke sudut ' + navYaw + '°',
                },
            });
        });

        const panoDataUrl = buildCorridorPanorama(floorKey);
        mountPannellumViewer(panoDataUrl, initialYaw || 0, hotSpots);
        renderMiniMapAndQuickStrip(fData);
    }

    /**
     * Muat Panorama 360° Interior Dalam Ruang Kuliah
     */
    function loadInterior360(roomCode) {
        const fData = TOUR_DATA[currentFloorKey];
        const room = fData.rooms.find(r => r.code === roomCode);
        if (!room) return;

        sceneMode = 'interior';
        activeRoomCode = room.code;

        sceneModeEl.textContent = '360° CLASSROOM INTERIOR';
        sceneTitleEl.textContent = 'Classroom ' + room.code + ' (' + room.status_label + ')';
        sceneSubEl.textContent = fData.label + ' · ' + room.wing + ' · ' + room.status_note;

        const hotSpots = [
            {
                pitch: 4,
                yaw: 0,
                type: 'custom',
                createTooltipFunc: createInfoHotspotElement,
                createTooltipArgs: room,
            },
            {
                pitch: -12,
                yaw: 175,
                type: 'custom',
                createTooltipFunc: createChevronHotspotElement,
                createTooltipArgs: {
                    action: 'exit',
                    targetYaw: room.yaw,
                    label: 'Keluar kembali ke Koridor ' + fData.label,
                },
            },
        ];

        const panoDataUrl = buildInteriorPanorama(currentFloorKey, room.code);
        mountPannellumViewer(panoDataUrl, 0, hotSpots);
        openInfoDrawer(room.code);
    }

    function mountPannellumViewer(panoramaUrl, startYaw, hotSpots) {
        if (viewer) {
            try {
                viewer.destroy();
            } catch (e) {}
        }

        viewer = pannellum.viewer('vt360-pano-viewer', {
            type: 'equirectangular',
            panorama: panoramaUrl,
            autoLoad: true,
            showControls: false,
            compass: false,
            yaw: startYaw,
            pitch: 0,
            hfov: 100,
            minHfov: 60,
            maxHfov: 120,
            autoRotate: autoRotateEnabled && sceneMode === 'corridor' ? -2.2 : 0,
            hotSpots: hotSpots,
        });
    }

    // Sinkronisasi Radar Kompas & Kerucut FOV dengan Sudut Kamera WebGL Pannellum
    function syncRadarCompass() {
        if (viewer && typeof viewer.getYaw === 'function') {
            const currentYaw = viewer.getYaw();
            const norm = ((Math.round(currentYaw) % 360) + 360) % 360;
            let dir = 'UTARA';
            if (norm >= 45 && norm < 135) dir = 'TIMUR';
            else if (norm >= 135 && norm < 225) dir = 'SELATAN';
            else if (norm >= 225 && norm < 315) dir = 'BARAT';

            compassDegEl.textContent = norm + '° ' + dir;
            fovConeEl.style.transform = `translate(-50%, -50%) rotate(${currentYaw}deg)`;
        }
        requestAnimationFrame(syncRadarCompass);
    }

    function renderMiniMapAndQuickStrip(fData) {
        minimapNorth.innerHTML = '';
        minimapSouth.innerHTML = '';

        fData.wing_north.forEach(function (r) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'vt360-mmdot ' + (r.is_busy ? 'is-busy' : 'is-free');
            dot.title = r.code + ' (' + r.status_label + ')';
            dot.textContent = r.code.split('.')[1] || r.code;
            dot.addEventListener('click', function () {
                focusRoomIn360(r.code);
            });
            minimapNorth.appendChild(dot);
        });

        fData.wing_south.forEach(function (r) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'vt360-mmdot ' + (r.is_busy ? 'is-busy' : 'is-free');
            dot.title = r.code + ' (' + r.status_label + ')';
            dot.textContent = r.code.split('.')[1] || r.code;
            dot.addEventListener('click', function () {
                focusRoomIn360(r.code);
            });
            minimapSouth.appendChild(dot);
        });

        quickTitle.textContent = 'Pintasan Kamera Pintu Ruangan · ' + fData.label;
        quickPills.innerHTML = '';
        fData.rooms.forEach(function (r) {
            const pill = document.createElement('button');
            pill.type = 'button';
            pill.className = 'vt360-qpill ' + (r.is_busy ? 'is-busy' : 'is-free');
            pill.innerHTML = `<strong>${r.code}</strong><span>${r.is_busy ? 'Terpakai' : 'Kosong'}</span>`;
            pill.addEventListener('click', function () {
                focusRoomIn360(r.code);
            });
            pill.addEventListener('dblclick', function () {
                loadInterior360(r.code);
            });
            quickPills.appendChild(pill);
        });
    }

    function focusRoomIn360(roomCode) {
        const fData = TOUR_DATA[currentFloorKey];
        const room = fData.rooms.find(r => r.code === roomCode);
        if (!room) return;

        if (sceneMode !== 'corridor') {
            loadCorridor360(currentFloorKey, room.yaw);
        } else if (viewer) {
            viewer.stopAutoRotate();
            document.getElementById('vt360-btn-autorotate').classList.remove('is-active');
            viewer.lookAt(-3, room.yaw, 95, true);
        }
        openInfoDrawer(room.code);
    }

    function openInfoDrawer(roomCode) {
        const fData = TOUR_DATA[currentFloorKey];
        const room = fData.rooms.find(r => r.code === roomCode);
        if (!room) return;

        activeRoomCode = room.code;
        infoDrawer.hidden = false;
        infoWing.textContent = fData.label.toUpperCase() + ' · ' + room.wing.toUpperCase();
        infoCode.textContent = 'Ruang ' + room.code;
        infoStatusBox.className = 'vt360-info-status ' + (room.is_busy ? 'is-busy' : 'is-free');
        infoStatusLabel.textContent = room.status_label;
        infoStatusNote.textContent = room.status_note;

        if (sceneMode === 'interior') {
            infoEnterBtn.textContent = '↩ Keluar ke Koridor 360°';
        } else {
            infoEnterBtn.textContent = 'Masuk ke Interior Ruang ' + room.code + ' (360°) →';
        }

        if (saveForm && saveSessionInput) {
            if (room.active_session_id) {
                saveForm.hidden = false;
                saveSessionInput.value = room.active_session_id;
            } else {
                saveForm.hidden = true;
            }
        }

        sessionListEl.innerHTML = '';
        if (room.sessions.length === 0) {
            sessionListEl.innerHTML = '<li class="vt360-session-empty">Tidak ada jadwal kuliah di ruangan ini hari ini.</li>';
        } else {
            room.sessions.forEach(function (s) {
                const li = document.createElement('li');
                li.className = 'vt360-session-item ' + (s.is_current ? 'is-current' : '');
                li.innerHTML = `
                    <div class="vt360-stime">${s.start} – ${s.end}</div>
                    <div class="vt360-sbody">
                        <strong>${s.title}</strong>
                        <span>${s.program} · Kelas ${s.group}</span>
                        <small>${s.lecturer}</small>
                    </div>
                `;
                sessionListEl.appendChild(li);
            });
        }
    }

    // Control Dock Handlers
    const floorDrawer = document.getElementById('vt360-floor-drawer');
    document.getElementById('vt360-btn-floors').addEventListener('click', function () {
        this.classList.toggle('is-active');
        floorDrawer.classList.toggle('is-hidden');
    });

    document.querySelectorAll('[data-switch-floor]').forEach(function (card) {
        card.addEventListener('click', function () {
            const targetFloor = card.getAttribute('data-switch-floor');
            document.querySelectorAll('[data-switch-floor]').forEach(c => c.classList.toggle('is-active', c === card));
            infoDrawer.hidden = true;
            loadCorridor360(targetFloor, 0);
        });
    });

    const allModal = document.getElementById('vt360-all-modal');
    document.getElementById('vt360-btn-all').addEventListener('click', function () {
        allModal.hidden = false;
    });
    document.getElementById('vt360-close-modal').addEventListener('click', function () {
        allModal.hidden = true;
    });

    document.querySelectorAll('[data-teleport-room]').forEach(function (card) {
        card.addEventListener('click', function () {
            const targetFloor = card.getAttribute('data-teleport-floor');
            const targetRoom = card.getAttribute('data-teleport-room');
            document.querySelectorAll('[data-switch-floor]').forEach(function (c) {
                c.classList.toggle('is-active', c.getAttribute('data-switch-floor') === targetFloor);
            });
            allModal.hidden = true;
            loadCorridor360(targetFloor, 0);
            focusRoomIn360(targetRoom);
        });
    });

    const modalSearch = document.getElementById('vt360-modal-search');
    let modalStatusFilter = 'all';

    function filterModalCards() {
        const q = (modalSearch.value || '').trim().toLowerCase();
        document.querySelectorAll('[data-teleport-room]').forEach(function (card) {
            const st = card.getAttribute('data-room-status');
            const txt = card.getAttribute('data-room-search') || '';
            const statusOk = modalStatusFilter === 'all' || st === modalStatusFilter;
            const searchOk = q === '' || txt.includes(q);
            card.hidden = !(statusOk && searchOk);
        });
    }

    modalSearch.addEventListener('input', filterModalCards);
    document.querySelectorAll('[data-modal-filter]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            modalStatusFilter = chip.getAttribute('data-modal-filter');
            document.querySelectorAll('[data-modal-filter]').forEach(c => c.classList.toggle('is-active', c === chip));
            filterModalCards();
        });
    });

    document.getElementById('vt360-btn-filter-free').addEventListener('click', function () {
        onlyFreeHighlight = !onlyFreeHighlight;
        this.classList.toggle('is-active', onlyFreeHighlight);
        if (sceneMode === 'corridor') {
            const currentYaw = viewer ? viewer.getYaw() : 0;
            loadCorridor360(currentFloorKey, currentYaw);
        }
    });

    document.getElementById('vt360-btn-hotspots').addEventListener('click', function () {
        hideHotspots = !hideHotspots;
        this.classList.toggle('is-active', hideHotspots);
        stage.classList.toggle('vt360-hide-hotspots', hideHotspots);
    });

    document.getElementById('vt360-btn-autorotate').addEventListener('click', function () {
        autoRotateEnabled = !autoRotateEnabled;
        this.classList.toggle('is-active', autoRotateEnabled);
        if (viewer) {
            if (autoRotateEnabled) {
                viewer.startAutoRotate(-2.2);
            } else {
                viewer.stopAutoRotate();
            }
        }
    });

    document.getElementById('vt360-btn-fullscreen').addEventListener('click', function () {
        if (!document.fullscreenElement) {
            stage.requestFullscreen().catch(function () {});
        } else {
            document.exitFullscreen().catch(function () {});
        }
    });

    document.getElementById('vt360-info-close').addEventListener('click', function () {
        infoDrawer.hidden = true;
    });

    infoEnterBtn.addEventListener('click', function () {
        if (!activeRoomCode) return;
        if (sceneMode === 'interior') {
            const fData = TOUR_DATA[currentFloorKey];
            const room = fData.rooms.find(r => r.code === activeRoomCode);
            loadCorridor360(currentFloorKey, room ? room.yaw : 0);
        } else {
            loadInterior360(activeRoomCode);
        }
    });

    // Jalankan pertama kali
    loadCorridor360(currentFloorKey, 0);
    requestAnimationFrame(syncRadarCompass);
});
</script>
@endsection
