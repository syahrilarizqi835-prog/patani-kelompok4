<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransaksiPremium;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransaksiAdminController extends Controller
{
    public function index()
    {
        $transaksiList = TransaksiPremium::with('user')
            ->latest()->paginate(20);

        $stats = [
            'pending'             => TransaksiPremium::where('status', 'pending')->count(),
            'menunggu_konfirmasi' => TransaksiPremium::where('status', 'menunggu_konfirmasi')->count(),
            'aktif'               => TransaksiPremium::where('status', 'aktif')->count(),
        ];

        return view('admin.transaksi', compact('transaksiList', 'stats'));
    }

    public function konfirmasi(Request $request, $id)
    {
        $transaksi = TransaksiPremium::findOrFail($id);

        $transaksi->update([
            'status'            => 'aktif',
            'dikonfirmasi_at'   => now(),
            'dikonfirmasi_oleh' => Auth::id(),
            'catatan_admin'     => $request->catatan,
        ]);

        // Aktifkan premium user
        $user  = $transaksi->user;
        $until = $user->premium_until && $user->premium_until > now()
            ? $user->premium_until->addMonths($transaksi->durasi_bulan)
            : now()->addMonths($transaksi->durasi_bulan);

        $user->update([
            'is_premium'    => true,
            'premium_until' => $until,
        ]);

        return redirect()->back()->with('success', "Premium {$transaksi->paket_label} berhasil diaktifkan untuk {$user->name}.");
    }

    public function tolak(Request $request, $id)
    {
        $transaksi = TransaksiPremium::findOrFail($id);

        $transaksi->update([
            'status'        => 'ditolak',
            'catatan_admin' => $request->catatan ?? 'Bukti pembayaran tidak valid.',
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil ditolak.');
    }

    public function hapus($id)
    {
        $transaksi = TransaksiPremium::findOrFail($id);

        // Hapus file bukti bayar jika ada
        if ($transaksi->bukti_bayar) {
            Storage::disk('public')->delete($transaksi->bukti_bayar);
        }

        // Jika transaksi aktif, reset status premium user
        if ($transaksi->status === 'aktif') {
            $transaksi->user->update([
                'is_premium'    => false,
                'premium_until' => null,
            ]);
        }

        $transaksi->delete();

        return redirect()->back()->with('success', 'Data transaksi berhasil dihapus.');
    }
}