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

    /**
     * Format nomor invoice / kwitansi standar (misal: INV-20261108-0001).
     */
    public function getKodeInvoiceAttribute(): string
    {
        $tanggal = $this->tanggal_bayar
            ? $this->tanggal_bayar->format('Ymd')
            : ($this->created_at ? $this->created_at->format('Ymd') : date('Ymd'));

        return 'INV-' . $tanggal . '-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Label metode pembayaran yang rapi untuk antarmuka.
     */
    public function getLabelMetodeAttribute(): string
    {
        return match ($this->metode_pembayaran) {
            'transfer_bank' => 'Transfer Bank',
            'e_wallet' => 'E-Wallet',
            'tunai' => 'Tunai',
            default => ucwords(str_replace('_', ' ', (string) $this->metode_pembayaran)),
        };
    }
}
