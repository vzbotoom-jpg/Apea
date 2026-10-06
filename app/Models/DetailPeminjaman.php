<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'detail_peminjamans';

    protected $fillable = [
        'peminjaman_id',
        'alat_id',
        'jumlah',
        'harga_sewa_saat_pinjam',
        'subtotal',
    ];

    // Relationships
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function alat()
    {
        return $this->belongsTo(Alat::class);
    }

    // Accessors
    public function getSubtotalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getHargaSewaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga_sewa_saat_pinjam, 0, ',', '.');
    }
}