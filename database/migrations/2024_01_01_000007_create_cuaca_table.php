<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuaca', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi');
            $table->date('tanggal');
            $table->decimal('suhu', 5, 2)->nullable(); // celsius
            $table->decimal('kelembaban', 5, 2)->nullable(); // persen
            $table->decimal('curah_hujan', 8, 2)->nullable(); // mm
            $table->decimal('kecepatan_angin', 5, 2)->nullable(); // km/h
            $table->string('kondisi')->nullable(); // cerah, berawan, hujan, dll
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuaca');
    }
};
