<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'day' => ['nullable', 'integer', 'between:1,7'],
            'building' => ['nullable', 'in:F,G'],
            'program' => ['nullable', 'string', 'max:100'],
            'replace' => ['nullable', 'integer'],
        ]);

        $replacement = null;
        if (! empty($filters['replace'])) {
            abort_unless($request->user(), 403);
            $replacement = TimetableEntry::where('user_id', $request->user()->id)
                ->findOrFail($filters['replace']);
        }

        $query = CourseSession::query();

        if (! empty($filters['q'])) {
            $term = '%'.$filters['q'].'%';
            $query->where(fn ($q) => $q
                ->where('title', 'like', $term)
                ->orWhere('room', 'like', $term)
                ->orWhere('class_group', 'like', $term)
                ->orWhere('lecturer', 'like', $term)
            );
        }

        if (! empty($filters['day'])) {
            $query->where('weekday', $filters['day']);
        }

        if (! empty($filters['building'])) {
            $query->where('room', 'like', $filters['building'].'%');
        }

        if (! empty($filters['program'])) {
            $query->where('program', $filters['program']);
        }

        return view('catalog', [
            'classes' => $query->orderBy('weekday')
                ->orderBy('start_time')
                ->orderBy('room')
                ->paginate(18)
                ->withQueryString(),
            'programs' => CourseSession::distinct()->orderBy('program')->pluck('program'),
            'selected' => $request->user()
                ? TimetableEntry::where('user_id', $request->user()->id)->pluck('course_session_id')->all()
                : [],
            'replacement' => $replacement,
        ]);
    }

    public function rooms(Request $request)
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