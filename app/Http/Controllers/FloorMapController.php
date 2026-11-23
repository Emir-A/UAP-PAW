<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use Illuminate\Http\Request;

class FloorMapController extends Controller
{
    private function getFloorLayouts(): array
    {
        return [
            'F2' => [
                'key' => 'F2',
                'building' => 'F',
                'floor' => 2,
                'label' => 'Gedung F - Lantai 2',
                'corridor_title' => 'Aisle · Main Building 2nd Floor',
                'corridor_subtitle' => 'Gedung F Lantai 2 · Sayap Kelas Reguler',
                'theme' => [
                    'ceiling' => '#e8eee9',
                    'wallTop' => '#f5f8f5',
                    'wallBottom' => '#d8e3da',
                    'floor' => '#b7c7bc',
                    'accent' => '#194e40',
                    'strip' => '#276955',
                ],
                'wing_north' => ['F2.1', 'F2.2', 'F2.4', 'F2.5'],
                'wing_south' => ['F2.6', 'F2.8', 'F2.9'],
            ],
            'F3' => [
                'key' => 'F3',
                'building' => 'F',
                'floor' => 3,
                'label' => 'Gedung F - Lantai 3',
                'corridor_title' => 'Aisle · Main Building 3rd Floor',
                'corridor_subtitle' => 'Gedung F Lantai 3 · Zona Kelas Intensif',
                'theme' => [
                    'ceiling' => '#e6eef2',
                    'wallTop' => '#f4f8fa',
                    'wallBottom' => '#d2e0e8',
                    'floor' => '#b0c2cf',
                    'accent' => '#1b4d63',
                    'strip' => '#286b8a',
                ],
                'wing_north' => ['F3.1', 'F3.2', 'F3.3', 'F3.4', 'F3.5', 'F3.6', 'F3.7', 'F3.8', 'F3.9a', 'F3.9b'],
                'wing_south' => ['F3.10', 'F3.11', 'F3.12', 'F3.13', 'F3.14', 'F3.15', 'F3.16', 'F3.17', 'F3.18'],
            ],
            'F4' => [
                'key' => 'F4',
                'building' => 'F',
                'floor' => 4,
                'label' => 'Gedung F - Lantai 4',
                'corridor_title' => 'Hybrid Wing · Main Building 4th Floor',
                'corridor_subtitle' => 'Gedung F Lantai 4 · Zona Smart & Hybrid Classroom',
                'theme' => [
                    'ceiling' => '#eceaf2',
                    'wallTop' => '#f7f6fa',
                    'wallBottom' => '#dcd8e8',
                    'floor' => '#bcb6cf',
                    'accent' => '#38305e',
                    'strip' => '#514685',
                ],
                'wing_north' => ['F4.1', 'F4.2', 'F4.3', 'F4.4', 'F4.5', 'F4.6'],
                'wing_south' => ['F4.9', 'F4.10', 'F4.11', 'F4.12', 'F4.13', 'F4.14'],
            ],
            'F11' => [
                'key' => 'F11',
                'building' => 'F',
                'floor' => 11,
                'label' => 'Gedung F - Lantai 11',
                'corridor_title' => 'Sky Hallway · Main Building 11th Floor',
                'corridor_subtitle' => 'Gedung F Lantai 11 · Ruang Kelas Atas & Lab Magister',
                'theme' => [
                    'ceiling' => '#f2ede6',
                    'wallTop' => '#fbf9f5',
                    'wallBottom' => '#e6dccf',
                    'floor' => '#cbbca9',
                    'accent' => '#5e4326',
                    'strip' => '#856038',
                ],
                'wing_north' => ['F11.1', 'F11.2'],
                'wing_south' => ['F11.3', 'F11.4'],
            ],
            'G1' => [
                'key' => 'G1',
                'building' => 'G',
                'floor' => 1,
                'label' => 'Gedung G - Lantai 1',
                'corridor_title' => 'Main Hall · Building G 1st Floor',
                'corridor_subtitle' => 'Gedung G Lantai 1 · Ruang Kuliah & Praktikum',
                'theme' => [
                    'ceiling' => '#e5f0ec',
                    'wallTop' => '#f4faf8',
                    'wallBottom' => '#cfe3dc',
                    'floor' => '#acc7be',
                    'accent' => '#145243',
                    'strip' => '#207561',
                ],
                'wing_north' => ['G1.1', 'G1.2', 'G1.3'],
                'wing_south' => ['G1.4', 'G1.5', 'G1.6'],
            ],
        ];
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'floor' => ['nullable', 'string'],
            'day' => ['nullable', 'integer', 'between:1,7'],
            'time' => ['nullable', 'date_format:H:i'],
        ]);

        $layouts = $this->getFloorLayouts();
        $now = now('Asia/Jakarta');

        $floorKey = array_key_exists($validated['floor'] ?? '', $layouts)
            ? $validated['floor']
            : 'F2';
        $day = (int) ($validated['day'] ?? $now->dayOfWeekIso);
        $time = $validated['time'] ?? $now->format('H:i');

        $daySessions = CourseSession::where('weekday', $day)
            ->orderBy('start_time')
            ->get()
            ->groupBy('room');

        $tourFloors = [];
        foreach ($layouts as $key => $layout) {
            $northRooms = $this->buildTourRooms($layout['wing_north'], 'Sayap Utara', $layout, $daySessions, $time);
            $southRooms = $this->buildTourRooms($layout['wing_south'], 'Sayap Selatan', $layout, $daySessions, $time);
            $allRooms = array_merge($northRooms, $southRooms);

            $freeCount = collect($allRooms)->where('is_busy', false)->count();
            $busyCount = collect($allRooms)->where('is_busy', true)->count();

            $tourFloors[$key] = [
                'key' => $key,
                'building' => $layout['building'],
                'floor' => $layout['floor'],
                'label' => $layout['label'],
                'corridor_title' => $layout['corridor_title'],
                'corridor_subtitle' => $layout['corridor_subtitle'],
                'theme' => $layout['theme'],
                'total_rooms' => count($allRooms),
                'free_count' => $freeCount,
                'busy_count' => $busyCount,
                'wing_north' => $northRooms,
                'wing_south' => $southRooms,
                'rooms' => $allRooms,
            ];
        }

        return view('map.index', [
            'initialFloor' => $floorKey,
            'tourFloors' => $tourFloors,
            'day' => $day,
            'time' => $time,
        ]);
    }

    private function buildTourRooms(
        array $codes,
        string $wingName,
        array $layout,
        $daySessions,
        string $time
    ): array {
        $rooms = [];
        $totalInWing = max(1, count($codes));

        foreach ($codes as $idx => $code) {
            $sessions = $daySessions->get($code, collect())->values();

            $activeSession = $sessions->first(function ($s) use ($time) {
                return $s->start_time <= $time && $s->end_time > $time;
            });

            $nextSession = $sessions->first(function ($s) use ($time) {
                return $s->start_time > $time;
            });

            $isBusy = $activeSession !== null;

            if ($isBusy) {
                $statusLabel = 'TERPAKAI';
                $statusNote = 'Kosong setelah pukul '.$activeSession->end_time.' WIB';
                $boardTitle = $activeSession->title;
                $boardMeta = 'Kelas '.$activeSession->class_group.' · '.$activeSession->start_time.' - '.$activeSession->end_time.' WIB';
                $boardLecturer = $activeSession->lecturer;
            } elseif ($nextSession) {
                $statusLabel = 'RUANG KOSONG';
                $statusNote = 'Kosong s/d pukul '.$nextSession->start_time.' WIB';
                $boardTitle = 'RUANG KOSONG (TERSEDIA)';
                $boardMeta = 'Sesi berikutnya: '.$nextSession->start_time.' WIB ('.$nextSession->title.')';
                $boardLecturer = 'Dapat digunakan sebelum sesi berikutnya dimulai';
            } else {
                $statusLabel = 'RUANG KOSONG';
                $statusNote = 'Kosong sepanjang sisa hari ini';
                $boardTitle = 'RUANG KOSONG (TERSEDIA)';
                $boardMeta = 'Tidak ada jadwal kuliah lanjutan hari ini';
                $boardLecturer = 'Gedung '.$layout['building'].' · Lantai '.$layout['floor'];
            }

            // Sebar sudut Yaw 360 derajat (Utara: -68..+68 deg, Selatan: 112..248 deg)
            $ratio = $totalInWing > 1 ? ($idx / ($totalInWing - 1)) : 0.5;
            if ($wingName === 'Sayap Utara') {
                $yawDeg = round(-68 + ($ratio * 136), 1);
            } else {
                $yawDeg = round(112 + ($ratio * 136), 1);
                if ($yawDeg > 180) {
                    $yawDeg -= 360;
                }
            }

            $rooms[] = [
                'code' => $code,
                'building' => $layout['building'],
                'floor' => $layout['floor'],
                'wing' => $wingName,
                'yaw' => $yawDeg,
                'is_busy' => $isBusy,
                'status_label' => $statusLabel,
                'status_note' => $statusNote,
                'board_title' => $boardTitle,
                'board_meta' => $boardMeta,
                'board_lecturer' => $boardLecturer,
                'active_session_id' => $activeSession ? $activeSession->id : null,
                'session_count' => $sessions->count(),
                'sessions' => $sessions->map(function ($s) use ($time) {
                    return [
                        'id' => $s->id,
                        'title' => $s->title,
                        'program' => $s->program,
                        'group' => $s->class_group,
                        'lecturer' => $s->lecturer,
                        'start' => $s->start_time,
                        'end' => $s->end_time,
                        'is_current' => ($s->start_time <= $time && $s->end_time > $time),
                    ];
                })->all(),
            ];
        }

        return $rooms;
    }
}
