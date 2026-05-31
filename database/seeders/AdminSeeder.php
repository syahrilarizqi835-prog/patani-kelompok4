<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the default admin account for the PATANI application.
     *
     * Uses firstOrCreate() so re-running the seeder is idempotent —
     * the record is only inserted once and never duplicated.
     */
    public function run(): void
    {
        User::firstOrCreate(
            // Lookup key — uniquely identifies the admin account
            ['email' => 'admin@patani.com'],
            // Attributes applied only when creating a new record
            [
                'name'     => 'Admin PATANI',
                'password' => Hash::make('Admin@2026'),
                'role'     => 'admin',
                'phone'    => '081234567890',
                'status'   => 'aktif',
            ]
        );
    }
}
