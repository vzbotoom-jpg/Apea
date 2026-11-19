<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pembayaran extends Model
{
    public const METODE_LIST = [
        'tunai' => 'Tunai',
        'transfer_bri' => 'Transfer Bank BRI',
        'transfer_bca' => 'Transfer Bank BCA',
        'transfer_bni' => 'Transfer Bank BNI',
        'qris' => 'QRIS',
        'dana' => 'DANA',
        'gopay' => 'GoPay',
        'ovo' => 'OVO',
        'shopeepay' => 'ShopeePay',
    ];

    protected $fillable = [
        'peminjaman_id', 'user_id', 'metode', 'nominal', 'bukti_path', 'qr_path',
        'status', 'verified_by', 'verified_at', 'catatan',
    ];

    protected $casts = ['nominal' => 'integer', 'verified_at' => 'datetime'];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getMetodeLabelAttribute()
    {
        return self::METODE_LIST[$this->metode] ?? match ($this->metode) {
            'transfer', 'transfer_bank' => 'Transfer Bank',
            default => ucfirst($this->metode),
        };
    }

    public static function normalizeMethods(array $methods): array
    {
        $normalized = [];

        foreach ($methods as $method) {
            if (! is_string($method)) {
                continue;
            }

            $expanded = match ($method) {
                'transfer', 'transfer_bank' => ['transfer_bri', 'transfer_bca', 'transfer_bni'],
                default => isset(self::METODE_LIST[$method]) ? [$method] : [],
            };

            array_push($normalized, ...$expanded);
        }

        return array_values(array_unique($normalized));
    }

    public static function qrCodes(): array
    {
        $qrCodes = json_decode(Setting::get('qr_pembayaran', '{}'), true);

        return is_array($qrCodes)
            ? array_filter(
                $qrCodes,
                fn ($path, $method): bool => is_string($method)
                    && isset(self::METODE_LIST[$method])
                    && is_string($path),
                ARRAY_FILTER_USE_BOTH
            )
            : [];
    }

    public static function enabledMethods(): array
    {
        $configured = json_decode(Setting::get('metode_pembayaran_aktif', json_encode(['tunai'])), true);
        $configured = is_array($configured) ? self::normalizeMethods($configured) : ['tunai'];
        $qrCodes = self::qrCodes();

        return array_values(array_filter(
            $configured,
            fn (string $method): bool => $method === 'tunai'
                || (! empty($qrCodes[$method]) && Storage::disk('public')->exists($qrCodes[$method]))
        ));
    }
}
