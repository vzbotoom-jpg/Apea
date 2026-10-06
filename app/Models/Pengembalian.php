<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $fillable = [
        'peminjaman_id',
        'petugas_id',
        'tanggal_kembali',
        'denda',
        'hari_terlambat',
        'kondisi_alat',
        'catatan',
        'bukti_foto',
    ];

    protected $casts = [
        'tanggal_kembali' => 'date',
        'denda' => 'integer',
        'hari_terlambat' => 'integer',
    ];

    // Relationships
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    // Accessors
    public function getDendaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->denda, 0, ',', '.');
    }

    public function getKondisiBadgeAttribute()
    {
        $badges = [
            'baik' => 'bg-green-100 text-green-800',
            'rusak_ringan' => 'bg-yellow-100 text-yellow-800',
            'rusak_berat' => 'bg-red-100 text-red-800',
            'hilang' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->kondisi_alat] ?? 'bg-gray-100 text-gray-800';
    }

    public function getKondisiLabelAttribute()
    {
        $labels = [
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            'hilang' => 'Hilang',
        ];

        return $labels[$this->kondisi_alat] ?? $this->kondisi_alat;
    }
}