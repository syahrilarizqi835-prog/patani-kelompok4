<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_premium', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('paket', ['1_bulan', '3_bulan', '12_bulan']);
            $table->integer('durasi_bulan');
            $table->decimal('harga', 10, 2);
            $table->enum('metode_bayar', ['qris', 'transfer_bank', 'shopee_pay', 'dana', 'ovo', 'gopay'])->nullable();
            $table->enum('status', ['pending', 'menunggu_konfirmasi', 'aktif', 'ditolak'])->default('pending');
            $table->string('bukti_bayar')->nullable();
            $table->timestamp('dikonfirmasi_at')->nullable();
            $table->foreignId('dikonfirmasi_oleh')->nullable()->constrained('users');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_premium');
    }
};