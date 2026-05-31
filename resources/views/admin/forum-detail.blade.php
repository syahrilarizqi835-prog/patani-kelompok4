@extends('layouts.dashboard')
@section('page-title', 'Detail Topik Forum')

@section('content')
<div class="space-y-6 max-w-4xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.forum') }}" class="hover:text-green-600">Forum</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-800 font-medium">{{ \Str::limit($topic->title, 50) }}</span>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    {{-- =====================================================================
         TOPIK UTAMA
         ===================================================================== --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        {{-- Header topik --}}
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    {{-- Badge status --}}
                    <div class="flex flex-wrap gap-2 mb-2">
                        @if($topic->is_pinned)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                             Disematkan
                        </span>
                        @endif
                        @if($topic->is_locked)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                             Dikunci
                        </span>
                        @endif
                        @if($topic->is_hot)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                            🔥 Hot
                        </span>
                        @endif
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            {{ ucwords(str_replace('_', ' ', $topic->category)) }}
                        </span>
                    </div>

                    <h2 class="text-xl font-bold text-gray-800">{{ $topic->title }}</h2>

                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                        <span><i class="fas fa-user mr-1"></i>{{ $topic->user->name ?? '-' }}</span>
                        <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $topic->user->desa ?? '-' }}</span>
                        <span><i class="fas fa-clock mr-1"></i>{{ $topic->created_at->diffForHumans() }}</span>
                        <span><i class="fas fa-eye mr-1"></i>{{ number_format($topic->views) }} views</span>
                        <span><i class="fas fa-comments mr-1"></i>{{ $topic->replies->count() }} balasan</span>
                    </div>
                </div>

                {{-- Tombol aksi cepat --}}
                <div class="flex flex-col gap-2 flex-shrink-0">
                    {{-- Pin --}}
                    <form action="{{ route('admin.forum.pin', $topic->id) }}" method="POST">
                        @csrf
                        <button class="w-full px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                            {{ $topic->is_pinned ? 'bg-blue-200 text-blue-800 hover:bg-blue-300' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                             {{ $topic->is_pinned ? 'Unpin' : 'Pin' }}
                        </button>
                    </form>

                    {{-- Lock --}}
                    <button onclick="openLockModal({{ $topic->id }}, {{ $topic->is_locked ? 'true' : 'false' }}, '{{ addslashes($topic->admin_note ?? '') }}')"
                        class="w-full px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                            {{ $topic->is_locked ? 'bg-red-200 text-red-800 hover:bg-red-300' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                        {{ $topic->is_locked ? ' Buka Kunci' : ' Kunci' }}
                    </button>

                  
                    </form>
                </div>
            </div>

            {{-- Catatan admin (jika ada) --}}
            @if($topic->admin_note)
            <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-700 flex items-start gap-2">
                <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
                <div>
                    <span class="font-medium">Catatan Admin:</span> {{ $topic->admin_note }}
                </div>
            </div>
            @endif
        </div>

        {{-- Isi topik --}}
        <div class="p-6">
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $topic->content }}</div>
        </div>
    </div>

    {{-- =====================================================================
         SEMUA BALASAN
         ===================================================================== --}}
    <div class="space-y-3">
        <h3 class="text-base font-semibold text-gray-700">
            {{ $topic->replies->count() }} Balasan
        </h3>

        @forelse($topic->replies->sortBy('created_at') as $reply)
        @php $isAdmin = $reply->user?->role === 'admin'; @endphp
        <div class="bg-white rounded-xl shadow p-5 {{ $isAdmin ? 'border-l-4 border-green-500 bg-green-50/30' : '' }}">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 flex-1">
                  {{-- Avatar --}}
<div class="w-9 h-9 rounded-full flex-shrink-0 overflow-hidden">
    @if($reply->user?->foto_profil)
        <img src="{{ asset('storage/' . $reply->user->foto_profil) }}"
             alt="{{ $reply->user->name }}"
             class="w-full h-full object-cover">
    @else
        <div class="w-full h-full flex items-center justify-center text-white text-sm font-bold
            {{ $isAdmin ? 'bg-green-600' : 'bg-gray-400' }}">
            {{ strtoupper(substr($reply->user?->name ?? '?', 0, 1)) }}
        </div>
    @endif
</div>

                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-medium text-gray-800 text-sm">{{ $reply->user?->name ?? 'Pengguna' }}</span>
                            @if($isAdmin)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-600 text-white">
                                 Admin PATANI
                            </span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-700 mt-1 leading-relaxed whitespace-pre-wrap">{{ $reply->content }}</p>
                    </div>
                </div>

                {{-- Hapus balasan --}}
                <form action="{{ route('admin.forum.reply.destroy', $reply->id) }}" method="POST"
                      onsubmit="return confirm('Hapus balasan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Hapus Balasan"
                        class="text-gray-300 hover:text-red-500 transition-colors text-xs">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">
            <i class="fas fa-comment-slash text-3xl mb-2 block"></i>
            Belum ada balasan untuk topik ini.
        </div>
        @endforelse
    </div>

    {{-- =====================================================================
         FORM REPLY ADMIN
         ===================================================================== --}}
    @if($topic->is_locked)
    <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-center">
        <i class="fas fa-lock text-red-400 text-2xl mb-2 block"></i>
        <p class="text-sm text-red-600 font-medium">Topik ini dikunci — balasan tidak bisa ditambahkan.</p>
        <button onclick="openLockModal({{ $topic->id }}, true, '{{ addslashes($topic->admin_note ?? '') }}')"
            class="mt-3 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
            🔓 Buka Kunci untuk Membalas
        </button>
    </div>
  @else
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            @if(auth()->user()->foto_profil)
                <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                     alt="Admin"
                     class="w-6 h-6 rounded-full object-cover flex-shrink-0">
            @else
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-600 text-white text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
            @endif
            Balas sebagai Admin PATANI
        </h4>
        <form action="{{ route('admin.forum.reply', $topic->id) }}" method="POST">
            @csrf
            <textarea name="content" rows="4" required
                placeholder="Tulis balasan resmi sebagai Admin PATANI..."
                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none">{{ old('content') }}</textarea>
            @error('content')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            <div class="flex justify-end mt-3">
                <button type="submit"
                    class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Kirim Balasan
                </button>
            </div>
        </form>
    </div>
    @endif

</div>

{{-- Modal Lock --}}
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
                    placeholder="Contoh: Topik ini dikunci karena konten tidak sesuai..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500"></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeLockModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" id="lockSubmitBtn"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">Kunci</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openLockModal(topicId, isLocked, adminNote) {
    const modal   = document.getElementById('lockModal');
    const form    = document.getElementById('lockForm');
    const title   = document.getElementById('lockModalTitle');
    const catatan = document.getElementById('lockCatatan');
    const btn     = document.getElementById('lockSubmitBtn');

    form.action = `/admin/forum/${topicId}/lock`;

    if (isLocked) {
        title.textContent = ' Buka Kunci Topik';
        btn.textContent   = 'Buka Kunci';
        btn.className     = 'px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg';
    } else {
        title.textContent = ' Kunci Topik';
        btn.textContent   = 'Kunci Topik';
        btn.className     = 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg';
    }

    catatan.value = adminNote || '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeLockModal() {
    document.getElementById('lockModal').classList.add('hidden');
    document.getElementById('lockModal').classList.remove('flex');
}

document.getElementById('lockModal').addEventListener('click', function(e) {
    if (e.target === this) closeLockModal();
});
</script>
@endpush