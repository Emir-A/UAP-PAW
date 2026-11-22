<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use App\Models\RoomReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RoomReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = $request->user()
            ->roomReports()
            ->latest()
            ->get();

        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        return view('reports.create', [
            'categories' => RoomReport::CATEGORIES,
            'rooms' => $this->rooms(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room' => ['required', 'string', 'max:50'],
            'category' => ['required', Rule::in(array_keys(RoomReport::CATEGORIES))],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')
                ->store('room-reports', 'public');
        }

        unset($data['photo']);

        $request->user()->roomReports()->create($data);

        return redirect()
            ->route('reports.index')
            ->with('status', 'Laporan ruangan berhasil dikirim.');
    }

    public function show(Request $request, int $id)
    {
        $report = $request->user()
            ->roomReports()
            ->findOrFail($id);

        return view('reports.show', compact('report'));
    }

    public function edit(Request $request, int $id)
    {
        $report = $request->user()
            ->roomReports()
            ->findOrFail($id);

        return view('reports.edit', [
            'report' => $report,
            'categories' => RoomReport::CATEGORIES,
            'statuses' => RoomReport::STATUSES,
            'rooms' => $this->rooms(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $report = $request->user()
            ->roomReports()
            ->findOrFail($id);

        $data = $request->validate([
            'room' => ['required', 'string', 'max:50'],
            'category' => ['required', Rule::in(array_keys(RoomReport::CATEGORIES))],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'status' => ['required', Rule::in(array_keys(RoomReport::STATUSES))],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            if ($report->photo_path) {
                Storage::disk('public')->delete($report->photo_path);
            }

            $data['photo_path'] = $request->file('photo')
                ->store('room-reports', 'public');
        }

        unset($data['photo']);

        $status = $data['status'];
        unset($data['status']);

        $report->fill($data);
        $report->status = $status;
        $report->save();

        return redirect()
            ->route('reports.index')
            ->with('status', 'Laporan ruangan berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id)
    {
        $report = $request->user()
            ->roomReports()
            ->findOrFail($id);

        if ($report->photo_path) {
            Storage::disk('public')->delete($report->photo_path);
        }

        $report->delete();

        return redirect()
            ->route('reports.index')
            ->with('status', 'Laporan ruangan berhasil dihapus.');
    }

    private function rooms()
    {
        return CourseSession::query()
            ->distinct()
            ->orderBy('room')
            ->pluck('room');
    }
}