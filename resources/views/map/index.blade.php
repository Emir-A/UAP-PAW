@extends('layout')

@section('title', 'Virtual Tour 360° Map Per Lantai')
@section('heading', 'Virtual Tour 360° & Denah Ruang Per Lantai')

@section('description')
    Pilih gedung, lantai, dan ruangan untuk melihat panorama Virtual Tour 360° sekaligus memeriksa ketersediaan ruang kuliah secara langsung.
@endsection

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

<style>
    .vt360-page {
        display: grid;
        gap: 18px;
    }

    .vt360-card {
        background: #ffffff;
        border: 1px solid #d8e2dc;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 4px 14px rgba(15, 34, 28, 0.05);
    }

    .vt360-filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 12px;
        align-items: end;
    }

    .vt360-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .vt360-field label {
        margin: 0;
        font-size: 12px;
        font-weight: 700;
        color: #2c4a3e;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .vt360-field select,
    .vt360-field input[type="time"] {
        width: 100%;
        height: 40px;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid #bfd0c6;
        background: #f9fbfa;
        color: #162b25;
        font-size: 14px;
        font-weight: 600;
    }

    .vt360-field select:focus,
    .vt360-field input[type="time"]:focus {
        outline: none;
        border-color: #194e40;
        background: #ffffff;
    }

    .vt360-time-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #e7efea;
    }

    .vt360-time-form {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .vt360-time-form label {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #2c4a3e;
    }

    .vt360-time-form select,
    .vt360-time-form input[type="time"] {
        height: 36px;
        padding: 4px 10px;
        border-radius: 7px;
        border: 1px solid #bfd0c6;
        background: #ffffff;
        font-size: 13px;
        font-weight: 600;
    }

    .vt360-btn-submit {
        height: 36px;
        padding: 0 14px;
        border-radius: 7px;
        border: none;
        background: #194e40;
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .vt360-btn-submit:hover {
        background: #133b30;
    }

    .vt360-summary-badges {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 700;
    }

    .vt360-badge-free {
        padding: 5px 12px;
        border-radius: 99px;
        background: #e7f6ed;
        color: #16653c;
        border: 1px solid #a5dfc0;
    }

    .vt360-badge-busy {
        padding: 5px 12px;
        border-radius: 99px;
        background: #fff2e8;
        color: #9e430e;
        border: 1px solid #f2bfa0;
    }

    .vt360-main-layout {
        display: grid;
        grid-template-columns: 1fr 330px;
        gap: 18px;
        align-items: start;
    }

    @media (max-width: 960px) {
        .vt360-main-layout {
            grid-template-columns: 1fr;
        }
    }

    .vt360-viewer-box {
        position: relative;
        width: 100%;
        height: 580px;
        border-radius: 14px;
        overflow: hidden;
        border: 2px solid #194e40;
        background: #0d1d18;
        box-shadow: 0 12px 28px rgba(15, 34, 28, 0.16);
    }

    .vt360-viewer-box:fullscreen {
        height: 100vh;
        border-radius: 0;
    }

    .vt360-iframe {
        width: 100%;
        height: 100%;
        border: 0;
        display: block;
    }

    .vt360-viewer-topbar {
        position: absolute;
        top: 14px;
        right: 14px;
        z-index: 5;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        pointer-events: none;
    }

    .vt360-bottom-left-watermark {
        position: absolute;
        bottom: 22px;
        left: 22px;
        z-index: 5;
        padding: 10px 16px;
        border-radius: 12px;
        background: rgba(14, 31, 26, 0.78);
        backdrop-filter: blur(6px);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.18);
        pointer-events: none;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
    }

    .vt360-bottom-left-watermark strong {
        display: block;
        font-size: 22px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: 0.01em;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
    }

    .vt360-bottom-left-watermark span {
        display: block;
        margin-top: 3px;
        font-size: 14px;
        font-weight: 600;
        color: #b7eed0;
        text-shadow: 0 2px 6px rgba(0, 0, 0, 0.6);
    }

    .vt360-viewer-actions {
        display: flex;
        gap: 8px;
        pointer-events: auto;
    }

    .vt360-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        background: rgba(14, 31, 26, 0.85);
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .vt360-action-btn:hover {
        background: #227858;
        border-color: #79eeb7;
    }

    .vt360-side-panel {
        display: grid;
        gap: 14px;
    }

    .vt360-room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(92px, 1fr));
        gap: 8px;
        margin-top: 10px;
    }

    .vt360-room-pill {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 8px 6px;
        border-radius: 8px;
        border: 1px solid #d5e0da;
        background: #f8fbf9;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .vt360-room-pill strong {
        font-size: 14px;
        color: #152b24;
    }

    .vt360-room-pill span {
        font-size: 11px;
        font-weight: 700;
    }

    .vt360-room-pill.is-free {
        border-color: #8fd4ae;
        background: #eef9f2;
    }

    .vt360-room-pill.is-free span {
        color: #177043;
    }

    .vt360-room-pill.is-busy {
        border-color: #efb38c;
        background: #fff5ee;
    }

    .vt360-room-pill.is-busy span {
        color: #a9470e;
    }

    .vt360-room-pill.is-selected,
    .vt360-room-pill:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(25, 78, 64, 0.15);
        border-width: 2px;
        border-color: #194e40;
    }

    .vt360-detail-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .vt360-detail-head h3 {
        margin: 0;
        font-size: 18px;
    }

    .vt360-status-banner {
        padding: 10px 12px;
        border-radius: 8px;
        margin-bottom: 12px;
        font-size: 13px;
    }

    .vt360-status-banner.is-free {
        background: #e9f7ef;
        border-left: 4px solid #1f8a5b;
        color: #144930;
    }

    .vt360-status-banner.is-busy {
        background: #fff4ec;
        border-left: 4px solid #d9641e;
        color: #7c350b;
    }

    .vt360-status-banner p {
        margin: 4px 0 0;
        font-size: 12px;
    }

    .vt360-schedule-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 8px;
        max-height: 240px;
        overflow-y: auto;
    }

    .vt360-schedule-item {
        padding: 8px 10px;
        border-radius: 8px;
        border: 1px solid #e1eae5;
        background: #f9fbfa;
        font-size: 12px;
    }

    .vt360-schedule-item.is-current {
        border-color: #d9641e;
        background: #fff6ee;
    }

    .vt360-schedule-time {
        font-weight: 800;
        color: #194e40;
    }
