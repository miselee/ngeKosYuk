<?php

namespace App\Models;

use Database\Factories\PembayaranFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    /** @use HasFactory<PembayaranFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'sewa_id',
        'jumlah',
        'metode_pembayaran',
        'bukti_pembayaran',
        'status',
        'tanggal_bayar',
        'keterangan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'tanggal_bayar' => 'date',
            'metode_pembayaran' => 'string',
            'status' => 'string',
        ];
    }

    /**
     * Sewa terkait pembayaran ini.
     */
    public function sewa(): BelongsTo
    {
        return $this->belongsTo(Sewa::class);
    }
}
