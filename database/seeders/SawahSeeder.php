<?php

namespace Database\Seeders;

use App\Models\Sawah;
use App\Models\Perawatan;
use App\Models\RiwayatPanen;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SawahSeeder extends Seeder
{
    public function run(): void
    {
        $sawahData = [
            [
                'user_id' => 2, // Ahmad
                'nama_sawah' => 'Sawah Utara',
                'lokasi' => 'Blok A Desa Jatibarang',
                'desa' => 'Jatibarang',
                'kecamatan' => 'Jatibarang',
                'luas' => 2.5,
                'jenis_padi' => 'IR64',
                'tanggal_tanam' => Carbon::now()->subDays(45),
                'estimasi_panen' => Carbon::now()->addDays(75),
                'kondisi_tanah' => 'subur',
                'kondisi_air' => 'baik',
                'fase_tanam' => 'vegetatif',
                'status' => 'aktif',
            ],
            [
                'user_id' => 3, // Budi
                'nama_sawah' => 'Sawah Timur',
                'lokasi' => 'Blok B Desa Lohbener',
                'desa' => 'Lohbener',
                'kecamatan' => 'Lohbener',
                'luas' => 1.8,
                'jenis_padi' => 'Ciherang',
                'tanggal_tanam' => Carbon::now()->subDays(60),
                'estimasi_panen' => Carbon::now()->addDays(60),
                'kondisi_tanah' => 'sedang',
                'kondisi_air' => 'cukup',
                'fase_tanam' => 'generatif',
                'status' => 'aktif',
            ],
            [
                'user_id' => 4, // Siti
                'nama_sawah' => 'Sawah Selatan',
                'lokasi' => 'Blok C Desa Karangampel',
                'desa' => 'Karangampel',
                'kecamatan' => 'Karangampel',
                'luas' => 3.2,
                'jenis_padi' => 'Inpari 32',
                'tanggal_tanam' => Carbon::now()->subDays(90),
                'estimasi_panen' => Carbon::now()->addDays(30),
                'kondisi_tanah' => 'subur',
                'kondisi_air' => 'baik',
                'fase_tanam' => 'pematangan',
                'status' => 'aktif',
            ],
        ];

        foreach ($sawahData as $data) {
            $sawah = Sawah::create($data);
            
            // Add some perawatan records
            Perawatan::create([
                'sawah_id' => $sawah->id,
                'tanggal' => Carbon::now()->subDays(30),
                'jenis_perawatan' => 'pemupukan',
                'nama_kegiatan' => 'Pemupukan Urea',
                'deskripsi' => 'Pemupukan tahap pertama',
                'bahan_digunakan' => 'Urea',
                'jumlah' => 50,
                'satuan' => 'kg',
                'biaya' => 350000,
            ]);
            
            Perawatan::create([
                'sawah_id' => $sawah->id,
                'tanggal' => Carbon::now()->subDays(15),
                'jenis_perawatan' => 'penyemprotan',
                'nama_kegiatan' => 'Penyemprotan Pestisida',
                'deskripsi' => 'Pengendalian hama wereng',
                'bahan_digunakan' => 'Pestisida organik',
                'jumlah' => 2,
                'satuan' => 'liter',
                'biaya' => 200000,
            ]);
            
            // Add harvest history if applicable
            if ($sawah->user_id === 2) {
                RiwayatPanen::create([
                    'sawah_id' => $sawah->id,
                    'tanggal_panen' => Carbon::now()->subMonths(6),
                    'hasil_panen' => 15750, // kg
                    'hasil_per_hektar' => 6.3,
                    'kualitas' => 'baik',
                    'harga_jual' => 5500,
                    'total_pendapatan' => 86625000,
                    'catatan' => 'Hasil panen memuaskan',
                ]);
            }
        }
    }
}
