<?php

namespace App\Models;

use Database\Factories\KamarFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    /** @use HasFactory<KamarFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'kos_id',
        'nomor_kamar',
        'harga_per_bulan',
        'fasilitas',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga_per_bulan' => 'integer',
            'status' => 'string',
        ];
    }

    /**
     * Kos tempat kamar ini berada.
     */
    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class);
    }

    /**
     * Daftar sewa untuk kamar ini.
     */
    public function sewas(): HasMany
    {
        return $this->hasMany(Sewa::class);
    }
}
