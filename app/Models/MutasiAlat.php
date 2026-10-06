<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiAlat extends Model
{
    protected $table = 'mutasi_alats';

    protected $fillable = ['alat_id', 'jenis', 'jumlah', 'keterangan', 'ref_type', 'ref_id', 'user_id'];

    public function alat()
    {
        return $this->belongsTo(Alat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getJenisBadgeAttribute()
    {
        return match ($this->jenis) {
            'masuk'     => 'bg-green-100 text-green-800',
            'keluar'    => 'bg-blue-100 text-blue-800',
            'perbaikan' => 'bg-orange-100 text-orange-800',
            default     => 'bg-gray-100 text-gray-800',
        };
    }

    /** Pencatatan terpusat */
    public static function catat(Alat $alat, string $jenis, int $jumlah, string $keterangan, $ref = null): void
    {
        static::create([
            'alat_id'    => $alat->id,
            'jenis'      => $jenis,
            'jumlah'     => $jumlah,
            'keterangan' => $keterangan,
            'ref_type'   => $ref ? class_basename($ref) : null,
            'ref_id'     => $ref?->id,
            'user_id'    => auth()->id(),
        ]);
    }
}