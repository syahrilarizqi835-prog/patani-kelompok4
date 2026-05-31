@extends('layouts.dashboard')
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard.transaksi') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Detail Transaksi</h1>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
        <i class="fas fa-check-circle text-green-600 mr-2"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
        <i class="fas fa-times-circle text-red-600 mr-2"></i> {{ session('error') }}
    </div>
    @endif

    @php $badge = $transaksi->status_badge; @endphp

    {{-- Info Transaksi --}}
    <div class="bg-white rounded-xl shadow border p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-lg">Paket {{ $transaksi->paket_label }}</h3>
            <span id="status-badge" class="px-3 py-1 text-xs font-bold rounded-full
                {{ $badge['color'] === 'green'  ? 'bg-green-100 text-green-700' :
                   ($badge['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-700' :
                   ($badge['color'] === 'blue'   ? 'bg-blue-100 text-blue-700' :
                   ($badge['color'] === 'red'    ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'))) }}">
                {{ $badge['label'] }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Total Bayar</p>
                <p class="font-bold text-xl text-green-600">{{ $transaksi->harga_format }}</p>
            </div>
            <div>
                <p class="text-gray-500">Metode</p>
                <p class="font-semibold">QRIS</p>
            </div>
            <div>
                <p class="text-gray-500">Durasi</p>
                <p class="font-semibold">{{ $transaksi->durasi_bulan }} Bulan</p>
            </div>
            <div>
                <p class="text-gray-500">Tanggal Order</p>
                <p class="font-semibold">{{ $transaksi->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Tombol Bayar (hanya saat pending dan ada snap_token) --}}
    @if($transaksi->status === 'pending' && $transaksi->snap_token)
    <div class="bg-white rounded-xl shadow border p-6 text-center">
        <i class="fas fa-qrcode text-6xl text-gray-300 mb-4 block"></i>
        <h3 class="font-bold text-gray-800 text-lg mb-2">Selesaikan Pembayaran</h3>
        <p class="text-sm text-gray-500 mb-4">
            Klik tombol di bawah untuk membuka halaman pembayaran QRIS.<br>
            Bisa dibayar via GoPay, OVO, ShopeePay, DANA, dan semua e-wallet.
        </p>

        <button id="pay-button"
            class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-xl transition text-lg">
            <i class="fas fa-qrcode mr-2"></i>Bayar Sekarang — {{ $transaksi->harga_format }}
        </button>

        <div id="checking-status" class="hidden mt-4">
            <div class="flex items-center justify-center gap-2 text-blue-600">
                <svg class="animate-spin w-5 h-5" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <span class="text-sm font-medium">Mengecek status pembayaran...</span>
            </div>
        </div>

        <p class="text-xs text-gray-400 mt-3">
            <i class="fas fa-shield-alt mr-1"></i>Pembayaran aman menggunakan Midtrans
        </p>
    </div>
    @endif

    {{-- Sudah aktif --}}
    @if($transaksi->status === 'aktif')
    <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center" id="success-box">
        <i class="fas fa-check-circle text-green-500 text-5xl mb-3 block"></i>
        <p class="font-bold text-green-800 text-xl">Pembayaran Berhasil!</p>
        <p class="text-sm text-green-600 mt-1">
            Paket {{ $transaksi->paket_label }} aktif sejak {{ $transaksi->dikonfirmasi_at?->format('d M Y H:i') }}
        </p>
        @if(Auth::user()->premium_until)
        <p class="text-sm text-green-700 font-semibold mt-1">
            Berlaku hingga: {{ Auth::user()->premium_until->format('d M Y') }}
        </p>
        @endif
        <a href="{{ route('dashboard.chatbot') }}"
            class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-xl transition">
            <i class="fas fa-robot mr-2"></i>Gunakan Chatbot Sekarang
        </a>
    </div>
    @endif

    {{-- Ditolak / expired --}}
    @if($transaksi->status === 'ditolak')
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
        <i class="fas fa-times-circle text-red-500 text-4xl mb-2 block"></i>
        <p class="font-bold text-red-800">Transaksi Dibatalkan / Expired</p>
        <p class="text-sm text-red-600 mt-1">Silakan buat transaksi baru.</p>
        <a href="{{ route('dashboard.transaksi') }}"
            class="inline-block mt-3 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-5 rounded-xl transition text-sm">
            Beli Paket Lagi
        </a>
    </div>
    @endif

</div>

{{-- Midtrans Snap JS — SANDBOX --}}
@if($transaksi->status === 'pending' && $transaksi->snap_token)
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ $clientKey }}"></script>

<script>
const snapToken = "{{ $transaksi->snap_token }}";
const cekUrl    = "{{ route('dashboard.transaksi.cek', $transaksi->id) }}";
const detailUrl = "{{ route('dashboard.transaksi.show', $transaksi->id) }}";
let polling     = null;

document.getElementById('pay-button').addEventListener('click', function () {
    snap.pay(snapToken, {
        onSuccess: function (result) {
            startPolling();
        },
        onPending: function (result) {
            document.getElementById('checking-status').classList.remove('hidden');
            startPolling();
        },
        onError: function (result) {
            alert('Pembayaran gagal. Silakan coba lagi.');
        },
        onClose: function () {
            startPolling();
        }
    });
});

function startPolling() {
    document.getElementById('pay-button').classList.add('hidden');
    document.getElementById('checking-status').classList.remove('hidden');

    let tries = 0;
    polling = setInterval(async function () {
        tries++;
        try {
            const res  = await fetch(cekUrl);
            const data = await res.json();
            if (data.is_aktif) {
                clearInterval(polling);
                window.location.reload();
            }
        } catch (e) {}

        if (tries >= 40) {
            clearInterval(polling);
            document.getElementById('checking-status').innerHTML =
                '<p class="text-sm text-gray-500 mt-2">Pembayaran sedang diproses. <a href="' + detailUrl + '" class="text-blue-600 underline">Refresh manual</a>.</p>';
        }
    }, 3000);
}
</script>
@endif

@endsection