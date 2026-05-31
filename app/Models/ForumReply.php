<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'forum_replies';

    protected $fillable = [
        'topic_id',
        'parent_id',   // ← null = balasan ke topik, isi = balasan ke komentar
        'user_id',
        'content',
        'likes',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function topic()
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Komentar induk (jika ini adalah balasan ke komentar lain)
    public function parent()
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    // Balasan-balasan ke komentar ini
    public function children()
    {
        return $this->hasMany(ForumReply::class, 'parent_id')->with('user')->oldest();
    }

    public function likesRelation()
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->user?->role === 'admin';
    }
}