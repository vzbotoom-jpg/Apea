<?php

namespace App\Helpers;

use App\Models\Notifikasi;
use App\Models\LogActivity;
use Illuminate\Support\Str;

class Helper
{
    /**
     * Send notification to user
     */
    public static function sendNotification($userId, $title, $message, $type = 'info', $link = null)
    {
        return Notifikasi::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    /**
     * Get user notifications
     */
    public static function getNotifications($userId, $limit = 10)
    {
        return Notifikasi::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notification count
     */
    public static function getUnreadCount($userId)
    {
        return Notifikasi::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Generate unique code
     */
    public static function generateKode($prefix, $model, $field = 'kode_', $length = 3)
    {
        $last = $model::latest()->first();
        if (!$last) {
            return $prefix . str_pad('1', $length, '0', STR_PAD_LEFT);
        }
        
        $lastNumber = (int) substr($last->$field, -$length);
        $newNumber = str_pad($lastNumber + 1, $length, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    /**
     * Log activity
     */
    public static function logActivity($userId, $activity, $description = null)
    {
        return LogActivity::log($userId, $activity, $description);
    }

    /**
     * Format currency
     */
    public static function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Generate slug
     */
    public static function generateSlug($string)
    {
        return Str::slug($string) . '-' . uniqid();
    }

    /**
     * Get days difference
     */
    public static function daysDiff($date1, $date2)
    {
        $diff = $date1->diffInDays($date2);
        return $diff;
    }

    /**
     * Check if date is past
     */
    public static function isPast($date)
    {
        return now()->greaterThan($date);
    }

    /**
     * Get status badge class
     */
    public static function getStatusBadge($status)
    {
        $badges = [
            'menunggu' => 'bg-yellow-100 text-yellow-800',
            'diverifikasi' => 'bg-blue-100 text-blue-800',
            'dipinjam' => 'bg-purple-100 text-purple-800',
            'dikembalikan' => 'bg-green-100 text-green-800',
            'dibatalkan' => 'bg-red-100 text-red-800',
            'terlambat' => 'bg-red-100 text-red-800',
            'tersedia' => 'bg-green-100 text-green-800',
            'perbaikan' => 'bg-orange-100 text-orange-800',
            'tidak_tersedia' => 'bg-gray-100 text-gray-800',
            'baik' => 'bg-green-100 text-green-800',
            'rusak_ringan' => 'bg-yellow-100 text-yellow-800',
            'rusak_berat' => 'bg-red-100 text-red-800',
            'hilang' => 'bg-red-100 text-red-800',
            'active' => 'bg-green-100 text-green-800',
            'inactive' => 'bg-gray-100 text-gray-800',
            'belum_bayar' => 'bg-red-100 text-red-800',
            'lunas' => 'bg-green-100 text-green-800',
        ];

        return $badges[$status] ?? 'bg-gray-100 text-gray-800';
    }
}