@extends('layouts.dashboard')
@section('page-title', 'Detail Sawah')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('admin.sawah') }}" class="hover:text-green-600">
                    <i class="fas fa-map mr-1"></i> Data Sawah
                </a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-700">{{ $sawah->nama_sawah }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $sawah->nama_sawah }}</h1>
            <p class="text-gray-500 text-sm mt-0.5">{{ $sawah->desa }}, {{ $sawah->kecamatan }}</p>
        </div>
        <a href="{{ route('admin.sawah') }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    {{-- =====================================================================
         INFO UTAMA + STATS
         ===================================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Info Sawah --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-green-600"></i> Informasi Sawah
            </h2>

            {{-- Foto lahan --}}
            @if($sawah->foto_lahan)
            <div class="mb-4">
                <img src="{{ asset('storage/' . $sawah->foto_lahan) }}"
                     alt="Foto Lahan"
                     class="w-full max-h-64 object-cover rounded-lg border border-gray-200">
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Pemilik</p>
                    <p class="font-medium text-gray-800">{{ $sawah->user->name ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $sawah->user->phone ?? '' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Email</p>
                    <p class="font-medium text-gray-800">{{ $sawah->user->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Lokasi</p>
                    <p class="font-medium text-gray-800">{{ $sawah->desa }}, {{ $sawah->kecamatan }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Luas</p>
                    <p class="font-medium text-gray-800">{{ $sawah->luas }} Ha</p>
                </div>
                <div>
                    <p class="text-gray-500">Jenis Padi</p>
                    <p class="font-medium text-gray-800">{{ $sawah->jenis_padi ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Status</p>
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
                </div>
                <div>
                    <p class="text-gray-500">Tanggal Tanam</p>
                    <p class="font-medium text-gray-800">
                        {{ $sawah->tanggal_tanam ? $sawah->tanggal_tanam->format('d M Y') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Estimasi Panen</p>
                    <p class="font-medium text-gray-800">
                        {{ $sawah->estimasi_panen ? $sawah->estimasi_panen->format('d M Y') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Kondisi Tanah</p>
                    <p class="font-medium text-gray-800">{{ ucfirst($sawah->kondisi_tanah ?? '-') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Kondisi Air</p>
                    <p class="font-medium text-gray-800">{{ ucfirst($sawah->kondisi_air ?? '-') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Fase Tanam</p>
                    <p class="font-medium text-gray-800">{{ ucfirst($sawah->fase_tanam ?? '-') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Umur Tanaman</p>
                    <p class="font-medium text-gray-800">{{ $sawah->umur_tanaman }} hari</p>
                </div>
            </div>

            @if($sawah->catatan)
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Catatan Petani</p>
                <p class="text-sm text-gray-700">{{ $sawah->catatan }}</p>
            </div>
            @endif
        </div>

        {{-- Verifikasi + Stats --}}
        <div class="space-y-4">

            {{-- Status Verifikasi --}}
            @php $vBadge = $sawah->verifikasi_badge; @endphp
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Status Verifikasi</h3>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-{{ $vBadge['color'] }}-100 text-{{ $vBadge['color'] }}-800">
                    {{ $vBadge['label'] }}
                </span>
                @if($sawah->verifikasi_at)
                <p class="text-xs text-gray-400 mt-2">{{ $sawah->verifikasi_at->format('d M Y, H:i') }} WIB</p>
                @endif
                @if($sawah->verifikasi_catatan)
                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Catatan Admin</p>
                    <p class="text-sm text-gray-700">{{ $sawah->verifikasi_catatan }}</p>
                </div>
                @endif

                {{-- Tombol Verifikasi --}}
                <div class="mt-4 flex flex-col gap-2">
                    @if($sawah->verifikasi_status !== 'lulus')
                    <button onclick="openVerifikasiModal({{ $sawah->id }}, 'lulus', '{{ addslashes($sawah->nama_sawah) }}')"
                        class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
                         Verifikasi Lulus
                    </button>
                    @endif
                    @if($sawah->verifikasi_status !== 'ditolak')
                    <button onclick="openVerifikasiModal({{ $sawah->id }}, 'tolak', '{{ addslashes($sawah->nama_sawah) }}')"
                        class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">
                         Tolak Verifikasi
                    </button>
                    @endif
                    @if($sawah->verifikasi_status !== 'belum')
                    <form action="{{ route('admin.sawah.verifikasi.reset', $sawah->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full px-3 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50">
                            <i class="fas fa-redo mr-1"></i> Reset Verifikasi
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Stats Ringkasan --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Ringkasan Produksi</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total Panen</span>
                        <span class="font-medium text-gray-800">{{ $statsSawah['totalPanen'] }}x</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total Produksi</span>
                        <span class="font-medium text-gray-800">{{ $statsSawah['totalProduksi'] }} ton</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total Pendapatan</span>
                        <span class="font-medium text-gray-800">Rp {{ $statsSawah['totalPendapatan'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total Biaya</span>
                        <span class="font-medium text-gray-800">Rp {{ $statsSawah['totalBiaya'] }}</span>
                    </div>
                    @if($statsSawah['rataHasilPerHa'])
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Rata Hasil/Ha</span>
                        <span class="font-medium text-gray-800">{{ number_format($statsSawah['rataHasilPerHa'], 0, ',', '.') }} kg</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Kirim Notifikasi --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Kirim Notifikasi</h3>
                <button onclick="openNotifModal({{ $sawah->id }}, '{{ addslashes($sawah->user->name ?? '') }}')"
                    class="w-full px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg">
                    <i class="fas fa-bell mr-1"></i> Kirim Pesan ke Petani
                </button>
            </div>
        </div>
    </div>

    {{-- =====================================================================
         RIWAYAT PERAWATAN
         ===================================================================== --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-clipboard-list text-green-600"></i> Riwayat Perawatan
        </h2>
        @if($sawah->perawatan->isEmpty())
        <p class="text-gray-400 text-sm text-center py-6">Belum ada data perawatan.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Jenis</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Keterangan</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase">Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sawah->perawatan as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600">
                            {{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                {{ ucfirst($p->jenis_perawatan ?? '-') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $p->keterangan ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-700">
                            Rp {{ number_format($p->biaya ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- =====================================================================
         RIWAYAT PANEN
         ===================================================================== --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-seedling text-green-600"></i> Riwayat Panen
        </h2>
        @if($sawah->riwayatPanen->isEmpty())
        <p class="text-gray-400 text-sm text-center py-6">Belum ada data panen.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal Panen</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase">Hasil (kg)</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase">Hasil/Ha</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase">Pendapatan</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase">Kualitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sawah->riwayatPanen as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600">
                            {{ \Carbon\Carbon::parse($r->tanggal_panen)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-700">
                            {{ number_format($r->hasil_panen ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">
                            {{ $r->hasil_per_hektar ? number_format($r->hasil_per_hektar, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-700">
                            Rp {{ number_format($r->total_pendapatan ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $kColor = match($r->kualitas ?? '') {
                                    'premium' => 'yellow',
                                    'baik'    => 'green',
                                    'sedang'  => 'blue',
                                    'buruk'   => 'red',
                                    default   => 'gray',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $kColor }}-100 text-{{ $kColor }}-800">
                                {{ ucfirst($r->kualitas ?? '-') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- =====================================================================
         RIWAYAT NOTIFIKASI ADMIN
         ===================================================================== --}}
    @if($notifikasiList->isNotEmpty())
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-bell text-purple-600"></i> Notifikasi Terakhir
        </h2>
        <div class="space-y-3">
            @foreach($notifikasiList as $notif)
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-medium text-gray-800 text-sm">{{ $notif->judul }}</p>
                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-600 mt-1">{{ $notif->pesan }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

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
     MODAL NOTIFIKASI
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
                    placeholder="Contoh: Waspada Serangan Wereng..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
                <textarea name="pesan" rows="4" required minlength="10"
                    placeholder="Tulis pesan untuk petani..."
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

@endsection

@push('scripts')
<script>
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
        form.action       = `/admin/sawah/${sawahId}/verifikasi-lulus`;
        title.textContent = ' Verifikasi Lulus';
        desc.textContent  = `Sawah "${namaSawah}" akan ditandai terverifikasi.`;
        btn.textContent   = 'Verifikasi Lulus';
        btn.className     = 'px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg';
        hint.textContent  = 'Kosongkan untuk pesan standar.';
        req.classList.add('hidden'); opt.classList.remove('hidden');
        cat.required = false;
        cat.placeholder = 'Contoh: Lahan sudah sesuai kriteria, dokumen lengkap.';
    } else {
        form.action       = `/admin/sawah/${sawahId}/verifikasi-tolak`;
        title.textContent = ' Tolak Verifikasi';
        desc.textContent  = `Sawah "${namaSawah}" akan ditolak. Petani wajib diberitahu alasannya.`;
        btn.textContent   = 'Tolak & Kirim Notifikasi';
        btn.className     = 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg';
        hint.textContent  = 'Wajib diisi agar petani tahu yang harus diperbaiki.';
        req.classList.remove('hidden'); opt.classList.add('hidden');
        cat.required = true;
        cat.placeholder = 'Contoh: Data luas tidak sesuai, foto lahan kurang jelas...';
    }

    cat.value = '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeVerifikasiModal() {
    document.getElementById('verifikasiModal').classList.add('hidden');
    document.getElementById('verifikasiModal').classList.remove('flex');
}

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

['verifikasiModal','notifModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden'), this.classList.remove('flex');
    });
});
</script>
@endpush