<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Sawah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sawah';

    protected $fillable = [
        'user_id',
        'nama_sawah',
        'lokasi',
        'desa',
        'kecamatan',
        'luas',
        'jenis_padi',
        'tanggal_tanam',
        'estimasi_panen',
        'kondisi_tanah',
        'kondisi_air',
        'fase_tanam',
        'status',
        'catatan',
        'foto_lahan',
        'verifikasi_status',   // ← baru: belum | lulus | ditolak
        'verifikasi_catatan',  // ← baru: catatan admin soal verifikasi
        'verifikasi_at',       // ← baru: kapan diverifikasi
    ];

    protected $casts = [
        'tanggal_tanam'   => 'date',
        'estimasi_panen'  => 'date',
        'verifikasi_at'   => 'datetime',
        'luas'            => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function perawatan()
    {
        return $this->hasMany(Perawatan::class);
    }

    public function riwayatPanen()
    {
        return $this->hasMany(RiwayatPanen::class);
    }

    public function prediksiPanen()
    {
        return $this->hasMany(PrediksiPanen::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getUmurTanamanAttribute()
    {
        if (!$this->tanggal_tanam) return 0;
        return Carbon::parse($this->tanggal_tanam)->diffInDays(now());
    }

    public function getHariSampaiPanenAttribute()
    {
        if (!$this->estimasi_panen) return null;
        $days = now()->diffInDays(Carbon::parse($this->estimasi_panen), false);
        return $days > 0 ? $days : 0;
    }

    public function getStatusBadgeAttribute()
    {
        return ['aktif' => 'success', 'panen' => 'warning', 'istirahat' => 'secondary'][$this->status] ?? 'secondary';
    }

    public function getVerifikasiBadgeAttribute(): array
    {
        return match ($this->verifikasi_status) {
            'lulus'   => ['label' => 'Terverifikasi', 'color' => 'green'],
            'ditolak' => ['label' => 'Ditolak',       'color' => 'red'],
            default   => ['label' => 'Belum Diverifikasi', 'color' => 'gray'],
        };
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBelumVerifikasi($query)
    {
        return $query->where('verifikasi_status', 'belum');
    }

    public function scopeVerifikasiLulus($query)
    {
        return $query->where('verifikasi_status', 'lulus');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getTotalBiayaPerawatan()
    {
        return $this->perawatan()->sum('biaya');
    }

    public function getLastHarvestResult()
    {
        return $this->riwayatPanen()->latest('tanggal_panen')->first();
    }
}