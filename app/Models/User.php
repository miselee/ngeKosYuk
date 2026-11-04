<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nomor_hp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Daftar kos yang dimiliki (untuk pemilik).
     */
    public function kosYangDimiliki(): HasMany
    {
        return $this->hasMany(Kos::class);
    }

    /**
     * Alias daftar kos yang dimiliki (untuk pemilik).
     */
    public function kos(): HasMany
    {
        return $this->hasMany(Kos::class);
    }

    /**
     * Daftar sewa (untuk penyewa).
     */
    public function sewas(): HasMany
    {
        return $this->hasMany(Sewa::class);
    }

    /**
     * Daftar pembayaran melalui sewa (untuk penyewa).
     */
    public function pembayarans(): HasManyThrough
    {
        return $this->hasManyThrough(Pembayaran::class, Sewa::class);
    }

    /**
     * Daftar ulasan (untuk penyewa).
     */
    public function ulasans(): HasMany
    {
        return $this->hasMany(Ulasan::class);
    }
}
