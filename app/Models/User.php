<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_telepon',
        'alamat',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'user_id');
    }

    public function peminjamansSebagaiPetugas()
    {
        return $this->hasMany(Peminjaman::class, 'petugas_id');
    }

    public function pengembalians()
    {
        return $this->hasMany(Pengembalian::class, 'petugas_id');
    }

    public function logActivities()
    {
        return $this->hasMany(LogActivity::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPetugas()
    {
        return $this->role === 'petugas';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function hasRole($roles)
    {
        return in_array($this->role, (array) $roles);
    }
}