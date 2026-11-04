<?php

namespace App\Models;

use Database\Factories\UlasanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ulasan extends Model
{
    /** @use HasFactory<UlasanFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'kos_id',
        'sewa_id',
        'rating',
        'komentar',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    /**
     * Penyewa yang memberi ulasan.
     */
    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Kos yang diulas.
     */
    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class);
    }

    /**
     * Sewa terkait ulasan.
     */
    public function sewa(): BelongsTo
    {
        return $this->belongsTo(Sewa::class);
    }
}
