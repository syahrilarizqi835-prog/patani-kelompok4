@extends('layouts.dashboard')
@section('page-title', 'Data Sawah')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Sawah Seluruh Petani</h1>
            <p class="text-gray-500 text-sm mt-0.5">Verifikasi lahan dan kirim notifikasi ke petani</p>
        </div>
        {{-- Tombol broadcast --}}
        <button onclick="openBroadcastModal()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg shadow transition-colors">
            <i class="fas fa-bullhorn"></i> Kirim Pengumuman Massal
        </button>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
        <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
    </div>
    @endif

    {{-- =====================================================================
         STATS CARDS
         ===================================================================== --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-xs text-gray-500">Total Sawah</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['totalSawah'] }}</p>
            <p class="text-xs text-gray-400">{{ $stats['totalLuas'] }} Ha total</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-emerald-500">
            <p class="text-xs text-gray-500">Aktif</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['sawahAktif'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-400">
            <p class="text-xs text-gray-500">Belum Verifikasi</p>
            <p class="text-2xl font-bold text-orange-500">{{ $stats['belumVerifikasi'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500">Terverifikasi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['sudahVerifikasi'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-xs text-gray-500">Ditolak</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['ditolak'] }}</p>
        </div>
    </div>

    {{-- =====================================================================
         FILTER
         ===================================================================== --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.sawah') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nama sawah, pemilik, desa..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status Sawah</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    <option value="">Semua</option>
                    <option value="aktif"     {{ request('status') === 'aktif'     ? 'selected' : '' }}>Aktif</option>
                    <option value="panen"     {{ request('status') === 'panen'     ? 'selected' : '' }}>Siap Panen</option>
                    <option value="istirahat" {{ request('status') === 'istirahat' ? 'selected' : '' }}>Istirahat</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Verifikasi</label>
                <select name="verifikasi" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    <option value="">Semua</option>
                    <option value="belum"   {{ request('verifikasi') === 'belum'   ? 'selected' : '' }}>⏳ Belum</option>
                    <option value="lulus"   {{ request('verifikasi') === 'lulus'   ? 'selected' : '' }}>✅ Lulus</option>
                    <option value="ditolak" {{ request('verifikasi') === 'ditolak' ? 'selected' : '' }}>❌ Ditolak</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'verifikasi']))
            <a href="{{ route('admin.sawah') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50">
                <i class="fas fa-times mr-1"></i> Reset
            </a>
            @endif
        </form>
    </div>

    {{-- =====================================================================
         TABEL SAWAH
         ===================================================================== --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-green-50 border-b border-green-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sawah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pemilik</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Lokasi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Luas</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Verifikasi</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sawahList as $sawah)
                    @php $vBadge = $sawah->verifikasi_badge; @endphp
                    <tr class="hover:bg-gray-50 transition-colors">

                        {{-- Nama Sawah --}}
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-800">{{ $sawah->nama_sawah }}</div>
                            <div class="text-xs text-gray-400">{{ $sawah->jenis_padi }}</div>
                        </td>

                        {{-- Pemilik --}}
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-700">{{ $sawah->user->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $sawah->user->phone ?? '' }}</div>
                        </td>

                        {{-- Lokasi --}}
                        <td class="px-4 py-4 text-gray-600 text-xs">
                            {{ $sawah->desa }}, {{ $sawah->kecamatan }}
                        </td>

                        {{-- Luas --}}
                        <td class="px-4 py-4 text-right font-medium text-gray-700">
                            {{ $sawah->luas }} Ha
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-4 text-center">
                            @php
                                $statusColor = match($sawah->status) {
                                    'aktif'     => 'green',
                                    'panen'     => 'yellow',
                                    'istirahat' => 'gray',
                                    default     => 'gray',
                                };
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                {{ ucfirst($sawah->status) }}
                            </span>
                        </td>

                        {{-- Verifikasi --}}
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $vBadge['color'] }}-100 text-{{ $vBadge['color'] }}-800">
                                {{ $vBadge['label'] }}
                            </span>
                            @if($sawah->verifikasi_at)
                            <div class="text-xs text-gray-400 mt-0.5">{{ $sawah->verifikasi_at->format('d/m/Y') }}</div>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-1 flex-wrap">

                                {{-- Detail --}}
                                <a href="{{ route('admin.sawah.show', $sawah->id) }}"
                                   class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700 hover:bg-green-200">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>

                                {{-- Verifikasi Lulus --}}
                                @if($sawah->verifikasi_status !== 'lulus')
                                <button onclick="openVerifikasiModal({{ $sawah->id }}, 'lulus', '{{ addslashes($sawah->nama_sawah) }}')"
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200">
                                     Lulus
                                </button>
                                @endif

                                {{-- Verifikasi Tolak --}}
                                @if($sawah->verifikasi_status !== 'ditolak')
                                <button onclick="openVerifikasiModal({{ $sawah->id }}, 'tolak', '{{ addslashes($sawah->nama_sawah) }}')"
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-700 hover:bg-red-200">
                                     Tolak
                                </button>
                                @endif

                                {{-- Kirim Notif --}}
                                <button onclick="openNotifModal({{ $sawah->id }}, '{{ addslashes($sawah->user->name ?? '') }}')"
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-700 hover:bg-purple-200">
                                    <i class="fas fa-bell mr-1"></i> Notif
                                </button>

                                {{-- Hapus --}}
                                <form action="{{ route('admin.sawah.destroy', $sawah->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin hapus sawah {{ addslashes($sawah->nama_sawah) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600 hover:bg-red-100 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-tractor text-4xl mb-2 block"></i>
                            Belum ada data sawah ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sawahList->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $sawahList->links() }}</div>
        @endif
    </div>
