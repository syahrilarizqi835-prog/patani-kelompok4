<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perawatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sawah_id')->constrained('sawah')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('jenis_perawatan', ['pemupukan', 'penyemprotan', 'pengairan', 'penyiangan', 'lainnya']);
            $table->string('nama_kegiatan');
            $table->text('deskripsi')->nullable();
            $table->string('bahan_digunakan')->nullable();
            $table->decimal('jumlah', 10, 2)->nullable();
            $table->string('satuan')->nullable();
            $table->decimal('biaya', 12, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perawatan');
    }
};
