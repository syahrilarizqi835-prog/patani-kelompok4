<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\TransaksiPremium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    const HARGA = [
        '3_bulan'  => ['harga' => 50000,  'durasi' => 3,  'label' => '3 Bulan'],
        '6_bulan'  => ['harga' => 90000,  'durasi' => 6,  'label' => '6 Bulan'],
        '12_bulan' => ['harga' => 159000, 'durasi' => 12, 'label' => '12 Bulan'],
    ];

    private function setupMidtrans()
    {
        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;
    }

    public function index()
    {
        $transaksiList = TransaksiPremium::where('user_id', Auth::id())
            ->latest()->get();

        $transaksiAktif = TransaksiPremium::where('user_id', Auth::id())
            ->where('status', 'aktif')->latest()->first();

        return view('dashboard.transaksi', compact('transaksiList', 'transaksiAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paket' => 'required|in:3_bulan,6_bulan,12_bulan',
        ]);

        $existing = TransaksiPremium::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return redirect()->route('dashboard.transaksi.show', $existing->id)
                ->with('info', 'Anda masih memiliki transaksi yang belum dibayar.');
        }

        $paket = self::HARGA[$request->paket];
        $user  = Auth::user();

        $transaksi = TransaksiPremium::create([
            'user_id'      => $user->id,
            'paket'        => $request->paket,
            'durasi_bulan' => $paket['durasi'],
            'harga'        => $paket['harga'],
            'metode_bayar' => 'qris',
            'status'       => 'pending',
        ]);

        $this->setupMidtrans();

        $orderId = 'PATANI-' . $transaksi->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $paket['harga'],
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone ?? '08000000000',
            ],
            'item_details' => [
                [
                    'id'       => $request->paket,
                    'price'    => (int) $paket['harga'],
                    'quantity' => 1,
                    'name'     => 'Premium PATANI ' . $paket['label'],
                ],
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $transaksi->update([
                'snap_token' => $snapToken,
                'order_id'   => $orderId,
            ]);

            return redirect()->route('dashboard.transaksi.show', $transaksi->id);

        } catch (\Exception $e) {
            $transaksi->delete();
            Log::error('Midtrans error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $transaksi = TransaksiPremium::where('user_id', Auth::id())->findOrFail($id);
        $clientKey = config('midtrans.client_key');
        return view('dashboard.transaksi-detail', compact('transaksi', 'clientKey'));
    }

    /**
     * Webhook dari Midtrans
     */
    public function webhook(Request $request)
    {
        $this->setupMidtrans();

        try {
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans webhook error: ' . $e->getMessage());
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $orderId           = $notif->order_id;
        $transactionStatus = $notif->transaction_status;
        $fraudStatus       = $notif->fraud_status;

        Log::info('Midtrans webhook', compact('orderId', 'transactionStatus', 'fraudStatus'));

        $transaksi = TransaksiPremium::where('order_id', $orderId)->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        if (in_array($transactionStatus, ['settlement', 'capture'])) {
            if ($fraudStatus === 'accept' || $fraudStatus === null) {
                $this->aktifkanPremium($transaksi);
            }
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $transaksi->update(['status' => 'ditolak']);
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Cek status ke Midtrans API langsung — dipanggil via AJAX polling
     * Ini yang membuat premium langsung aktif tanpa webhook
     */
    public function cekStatus($id)
    {
        $transaksi = TransaksiPremium::where('user_id', Auth::id())->findOrFail($id);

        // Kalau sudah aktif, langsung return
        if ($transaksi->status === 'aktif') {
            return response()->json(['status' => 'aktif', 'is_aktif' => true]);
        }

        // Kalau masih pending dan punya order_id, cek langsung ke Midtrans
        if ($transaksi->status === 'pending' && $transaksi->order_id) {
            try {
                $this->setupMidtrans();

                // Cek status transaksi ke Midtrans API
                $status = \Midtrans\Transaction::status($transaksi->order_id);

                $transactionStatus = $status->transaction_status ?? null;
                $fraudStatus       = $status->fraud_status ?? null;

                Log::info('Cek status Midtrans', [
                    'order_id' => $transaksi->order_id,
                    'status'   => $transactionStatus,
                    'fraud'    => $fraudStatus,
                ]);

                if (in_array($transactionStatus, ['settlement', 'capture'])) {
                    if ($fraudStatus === 'accept' || $fraudStatus === null) {
                        $this->aktifkanPremium($transaksi);
                        return response()->json(['status' => 'aktif', 'is_aktif' => true]);
                    }
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                    $transaksi->update(['status' => 'ditolak']);
                    return response()->json(['status' => 'ditolak', 'is_aktif' => false]);
                }

            } catch (\Exception $e) {
                Log::warning('Gagal cek status Midtrans: ' . $e->getMessage());
            }
        }

        return response()->json(['status' => $transaksi->status, 'is_aktif' => false]);
    }

    private function aktifkanPremium(TransaksiPremium $transaksi)
    {
        if ($transaksi->status === 'aktif') return;

        $user  = $transaksi->user;
        $until = $user->premium_until && $user->premium_until > now()
            ? $user->premium_until->addMonths($transaksi->durasi_bulan)
            : now()->addMonths($transaksi->durasi_bulan);

        $transaksi->update([
            'status'          => 'aktif',
            'dikonfirmasi_at' => now(),
        ]);

        $user->update([
            'is_premium'    => true,
            'premium_until' => $until,
        ]);

        Log::info("Premium aktif: user {$user->id} hingga {$until}");
    }
}