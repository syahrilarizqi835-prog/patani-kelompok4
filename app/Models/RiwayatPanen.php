<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPanen extends Model
{
    use HasFactory;

    protected $table = 'riwayat_panen';

    protected $fillable = [
        'sawah_id',
        'tanggal_panen',
        'hasil_panen',
        'hasil_per_hektar',
        'kualitas',
        'harga_jual',
        'total_pendapatan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_panen' => 'date',
        'hasil_panen' => 'decimal:2',
        'hasil_per_hektar' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'total_pendapatan' => 'decimal:2',
    ];

    public function sawah()
    {
        return $this->belongsTo(Sawah::class);
    }

    public function scopeBySawah($query, $sawahId)
    {
        return $query->where('sawah_id', $sawahId);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('tanggal_panen', $year);
    }
}
