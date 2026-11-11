<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin default
        User::firstOrCreate(
            ['email' => 'admin@apic.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'no_telepon' => '081234567890',
                'alamat' => 'Kantor Pusat APIC',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Petugas default
        User::firstOrCreate(
            ['email' => 'petugas@apic.test'],
            [
                'name' => 'Petugas APIC',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'no_telepon' => '081234567891',
                'alamat' => 'Ruang Operasional APIC',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // User default
        User::firstOrCreate(
            ['email' => 'user@apic.test'],
            [
                'name' => 'Peminjam Demo',
                'password' => Hash::make('password'),
                'role' => 'user',
                'no_telepon' => '081234567892',
                'alamat' => 'Jl. Merdeka No. 12',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}
