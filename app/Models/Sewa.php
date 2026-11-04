<?php

namespace App\Models;

use Database\Factories\SewaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sewa extends Model
{
    /** @use HasFactory<SewaFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'kamar_id',
        'kos_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'catatan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'status' => 'string',
        ];
    }

    /**
     * Penyewa yang mengajukan sewa.
     */
    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Kamar yang disewa.
     */
    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    /**
     * Kos terkait sewa ini.
     */
    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class);
    }

    /**
     * Daftar pembayaran untuk sewa ini.
     */
    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Ulasan untuk sewa ini.
     */
    public function ulasan(): HasOne
    {
        return $this->hasOne(Ulasan::class);
    }
}
