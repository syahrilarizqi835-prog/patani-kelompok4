<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotifikasi;
use App\Models\Sawah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SawahController extends Controller
{
    // =========================================================================
    // INDEX — Daftar semua sawah dengan filter & stats
    // =========================================================================

    public function index(Request $request)
    {
        $query = Sawah::with('user')->latest();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_sawah', 'like', "%{$search}%")
                  ->orWhere('desa', 'like', "%{$search}%")
                  ->orWhere('kecamatan', 'like', "%{$search}%")
                  ->orWhere('jenis_padi', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter status sawah
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter verifikasi
        if ($request->filled('verifikasi')) {
            $query->where('verifikasi_status', $request->verifikasi);
        }

        $sawahList = $query->paginate(20)->withQueryString();

        // Stats cards
        $stats = [
            'totalSawah'        => Sawah::count(),
            'totalLuas'         => number_format(Sawah::sum('luas'), 2, ',', '.'),
            'rataLuas'          => Sawah::count() > 0
                                    ? number_format(Sawah::sum('luas') / Sawah::count(), 2, ',', '.')
                                    : '0',
            'sawahAktif'        => Sawah::where('status', 'aktif')->count(),
            'sawahPanen'        => Sawah::where('status', 'panen')->count(),
            'sawahIstirahat'    => Sawah::where('status', 'istirahat')->count(),
            'belumVerifikasi'   => Sawah::where('verifikasi_status', 'belum')->count(),
            'sudahVerifikasi'   => Sawah::where('verifikasi_status', 'lulus')->count(),
            'ditolak'           => Sawah::where('verifikasi_status', 'ditolak')->count(),
        ];

        return view('admin.sawah', compact('sawahList', 'stats'));
    }

    // =========================================================================
    // SHOW — Detail satu sawah: info, perawatan, riwayat panen
    // =========================================================================

    public function show($id)
    {
        $sawah = Sawah::with([
            'user',
            'perawatan'    => fn($q) => $q->latest()->limit(10),
            'riwayatPanen' => fn($q) => $q->latest('tanggal_panen')->limit(10),
        ])->findOrFail($id);

        // Statistik sawah ini
        $statsSawah = [
            'totalPanen'      => $sawah->riwayatPanen()->count(),
            'totalProduksi'   => number_format($sawah->riwayatPanen()->sum('hasil_panen') / 1000, 2, ',', '.'),
            'totalPendapatan' => number_format($sawah->riwayatPanen()->sum('total_pendapatan'), 0, ',', '.'),
            'totalBiaya'      => number_format($sawah->perawatan()->sum('biaya'), 0, ',', '.'),
            'rataHasilPerHa'  => $sawah->riwayatPanen()->whereNotNull('hasil_per_hektar')->avg('hasil_per_hektar'),
        ];

        // Riwayat notifikasi admin untuk sawah ini
        $notifikasiList = AdminNotifikasi::where('sawah_id', $sawah->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.sawah-detail', compact('sawah', 'statsSawah', 'notifikasiList'));
    }

    // =========================================================================
    // VERIFIKASI LULUS — Admin setujui sawah
    // =========================================================================

    public function verifikasiLulus(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        $sawah = Sawah::with('user')->findOrFail($id);

        $sawah->update([
            'verifikasi_status'  => 'lulus',
            'verifikasi_catatan' => $request->input('catatan') ?: 'Lahan telah memenuhi syarat dan diverifikasi oleh admin.',
            'verifikasi_at'      => now(),
        ]);

        // Kirim notifikasi ke petani
        AdminNotifikasi::create([
            'user_id'  => $sawah->user_id,
            'sawah_id' => $sawah->id,
            'tipe'     => 'verifikasi_lulus',
            'judul'    => 'Sawah Anda Telah Terverifikasi',
            'pesan'    => "Sawah \"{$sawah->nama_sawah}\" di {$sawah->desa}, {$sawah->kecamatan} telah berhasil diverifikasi oleh admin PATANI. "
                        . ($request->input('catatan') ? "Catatan: {$request->input('catatan')}" : 'Selamat, lahan Anda sudah terdaftar resmi!'),
        ]);

        return redirect()->back()->with('success', "Sawah \"{$sawah->nama_sawah}\" berhasil diverifikasi. Notifikasi dikirim ke {$sawah->user->name}.");
    }

    // =========================================================================
    // VERIFIKASI TOLAK — Admin tolak sawah, minta petani perbaiki
    // =========================================================================

    public function verifikasiTolak(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|min:10|max:500',
        ], [
            'catatan.required' => 'Wajib isi alasan penolakan agar petani tahu yang harus diperbaiki.',
            'catatan.min'      => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $sawah = Sawah::with('user')->findOrFail($id);

        $sawah->update([
            'verifikasi_status'  => 'ditolak',
            'verifikasi_catatan' => $request->input('catatan'),
            'verifikasi_at'      => now(),
        ]);

        // Kirim notifikasi ke petani
        AdminNotifikasi::create([
            'user_id'  => $sawah->user_id,
            'sawah_id' => $sawah->id,
            'tipe'     => 'verifikasi_tolak',
            'judul'    => 'Verifikasi Sawah Perlu Perbaikan',
            'pesan'    => "Sawah \"{$sawah->nama_sawah}\" di {$sawah->desa}, {$sawah->kecamatan} belum dapat diverifikasi. "
                        . "Alasan: {$request->input('catatan')} — Silakan perbaiki data dan hubungi admin untuk verifikasi ulang.",
        ]);

        return redirect()->back()->with('success', "Sawah ditolak. Petani {$sawah->user->name} sudah diberitahu.");
    }

    // =========================================================================
    // RESET VERIFIKASI — Kembalikan ke status "belum" untuk verifikasi ulang
    // =========================================================================

    public function verifikasiReset($id)
    {
        $sawah = Sawah::findOrFail($id);

        $sawah->update([
            'verifikasi_status'  => 'belum',
            'verifikasi_catatan' => null,
            'verifikasi_at'      => null,
        ]);

        return redirect()->back()->with('success', 'Status verifikasi sawah direset. Siap untuk diverifikasi ulang.');
    }

    // =========================================================================
    // KIRIM NOTIFIKASI — Admin kirim pesan/peringatan ke petani tertentu
    // =========================================================================

    public function kirimNotifikasi(Request $request, $id)
    {
        $request->validate([
            'tipe'  => 'required|in:peringatan_hama,rekomendasi,pengumuman',
            'judul' => 'required|string|max:200',
            'pesan' => 'required|string|min:10|max:1000',
        ], [
            'tipe.required'  => 'Pilih jenis notifikasi.',
            'judul.required' => 'Judul notifikasi wajib diisi.',
            'pesan.required' => 'Isi pesan wajib diisi.',
            'pesan.min'      => 'Pesan minimal 10 karakter.',
        ]);

        $sawah = Sawah::with('user')->findOrFail($id);

        AdminNotifikasi::create([
            'user_id'  => $sawah->user_id,
            'sawah_id' => $sawah->id,
            'tipe'     => $request->input('tipe'),
            'judul'    => $request->input('judul'),
            'pesan'    => $request->input('pesan'),
        ]);

        return redirect()->back()->with('success', "Notifikasi berhasil dikirim ke {$sawah->user->name}.");
    }

    // =========================================================================
    // KIRIM NOTIFIKASI BROADCAST — Admin kirim ke SEMUA petani sekaligus
    // =========================================================================

    public function broadcast(Request $request)
    {
        $request->validate([
            'tipe'  => 'required|in:peringatan_hama,rekomendasi,pengumuman',
            'judul' => 'required|string|max:200',
            'pesan' => 'required|string|min:10|max:1000',
        ]);

        $petaniList = User::petani()->where('status', 'aktif')->get();

        $bulk = $petaniList->map(fn($p) => [
            'user_id'    => $p->id,
            'sawah_id'   => null,
            'tipe'       => $request->input('tipe'),
            'judul'      => $request->input('judul'),
            'pesan'      => $request->input('pesan'),
            'is_read'    => false,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        // Insert sekaligus agar efisien
        AdminNotifikasi::insert($bulk);

        return redirect()->back()->with('success', "Pengumuman dikirim ke {$petaniList->count()} petani aktif.");
    }

    // =========================================================================
    // DESTROY — Hapus sawah
    // =========================================================================

    public function destroy($id)
    {
        $sawah = Sawah::findOrFail($id);
        $nama  = $sawah->nama_sawah;
        $sawah->delete();

        return redirect()->back()->with('success', "Sawah \"{$nama}\" berhasil dihapus.");
    }
}