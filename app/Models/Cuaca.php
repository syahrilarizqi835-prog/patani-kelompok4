<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuaca extends Model
{
    protected $table = 'cuaca';

    protected $fillable = [
        'lokasi',
        'tanggal',
        'suhu',
        'kelembaban',
        'curah_hujan',
        'kecepatan_angin',
        'kondisi'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'suhu' => 'decimal:2',
        'kelembaban' => 'decimal:2',
        'curah_hujan' => 'decimal:2',
        'kecepatan_angin' => 'decimal:2',
    ];
}
