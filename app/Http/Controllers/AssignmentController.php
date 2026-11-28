<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $baseQuery = Assignment::where('user_id', $userId);

        $counts = [
            'total' => (clone $baseQuery)->count(),
            'belum_dikerjakan' => (clone $baseQuery)->where('status', 'belum_dikerjakan')->count(),
            'dikerjakan' => (clone $baseQuery)->where('status', 'dikerjakan')->count(),
            'selesai' => (clone $baseQuery)->where('status', 'selesai')->count(),
        ];

        $query = (clone $baseQuery)
            ->orderBy('due_at', 'asc')
            ->orderBy('id', 'asc');

        if ($request->filled('status') && array_key_exists($request->status, Assignment::STATUSES)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $term = '%'.$request->q.'%';
            $query->where(fn ($q) => $q
                ->where('title', 'like', $term)
                ->orWhere('course_name', 'like', $term)
                ->orWhere('description', 'like', $term)
            );
        }

        return view('assignments.index', [
            'assignments' => $query->get(),
            'counts' => $counts,
            'statuses' => Assignment::STATUSES,
        ]);
    }

    public function create(Request $request)
    {
        return view('assignments.form', [
            'assignment' => null,
            'statuses' => Assignment::STATUSES,
            'courses' => $this->courseSuggestions($request->user()->id),
        ]);
    }

    public function store(Request $request)
    {
        return $this->save($request);
    }

    public function edit(Request $request, int $id)
    {
        $assignment = Assignment::where('user_id', $request->user()->id)->findOrFail($id);

        return view('assignments.form', [
            'assignment' => $assignment,
            'statuses' => Assignment::STATUSES,
            'courses' => $this->courseSuggestions($request->user()->id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $assignment = Assignment::where('user_id', $request->user()->id)->findOrFail($id);

        return $this->save($request, $assignment);
    }

    public function updateStatus(Request $request, int $id)
    {
        $assignment = Assignment::where('user_id', $request->user()->id)->findOrFail($id);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Assignment::STATUSES))],
        ], [
            'status.required' => 'Status tugas wajib dipilih.',
            'status.in' => 'Status tugas tidak valid.',
        ]);

        $assignment->update(['status' => $data['status']]);

        return redirect()
            ->route('assignments.index')
            ->with('status', 'Status tugas "'.$assignment->title.'" diperbarui menjadi '.$assignment->statusLabel().'.');
    }

    public function destroy(Request $request, int $id)
    {
        $assignment = Assignment::where('user_id', $request->user()->id)->findOrFail($id);
        $assignment->delete();

        return redirect()
            ->route('assignments.index')
            ->with('status', 'Tugas kuliah berhasil dihapus.');
    }

    private function save(Request $request, ?Assignment $assignment = null)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'course_name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_at' => ['required', 'date'],
            'status' => ['required', Rule::in(array_keys(Assignment::STATUSES))],
        ], [
            'title.required' => 'Judul tugas wajib diisi.',
            'title.max' => 'Judul tugas maksimal 150 karakter.',
            'course_name.required' => 'Mata kuliah wajib diisi.',
            'course_name.max' => 'Nama mata kuliah maksimal 120 karakter.',
            'description.max' => 'Deskripsi tugas maksimal 2000 karakter.',
            'due_at.required' => 'Tenggat waktu wajib diisi.',
            'due_at.date' => 'Format tenggat waktu tidak valid.',
            'status.required' => 'Status pengerjaan wajib dipilih.',
            'status.in' => 'Status pengerjaan tidak valid.',
        ]);

        $payload = [
            'title' => trim($data['title']),
            'course_name' => trim($data['course_name']),
            'description' => isset($data['description']) && trim($data['description']) !== ''
                ? trim($data['description'])
                : null,
            'due_at' => $data['due_at'],
            'status' => $data['status'],
        ];

        if ($assignment) {
            $assignment->update($payload);
            $message = 'Tugas kuliah berhasil diperbarui.';
        } else {
            $payload['user_id'] = $request->user()->id;
            Assignment::create($payload);
            $message = 'Tugas kuliah berhasil ditambahkan.';
        }

        return redirect()
            ->route('assignments.index')
            ->with('status', $message);
    }

    private function courseSuggestions(int $userId)
    {
        if (! Schema::hasTable('course_sessions')) {
            return collect();
        }

        $personal = collect();
        if (Schema::hasTable('timetable_entries')) {
            $personal = DB::table('timetable_entries')
                ->join('course_sessions', 'timetable_entries.course_session_id', '=', 'course_sessions.id')
                ->where('timetable_entries.user_id', $userId)
                ->distinct()
                ->orderBy('course_sessions.title')
                ->pluck('course_sessions.title');
        }

        if ($personal->isNotEmpty()) {
            return $personal;
        }

        return DB::table('course_sessions')
            ->distinct()
            ->orderBy('title')
            ->limit(100)
            ->pluck('title');
    }
}
