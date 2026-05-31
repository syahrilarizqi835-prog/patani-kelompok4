<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin PATANI',
            'email' => 'admin@patani.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'status' => 'aktif',
        ]);

        // Petani users
        $petaniData = [
            [
                'name' => 'Ahmad Sudirman',
                'email' => 'ahmad@patani.com',
                'password' => Hash::make('petani123'),
                'role' => 'petani',
                'phone' => '081234567891',
                'nik' => '3212345678901234',
                'desa' => 'Jatibarang',
                'kecamatan' => 'Jatibarang',
                'alamat' => 'Jl. Raya Jatibarang No. 123',
                'status' => 'aktif',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@patani.com',
                'password' => Hash::make('petani123'),
                'role' => 'petani',
                'phone' => '081234567892',
                'nik' => '3212345678901235',
                'desa' => 'Lohbener',
                'kecamatan' => 'Lohbener',
                'alamat' => 'Jl. Raya Lohbener No. 45',
                'status' => 'aktif',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@patani.com',
                'password' => Hash::make('petani123'),
                'role' => 'petani',
                'phone' => '081234567893',
                'nik' => '3212345678901236',
                'desa' => 'Karangampel',
                'kecamatan' => 'Karangampel',
                'alamat' => 'Jl. Raya Karangampel No. 67',
                'status' => 'aktif',
            ],
        ];

        foreach ($petaniData as $data) {
            User::create($data);
        }
    }
}
