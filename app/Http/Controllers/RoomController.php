<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'day' => ['nullable', 'integer', 'between:1,7'],
            'time' => ['nullable', 'date_format:H:i'],
            'building' => ['nullable', 'in:F,G'],
        ]);

        $now = now('Asia/Jakarta');
        $day = $data['day'] ?? $now->dayOfWeekIso;
        $time = $data['time'] ?? $now->format('H:i');

        $sessions = CourseSession::where('weekday', $day)
            ->orderBy('start_time')
            ->get();

        $rooms = DB::table('rooms')
            ->when($data['building'] ?? null, fn ($q, $b) => $q->where('building', $b))
            ->get()
            ->sortBy('code', SORT_NATURAL);

        return view('rooms', compact('rooms', 'sessions', 'day', 'time'));
    }
}