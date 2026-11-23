<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LostItemController extends Controller
{
    public function index(Request $request)
    {
        $query = LostItem::with('user')->latest('lost_at');

        if ($request->filled('status') && in_array($request->status, ['hilang', 'ditemukan'], true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $term = '%'.$request->q.'%';
            $query->where(fn ($q) => $q
                ->where('item_name', 'like', $term)
                ->orWhere('description', 'like', $term)
                ->orWhere('location', 'like', $term)
            );
        }

        return view('lost-items.index', [
            'items' => $query->get(),
        ]);
    }

    public function create()
    {
        return view('lost-items.form', [
            'item' => null,
            'rooms' => $this->rooms(),
        ]);
    }

    public function store(Request $request)
    {
        return $this->save($request);
    }

    public function edit(Request $request, int $id)
    {
        $item = LostItem::findOrFail($id);

        return view('lost-items.form', [
            'item' => $item,
            'rooms' => $this->rooms(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $item = LostItem::findOrFail($id);

        return $this->save($request, $item);
    }

    public function markFound(int $id)
    {
        $item = LostItem::findOrFail($id);
        $item->update(['status' => 'ditemukan']);

        return redirect()
            ->route('lost-items.index')
            ->with('status', 'Laporan barang ditandai selesai (barang sudah ditemukan).');
    }

    public function destroy(int $id)
    {
        $item = LostItem::findOrFail($id);

        if ($item->photo_path && file_exists(public_path($item->photo_path))) {
            @unlink(public_path($item->photo_path));
        }

        $item->delete();

        return redirect()
            ->route('lost-items.index')
            ->with('status', 'Laporan barang hilang berhasil dihapus.');
    }

    private function rooms()
    {
        if (! Schema::hasTable('rooms')) {
            return collect();
        }

        return DB::table('rooms')
            ->get()
            ->sortBy('code', SORT_NATURAL);
    }

    private function save(Request $request, ?LostItem $item = null)
    {
        $data = $request->validate([
            'item_name' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:1000'],
            'location' => ['required', 'string', 'max:100'],
            'lost_at' => ['required', 'date'],
            'status' => ['nullable', 'in:hilang,ditemukan'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'item_name.required' => 'Nama barang wajib diisi.',
            'description.required' => 'Ciri-ciri atau informasi barang wajib diisi.',
            'location.required' => 'Lokasi kehilangan (ruangan atau lantai) wajib diisi.',
            'lost_at.required' => 'Waktu kehilangan wajib diisi.',
            'photo.image' => 'File foto harus berupa gambar (JPG, PNG, WEBP).',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $photoPath = $item?->photo_path;

        if ($request->hasFile('photo')) {
            $uploadDir = public_path('uploads/lost-items');
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if ($photoPath && file_exists(public_path($photoPath))) {
                @unlink(public_path($photoPath));
            }

            $file = $request->file('photo');
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9.\-_]/', '_', $file->getClientOriginalName());
            $file->move($uploadDir, $filename);
            $photoPath = 'uploads/lost-items/'.$filename;
        }

        $payload = [
            'item_name' => $data['item_name'],
            'description' => $data['description'],
            'location' => $data['location'],
            'lost_at' => $data['lost_at'],
            'photo_path' => $photoPath,
            'status' => $data['status'] ?? ($item?->status ?? 'hilang'),
        ];

        if ($item) {
            $item->update($payload);
            $message = 'Laporan barang hilang berhasil diperbarui.';
        } else {
            $payload['user_id'] = $request->user()->id;
            LostItem::create($payload);
            $message = 'Laporan barang hilang berhasil ditambahkan.';
        }

        return redirect()
            ->route('lost-items.index')
            ->with('status', $message);
    }
}