</div>

{{-- =========================================================================
     MODAL VERIFIKASI
     ========================================================================= --}}
<div id="verifikasiModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 id="verifikasiModalTitle" class="text-lg font-semibold text-gray-800 mb-1">Verifikasi Sawah</h3>
        <p id="verifikasiModalDesc" class="text-sm text-gray-500 mb-4"></p>
        <form id="verifikasiForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan <span id="catatanRequired" class="text-red-500">*</span>
                    <span id="catatanOptional" class="text-gray-400 font-normal hidden">(opsional)</span>
                </label>
                <textarea id="verifikasiCatatan" name="catatan" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500"></textarea>
                <p id="verifikasiHint" class="text-xs text-gray-400 mt-1"></p>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeVerifikasiModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" id="verifikasiSubmitBtn"
                    class="px-4 py-2 text-white text-sm font-medium rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================================================
     MODAL NOTIFIKASI KE PETANI
     ========================================================================= --}}
<div id="notifModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-1">Kirim Notifikasi</h3>
        <p id="notifModalDesc" class="text-sm text-gray-500 mb-4"></p>
        <form id="notifForm" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Notifikasi</label>
                <select name="tipe" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500">
                    <option value="peringatan_hama"> Peringatan Hama</option>
                    <option value="rekomendasi"> Rekomendasi Teknis</option>
                    <option value="pengumuman"> Pengumuman</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                <input type="text" name="judul" required maxlength="200"
                    placeholder="Contoh: Waspada Serangan Wereng di Kecamatan..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
                <textarea name="pesan" rows="4" required minlength="10"
                    placeholder="Tulis pesan yang jelas dan informatif untuk petani..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500"></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeNotifModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg">
                    <i class="fas fa-paper-plane mr-1"></i> Kirim
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================================================
     MODAL BROADCAST KE SEMUA PETANI
     ========================================================================= --}}
