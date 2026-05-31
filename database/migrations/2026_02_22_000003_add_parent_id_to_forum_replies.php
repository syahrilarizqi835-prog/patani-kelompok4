<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_replies', function (Blueprint $table) {

            // Cegah error jika kolom sudah pernah dibuat
            if (!Schema::hasColumn('forum_replies', 'parent_id')) {

                // parent_id nullable
                // null  = balasan langsung ke topik
                // isi   = balasan ke komentar lain (nested 1 level)
                $table->unsignedBigInteger('parent_id')
                      ->nullable()
                      ->after('topic_id');

                $table->foreign('parent_id')
                      ->references('id')
                      ->on('forum_replies')
                      ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('forum_replies', function (Blueprint $table) {

            if (Schema::hasColumn('forum_replies', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
        });
    }
};