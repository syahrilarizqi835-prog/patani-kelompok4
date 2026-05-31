@extends('layouts.dashboard')
@section('page-title', 'Kelola Forum Diskusi')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Forum Diskusi</h1>
            <p class="text-gray-500 text-sm mt-0.5">Moderasi topik, pin, kunci, dan balas sebagai admin</p>
        </div>
    </div>

    {{-- Alert sukses / error --}}
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

    {{-- STATS CARDS (tanpa Hot) --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-xs text-gray-500">Total Topik</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500"> Disematkan</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['pinned'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-xs text-gray-500"> Dikunci</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['locked'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <p class="text-xs text-gray-500">Minggu Ini</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['mingguIni'] }}</p>
        </div>
    </div>

    {{-- FILTER & PENCARIAN --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.forum') }}" class="flex flex-wrap gap-3 items-end">

            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari Topik</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Judul atau isi topik..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                <select name="kategori" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    <option value="">Semua</option>
                    <option value="hama_penyakit"   {{ request('kategori') === 'hama_penyakit'   ? 'selected' : '' }}>Hama & Penyakit</option>
                    <option value="varietas_padi"   {{ request('kategori') === 'varietas_padi'   ? 'selected' : '' }}>Varietas Padi</option>
                    <option value="teknik_budidaya" {{ request('kategori') === 'teknik_budidaya' ? 'selected' : '' }}>Teknik Budidaya</option>
                    <option value="pemupukan"       {{ request('kategori') === 'pemupukan'       ? 'selected' : '' }}>Pemupukan</option>
                    <option value="pengairan"       {{ request('kategori') === 'pengairan'       ? 'selected' : '' }}>Pengairan</option>
                    <option value="umum"            {{ request('kategori') === 'umum'            ? 'selected' : '' }}>Umum</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    <option value="">Semua</option>
                    <option value="pinned" {{ request('status') === 'pinned' ? 'selected' : '' }}> Disematkan</option>
                    <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}> Dikunci</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
                <i class="fas fa-search mr-1"></i> Filter
            </button>

            @if(request()->hasAny(['search', 'kategori', 'status']))
            <a href="{{ route('admin.forum') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50">
                <i class="fas fa-times mr-1"></i> Reset
            </a>
            @endif
        </form>
    </div>

    {{-- TABEL TOPIK --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-green-50 border-b border-green-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Topik</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Penulis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Reply</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Views</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topics as $topic)
                    <tr class="hover:bg-gray-50 transition-colors {{ $topic->is_pinned ? 'bg-blue-50/40' : '' }} {{ $topic->is_locked ? 'opacity-75' : '' }}">

                        {{-- Judul --}}
                        <td class="px-4 py-4 max-w-xs">
                            <div class="flex items-start gap-2">
                                <div class="flex-shrink-0 flex gap-1 mt-0.5">
                                    @if($topic->is_pinned)
                                        <span title="Disematkan" class="text-blue-500 text-xs"></span>
                                    @endif
                                    @if($topic->is_locked)
                                        <span title="Dikunci" class="text-red-500 text-xs"></span>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('admin.forum.show', $topic->id) }}"
                                       class="font-medium text-gray-800 hover:text-green-700 line-clamp-2 leading-snug">
                                        {{ $topic->title }}
                                    </a>
                                    @if($topic->admin_note)
                                    <p class="text-xs text-orange-500 mt-0.5 italic">
                                        📋 {{ \Str::limit($topic->admin_note, 60) }}
                                    </p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $topic->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Penulis --}}
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-700">{{ $topic->user->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $topic->user->desa ?? '' }}</div>
                        </td>

                        {{-- Kategori --}}
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                {{ ucwords(str_replace('_', ' ', $topic->category)) }}
                            </span>
                        </td>

                        {{-- Reply & Views --}}
                        <td class="px-4 py-4 text-center text-gray-600">{{ $topic->replies_count }}</td>
                        <td class="px-4 py-4 text-center text-gray-600">{{ number_format($topic->views) }}</td>

                        {{-- Status Badges (tanpa Hot) --}}
                        <td class="px-4 py-4 text-center">
                            <div class="flex flex-col gap-1 items-center">
                                @if($topic->is_pinned)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">Pin</span>
                                @endif
                                @if($topic->is_locked)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium">Kunci</span>
                                @endif
                                @if(!$topic->is_pinned && !$topic->is_locked)
                                <span class="text-xs text-gray-400">Normal</span>
                                @endif
                            </div>
                        </td>

                        {{-- Aksi (tanpa tombol Hot) --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-1 flex-wrap">

                                {{-- Lihat Detail --}}
                                <a href="{{ route('admin.forum.show', $topic->id) }}"
                                   title="Lihat Detail & Balas"
                                   class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700 hover:bg-green-200 transition-colors">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>

                                {{-- Toggle Pin --}}
                                <form action="{{ route('admin.forum.pin', $topic->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        title="{{ $topic->is_pinned ? 'Lepas Sematan' : 'Sematkan' }}"
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium transition-colors
                                            {{ $topic->is_pinned ? 'bg-blue-200 text-blue-800 hover:bg-blue-300' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                                         {{ $topic->is_pinned ? 'Unpin' : 'Pin' }}
                                    </button>
                                </form>

                                {{-- Toggle Lock --}}
                                <button onclick="openLockModal({{ $topic->id }}, {{ $topic->is_locked ? 'true' : 'false' }}, '{{ addslashes($topic->admin_note ?? '') }}')"
                                    title="{{ $topic->is_locked ? 'Buka Kunci' : 'Kunci Topik' }}"
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium transition-colors
                                        {{ $topic->is_locked ? 'bg-red-200 text-red-800 hover:bg-red-300' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    {{ $topic->is_locked ? ' Buka' : ' Kunci' }}
                                </button>

                                {{-- Hapus --}}
                                <form action="{{ route('admin.forum.destroy', $topic->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin hapus topik ini beserta semua balasannya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600 hover:bg-red-100 hover:text-red-700 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-comments text-4xl mb-2 block"></i>
                            Belum ada topik ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($topics->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $topics->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL LOCK --}}
<div id="lockModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 id="lockModalTitle" class="text-lg font-semibold text-gray-800 mb-4">Kunci Topik</h3>

        <form id="lockForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan Admin <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="lockCatatan" name="catatan" rows="3"
                    placeholder="Contoh: Topik ini dikunci karena mengandung konten tidak sesuai..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                <p class="text-xs text-gray-400 mt-1">Catatan akan ditampilkan di bawah judul topik.</p>
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeLockModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" id="lockSubmitBtn"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">
                    Kunci Topik
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openLockModal(topicId, isLocked, adminNote) {
    const modal    = document.getElementById('lockModal');
    const form     = document.getElementById('lockForm');
    const title    = document.getElementById('lockModalTitle');
    const catatan  = document.getElementById('lockCatatan');
    const btn      = document.getElementById('lockSubmitBtn');

    form.action = `/admin/forum/${topicId}/lock`;

    if (isLocked) {
        title.textContent = ' Buka Kunci Topik';
        btn.textContent   = 'Buka Kunci';
        btn.className     = 'px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg';
    } else {
        title.textContent = 'Kunci Topik';
        btn.textContent   = 'Kunci Topik';
        btn.className     = 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg';
    }

    catatan.value = adminNote || '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeLockModal() {
    const modal = document.getElementById('lockModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('lockModal').addEventListener('click', function(e) {
    if (e.target === this) closeLockModal();
});
</script>
@endpush