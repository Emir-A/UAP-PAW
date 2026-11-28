<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    public const STATUSES = [
        'belum_dikerjakan' => 'Belum dikerjakan',
        'dikerjakan' => 'Dikerjakan',
        'selesai' => 'Selesai',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'course_name',
        'description',
        'due_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'selesai' && $this->due_at->isPast();
    }
}
