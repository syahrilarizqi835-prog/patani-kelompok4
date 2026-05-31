@extends('layouts.dashboard')
@section('page-title', 'Detail Diskusi')

@section('content')
<div class="space-y-5 max-w-3xl" x-data="forumDetail()">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard.forum') }}" class="hover:text-green-600">
            <i class="fas fa-comments mr-1"></i> Forum Diskusi
        </a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-700 truncate">{{ \Str::limit($topic->title, 50) }}</span>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="flex items-center gap-2 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-2 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
        <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
    </div>
    @endif

    {{-- =====================================================================
         TOPIK UTAMA
         ===================================================================== --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex flex-wrap gap-2 mb-3">
                @if($topic->is_pinned)
                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">📌 Disematkan</span>
                @endif
                @if($topic->is_locked)
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium">🔒 Dikunci Admin</span>
                @endif
                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                    {{ ucwords(str_replace('_', ' ', $topic->category)) }}
                </span>
            </div>

            <h1 class="text-xl font-bold text-gray-800 leading-snug">{{ $topic->title }}</h1>

            <div class="flex items-center gap-4 mt-2 text-xs text-gray-400 flex-wrap">
                <span class="flex items-center gap-1.5">
                    <img src="{{ $topic->user->foto_profil_url }}"
                         alt="{{ $topic->user->name }}"
                         class="w-5 h-5 rounded-full object-cover">
                    <span class="font-medium text-gray-600">{{ $topic->user->name ?? '-' }}</span>
                </span>
                <span>{{ $topic->created_at->locale('id')->isoFormat('D MMMM Y, HH:mm') }}</span>
                <span><i class="fas fa-eye mr-1"></i>{{ number_format($topic->views) }}</span>
                <span><i class="fas fa-comment mr-1"></i>{{ $topic->replies->count() }} balasan</span>
            </div>
        </div>

        <div class="p-6">
            <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap">{{ $topic->content }}</div>
        </div>

        @if($topic->is_locked && $topic->admin_note)
        <div class="mx-6 mb-4 p-3 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-700 flex items-start gap-2">
            <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
            <div><span class="font-medium">Catatan Admin:</span> {{ $topic->admin_note }}</div>
        </div>
        @endif

        <div class="px-6 pb-5 flex items-center gap-4 border-t border-gray-100 pt-4">
            <form action="{{ route('dashboard.forum.like', $topic->id) }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors
                        {{ $sudahLike ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    <i class="fas fa-heart"></i>
                    {{ $sudahLike ? 'Tidak Suka' : 'Suka' }}
                    <span class="font-bold">{{ $topic->likes }}</span>
                </button>
            </form>
            <a href="{{ route('dashboard.forum') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- =====================================================================
         KOMENTAR
         ===================================================================== --}}
    <div id="komentar">
        <h3 class="text-base font-semibold text-gray-700 mb-3">
            {{ $topic->replies->count() }} Komentar
        </h3>

        @if($topic->replies->isEmpty())
        <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
            <i class="fas fa-comment-slash text-4xl mb-2 block"></i>
            <p class="text-sm">Belum ada komentar. Jadilah yang pertama!</p>
        </div>
        @else
        <div class="space-y-4">
            @foreach($topic->replies as $reply)
            @php
                $isAdmin = $reply->user?->role === 'admin';
                $isOwn   = $reply->user_id === auth()->id();
            @endphp

            {{-- ── Komentar Level-1 ─────────────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow overflow-hidden
                {{ $isAdmin ? 'border-l-4 border-green-500' : '' }}">
                <div class="p-5">
                    <div class="flex items-start gap-3">

                        {{-- Avatar --}}
                        <img src="{{ $reply->user?->foto_profil_url ?? 'https://ui-avatars.com/api/?name=?&background=9ca3af&color=fff&size=128' }}"
                             alt="{{ $reply->user?->name }}"
                             class="flex-shrink-0 w-9 h-9 rounded-full object-cover">

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="font-semibold text-gray-800 text-sm">{{ $reply->user?->name ?? 'Pengguna' }}</span>
                                @if($isAdmin)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                    Admin PATANI
                                </span>
                                @endif
                                @if($isOwn && !$isAdmin)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-600">Anda</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>

                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $reply->content }}</p>

                            {{-- Tombol Balas komentar ini --}}
                            @if(!$topic->is_locked)
                            <button
                                @click="toggleReplyForm({{ $reply->id }}, '{{ addslashes($reply->user?->name ?? '') }}')"
                                class="mt-2 text-xs text-green-600 hover:text-green-800 font-medium flex items-center gap-1">
                                <i class="fas fa-reply"></i>
                                <span x-text="activeReply === {{ $reply->id }} ? 'Tutup' : 'Balas'"></span>
                            </button>
                            @endif
                        </div>
                    </div>

                    {{-- Form inline balas komentar level-1 --}}
                    @if(!$topic->is_locked)
                    <div x-show="activeReply === {{ $reply->id }}" x-cloak class="mt-4 ml-12">
                        <form action="{{ route('dashboard.forum.reply', $topic->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                            <div class="flex items-start gap-2">
                                <img src="{{ auth()->user()->foto_profil_url }}"
                                     alt="{{ auth()->user()->name }}"
                                     class="flex-shrink-0 w-7 h-7 rounded-full object-cover">
                                <div class="flex-1">
                                    <p class="text-xs text-gray-400 mb-1">
                                        Membalas <span class="font-medium text-gray-600" x-text="replyingTo"></span>
                                    </p>
                                    <textarea name="content" rows="2" required minlength="3"
                                        placeholder="Tulis balasan..."
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 resize-none"></textarea>
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit"
                                            class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700">
                                            <i class="fas fa-paper-plane mr-1"></i> Kirim
                                        </button>
                                        <button type="button" @click="activeReply = null"
                                            class="px-3 py-1.5 border border-gray-300 text-gray-600 text-xs rounded-lg hover:bg-gray-50">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>

                {{-- ── Nested Replies (balasan ke komentar ini) ──────────── --}}
                @if($reply->children->isNotEmpty())
                <div class="border-t border-gray-100 bg-gray-50/60">
                    @foreach($reply->children as $child)
                    @php
                        $childIsAdmin = $child->user?->role === 'admin';
                        $childIsOwn   = $child->user_id === auth()->id();
                    @endphp
                    <div class="px-5 py-4 flex items-start gap-3 border-b border-gray-100 last:border-0
                        {{ $childIsAdmin ? 'bg-green-50/50' : '' }}">

                        {{-- Indikator nested --}}
                        <div class="flex-shrink-0 flex items-center gap-2 ml-8">
                            <div class="w-4 h-px bg-gray-300"></div>
                            <img src="{{ $child->user?->foto_profil_url ?? 'https://ui-avatars.com/api/?name=?&background=9ca3af&color=fff&size=128' }}"
                                 alt="{{ $child->user?->name }}"
                                 class="flex-shrink-0 w-7 h-7 rounded-full object-cover">
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                <span class="font-semibold text-gray-800 text-sm">{{ $child->user?->name ?? 'Pengguna' }}</span>
                                @if($childIsAdmin)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                    Admin PATANI
                                </span>
                                @endif
                                @if($childIsOwn && !$childIsAdmin)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-600">Anda</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $child->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $child->content }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            {{-- ── Akhir Komentar Level-1 ───────────────────────────────── --}}

            @endforeach
        </div>
        @endif
    </div>

    {{-- =====================================================================
         FORM KOMENTAR BARU (level-1 ke topik)
         ===================================================================== --}}
    @if($topic->is_locked)
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center text-gray-500">
        <i class="fas fa-lock text-2xl mb-2 block text-gray-400"></i>
        <p class="text-sm font-medium">Topik ini dikunci oleh admin — komentar tidak bisa ditambahkan.</p>
        @if($topic->admin_note)
        <p class="text-xs text-orange-600 mt-1">{{ $topic->admin_note }}</p>
        @endif
    </div>
    @else
    <div class="bg-white rounded-xl shadow p-6">
        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <img src="{{ auth()->user()->foto_profil_url }}"
                 alt="{{ auth()->user()->name }}"
                 class="w-7 h-7 rounded-full object-cover">
            Tulis Komentar
        </h4>
        <form action="{{ route('dashboard.forum.reply', $topic->id) }}" method="POST">
            @csrf
            <input type="hidden" name="parent_id" value="">
            <textarea name="content" rows="3" required minlength="3"
                placeholder="Tulis komentar Anda di sini..."
                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 resize-none">{{ old('content') }}</textarea>
            @error('content')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            <div class="flex justify-end mt-3">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
                    <i class="fas fa-paper-plane"></i> Kirim Komentar
                </button>
            </div>
        </form>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function forumDetail() {
    return {
        activeReply: null,
        replyingTo: '',

        toggleReplyForm(replyId, userName) {
            if (this.activeReply === replyId) {
                this.activeReply = null;
                this.replyingTo  = '';
            } else {
                this.activeReply = replyId;
                this.replyingTo  = userName;
                this.$nextTick(() => {
                    const el = document.querySelector(`[x-show="activeReply === ${replyId}"]`);
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            }
        }
    }
}
</script>
@endpush