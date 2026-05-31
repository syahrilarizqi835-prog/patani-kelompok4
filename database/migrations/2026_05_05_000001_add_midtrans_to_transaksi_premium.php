<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_premium', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('metode_bayar');
            $table->string('order_id')->nullable()->after('snap_token');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_premium', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'order_id']);
        });
    }
};