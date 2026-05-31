@extends('layouts.dashboard')
@section('page-title', 'Premium')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Premium PATANI</h1>
        <p class="text-gray-600">Akses penuh fitur chatbot AI untuk pertanian</p>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-600"></i>
            {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-600"></i>
            {{ session('info') }}
        </div>
    @endif

    {{-- Status Premium --}}
    @if(Auth::user()->isPremium())
        <div class="bg-gradient-to-r from-yellow-400 to-orange-400 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-crown"></i> Premium Aktif
                    </p>
                    <p class="text-sm opacity-90 mt-1">
                        Berlaku hingga: <strong>{{ \Carbon\Carbon::parse(Auth::user()->premium_until)->format('d M Y') }}</strong>
                    </p>
                    @php
                        $sisaHari = (int) now()->startOfDay()->diffInDays(
                            \Carbon\Carbon::parse(Auth::user()->premium_until)->startOfDay()
                        );
                    @endphp
                    <p class="text-sm opacity-90">Sisa: <strong>{{ $sisaHari }} hari</strong></p>
                </div>
                <i class="fas fa-crown text-6xl opacity-30"></i>
            </div>
        </div>
    @else
        <div class="bg-gray-100 rounded-xl p-5 border border-gray-300 flex items-center gap-3">
            <i class="fas fa-lock text-gray-400 text-2xl"></i>
            <div>
                <p class="font-bold text-gray-700">Belum Premium</p>
                <p class="text-sm text-gray-500">Pilih paket di bawah untuk upgrade</p>
            </div>
        </div>
    @endif

    {{-- Form Pilih Paket --}}
    @if(!Auth::user()->isPremium() && !$transaksiList->where('status', 'pending')->count())
    <div class="bg-white rounded-xl shadow border p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Pilih Paket Premium</h3>
        <p class="text-sm text-gray-500 mb-5">
            <i class="fas fa-qrcode text-green-500 mr-1"></i>
            Pembayaran via <strong>QRIS</strong> — bisa dibayar dengan GoPay, OVO, ShopeePay, DANA, dan semua e-wallet
        </p>

        <form action="{{ route('dashboard.transaksi.store') }}" method="POST" x-data="{ paket: '' }">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                {{-- 3 Bulan --}}
                <label class="cursor-pointer">
                    <input type="radio" name="paket" value="3_bulan" x-model="paket" class="hidden">
                    <div :class="paket === '3_bulan' ? 'border-yellow-400 bg-yellow-50 shadow-md' : 'border-gray-200'"
                         class="border-2 rounded-xl p-5 transition hover:border-yellow-300 relative">
                        <p class="font-bold text-gray-800 text-base">3 Bulan</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">Rp 50.000</p>
                        <p class="text-xs text-gray-500 mt-1">Rp 16.700/bulan</p>
                        <div x-show="paket === '3_bulan'" class="absolute top-3 right-3 w-5 h-5 bg-yellow-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                </label>

                {{-- 6 Bulan --}}
                <label class="cursor-pointer relative">
                    <input type="radio" name="paket" value="6_bulan" x-model="paket" class="hidden">
                    <span class="absolute -top-2 left-1/2 -translate-x-1/2 bg-green-500 text-white text-xs px-3 py-0.5 rounded-full z-10">
                        TERPOPULER
                    </span>
                    <div :class="paket === '6_bulan' ? 'border-green-500 bg-green-50 shadow-md' : 'border-gray-200'"
                         class="border-2 rounded-xl p-5 transition hover:border-green-400 relative">
                        <p class="font-bold text-gray-800 text-base">6 Bulan</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">Rp 90.000</p>
                        <p class="text-xs text-gray-500 mt-1">Rp 15.000/bulan · Hemat 10%</p>
                        <div x-show="paket === '6_bulan'" class="absolute top-3 right-3 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                </label>

                {{-- 12 Bulan --}}
                <label class="cursor-pointer">
                    <input type="radio" name="paket" value="12_bulan" x-model="paket" class="hidden">
                    <div :class="paket === '12_bulan' ? 'border-blue-400 bg-blue-50 shadow-md' : 'border-gray-200'"
                         class="border-2 rounded-xl p-5 transition hover:border-blue-300 relative">
                        <p class="font-bold text-gray-800 text-base">12 Bulan</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">Rp 159.000</p>
                        <p class="text-xs text-gray-500 mt-1">Rp 13.250/bulan · Hemat 20%</p>
                        <div x-show="paket === '12_bulan'" class="absolute top-3 right-3 w-5 h-5 bg-blue-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                </label>
            </div>

            <button type="submit"
                    x-show="paket"
                    x-transition
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
                <i class="fas fa-qrcode"></i> Lanjut Bayar via QRIS
            </button>

            <p class="text-xs text-center text-gray-400 mt-3">
                <i class="fas fa-shield-alt mr-1"></i>Transaksi aman & terenkripsi oleh Midtrans
            </p>
        </form>
    </div>
    @endif

    {{-- Ada transaksi pending --}}
    @php $pendingTrx = $transaksiList->where('status', 'pending')->first(); @endphp
    @if($pendingTrx && !Auth::user()->isPremium())
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 flex items-center justify-between gap-4">
        <div>
            <p class="font-bold text-yellow-800">Ada transaksi yang belum dibayar</p>
            <p class="text-sm text-yellow-700">Paket {{ $pendingTrx->paket_label }} — {{ $pendingTrx->harga_format }}</p>
        </div>
        <a href="{{ route('dashboard.transaksi.show', $pendingTrx->id) }}"
            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg text-sm transition whitespace-nowrap">
            Bayar Sekarang
        </a>
    </div>
    @endif

    {{-- Riwayat Transaksi --}}
    @if($transaksiList->count() > 0)
    <div class="bg-white rounded-xl shadow border p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Riwayat Transaksi</h3>
        <div class="space-y-3">
            @foreach($transaksiList as $t)
                @php $badge = $t->status_badge; @endphp
                <div class="border border-gray-200 rounded-xl p-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-crown text-yellow-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Paket {{ $t->paket_label }}</p>
                            <p class="text-sm text-gray-500">{{ $t->harga_format }} · QRIS</p>
                            <p class="text-xs text-gray-400">{{ $t->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 text-xs font-bold rounded-full
                            {{ $badge['color'] === 'green'  ? 'bg-green-100 text-green-700'   :
                               ($badge['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-700' :
                               ($badge['color'] === 'blue'   ? 'bg-blue-100 text-blue-700'     :
                               ($badge['color'] === 'red'    ? 'bg-red-100 text-red-700'       :
                               'bg-gray-100 text-gray-700'))) }}">
                            {{ $badge['label'] }}
                        </span>
                        <a href="{{ route('dashboard.transaksi.show', $t->id) }}"
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-lg text-xs font-medium transition">
                            Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection