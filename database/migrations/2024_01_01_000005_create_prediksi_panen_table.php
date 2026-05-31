<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prediksi_panen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sawah_id')->constrained('sawah')->onDelete('cascade');
            $table->date('tanggal_prediksi');
            $table->decimal('prediksi_hasil', 10, 2); // dalam ton
            $table->decimal('confidence_level', 5, 2)->default(0); // persentase akurasi
            $table->json('faktor_prediksi')->nullable(); // cuaca, perawatan, dll
            $table->text('rekomendasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediksi_panen');
    }
};
