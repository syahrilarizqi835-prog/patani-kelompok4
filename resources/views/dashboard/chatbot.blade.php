@extends('layouts.dashboard')
@section('page-title', 'Chatbot AI')

@section('content')
<div class="space-y-4" x-data="{
    messages: [],
    input: '',
    loading: false,
    isPremium: {{ $isPremium ? 'true' : 'false' }},
    sisaPesan: {{ $sisaPesan ?? 'null' }},

    async kirim() {
        if (!this.input.trim() || this.loading) return;

        const pesan = this.input.trim();
        this.input = '';
        this.messages.push({ role: 'user', text: pesan });
        this.loading = true;
        this.scrollBawah();

        try {
            const resp = await fetch('{{ route('dashboard.chatbot.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: pesan })
            });

            const data = await resp.json();

            if (resp.status === 403 && data.error === 'limit') {
                this.messages.push({ role: 'limit', text: data.message });
                this.sisaPesan = 0;
            } else if (!resp.ok) {
                this.messages.push({ role: 'error', text: data.message ?? 'Terjadi kesalahan.' });
            } else {
                this.messages.push({ role: 'ai', text: data.response });
                if (data.sisaPesan !== null) this.sisaPesan = data.sisaPesan;
            }
        } catch (e) {
            this.messages.push({ role: 'error', text: 'Gagal terhubung ke server.' });
        }

        this.loading = false;
        this.scrollBawah();
    },

    scrollBawah() {
        this.$nextTick(() => {
            const el = document.getElementById('chatbox');
            if (el) el.scrollTop = el.scrollHeight;
        });
    },

    tanyaCepat(q) {
        this.input = q;
        this.kirim();
    }
}">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Chatbot AI Pertanian</h1>
            <p class="text-gray-600">Konsultasi pertanian 24/7 dengan asisten AI berbasis Groq LLaMA</p>
        </div>

        {{-- Badge Status --}}
        @if($isPremium)
        <div class="flex items-center gap-2 bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-2 rounded-full font-semibold text-sm">
            <i class="fas fa-crown text-yellow-500"></i>
            Premium Aktif
            @if(Auth::user()->premium_until)
            <span class="text-xs font-normal">s/d {{ \Carbon\Carbon::parse(Auth::user()->premium_until)->format('d M Y') }}</span>
            @endif
        </div>
        @else
        <div class="flex items-center gap-2 bg-gray-100 border border-gray-300 text-gray-600 px-4 py-2 rounded-full text-sm">
            <i class="fas fa-comment"></i>
            Sisa pesan hari ini:
            <span class="font-bold" :class="sisaPesan <= 1 ? 'text-red-600' : 'text-green-600'"
                x-text="sisaPesan + '/{{ \App\Http\Controllers\Petani\ChatbotController::BATAS_GRATIS }}'"></span>
        </div>
        @endif
    </div>

    {{-- Banner Premium (hanya user gratis) --}}
    @if(!$isPremium)
    <div class="bg-gradient-to-r from-yellow-400 to-orange-400 rounded-xl p-4 text-white flex items-center justify-between">
        <div>
            <p class="font-bold text-lg">Upgrade ke Premium</p>
            <p class="text-sm opacity-90">Chat unlimited • Analisis foto lahan • Laporan bulanan AI • Prioritas respons</p>
        </div>
        <button onclick="document.getElementById('modalPremium').classList.remove('hidden')"
            class="bg-white text-orange-500 font-bold px-4 py-2 rounded-lg hover:bg-orange-50 text-sm whitespace-nowrap ml-4">
            Lihat Paket
        </button>
    </div>
    @endif

    {{-- Chat Area --}}
    <div class="bg-white rounded-xl shadow border border-gray-200 flex flex-col" style="height: 520px;">

        {{-- Header Chat --}}
        <div class="flex items-center gap-3 p-4 border-b bg-gray-50 rounded-t-xl">
            <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                <i class="fas fa-robot text-white"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">PATANI Assistant</p>
                <p class="text-xs text-green-600 flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full inline-block"></span>
                    Online • Powered by Groq LLaMA AI
                </p>
            </div>
            @if($isPremium)
            <span class="ml-auto bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-1 rounded-full">
                <i class="fas fa-crown mr-1"></i>PREMIUM
            </span>
            @endif
        </div>

        {{-- Messages --}}
        <div id="chatbox" class="flex-1 overflow-y-auto p-4 space-y-4">

            {{-- Pesan sambutan --}}
            <div class="flex gap-3">
                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-robot text-white text-xs"></i>
                </div>
                <div class="bg-gray-100 rounded-2xl rounded-tl-none px-4 py-3 max-w-md">
                    <p class="text-gray-800 text-sm">Halo <strong>{{ Auth::user()->name }}</strong>! Saya PATANI Assistant siap membantu konsultasi pertanian Anda.</p>
                    @if($sawahList->count() > 0)
                    <p class="text-gray-600 text-xs mt-2">Saya sudah mengetahui data sawah Anda: <strong>{{ $sawahList->pluck('nama_sawah')->join(', ') }}</strong>. Tanyakan apa saja!</p>
                    @endif
                </div>
            </div>

            {{-- Riwayat percakapan dari DB --}}
            @foreach($riwayat as $chat)
            <div class="flex gap-3 justify-end">
                <div class="bg-green-600 text-white rounded-2xl rounded-tr-none px-4 py-3 max-w-md">
                    <p class="text-sm">{{ $chat->message }}</p>
                </div>
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 text-green-700 font-bold text-xs">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
            <div class="flex gap-3">
                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-robot text-white text-xs"></i>
                </div>
                <div class="bg-gray-100 rounded-2xl rounded-tl-none px-4 py-3 max-w-lg">
                    <p class="text-gray-800 text-sm whitespace-pre-line">{{ $chat->response }}</p>
                </div>
            </div>
            @endforeach

            {{-- Pesan baru (Alpine) --}}
            <template x-for="(msg, i) in messages" :key="i">
                <div>
                    {{-- User --}}
                    <div x-show="msg.role === 'user'" class="flex gap-3 justify-end mb-4">
                        <div class="bg-green-600 text-white rounded-2xl rounded-tr-none px-4 py-3 max-w-md">
                            <p class="text-sm" x-text="msg.text"></p>
                        </div>
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 text-green-700 font-bold text-xs">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                    {{-- AI --}}
                    <div x-show="msg.role === 'ai'" class="flex gap-3 mb-4">
                        <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-robot text-white text-xs"></i>
                        </div>
                        <div class="bg-gray-100 rounded-2xl rounded-tl-none px-4 py-3 max-w-lg">
                            <p class="text-gray-800 text-sm whitespace-pre-line" x-text="msg.text"></p>
                        </div>
                    </div>
                    {{-- Limit --}}
                    <div x-show="msg.role === 'limit'" class="flex gap-3 mb-4">
                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-lock text-white text-xs"></i>
                        </div>
                        <div class="bg-orange-50 border border-orange-200 rounded-2xl rounded-tl-none px-4 py-3 max-w-md">
                            <p class="text-orange-800 text-sm font-semibold">Batas Pesan Tercapai</p>
                            <p class="text-orange-700 text-xs mt-1" x-text="msg.text"></p>
                            <button onclick="document.getElementById('modalPremium').classList.remove('hidden')"
                                class="mt-2 bg-orange-500 text-white text-xs px-3 py-1 rounded-lg hover:bg-orange-600">
                                <i class="fas fa-crown mr-1"></i>Upgrade Premium
                            </button>
                        </div>
                    </div>
                    {{-- Error --}}
                    <div x-show="msg.role === 'error'" class="flex gap-3 mb-4">
                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation text-white text-xs"></i>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-2xl rounded-tl-none px-4 py-3 max-w-md">
                            <p class="text-red-700 text-sm" x-text="msg.text"></p>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Loading --}}
            <div x-show="loading" class="flex gap-3">
                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-robot text-white text-xs"></i>
                </div>
                <div class="bg-gray-100 rounded-2xl rounded-tl-none px-4 py-3">
                    <div class="flex gap-1 items-center">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:150ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:300ms"></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Input --}}
        <div class="p-4 border-t bg-gray-50 rounded-b-xl">
            <div class="flex gap-2">
                <input
                    x-model="input"
                    @keydown.enter.prevent="kirim()"
                    :disabled="loading || sisaPesan === 0"
                    type="text"
                    placeholder="Ketik pertanyaan Anda..."
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                <button
                    @click="kirim()"
                    :disabled="loading || !input.trim() || sisaPesan === 0"
                    class="bg-green-600 text-white px-4 py-3 rounded-xl hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Pertanyaan Cepat --}}
    <div class="bg-white rounded-xl shadow border border-gray-200 p-4">
        <p class="text-sm font-semibold text-gray-700 mb-3">Pertanyaan Cepat:</p>
        <div class="flex flex-wrap gap-2">
            @php
            $quickQ = [
                'Cara mengatasi wereng coklat pada padi',
                'Waktu pemupukan urea yang tepat',
                'Kebutuhan air padi fase generatif',
                'Gejala penyakit blast dan cara mengatasinya',
                'Dosis pupuk NPK per hektar',
                'Tanda padi siap panen',
            ];
            @endphp
            @foreach($quickQ as $q)
            <button @click="tanyaCepat('{{ $q }}')"
                :disabled="sisaPesan === 0"
                class="text-xs bg-gray-100 hover:bg-green-50 hover:text-green-700 hover:border-green-300 border border-gray-200 text-gray-600 px-3 py-2 rounded-full transition disabled:opacity-40 disabled:cursor-not-allowed">
                {{ $q }}
            </button>
            @endforeach

            {{-- Pertanyaan khusus premium --}}
            @if(!$isPremium)
            <button onclick="document.getElementById('modalPremium').classList.remove('hidden')"
                class="text-xs bg-yellow-50 border border-yellow-300 text-yellow-700 px-3 py-2 rounded-full flex items-center gap-1 hover:bg-yellow-100">
                <i class="fas fa-crown text-yellow-500"></i> Analisis foto lahan
            </button>
            <button onclick="document.getElementById('modalPremium').classList.remove('hidden')"
                class="text-xs bg-yellow-50 border border-yellow-300 text-yellow-700 px-3 py-2 rounded-full flex items-center gap-1 hover:bg-yellow-100">
                <i class="fas fa-crown text-yellow-500"></i> Laporan bulanan AI
            </button>
            @endif
        </div>
    </div>

    {{-- Modal Premium --}}
    <div id="modalPremium" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="fixed inset-0 bg-black opacity-50" onclick="document.getElementById('modalPremium').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-4 z-10">

            <button onclick="document.getElementById('modalPremium').classList.add('hidden')"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>

            <div class="text-center mb-3">
                <h3 class="text-lg font-bold text-gray-800">PATANI Premium</h3>
                <p class="text-gray-500 text-xs mt-0.5">Akses penuh semua fitur AI pertanian</p>
            </div>

            {{-- Perbandingan --}}
            <div class="grid grid-cols-2 gap-3 mb-3">
                {{-- Gratis --}}
                <div class="border border-gray-200 rounded-xl p-3">
                    <p class="font-bold text-gray-700 mb-2 text-center text-sm">Gratis</p>
                    <ul class="space-y-1 text-xs">
                        <li class="flex items-center gap-1.5 text-gray-600"><i class="fas fa-check text-green-500"></i> 5 pesan/hari</li>
                        <li class="flex items-center gap-1.5 text-gray-600"><i class="fas fa-check text-green-500"></i> Pertanyaan umum</li>
                        <li class="flex items-center gap-1.5 text-red-400"><i class="fas fa-times"></i> Analisis foto lahan</li>
                        <li class="flex items-center gap-1.5 text-red-400"><i class="fas fa-times"></i> Laporan bulanan AI</li>
                        <li class="flex items-center gap-1.5 text-red-400"><i class="fas fa-times"></i> Diagnosa hama dari foto</li>
                        <li class="flex items-center gap-1.5 text-red-400"><i class="fas fa-times"></i> Riwayat chat unlimited</li>
                    </ul>
                </div>
                {{-- Premium --}}
                <div class="border-2 border-yellow-400 rounded-xl p-3 bg-yellow-50">
                    <p class="font-bold text-yellow-700 mb-2 text-center flex items-center justify-center gap-1 text-sm">
                        <i class="fas fa-crown"></i> Premium
                    </p>
                    <ul class="space-y-1 text-xs">
                        <li class="flex items-center gap-1.5 text-gray-700"><i class="fas fa-check text-green-500"></i> Chat <strong>unlimited</strong></li>
                        <li class="flex items-center gap-1.5 text-gray-700"><i class="fas fa-check text-green-500"></i> Pertanyaan mendalam</li>
                        <li class="flex items-center gap-1.5 text-gray-700"><i class="fas fa-check text-green-500"></i> Analisis foto lahan</li>
                        <li class="flex items-center gap-1.5 text-gray-700"><i class="fas fa-check text-green-500"></i> Laporan bulanan AI</li>
                        <li class="flex items-center gap-1.5 text-gray-700"><i class="fas fa-check text-green-500"></i> Diagnosa hama dari foto</li>
                        <li class="flex items-center gap-1.5 text-gray-700"><i class="fas fa-check text-green-500"></i> Riwayat chat unlimited</li>
                    </ul>
                </div>
            </div>

            {{-- Harga --}}
            <div class="space-y-2 mb-3">
                <button class="w-full border-2 border-yellow-400 hover:bg-yellow-50 rounded-xl p-3 text-left transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-bold text-gray-800 text-sm">1 Bulan</p>
                            <p class="text-xs text-gray-500">Akses penuh 30 hari</p>
                        </div>
                        <p class="text-base font-bold text-yellow-600">Rp 29.000</p>
                    </div>
                </button>
                <button class="w-full border-2 border-green-500 bg-green-50 hover:bg-green-100 rounded-xl p-3 text-left transition relative">
                    <span class="absolute -top-2 right-4 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">TERPOPULER</span>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-bold text-gray-800 text-sm">3 Bulan</p>
                            <p class="text-xs text-gray-500">Hemat 33% — Rp 19.000/bln</p>
                        </div>
                        <p class="text-base font-bold text-green-600">Rp 59.000</p>
                    </div>
                </button>
                <button class="w-full border-2 border-gray-300 hover:bg-gray-50 rounded-xl p-3 text-left transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-bold text-gray-800 text-sm">12 Bulan</p>
                            <p class="text-xs text-gray-500">Hemat 54% — Rp 13.000/bln</p>
                        </div>
                        <p class="text-base font-bold text-gray-700">Rp 159.000</p>
                    </div>
                </button>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-2.5 text-center mb-3">
                <p class="text-xs text-blue-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    Hubungi admin untuk aktivasi premium via WhatsApp atau transfer bank.
                </p>
            </div>

            <a href="{{ route('dashboard.transaksi') }}"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-xl flex items-center justify-center gap-2 transition text-sm">
                Berlangganan Premium Sekarang
            </a>
        </div>
    </div>

</div>
@endsection