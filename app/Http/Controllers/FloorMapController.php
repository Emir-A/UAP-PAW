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
                'corridor_title' => 'Koridor Utama Lantai 2',
                'corridor_subtitle' => 'Main Building · 2nd Floor Academic Hallway',
                'theme' => ['wall' => '#e8efe9', 'floor' => '#cbd8ce', 'accent' => '#194e40'],
                'wing_north' => ['F2.1', 'F2.2', 'F2.4', 'F2.5'],
                'wing_south' => ['F2.6', 'F2.8', 'F2.9'],
                'tour_base_url' => 'https://rendicahya.github.io/filkom-virtual-tour/index.htm',
                'tour_default_media' => 125,
                'tour_room_media' => [
                    'F2.1' => 126,
                    'F2.2' => 128,
                    'F2.4' => 130,
                    'F2.5' => 129,
                    'F2.6' => 131,
                    'F2.8' => 134,
                    'F2.9' => 136,
                ],
                'tour_scenes' => [
                    ['media' => 125, 'label' => 'Koridor Utama F2'],
                    ['media' => 126, 'label' => 'Classroom F2.1'],
                    ['media' => 128, 'label' => 'Classroom F2.2'],
                    ['media' => 130, 'label' => 'Classroom F2.4'],
                    ['media' => 131, 'label' => 'Classroom F2.6'],
                    ['media' => 134, 'label' => 'Classroom F2.8'],
                    ['media' => 136, 'label' => 'Classroom F2.9'],
                    ['media' => 160, 'label' => 'Ruang Baca / Library'],
                    ['media' => 137, 'label' => 'Lift Lantai 2'],
                    ['media' => 124, 'label' => 'Tangga Lantai 2'],
                ],
            ],
            'F3' => [
                'key' => 'F3',
                'building' => 'F',
                'floor' => 3,
                'label' => 'Gedung F - Lantai 3',
                'corridor_title' => 'Koridor Utama Lantai 3',
                'corridor_subtitle' => 'Main Building · 3rd Floor Intensive Wing',
                'theme' => ['wall' => '#e6edf2', 'floor' => '#c7d4de', 'accent' => '#1e4f66'],
                'wing_north' => ['F3.1', 'F3.2', 'F3.3', 'F3.4', 'F3.5', 'F3.6', 'F3.7', 'F3.8', 'F3.9a', 'F3.9b'],
                'wing_south' => ['F3.10', 'F3.11', 'F3.12', 'F3.13', 'F3.14', 'F3.15', 'F3.16', 'F3.17', 'F3.18'],
                'tour_base_url' => 'https://rendicahya.github.io/filkom-virtual-tour/index.htm',
                'tour_default_media' => 106,
                'tour_room_media' => [
                    'F3.1' => 115,
                    'F3.2' => 114,
                    'F3.3' => 117,
                    'F3.4' => 118,
                    'F3.5' => 107,
                    'F3.6' => 108,
                    'F3.7' => 110,
                    'F3.8' => 111,
                    'F3.9a' => 112,
                    'F3.9b' => 112,
                    'F3.10' => 116,
                    'F3.11' => 119,
                    'F3.12' => 120,
                    'F3.13' => 121,
                    'F3.14' => 122,
                    'F3.15' => 123,
                    'F3.16' => 234,
                    'F3.17' => 235,
                    'F3.18' => 236,
                ],
                'tour_scenes' => [
                    ['media' => 106, 'label' => 'Koridor Utama F3'],
                    ['media' => 115, 'label' => 'Classroom F3.1'],
                    ['media' => 114, 'label' => 'Classroom F3.2'],
                    ['media' => 117, 'label' => 'Classroom F3 (Utara)'],
                    ['media' => 107, 'label' => 'Hybrid Classroom 1'],
                    ['media' => 108, 'label' => 'Hybrid Classroom 2'],
                    ['media' => 110, 'label' => 'Hybrid Classroom 3'],
                    ['media' => 105, 'label' => 'Lift Lantai 3'],
                    ['media' => 113, 'label' => 'Tangga Lantai 3'],
                ],
            ],
            'F4' => [
                'key' => 'F4',
                'building' => 'F',
                'floor' => 4,
                'label' => 'Gedung F - Lantai 4',
                'corridor_title' => 'Koridor Smart & Hybrid Lantai 4',
                'corridor_subtitle' => 'Main Building · 4th Floor Hybrid Classroom Zone',
                'theme' => ['wall' => '#ece9f2', 'floor' => '#d1cce0', 'accent' => '#3b3266'],
                'wing_north' => ['F4.1', 'F4.2', 'F4.3', 'F4.4', 'F4.5', 'F4.6'],
                'wing_south' => ['F4.9', 'F4.10', 'F4.11', 'F4.12', 'F4.13', 'F4.14'],
                'tour_base_url' => 'https://rendicahya.github.io/filkom-virtual-tour/index.htm',
                'tour_default_media' => 76,
                'tour_room_media' => [
                    'F4.1' => 85,
                    'F4.2' => 84,
                    'F4.3' => 101,
                    'F4.4' => 102,
                    'F4.5' => 103,
                    'F4.6' => 104,
                    'F4.9' => 77,
                    'F4.10' => 78,
                    'F4.11' => 79,
                    'F4.12' => 81,
                    'F4.13' => 82,
                    'F4.14' => 86,
                ],
                'tour_scenes' => [
                    ['media' => 76, 'label' => 'Koridor Utama F4'],
                    ['media' => 85, 'label' => 'Classroom F4.1'],
                    ['media' => 84, 'label' => 'Classroom F4'],
                    ['media' => 101, 'label' => 'Hybrid Classroom A'],
                    ['media' => 102, 'label' => 'Hybrid Classroom B'],
                    ['media' => 103, 'label' => 'Hybrid Classroom C'],
                    ['media' => 104, 'label' => 'Hybrid Classroom D'],
                    ['media' => 80, 'label' => 'Balkon Lantai 4'],
                    ['media' => 75, 'label' => 'Lift Lantai 4'],
                    ['media' => 74, 'label' => 'Tangga Lantai 4'],
                ],
            ],
            'F11' => [
                'key' => 'F11',
                'building' => 'F',
                'floor' => 11,
                'label' => 'Gedung F - Lantai 11',
                'corridor_title' => 'Sky Hallway Lantai 11',
                'corridor_subtitle' => 'Main Building · 11th Floor Executive & Research Zone',
                'theme' => ['wall' => '#f2ede4', 'floor' => '#ddd3c2', 'accent' => '#6b4c2b'],
                'wing_north' => ['F11.1', 'F11.2'],
                'wing_south' => ['F11.3', 'F11.4'],
                'tour_base_url' => 'https://rendicahya.github.io/filkom-virtual-tour/index.htm',
                'tour_default_media' => 15,
                'tour_room_media' => [
                    'F11.1' => 14,
                    'F11.2' => 20,
                    'F11.3' => 87,
                    'F11.4' => 88,
                ],
                'tour_scenes' => [
                    ['media' => 15, 'label' => 'Koridor Utama F11'],
                    ['media' => 14, 'label' => 'Classroom F11.7 / F11.1'],
                    ['media' => 20, 'label' => 'Classroom F11.8 / F11.2'],
                    ['media' => 87, 'label' => 'Classroom F11 (Sayap Selatan)'],
                    ['media' => 16, 'label' => 'Master Student Lab. 1'],
                    ['media' => 22, 'label' => 'Master Student Lab. 2'],
                    ['media' => 19, 'label' => 'Lift Lantai 11'],
                    ['media' => 18, 'label' => 'Tangga Lantai 11'],
                ],
            ],
            'G1' => [
                'key' => 'G1',
                'building' => 'G',
                'floor' => 1,
                'label' => 'Gedung G - Lantai 1',
                'corridor_title' => 'Atrium & Koridor Gedung G',
                'corridor_subtitle' => 'Building G · 1st Floor Lecture & Studio Hall',
                'theme' => ['wall' => '#e4f0ec', 'floor' => '#c1d9d1', 'accent' => '#155749'],
                'wing_north' => ['G1.1', 'G1.2', 'G1.3'],
                'wing_south' => ['G1.4', 'G1.5', 'G1.6'],
                'tour_base_url' => 'https://rendicahya.github.io/filkom-virtual-tour/index.htm',
                'tour_default_media' => 208,
                'tour_room_media' => [
                    'G1.1' => 208,
                    'G1.2' => 209,
                    'G1.3' => 210,
                    'G1.4' => 211,
                    'G1.5' => 212,
                    'G1.6' => 155,
                ],
                'tour_scenes' => [
                    ['media' => 208, 'label' => 'Computer Lab. G1.1'],
                    ['media' => 209, 'label' => 'Computer Lab. G1.2'],
                    ['media' => 210, 'label' => 'Computer Lab. G1.3'],
                    ['media' => 211, 'label' => 'Computer Lab. G1.4'],
                    ['media' => 212, 'label' => 'Computer Lab. G1.5'],
                    ['media' => 152, 'label' => 'Tangga & Koridor G1'],
                    ['media' => 219, 'label' => 'Teras Gedung G'],
                    ['media' => 155, 'label' => 'Auditorium Algoritma (G2)'],
                ],
            ],
        ];
    }

    public function index(Request $request)
    {
        if ($request->boolean('embed')) {
            return response(
                '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" id="metaViewport" content="user-scalable=no, initial-scale=1, width=device-width, viewport-fit=cover" data-tdv-general-scale="0.5"/><base href="https://rendicahya.github.io/filkom-virtual-tour/"/><script src="lib/tdvplayer.js?v=1657803116594"></script><script src="script.js?v=1657803116594"></script><link rel="stylesheet" href="fonts.css?v=1657803116594"><style>html,body{height:100%;width:100%;margin:0;padding:0;overflow:hidden;background:#0d1d18}.fill-viewport{position:fixed;top:0;left:0;right:0;bottom:0;padding:0;margin:0;overflow:hidden}#viewer{z-index:1}</style></head><body><div id="viewer" class="fill-viewport"></div><script>var tour;var devicesUrl={"general":"script_general.js?v=1657803116594","mobile":"script_mobile.js?v=1657803116594"};function removeBuiltInWatermarks(){if(tour&&tour.player){["Container_22BB12F4_3075_D173_4184_EC3BC4955417","Image_C7A419C5_F2CB_F7AA_41E2_A946A57CE9B5","Label_22BB32F4_3075_D173_4191_C8B45B85DEB8","Label_DCD99B63_F692_F5B9_41B7_49F8ED8D8AD2","HTMLText_F4D5E1C7_FBE0_5012_41E2_87EAB4EE7615","Container_D5CC8F68_F6BE_4DB6_41D3_54B4E4F2513C","Container_B4203396_F693_D49B_41E6_BC64A2ADECE6","Container_39DE87B1_0C06_62AF_417B_8CB0FB5C9D15","Container_221B1648_0C06_E5FD_417F_E6FCCCB4A6D7","ThumbnailList_D9FC8006_F692_537B_41E1_7C4A56FAFCA6","IconButton_D2FA88E8_F692_54B6_41C0_EAB2B36C1F81","IconButton_B4056593_F6B2_DC99_41EB_AFF017592EDC","Button_D5CC9F68_F6BE_4DB6_41E8_D3DC6154D486","Button_D5CCCF67_F6BE_4DBA_41ED_DB0C51042345","Button_D5CCAF68_F6BE_4DB6_41E4_6C03DC07CFC5","Button_D5CCBF68_F6BE_4DB6_41E5_C1E2C5C1EA5C"].forEach(function(id){var c=tour.player.getById(id);if(c&&c.set)c.set("visible",false);});}document.querySelectorAll("img[src*=\"Image_C7A419C5\"],img[src*=\"IconButton_D2FA88E8\"],img[src*=\"IconButton_B4056593\"],img[src*=\"Button_D5CC\"]").forEach(function(el){if(el.parentElement&&el.parentElement.parentElement)el.parentElement.parentElement.style.display="none";el.style.display="none";});}function initTour(){var s=new TDV.PlayerSettings();s.set(TDV.PlayerSettings.CONTAINER,document.getElementById("viewer"));s.set(TDV.PlayerSettings.WEBVR_POLYFILL_URL,"lib/WebVRPolyfill.js?v=1657803116594");s.set(TDV.PlayerSettings.HLS_URL,"lib/Hls.js?v=1657803116594");s.set(TDV.PlayerSettings.QUERY_STRING_PARAMETERS,"v=1657803116594");tour=new TDV.Tour(s,devicesUrl);tour.bind(TDV.Tour.EVENT_TOUR_INITIALIZED,function(){removeBuiltInWatermarks();setInterval(removeBuiltInWatermarks,250);});tour.bind(TDV.Tour.EVENT_TOUR_LOADED,removeBuiltInWatermarks);tour.load();}document.addEventListener("DOMContentLoaded",initTour);</script></body></html>'
            );
        }

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

            $defaultMedia = $layout['tour_default_media'] ?? 1;
            $baseUrl = $layout['tour_base_url'] ?? 'https://rendicahya.github.io/filkom-virtual-tour/index.htm';

            $tourFloors[$key] = [
                'key' => $key,
                'building' => $layout['building'],
                'floor' => $layout['floor'],
                'label' => $layout['label'],
                'corridor_title' => $layout['corridor_title'],
                'corridor_subtitle' => $layout['corridor_subtitle'],
                'theme' => $layout['theme'],
                'tour_base_url' => $baseUrl,
                'tour_default_media' => $defaultMedia,
                'tour_url' => $baseUrl.'#media='.$defaultMedia,
                'tour_scenes' => $layout['tour_scenes'] ?? [],
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
                $boardMeta = 'Sesi berikutnya pukul '.$nextSession->start_time.' ('.$nextSession->title.')';
                $boardLecturer = 'Silakan gunakan ruangan dengan tertib';
            } else {
                $statusLabel = 'RUANG KOSONG';
                $statusNote = 'Kosong sepanjang sisa hari ini';
                $boardTitle = 'RUANG KOSONG (TERSEDIA)';
                $boardMeta = 'Tidak ada jadwal kuliah lanjutan hari ini';
                $boardLecturer = 'Gedung '.$layout['building'].' Lantai '.$layout['floor'];
            }

            $ratio = $totalInWing > 1 ? ($idx / ($totalInWing - 1)) : 0.5;
            if ($wingName === 'Sayap Utara') {
                $yawDeg = round(-72 + ($ratio * 144));
            } else {
                $yawDeg = round(108 + ($ratio * 144));
            }

            $roomMedia = $layout['tour_room_media'][$code] ?? ($layout['tour_default_media'] ?? 1);
            $tourBaseUrl = $layout['tour_base_url'] ?? 'https://rendicahya.github.io/filkom-virtual-tour/index.htm';

            $rooms[] = [
                'code' => $code,
                'building' => $layout['building'],
                'floor' => $layout['floor'],
                'wing' => $wingName,
                'yaw' => $yawDeg,
                'tour_media' => $roomMedia,
                'tour_url' => $tourBaseUrl.'#media='.$roomMedia,
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