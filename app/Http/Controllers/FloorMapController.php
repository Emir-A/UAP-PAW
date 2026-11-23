<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use Illuminate\Http\Request;

class FloorMapController extends Controller
{

    private const FLOOR_LAYOUTS = [
        'F2' => [
            'building' => 'F',
            'floor' => 2,
            'label' => 'Gedung F · Lantai 2',
            'subtitle' => 'Sayap Kelas Reguler & Koridor Utama Lantai 2',
            'wing_north' => ['F2.1', 'F2.2', 'F2.4', 'F2.5'],
            'wing_south' => ['F2.6', 'F2.8', 'F2.9'],
            'facilities' => ['Lift Utama', 'Tangga Barat', 'Toilet', 'Tangga Timur'],
        ],
        'F3' => [
            'building' => 'F',
            'floor' => 3,
            'label' => 'Gedung F · Lantai 3',
            'subtitle' => 'Zona Ruang Kuliah Intensif Lantai 3',
            'wing_north' => ['F3.1', 'F3.2', 'F3.3', 'F3.4', 'F3.5', 'F3.6', 'F3.7', 'F3.8', 'F3.9a', 'F3.9b'],
            'wing_south' => ['F3.10', 'F3.11', 'F3.12', 'F3.13', 'F3.14', 'F3.15', 'F3.16', 'F3.17', 'F3.18'],
            'facilities' => ['Lift Utama', 'Tangga F3', 'Balkon Tengah', 'Toilet'],
        ],
        'F4' => [
            'building' => 'F',
            'floor' => 4,
            'label' => 'Gedung F · Lantai 4',
            'subtitle' => 'Zona Hybrid Classroom & Ruang Kelas Lantai 4',
            'wing_north' => ['F4.1', 'F4.2', 'F4.3', 'F4.4', 'F4.5', 'F4.6'],
            'wing_south' => ['F4.9', 'F4.10', 'F4.11', 'F4.12', 'F4.13', 'F4.14'],
            'facilities' => ['Lift Utama', 'Lounge Mahasiswa', 'Balkon Lantai 4', 'Tangga'],
        ],
        'F11' => [
            'building' => 'F',
            'floor' => 11,
            'label' => 'Gedung F · Lantai 11',
            'subtitle' => 'Zona Kelas Atas & Laboratorium Pascasarjana',
            'wing_north' => ['F11.1', 'F11.2'],
            'wing_south' => ['F11.3', 'F11.4'],
            'facilities' => ['Lift Ekspres', 'Lab Magister', 'Tangga Darurat'],
        ],
        'G1' => [
            'building' => 'G',
            'floor' => 1,
            'label' => 'Gedung G · Lantai 1',
            'subtitle' => 'Gedung Perkuliahan G & Ruang Studio',
            'wing_north' => ['G1.1', 'G1.2', 'G1.3'],
            'wing_south' => ['G1.4', 'G1.5', 'G1.6'],
            'facilities' => ['Pintu Masuk G', 'Lobby Gedung G', 'Tangga Utama'],
        ],
    ];

