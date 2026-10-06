<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_alat',
        'nama_alat',
        'slug',
        'kategori_id',
        'deskripsi',
        'stok',
        'stok_tersedia',
        'kondisi',
        'status',
        'gambar',
        'harga_sewa_per_hari',
        'denda_per_hari',
    ];

    // Relationships
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailPeminjamans()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'tersedia' => 'bg-green-100 text-green-800',
            'dipinjam' => 'bg-yellow-100 text-yellow-800',
            'perbaikan' => 'bg-orange-100 text-orange-800',
            'tidak_tersedia' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getKondisiBadgeAttribute()
    {
        $badges = [
            'baik' => 'bg-green-100 text-green-800',
            'rusak_ringan' => 'bg-yellow-100 text-yellow-800',
            'rusak_berat' => 'bg-red-100 text-red-800',
            'perbaikan' => 'bg-orange-100 text-orange-800',
        ];

        return $badges[$this->kondisi] ?? 'bg-gray-100 text-gray-800';
    }

    // Methods
    public function isAvailable()
    {
        return $this->status === 'tersedia' && $this->stok_tersedia > 0;
    }

    public function reduceStock($jumlah)
    {
        if ($this->stok_tersedia >= $jumlah) {
            $this->stok_tersedia -= $jumlah;
            if ($this->stok_tersedia === 0) {
                $this->status = 'dipinjam';
            }
            $this->save();
            return true;
        }
        return false;
    }

    public function addStock($jumlah)
    {
        $this->stok_tersedia += $jumlah;
        if ($this->stok_tersedia > 0 && $this->status === 'dipinjam') {
            $this->status = 'tersedia';
        }
        $this->save();
    }

    public function mutasis()
{
    return $this->hasMany(MutasiAlat::class)->latest();
}
}