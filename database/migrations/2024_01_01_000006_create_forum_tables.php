<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('category', ['hama_penyakit', 'varietas_padi', 'teknik_budidaya', 'pemupukan', 'pengairan', 'umum'])->default('umum');
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->boolean('is_hot')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('forum_topics')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->integer('likes')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('forum_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('likeable_type'); // ForumTopic atau ForumReply
            $table->unsignedBigInteger('likeable_id');
            $table->timestamps();
            
            $table->unique(['user_id', 'likeable_type', 'likeable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_likes');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_topics');
    }
};
