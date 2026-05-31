<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotifikasi extends Model
{
    use HasFactory;

    protected $table = 'admin_notifikasi';

    protected $fillable = [
        'user_id',
        'sawah_id',
        'tipe',
        'judul',
        'pesan',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read'  => 'boolean',
        'read_at'  => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sawah()
    {
        return $this->belongsTo(Sawah::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeBelumDibaca($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeUntukPetani($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function tandaiDibaca(): void
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }

    // Label & warna per tipe untuk tampilan view
    public function getTipeInfoAttribute(): array
    {
        return match ($this->tipe) {
            'verifikasi_lulus'  => ['label' => 'Verifikasi Lulus',   'color' => 'green',  'icon' => 'fa-check-circle'],
            'verifikasi_tolak'  => ['label' => 'Verifikasi Ditolak', 'color' => 'red',    'icon' => 'fa-times-circle'],
            'peringatan_hama'   => ['label' => 'Peringatan Hama',    'color' => 'orange', 'icon' => 'fa-bug'],
            'rekomendasi'       => ['label' => 'Rekomendasi',        'color' => 'blue',   'icon' => 'fa-lightbulb'],
            'pengumuman'        => ['label' => 'Pengumuman',         'color' => 'purple', 'icon' => 'fa-bullhorn'],
            default             => ['label' => 'Info',               'color' => 'gray',   'icon' => 'fa-info-circle'],
        };
    }
}