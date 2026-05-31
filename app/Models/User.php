<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'nik',
        'desa',
        'kecamatan',
        'alamat',
        'status',
        'is_premium',
        'premium_until',
        'foto_profil', // kolom baru
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'premium_until'     => 'datetime',
            'is_premium'        => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function sawah()
    {
        return $this->hasMany(Sawah::class);
    }

    public function forumTopics()
    {
        return $this->hasMany(ForumTopic::class);
    }

    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function chatbotConversations()
    {
        return $this->hasMany(ChatbotConversation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopePetani($query)
    {
        return $query->where('role', 'petani');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPetani()
    {
        return $this->role === 'petani';
    }

    public function getTotalLuasSawah()
    {
        return $this->sawah()->sum('luas');
    }

    public function isPremium(): bool
    {
        if (!$this->is_premium) {
            return false;
        }

        if ($this->premium_until && $this->premium_until < now()) {
            $this->update([
                'is_premium'    => false,
                'premium_until' => null,
            ]);

            return false;
        }

        return true;
    }

    public function chatHariIni(): int
    {
        return \App\Models\ChatbotConversation::where('user_id', $this->id)
            ->whereDate('created_at', today())
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getInitialAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    /**
     * URL foto profil.
     * Jika tidak ada, gunakan avatar otomatis.
     */
    public function getFotoProfilUrlAttribute(): string
    {
        if ($this->foto_profil && Storage::disk('public')->exists($this->foto_profil)) {
            return Storage::url($this->foto_profil);
        }

        return 'https://ui-avatars.com/api/?name='
            . urlencode($this->name)
            . '&background=16a34a&color=fff&size=128';
    }
}