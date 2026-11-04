<?php

namespace App\Models;

use Database\Factories\KosFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kos extends Model
{
    /** @use HasFactory<KosFactory> */
    use HasFactory;

    protected $table = 'kos';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'nama_kos',
        'alamat',
        'deskripsi',
        'tipe_kos',
        'foto',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipe_kos' => 'string',
        ];
    }

    /**
     * Pemilik kos.
     */
    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias user pemilik kos.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Daftar kamar di kos ini.
     */
    public function kamars(): HasMany
    {
        return $this->hasMany(Kamar::class);
    }

    /**
     * Daftar sewa di kos ini.
     */
    public function sewas(): HasMany
    {
        return $this->hasMany(Sewa::class);
    }

    /**
     * Daftar ulasan untuk kos ini.
     */
    public function ulasans(): HasMany
    {
        return $this->hasMany(Ulasan::class);
    }
}
