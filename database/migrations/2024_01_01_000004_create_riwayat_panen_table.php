<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_panen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sawah_id')->constrained('sawah')->onDelete('cascade');
            $table->date('tanggal_panen');
            $table->decimal('hasil_panen', 10, 2); // dalam kg
            $table->decimal('hasil_per_hektar', 10, 2)->nullable(); // ton/ha
            $table->enum('kualitas', ['sangat_baik', 'baik', 'sedang', 'kurang'])->default('baik');
            $table->decimal('harga_jual', 12, 2)->nullable();
            $table->decimal('total_pendapatan', 12, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_panen');
    }
};
