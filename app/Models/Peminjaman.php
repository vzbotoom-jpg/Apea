<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans'; // ✅ sesuai tabel asli

    protected static function booted(): void
    {
        static::creating(function (self $peminjaman): void {
            $peminjaman->qr_token ??= bin2hex(random_bytes(32));
        });
    }

    protected $fillable = [
        'kode_peminjaman',
        'user_id',
        'petugas_id',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'tanggal_verifikasi',
        'tanggal_ambil',
        'status',
        'catatan',
        'total_denda',
        'status_pembayaran',
        'metode_pembayaran',
        'tanggal_bayar',
        'dibayar_oleh',
        // ✅ BARU — Perpanjangan
        'perpanjangan_requested',
        'tanggal_perpanjangan',
        'status_perpanjangan',
        'alasan_perpanjangan',
        'approved_by',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'tanggal_ambil' => 'datetime',
        'tanggal_bayar' => 'datetime',
        'perpanjangan_requested' => 'boolean',   // ✅ BARU
        'tanggal_perpanjangan' => 'date',        // ✅ BARU
    ];

    // ================= Relationships =================
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function approvedBy() // ✅ BARU
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function pembayar()
    {
        return $this->belongsTo(User::class, 'dibayar_oleh');
    }

    public function detailPeminjamans()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class);
    }

    public function denda()
    {
        return $this->hasOne(Denda::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    // ================= Accessors =================
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'menunggu' => 'bg-yellow-100 text-yellow-800',
            'diverifikasi' => 'bg-blue-100 text-blue-800',
            'dipinjam' => 'bg-purple-100 text-purple-800',
            'dikembalikan' => 'bg-green-100 text-green-800',
            'dibatalkan' => 'bg-red-100 text-red-800',
            'terlambat' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getPerpanjanganBadgeAttribute() // ✅ BARU
    {
        return match ($this->status_perpanjangan) {
            'menunggu' => 'bg-amber-100 text-amber-700',
            'disetujui' => 'bg-emerald-100 text-emerald-700',
            'ditolak' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }

    public function getTotalItemAttribute()
    {
        return $this->detailPeminjamans->sum('jumlah');
    }

    public function getTotalSewaAttribute()
    {
        return $this->detailPeminjamans->sum('subtotal');
    }

    public function getTotalBayarAttribute()
    {
        return $this->detailPeminjamans->sum('subtotal') + $this->getDendaTerutang();
    }

    public function getJumlahTerbayar(): int
    {
        $paid = (int) $this->pembayarans()
            ->where('status', 'disetujui')
            ->sum('nominal');

        if ($this->status_pembayaran === 'lunas') {
            $rentalTotal = $this->detailPeminjamans()
                ->sum('subtotal');
            $paidFine = $this->denda()
                ->where('status', 'lunas')
                ->value('jumlah_denda') ?? 0;

            return max($paid, (int) $rentalTotal + (int) $paidFine);
        }

        return $paid;
    }

    public function getSisaTagihan(): int
    {
        return max(0, $this->total_bayar - $this->getJumlahTerbayar());
    }

    // ================= Methods =================
    public function isLate()
    {
        return now()->startOfDay()->greaterThan($this->tanggal_jatuh_tempo) &&
            ! in_array($this->status, ['dikembalikan', 'dibatalkan'], true);
    }

    public function getLateDays()
    {
        if (! $this->isLate()) {
            return 0;
        }

        return (int) $this->tanggal_jatuh_tempo->startOfDay()->diffInDays(now()->startOfDay());
    }

    public static function rentalDaysBetween($startDate, $dueDate): int
    {
        return max(
            1,
            (int) Carbon::parse($startDate)->startOfDay()->diffInDays(Carbon::parse($dueDate)->startOfDay())
        );
    }

    public function getRentalDays(): int
    {
        return self::rentalDaysBetween($this->tanggal_pinjam, $this->tanggal_jatuh_tempo);
    }

    public function calculateDenda(): int
    {
        if ($this->status === 'dikembalikan') {
            return (int) ($this->getDendaTerutangFromRecord() ?? 0);
        }

        if (! $this->isLate()) {
            return 0;
        }

        $billableDays = max(0, $this->getLateDays() - (int) Setting::get('masa_tenggang_hari', 2));
        if ($billableDays === 0) {
            return 0;
        }

        $details = $this->relationLoaded('detailPeminjamans')
            ? $this->detailPeminjamans
            : $this->detailPeminjamans()->with('alat')->get();

        $defaultRate = (int) Setting::get('denda_per_hari', 5000);

        return (int) $details->sum(function (DetailPeminjaman $detail) use ($billableDays, $defaultRate) {
            $rate = $detail->denda_per_hari_saat_pinjam;
            if ($rate === null) {
                $rate = $detail->alat?->denda_per_hari;
            }

            $rate = (int) $rate ?: $defaultRate;

            return $detail->jumlah * (int) $rate * $billableDays;
        });
    }

    public function getDendaTerutang(): int
    {
        if ($this->status !== 'dikembalikan') {
            return $this->calculateDenda();
        }

        return (int) ($this->getDendaTerutangFromRecord() ?? 0);
    }

    private function getDendaTerutangFromRecord(): ?int
    {
        if ($this->relationLoaded('denda')) {
            return $this->denda?->jumlah_denda;
        }

        return $this->denda()->value('jumlah_denda');
    }

    // ✅ BARU — Helper Perpanjangan
    public function bisaPerpanjangan(): bool
    {
        return $this->status === 'dipinjam' && ! $this->perpanjangan_requested;
    }

    public function perpanjanganMenunggu(): bool
    {
        return $this->status_perpanjangan === 'menunggu';
    }
}
