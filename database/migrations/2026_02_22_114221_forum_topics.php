<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_topics', function (Blueprint $table) {
            // Kolom lock — mencegah petani membalas topik yang dikunci admin
            $table->boolean('is_locked')->default(false)->after('is_pinned');

            // Kolom catatan admin — admin bisa kasih keterangan kenapa dikunci/dihapus
            $table->text('admin_note')->nullable()->after('is_locked');
        });
    }

    public function down(): void
    {
        Schema::table('forum_topics', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'admin_note']);
        });
    }
};