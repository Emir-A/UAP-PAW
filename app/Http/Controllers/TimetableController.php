<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use App\Models\TimetableEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $entries = TimetableEntry::with('courseSession')
            ->where('user_id', $request->user()->id)
            ->get()
            ->sortBy(
                fn ($entry) =>
                    $entry->courseSession->weekday.' '.
                    $entry->courseSession->start_time
            );

        return view('timetable.index', compact('entries'));
    }

    public function create()
    {
        return redirect()->route('classes');
    }

    public function store(Request $request)
    {
        return $this->save($request);
    }

    public function edit(Request $request, int $id)
    {
        $entry = TimetableEntry::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return view('timetable.form', [
            'entry' => $entry,
            'classes' => $this->classes(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $entry = TimetableEntry::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return $this->save($request, $entry);
    }

    public function destroy(Request $request, int $id)
    {
        TimetableEntry::where('user_id', $request->user()->id)
            ->findOrFail($id)
            ->delete();

        return redirect()
            ->route('timetable.index')
            ->with('status', 'Kelas berhasil dihapus dari jadwal pribadi.');
    }

    private function classes()
    {
        return CourseSession::orderBy('weekday')
            ->orderBy('start_time')
            ->orderBy('title')
            ->get();
    }

    private function save(
        Request $request,
        ?TimetableEntry $entry = null
    ) {
        $data = $request->validate([
            'course_session_id' => [
                'required',
                'string',
                'exists:course_sessions,id',
            ],
        ]);

        DB::transaction(function () use ($request, $data, $entry) {
            User::whereKey($request->user()->id)
                ->lockForUpdate()
                ->firstOrFail();

            $class = CourseSession::findOrFail(
                $data['course_session_id']
            );

            $otherEntries = TimetableEntry::where(
                'user_id',
                $request->user()->id
            );

            if ($entry) {
                $otherEntries->where('id', '!=', $entry->id);
            }

            $duplicate = (clone $otherEntries)
                ->where('course_session_id', $class->id)
                ->exists();

            $clash = $otherEntries
                ->whereHas('courseSession', function ($query) use ($class) {
                    $query->where('weekday', $class->weekday)
                        ->where('start_time', '<', $class->end_time)
                        ->where('end_time', '>', $class->start_time);
                })
                ->exists();

            if ($duplicate || $clash) {
                throw ValidationException::withMessages([
                    'course_session_id' => $duplicate
                        ? 'Kelas sudah ada di jadwal pribadi.'
                        : 'Waktu kelas bertabrakan dengan jadwal Anda.',
                ]);
            }

            if ($entry) {
                $entry->update([
                    'course_session_id' => $class->id,
                ]);
            } else {
                TimetableEntry::create([
                    'user_id' => $request->user()->id,
                    'course_session_id' => $class->id,
                ]);
            }
        });

        return redirect()
            ->route('timetable.index')
            ->with('status', 'Jadwal pribadi berhasil disimpan.');
    }
}