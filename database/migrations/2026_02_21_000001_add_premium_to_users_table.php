<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('status');
            $table->timestamp('premium_until')->nullable()->after('is_premium');
        });

        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->string('tipe')->default('teks')->after('context'); // teks, foto, laporan
            $table->integer('tokens_used')->default(0)->after('tipe');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_premium', 'premium_until']);
        });
        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'tokens_used']);
        });
    }
};