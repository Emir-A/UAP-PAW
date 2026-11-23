<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LostItem extends Model
{
    protected $fillable = [
        'user_id',
        'item_name',
        'description',
        'location',
        'lost_at',  
        'photo_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'lost_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}