<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\AdminNotifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    // =========================================================================
    // INDEX — Semua notifikasi milik petani yang login
    // =========================================================================

    public function index()
    {
        $notifikasi = AdminNotifikasi::where('user_id', Auth::id())
            ->with('sawah')
            ->latest()
            ->paginate(20);

        // Tandai semua sebagai sudah dibaca setelah halaman dibuka
        AdminNotifikasi::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('dashboard.notifikasi', compact('notifikasi'));
    }

    // =========================================================================
    // COUNT — Jumlah notifikasi belum dibaca (untuk badge di navbar)
    // Dipanggil via helper di layout, bukan route langsung
    // =========================================================================

    public static function countBelumDibaca(): int
    {
        if (!Auth::check() || Auth::user()->role !== 'petani') {
            return 0;
        }

        return AdminNotifikasi::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }
}