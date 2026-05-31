<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'views',
        'likes',
        'is_hot',
        'is_pinned',
        'is_locked',   // ← kolom baru: dikunci admin
        'admin_note',  // ← kolom baru: catatan dari admin
    ];

    protected $casts = [
        'is_hot'    => 'boolean',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'topic_id');
    }

    public function likesRelation()
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePopular($query)
    {
        return $query->where('views', '>', 100)->orWhere('likes', '>', 10);
    }

    public function scopeHot($query)
    {
        return $query->where('is_hot', true);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getRepliesCountAttribute()
    {
        return $this->replies()->count();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function incrementViews()
    {
        $this->increment('views');
    }
}