</style>

<div class="vt360-page" id="vt360-app" data-initial-floor="{{ $initialFloor }}">

    <section class="vt360-card" aria-label="Filter Lokasi Virtual Tour 360">
        <div class="vt360-filter-grid">
            <div class="vt360-field">
                <label for="vt360-filter-building">1. Pilih Gedung</label>
                <select id="vt360-filter-building">
                    <option value="F" @selected($activeFloor['building'] === 'F')>Gedung F (Utama)</option>
                    <option value="G" @selected($activeFloor['building'] === 'G')>Gedung G</option>
                </select>
            </div>

            <div class="vt360-field">
                <label for="vt360-filter-floor">2. Pilih Lantai</label>
                <select id="vt360-filter-floor">
                    @foreach($tourFloors as $fKey => $fData)
                        <option value="{{ $fKey }}" @selected($fKey === $initialFloor)>
                            {{ $fData['label'] }} ({{ $fData['free_count'] }}/{{ $fData['total_rooms'] }} Kosong)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="vt360-field">
                <label for="vt360-filter-room">3. Pilih Ruangan / Spot 360°</label>
                <select id="vt360-filter-room">
                    <option value="">-- Koridor Utama Lantai {{ $activeFloor['floor'] }} --</option>
                    <optgroup label="Ruang Kuliah">
                        @foreach($activeFloor['rooms'] as $r)
                            <option value="room:{{ $r['code'] }}">
                                Ruang {{ $r['code'] }} ({{ $r['status_label'] }})
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Spot 360° Lainnya">
                        @foreach($activeFloor['tour_scenes'] as $sc)
                            <option value="media:{{ $sc['media'] }}:{{ $sc['label'] }}">
                                {{ $sc['label'] }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div class="vt360-field">
                <label for="vt360-filter-status">4. Filter Status Ruang</label>
                <select id="vt360-filter-status">
                    <option value="all">Semua Ruangan</option>
                    <option value="free">Hanya Ruang Kosong</option>
                    <option value="busy">Sedang Terpakai</option>
                </select>
            </div>
        </div>

        <div class="vt360-time-row">
            <form method="GET" action="{{ route('floor-map.index') }}" class="vt360-time-form">
                <input type="hidden" name="floor" id="vt360-floor-input" value="{{ $initialFloor }}">

                <label for="vt-day">Waktu Cek:</label>
                <select id="vt-day" name="day">
                    @foreach($days as $num => $label)
                        <option value="{{ $num }}" @selected($day === $num)>{{ $label }}</option>
                    @endforeach
                </select>

                <input id="vt-time" type="time" name="time" value="{{ $time }}" required>

                <button type="submit" class="vt360-btn-submit">Cek Jam Ini</button>
            </form>

            <div class="vt360-summary-badges">
                <span class="vt360-badge-free"><b id="vt360-free-count">{{ $activeFloor['free_count'] }}</b> Ruang Kosong</span>
                <span class="vt360-badge-busy"><b id="vt360-busy-count">{{ $activeFloor['busy_count'] }}</b> Terpakai</span>
            </div>
        </div>
    </section>

    <div class="vt360-main-layout">

        <section class="vt360-viewer-box" id="vt360-stage" aria-label="Virtual Tour 360">
            <div class="vt360-viewer-topbar">
                <div class="vt360-viewer-actions">
                    <button type="button" class="vt360-action-btn" id="vt360-btn-fullscreen">
                        ⛶ Fullscreen
                    </button>
                </div>
            </div>

            <div class="vt360-bottom-left-watermark">
                <strong id="vt360-scene-title">Ruang {{ $activeFloor['rooms'][0]['code'] ?? $activeFloor['corridor_title'] }}</strong>
                <span id="vt360-scene-subtitle">{{ $activeFloor['label'] }}</span>
            </div>

            <iframe
                id="vt360-live-iframe"
                class="vt360-iframe"
                src="{{ route('floor-map.index', ['embed' => 1]) }}#media={{ $activeFloor['tour_default_media'] }}"
                data-tour-src="{{ $activeFloor['tour_url'] }}"
                title="Virtual Tour 360 - {{ $activeFloor['label'] }}"
                allow="fullscreen; accelerometer; gyroscope; autoplay"
                allowfullscreen>
            </iframe>
        </section>

        <aside class="vt360-side-panel">
            <div class="vt360-card">
                <strong id="vt360-quick-title">Ruangan di {{ $activeFloor['label'] }}</strong>
                <p style="margin: 4px 0 0; font-size: 12px; color: #5b7065;">
                    Klik ruangan untuk melihat titik 360° &amp; jadwal kuliahnya:
                </p>

                <div class="vt360-room-grid" id="vt360-quick-pills">
                    @foreach($activeFloor['rooms'] as $room)
                        <button
                            type="button"
                            class="vt360-room-pill {{ $room['is_busy'] ? 'is-busy' : 'is-free' }}"
                            data-quick-room="{{ $room['code'] }}">
                            <strong>{{ $room['code'] }}</strong>
                            <span>{{ $room['is_busy'] ? 'Terpakai' : 'Kosong' }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="vt360-card" id="vt360-info-drawer">
                <div class="vt360-detail-head">
                    <div>
                        <small id="vt360-info-wing" style="font-weight: 700; color: #194e40;">{{ $activeFloor['label'] }}</small>
                        <h3 id="vt360-info-code">Pilih Ruangan</h3>
                    </div>
                </div>

                <div class="vt360-status-banner is-free" id="vt360-info-status-box">
                    <strong id="vt360-info-status-label">INFO KORIDOR</strong>
                    <p id="vt360-info-status-note">Klik salah satu nomor ruang di atas untuk melihat jadwal lengkap hari {{ $days[$day] }}.</p>
                </div>

                @auth
                    <form method="POST" action="{{ route('timetable.store') }}" id="vt360-save-form" hidden style="margin-bottom: 12px;">
                        @csrf
                        <input type="hidden" name="course_session_id" id="vt360-save-session-id" value="">
                        <button type="submit" class="vt360-btn-submit" style="width: 100%;">
                            + Simpan Kelas Ini ke Jadwal Pribadi
                        </button>
                    </form>
                @endauth

                <div>
                    <h4 style="margin: 0 0 8px; font-size: 13px;">Jadwal Hari {{ $days[$day] }} ({{ $time }} WIB)</h4>
                    <ul class="vt360-schedule-list" id="vt360-session-list">
                        <li class="vt360-schedule-item">Silakan pilih kode ruang untuk menampilkan jadwal kuliah.</li>
                    </ul>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const TOUR_DATA = @json($tourFloors);

    let currentFloorKey = document.getElementById('vt360-app').dataset.initialFloor || 'F2';
    let activeRoomCode = null;
    let roomStatusFilter = 'all';
    let currentTourMedia = (TOUR_DATA[currentFloorKey] && TOUR_DATA[currentFloorKey].tour_default_media) || 125;

    const stage = document.getElementById('vt360-stage');
    const liveIframe = document.getElementById('vt360-live-iframe');
    const floorInput = document.getElementById('vt360-floor-input');

    const filterBuildingEl = document.getElementById('vt360-filter-building');
    const filterFloorEl = document.getElementById('vt360-filter-floor');
    const filterRoomEl = document.getElementById('vt360-filter-room');
    const filterStatusEl = document.getElementById('vt360-filter-status');

    const sceneTitleEl = document.getElementById('vt360-scene-title');
    const sceneSubEl = document.getElementById('vt360-scene-subtitle');
    const freeCountEl = document.getElementById('vt360-free-count');
    const busyCountEl = document.getElementById('vt360-busy-count');
    const quickTitle = document.getElementById('vt360-quick-title');
    const quickPills = document.getElementById('vt360-quick-pills');

    const infoWing = document.getElementById('vt360-info-wing');
    const infoCode = document.getElementById('vt360-info-code');
    const infoStatusBox = document.getElementById('vt360-info-status-box');
    const infoStatusLabel = document.getElementById('vt360-info-status-label');
    const infoStatusNote = document.getElementById('vt360-info-status-note');
    const sessionListEl = document.getElementById('vt360-session-list');
    const saveForm = document.getElementById('vt360-save-form');
    const saveSessionInput = document.getElementById('vt360-save-session-id');

    const embedBaseUrl = @json(route('floor-map.index', ['embed' => 1]));

    function setTourMediaScene(mediaIndex, customTitle) {
        const fData = TOUR_DATA[currentFloorKey];
        if (!fData) return;

        currentTourMedia = mediaIndex || fData.tour_default_media || 1;
        const targetUrl = `${embedBaseUrl}#media=${currentTourMedia}`;

        if (liveIframe && liveIframe.getAttribute('src') !== targetUrl) {
            liveIframe.setAttribute('src', targetUrl);
        }

        sceneTitleEl.textContent = customTitle || fData.corridor_title;
        sceneSubEl.textContent = fData.label;
    }

    function syncFiltersAndRooms() {
        const fData = TOUR_DATA[currentFloorKey];
        if (!fData) return;

        if (floorInput) floorInput.value = currentFloorKey;
        freeCountEl.textContent = fData.free_count;
        busyCountEl.textContent = fData.busy_count;
        quickTitle.textContent = 'Ruangan di ' + fData.label;

        if (filterBuildingEl) {
            filterBuildingEl.value = fData.building;
        }

        if (filterFloorEl) {
            filterFloorEl.innerHTML = '';
            Object.values(TOUR_DATA).forEach(function (fl) {
                if (fl.building === fData.building) {
                    const opt = document.createElement('option');
                    opt.value = fl.key;
                    opt.selected = (fl.key === currentFloorKey);
                    opt.textContent = `${fl.label} (${fl.free_count}/${fl.total_rooms} Kosong)`;
                    filterFloorEl.appendChild(opt);
                }
            });
        }

        if (filterRoomEl) {
            filterRoomEl.innerHTML = '';
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = `-- Koridor Utama Lantai ${fData.floor} --`;
            filterRoomEl.appendChild(defaultOpt);

            const roomGroup = document.createElement('optgroup');
            roomGroup.label = 'Ruang Kuliah';
            fData.rooms.forEach(function (r) {
                if (roomStatusFilter === 'free' && r.is_busy) return;
                if (roomStatusFilter === 'busy' && !r.is_busy) return;
                const opt = document.createElement('option');
                opt.value = 'room:' + r.code;
                opt.textContent = `Ruang ${r.code} (${r.status_label})`;
                roomGroup.appendChild(opt);
            });
            filterRoomEl.appendChild(roomGroup);

            const sceneGroup = document.createElement('optgroup');
            sceneGroup.label = 'Spot 360° Lainnya';
            (fData.tour_scenes || []).forEach(function (sc) {
                const opt = document.createElement('option');
                opt.value = `media:${sc.media}:${sc.label}`;
                opt.textContent = sc.label;
                sceneGroup.appendChild(opt);
            });
            filterRoomEl.appendChild(sceneGroup);

            if (activeRoomCode) {
                filterRoomEl.value = 'room:' + activeRoomCode;
            }
        }

        quickPills.innerHTML = '';
        fData.rooms.forEach(function (r) {
            if (roomStatusFilter === 'free' && r.is_busy) return;
            if (roomStatusFilter === 'busy' && !r.is_busy) return;

            const pill = document.createElement('button');
            pill.type = 'button';
            pill.className = 'vt360-room-pill ' + (r.is_busy ? 'is-busy' : 'is-free') + (activeRoomCode === r.code ? ' is-selected' : '');
            pill.innerHTML = `<strong>${r.code}</strong><span>${r.is_busy ? 'Terpakai' : 'Kosong'}</span>`;
            pill.addEventListener('click', function () {
                selectRoom(r.code);
            });
            quickPills.appendChild(pill);
        });
    }

    function selectRoom(roomCode) {
        const fData = TOUR_DATA[currentFloorKey];
        const room = fData.rooms.find(r => r.code === roomCode);
        if (!room) return;

        activeRoomCode = room.code;
        if (filterRoomEl) {
            filterRoomEl.value = 'room:' + room.code;
        }

        quickPills.querySelectorAll('.vt360-room-pill').forEach(function (btn) {
            const codeEl = btn.querySelector('strong');
            btn.classList.toggle('is-selected', codeEl && codeEl.textContent === room.code);
        });

        setTourMediaScene(room.tour_media || fData.tour_default_media, 'Ruang ' + room.code + ' (' + room.status_label + ')');

        infoWing.textContent = fData.label + ' · ' + room.wing;
        infoCode.textContent = 'Ruang ' + room.code;
        infoStatusBox.className = 'vt360-status-banner ' + (room.is_busy ? 'is-busy' : 'is-free');
        infoStatusLabel.textContent = room.status_label;
        infoStatusNote.textContent = room.status_note;

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
            sessionListEl.innerHTML = '<li class="vt360-schedule-item">Tidak ada jadwal kuliah di ruangan ini hari ini.</li>';
        } else {
            room.sessions.forEach(function (s) {
                const li = document.createElement('li');
                li.className = 'vt360-schedule-item ' + (s.is_current ? 'is-current' : '');
                li.innerHTML = `
                    <div class="vt360-schedule-time">${s.start} – ${s.end} WIB</div>
                    <div><strong>${s.title}</strong></div>
                    <div>${s.program} · Kelas ${s.group}</div>
                    <small>${s.lecturer}</small>
                `;
                sessionListEl.appendChild(li);
            });
        }
    }

    filterBuildingEl.addEventListener('change', function () {
        const targetBuilding = this.value;
        const firstFloor = Object.values(TOUR_DATA).find(fl => fl.building === targetBuilding);
        if (firstFloor) {
            currentFloorKey = firstFloor.key;
            activeRoomCode = null;
            syncFiltersAndRooms();
            setTourMediaScene(firstFloor.tour_default_media, firstFloor.corridor_title);
            if (firstFloor.rooms.length > 0) {
                selectRoom(firstFloor.rooms[0].code);
            }
        }
    });

    filterFloorEl.addEventListener('change', function () {
        if (!TOUR_DATA[this.value]) return;
        currentFloorKey = this.value;
        activeRoomCode = null;
        const fData = TOUR_DATA[currentFloorKey];
        syncFiltersAndRooms();
        setTourMediaScene(fData.tour_default_media, fData.corridor_title);
        if (fData.rooms.length > 0) {
            selectRoom(fData.rooms[0].code);
        }
    });

    filterRoomEl.addEventListener('change', function () {
        const val = this.value;
        const fData = TOUR_DATA[currentFloorKey];
        if (!fData) return;

        if (!val) {
            activeRoomCode = null;
            setTourMediaScene(fData.tour_default_media, fData.corridor_title);
            return;
        }

        if (val.startsWith('room:')) {
            selectRoom(val.replace('room:', ''));
        } else if (val.startsWith('media:')) {
            const parts = val.split(':');
            const mediaId = Number(parts[1]);
            const label = parts.slice(2).join(':');
            setTourMediaScene(mediaId, label);
        }
    });

    filterStatusEl.addEventListener('change', function () {
        roomStatusFilter = this.value;
        syncFiltersAndRooms();
    });

    document.getElementById('vt360-btn-fullscreen').addEventListener('click', function () {
        if (!document.fullscreenElement) {
            stage.requestFullscreen().catch(function () {});
        } else {
            document.exitFullscreen().catch(function () {});
        }
    });

    syncFiltersAndRooms();
    const initialFloorData = TOUR_DATA[currentFloorKey];
    if (initialFloorData && initialFloorData.rooms.length > 0) {
        selectRoom(initialFloorData.rooms[0].code);
    }
});
</script>
@endsection