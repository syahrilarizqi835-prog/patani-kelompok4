<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Untuk MySQL: ubah enum langsung
        DB::statement("ALTER TABLE riwayat_panen MODIFY COLUMN kualitas ENUM('gabah_basah', 'gabah_kering', 'beras') NOT NULL DEFAULT 'gabah_kering'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE riwayat_panen MODIFY COLUMN kualitas ENUM('sangat_baik', 'baik', 'sedang', 'kurang') NOT NULL DEFAULT 'baik'");
    }
};