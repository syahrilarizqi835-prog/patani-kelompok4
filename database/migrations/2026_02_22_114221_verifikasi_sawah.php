<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifikasi', function (Blueprint $table) {
            $table->id();

            // Notifikasi dikirim ke petani ini
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Notifikasi terkait sawah mana (opsional)
            $table->foreignId('sawah_id')->nullable()->constrained('sawah')->onDelete('cascade');

            // Jenis notifikasi
            $table->enum('tipe', [
                'verifikasi_lulus',   // sawah diverifikasi, status OK
                'verifikasi_tolak',   // sawah ditolak, perlu perbaikan
                'peringatan_hama',    // admin mengirim peringatan hama
                'rekomendasi',        // rekomendasi teknis dari admin
                'pengumuman',         // info umum dari admin
            ])->default('pengumuman');

            $table->string('judul');
            $table->text('pesan');

            // Status baca oleh petani
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
        });

        // Kolom verifikasi di tabel sawah
        Schema::table('sawah', function (Blueprint $table) {
            $table->enum('verifikasi_status', ['belum', 'lulus', 'ditolak'])
                  ->default('belum')
                  ->after('status');
            $table->text('verifikasi_catatan')->nullable()->after('verifikasi_status');
            $table->timestamp('verifikasi_at')->nullable()->after('verifikasi_catatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifikasi');

        Schema::table('sawah', function (Blueprint $table) {
            $table->dropColumn(['verifikasi_status', 'verifikasi_catatan', 'verifikasi_at']);
        });
    }
};