    private const VIRTUAL_TOUR_ROOMS = [
        'F2.1' => 'Classroom F2.1 (Virtual Tour 360°)',
        'F2.2' => 'Classroom F2.2 (Virtual Tour 360°)',
        'F2.4' => 'Classroom F2.4 (Virtual Tour 360°)',
        'F2.6' => 'Classroom F2.6 (Virtual Tour 360°)',
        'F2.8' => 'Classroom F2.8 (Virtual Tour 360°)',
        'F2.9' => 'Classroom F2.9 (Virtual Tour 360°)',
        'F3.1' => 'Classroom F3.1 (Virtual Tour 360°)',
        'F3.2' => 'Classroom F3.2 (Virtual Tour 360°)',
        'F4.1' => 'Classroom F4.1 / Hybrid Classroom (Virtual Tour 360°)',
    ];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'floor' => ['nullable', 'string'],
            'day' => ['nullable', 'integer', 'between:1,7'],
            'time' => ['nullable', 'date_format:H:i'],
            'status' => ['nullable', 'in:all,free,busy'],
        ]);

        $now = now('Asia/Jakarta');
        $floorKey = array_key_exists($validated['floor'] ?? '', self::FLOOR_LAYOUTS)
            ? $validated['floor']
            : 'F2';
        $day = (int) ($validated['day'] ?? $now->dayOfWeekIso);
        $time = $validated['time'] ?? $now->format('H:i');
        $statusFilter = $validated['status'] ?? 'all';


        $daySessions = CourseSession::where('weekday', $day)
            ->orderBy('start_time')
            ->get()
            ->groupBy('room');

        // Hitung ringkasan ketersediaan tiap lantai untuk navigasi tab lantai
        $floorSummaries = [];
        foreach (self::FLOOR_LAYOUTS as $key => $layout) {
            $allCodes = array_merge($layout['wing_north'], $layout['wing_south']);
            $freeCount = 0;
            $busyCount = 0;

            foreach ($allCodes as $code) {
                $roomSessions = $daySessions->get($code, collect());
                $isBusy = $roomSessions->contains(
                    fn ($s) => $s->start_time <= $time && $s->end_time > $time
                );

                if ($isBusy) {
                    $busyCount++;
                } else {
                    $freeCount++;
                }
            }

            $floorSummaries[$key] = [
                'key' => $key,
                'building' => $layout['building'],
                'floor' => $layout['floor'],
                'label' => $layout['label'],
                'subtitle' => $layout['subtitle'],
                'total' => count($allCodes),
                'free' => $freeCount,
                'busy' => $busyCount,
            ];
        }

        // Bangun detail ruangan untuk lantai yang sedang aktif
        $currentLayout = self::FLOOR_LAYOUTS[$floorKey];
        $buildRoomData = function (array $codes) use ($daySessions, $time, $currentLayout) {
            $result = [];

            foreach ($codes as $code) {
                $sessions = $daySessions->get($code, collect())->values();

                $activeSession = $sessions->first(
                    fn ($s) => $s->start_time <= $time && $s->end_time > $time
                );

                $nextSession = $sessions->first(
                    fn ($s) => $s->start_time > $time
                );

                $isBusy = $activeSession !== null;

                if ($isBusy) {
                    $statusLabel = 'Terpakai';
                    $availabilityNote = 'Kosong setelah pukul '.$activeSession->end_time;
                } elseif ($nextSession) {
                    $statusLabel = 'Kosong';
                    $availabilityNote = 'Kosong s/d pukul '.$nextSession->start_time;
                } else {
                    $statusLabel = 'Kosong';
                    $availabilityNote = 'Kosong sepanjang sisa hari ini';
                }

                $result[] = [
                    'code' => $code,
                    'building' => $currentLayout['building'],
                    'floor' => $currentLayout['floor'],
                    'is_busy' => $isBusy,
                    'status_key' => $isBusy ? 'busy' : 'free',
                    'status_label' => $statusLabel,
                    'availability_note' => $availabilityNote,
                    'active_session' => $activeSession,
                    'next_session' => $nextSession,
                    'daily_sessions' => $sessions,
                    'session_count' => $sessions->count(),
                    'has_tour' => array_key_exists($code, self::VIRTUAL_TOUR_ROOMS),
                    'tour_label' => self::VIRTUAL_TOUR_ROOMS[$code] ?? null,
                ];
            }

            return $result;
        };

        $northWingRooms = $buildRoomData($currentLayout['wing_north']);
        $southWingRooms = $buildRoomData($currentLayout['wing_south']);
        $allFloorRooms = array_merge($northWingRooms, $southWingRooms);

        return view('map.index', [
            'floorKey' => $floorKey,
            'currentLayout' => $currentLayout,
            'floorSummaries' => $floorSummaries,
            'northWingRooms' => $northWingRooms,
            'southWingRooms' => $southWingRooms,
            'allFloorRooms' => $allFloorRooms,
            'day' => $day,
            'time' => $time,
            'statusFilter' => $statusFilter,
        ]);
    }
}