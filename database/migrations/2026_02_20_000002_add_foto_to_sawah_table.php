<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sawah', function (Blueprint $table) {
            $table->string('foto_lahan')->nullable()->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('sawah', function (Blueprint $table) {
            $table->dropColumn('foto_lahan');
        });
    }
};