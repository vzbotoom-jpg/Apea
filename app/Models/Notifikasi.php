<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasis';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
        'link',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
    }

    // Tambahkan di dalam class Notifikasi
public static function kirim($userId, $title, $message, $type = 'info', $link = null)
{
    return static::create([
        'user_id' => $userId,
        'title'   => $title,
        'message' => $message,
        'type'    => $type,   // info | success | warning | danger | payment
        'is_read' => false,
        'link'    => $link,
    ]);
}
}