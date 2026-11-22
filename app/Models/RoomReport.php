<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class RoomReport extends Model
{
    use HasFactory;
    public const CATEGORIES = [
        'fasilitas'  => 'Kerusakan fasilitas',
        'kebersihan' => 'Kebersihan',
        'listrik'    => 'Listrik/AC',
        'proyektor'  => 'Proyektor/perangkat',
        'lainnya'    => 'Lainnya',
    ];
    public const STATUSES = [
        'baru'     => 'Baru',
        'diproses' => 'Diproses',
        'selesai'  => 'Selesai',
    ];
    protected $fillable = [
        'room',
        'category',
        'title',
        'description',
        'photo_path',
    ];
    protected $attributes = [
        'status' => 'baru',
    ];
    public function user(): BelongsTo
    { 
        return $this->belongsTo(User::class);
    }
    public function categoryLabel(): string
    { 
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
    public function statusLabel(): string
    { 
        return self::STATUSES[$this->status] ?? $this->status;
    }
}