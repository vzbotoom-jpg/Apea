<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    public const METODE_LIST = [
        'transfer_bank' => 'Transfer Bank',
        'dana'          => 'DANA',
        'shopeepay'     => 'ShopeePay',
        'gopay'         => 'GoPay',
        'ovo'           => 'OVO',
        'tunai'         => 'Tunai (Loket)',
    ];

    protected $fillable = [
        'peminjaman_id', 'user_id', 'metode', 'nominal', 'bukti_path',
        'status', 'verified_by', 'verified_at', 'catatan',
    ];

    protected $casts = ['nominal' => 'integer', 'verified_at' => 'datetime'];

    public function peminjaman() { return $this->belongsTo(Peminjaman::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }

    public function getMetodeLabelAttribute()
    {
        return self::METODE_LIST[$this->metode] ?? ucfirst($this->metode);
    }
}