<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perawatan extends Model
{
    use HasFactory;

    protected $table = 'perawatan';

    protected $fillable = [
        'sawah_id',
        'tanggal',
        'jenis_perawatan',
        'nama_kegiatan',
        'deskripsi',
        'bahan_digunakan',
        'jumlah',
        'satuan',
        'biaya',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'biaya' => 'decimal:2',
    ];

    public function sawah()
    {
        return $this->belongsTo(Sawah::class);
    }

    public function scopeBySawah($query, $sawahId)
    {
        return $query->where('sawah_id', $sawahId);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_perawatan', $jenis);
    }
}
