<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrediksiPanen extends Model
{
    use HasFactory;

    protected $table = 'prediksi_panen';

    protected $fillable = [
        'sawah_id',
        'tanggal_prediksi',
        'prediksi_hasil',
        'confidence_level',
        'faktor_prediksi',
        'rekomendasi',
    ];

    protected $casts = [
        'tanggal_prediksi' => 'date',
        'prediksi_hasil' => 'decimal:2',
        'confidence_level' => 'decimal:2',
        'faktor_prediksi' => 'array',
    ];

    public function sawah()
    {
        return $this->belongsTo(Sawah::class);
    }
}
