<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPremium extends Model
{
    protected $table = 'transaksi_premium';

    protected $fillable = [
        'user_id', 'paket', 'durasi_bulan', 'harga',
        'metode_bayar', 'status', 'bukti_bayar',
        'snap_token', 'order_id',
        'dikonfirmasi_at', 'dikonfirmasi_oleh', 'catatan_admin'
    ];

    protected $casts = [
        'dikonfirmasi_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function konfirmator()
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    public function getPaketLabelAttribute(): string
    {
        return match($this->paket) {
            '1_bulan'  => '1 Bulan',
            '3_bulan'  => '3 Bulan',
            '6_bulan'  => '6 Bulan',
            '12_bulan' => '12 Bulan',
            default    => $this->paket,
        };
    }

    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending'             => ['label' => 'Menunggu Pembayaran', 'color' => 'yellow'],
            'menunggu_konfirmasi' => ['label' => 'Bukti Dikirim',       'color' => 'blue'],
            'aktif'               => ['label' => 'Aktif',               'color' => 'green'],
            'ditolak'             => ['label' => 'Ditolak',             'color' => 'red'],
            default               => ['label' => $this->status,         'color' => 'gray'],
        };
    }
}