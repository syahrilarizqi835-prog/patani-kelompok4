<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sawah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_sawah');
            $table->string('lokasi');
            $table->string('desa');
            $table->string('kecamatan');
            $table->decimal('luas', 10, 2); // dalam hektar
            $table->string('jenis_padi');
            $table->date('tanggal_tanam')->nullable();
            $table->date('estimasi_panen')->nullable();
            $table->enum('kondisi_tanah', ['subur', 'sedang', 'kurang'])->default('sedang');
            $table->enum('kondisi_air', ['baik', 'cukup', 'kurang'])->default('baik');
            $table->enum('fase_tanam', ['persiapan', 'vegetatif', 'generatif', 'pematangan', 'panen'])->default('persiapan');
            $table->enum('status', ['aktif', 'panen', 'istirahat'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sawah');
    }
};
