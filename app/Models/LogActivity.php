<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    use HasFactory;

    protected $table = 'log_activities';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'activity',
        'description',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByActivity($query, $activity)
    {
        return $query->where('activity', 'LIKE', "%{$activity}%");
    }

    // Methods
    public static function log($userId, $activity, $description = null, $data = [])
    {
        return self::create([
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'activity' => $activity,
            'description' => $description,
        ]);
    }
}