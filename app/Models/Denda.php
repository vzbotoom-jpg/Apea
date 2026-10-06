<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    use HasFactory;

    protected $fillable = [
        'peminjaman_id',
        'jumlah_denda',
        'tanggal_denda',
        'hari_terlambat',
        'status',
        'tanggal_bayar',
    ];

    protected $casts = [
        'tanggal_denda' => 'date',
        'tanggal_bayar' => 'date',
    ];

    // Relationships
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    // Accessors
    public function getJumlahDendaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_denda, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === 'lunas' 
            ? 'bg-green-100 text-green-800' 
            : 'bg-red-100 text-red-800';
    }

    // Methods
    public function markAsPaid()
    {
        $this->status = 'lunas';
        $this->tanggal_bayar = now();
        $this->save();
    }

    public function isPaid()
    {
        return $this->status === 'lunas';
    }
}