<div id="broadcastModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-1"> Kirim Pengumuman Massal</h3>
        <p class="text-sm text-orange-600 bg-orange-50 border border-orange-200 rounded-lg p-2 mb-4">
             Pesan ini akan dikirim ke <strong>semua petani aktif</strong> di sistem.
        </p>
        <form action="{{ route('admin.sawah.broadcast') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                <select name="tipe" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500">
                    <option value="peringatan_hama"> Peringatan Hama</option>
                    <option value="rekomendasi"> Rekomendasi</option>
                    <option value="pengumuman"> Pengumuman Umum</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                <input type="text" name="judul" required maxlength="200"
                    placeholder="Judul pengumuman..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
                <textarea name="pesan" rows="4" required minlength="10"
                    placeholder="Isi pengumuman untuk seluruh petani..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500"></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeBroadcastModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg">
                    <i class="fas fa-bullhorn mr-1"></i> Kirim ke Semua
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Modal Verifikasi ─────────────────────────────────────────────────────────
function openVerifikasiModal(sawahId, tipe, namaSawah) {
    const modal = document.getElementById('verifikasiModal');
    const form  = document.getElementById('verifikasiForm');
    const title = document.getElementById('verifikasiModalTitle');
    const desc  = document.getElementById('verifikasiModalDesc');
    const btn   = document.getElementById('verifikasiSubmitBtn');
    const hint  = document.getElementById('verifikasiHint');
    const req   = document.getElementById('catatanRequired');
    const opt   = document.getElementById('catatanOptional');
    const cat   = document.getElementById('verifikasiCatatan');

    if (tipe === 'lulus') {
        form.action   = `/admin/sawah/${sawahId}/verifikasi-lulus`;
        title.textContent = '✅ Verifikasi Lulus';
        desc.textContent  = `Sawah "${namaSawah}" akan ditandai terverifikasi. Petani akan mendapat notifikasi.`;
        btn.textContent   = 'Verifikasi Lulus';
        btn.className     = 'px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg';
        hint.textContent  = 'Tulis catatan khusus jika ada, atau kosongkan untuk pesan standar.';
        req.classList.add('hidden'); opt.classList.remove('hidden');
        cat.required = false;
        cat.placeholder = 'Contoh: Lahan sudah sesuai kriteria, dokumen lengkap.';
    } else {
        form.action   = `/admin/sawah/${sawahId}/verifikasi-tolak`;
        title.textContent = '❌ Tolak Verifikasi';
        desc.textContent  = `Sawah "${namaSawah}" akan ditolak. Petani WAJIB diberitahu alasannya.`;
        btn.textContent   = 'Tolak & Kirim Notifikasi';
        btn.className     = 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg';
        hint.textContent  = 'Wajib diisi — petani perlu tahu apa yang harus diperbaiki.';
        req.classList.remove('hidden'); opt.classList.add('hidden');
        cat.required = true;
        cat.placeholder = 'Contoh: Data luas tidak sesuai, foto lahan kurang jelas, lokasi tidak valid...';
    }

    cat.value = '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeVerifikasiModal() {
    document.getElementById('verifikasiModal').classList.add('hidden');
    document.getElementById('verifikasiModal').classList.remove('flex');
}

// ── Modal Notifikasi ─────────────────────────────────────────────────────────
function openNotifModal(sawahId, namaPetani) {
    const modal = document.getElementById('notifModal');
    document.getElementById('notifForm').action = `/admin/sawah/${sawahId}/notifikasi`;
    document.getElementById('notifModalDesc').textContent = `Kirim pesan langsung ke: ${namaPetani}`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeNotifModal() {
    document.getElementById('notifModal').classList.add('hidden');
    document.getElementById('notifModal').classList.remove('flex');
}

// ── Modal Broadcast ───────────────────────────────────────────────────────────
function openBroadcastModal() {
    document.getElementById('broadcastModal').classList.remove('hidden');
    document.getElementById('broadcastModal').classList.add('flex');
}

function closeBroadcastModal() {
    document.getElementById('broadcastModal').classList.add('hidden');
    document.getElementById('broadcastModal').classList.remove('flex');
}

// Tutup semua modal jika klik di luar
['verifikasiModal','notifModal','broadcastModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden'), this.classList.remove('flex');
    });
});
</script>
@endpush