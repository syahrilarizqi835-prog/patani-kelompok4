@extends('layouts.dashboard')
@section('page-title', 'Forum Diskusi')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Forum Diskusi</h1>
            <p class="text-gray-500 text-sm">Berbagi pengalaman dan tanya jawab sesama petani</p>
        </div>
        <button @click="showModal = true"
            class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors shadow">
            <i class="fas fa-plus"></i> Buat Topik
        </button>
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

    {{-- Filter & Search --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari topik..."
                class="flex-1 min-w-40 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
            <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                <option value="">Semua Kategori</option>
                <option value="hama_penyakit"   {{ request('category') === 'hama_penyakit'   ? 'selected' : '' }}>Hama & Penyakit</option>
                <option value="varietas_padi"   {{ request('category') === 'varietas_padi'   ? 'selected' : '' }}>Varietas Padi</option>
                <option value="teknik_budidaya" {{ request('category') === 'teknik_budidaya' ? 'selected' : '' }}>Teknik Budidaya</option>
                <option value="pemupukan"       {{ request('category') === 'pemupukan'       ? 'selected' : '' }}>Pemupukan</option>
                <option value="pengairan"       {{ request('category') === 'pengairan'       ? 'selected' : '' }}>Pengairan</option>
                <option value="umum"            {{ request('category') === 'umum'            ? 'selected' : '' }}>Umum</option>
            </select>
            <button type="submit" class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                <i class="fas fa-search"></i> Cari
            </button>
            @if(request()->hasAny(['search', 'category']))
            <a href="{{ route('dashboard.forum') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm">
                Reset
            </a>
            @endif
        </form>
    </div>

    {{-- Daftar Topik --}}
    <div class="space-y-3">
        @forelse($topics as $topic)
        <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow">
            <a href="{{ route('dashboard.forum.show', $topic->id) }}" class="block p-5">
                <div class="flex gap-4">

                   {{-- Avatar --}}
                    <img src="{{ $topic->user->foto_profil_url ?? 'https://ui-avatars.com/api/?name=?&background=16a34a&color=fff&size=128' }}"
                         alt="{{ $topic->user->name }}"
                         class="flex-shrink-0 w-11 h-11 rounded-full object-cover">

                    <div class="flex-1 min-w-0">
                        {{-- Badge & Judul --}}
                        <div class="flex flex-wrap items-start gap-2 mb-1">
                            @if($topic->is_pinned)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">📌 Pin</span>
                            @endif
                
                            @if($topic->is_locked)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium">🔒 Dikunci</span>
                            @endif
                            <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                                {{ ucwords(str_replace('_', ' ', $topic->category)) }}
                            </span>
                        </div>

                        <h3 class="text-base font-semibold text-gray-800 hover:text-green-700 leading-snug">
                            {{ $topic->title }}
                        </h3>

                        <p class="text-sm text-gray-500 mt-0.5">
                            oleh <span class="font-medium text-gray-700">{{ $topic->user->name ?? '-' }}</span>
                            · {{ $topic->created_at->diffForHumans() }}
                        </p>

                        <p class="text-sm text-gray-600 mt-2 line-clamp-2 leading-relaxed">
                            {{ \Str::limit($topic->content, 180) }}
                        </p>

                        {{-- Stats --}}
                        <div class="flex items-center gap-5 mt-3 text-xs text-gray-400">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-eye"></i> {{ number_format($topic->views) }}
                            </span>
                            <span class="flex items-center gap-1 {{ $topic->replies_count > 0 ? 'text-green-600 font-medium' : '' }}">
                                <i class="fas fa-comment"></i> {{ $topic->replies_count }} balasan
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-heart text-red-400"></i> {{ $topic->likes }}
                            </span>
                        </div>
                    </div>

                    {{-- Arrow --}}
                    <div class="flex-shrink-0 self-center text-gray-300">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow p-14 text-center text-gray-400" x-data>
            <i class="fas fa-comments text-5xl mb-3 block"></i>
            <p class="text-base font-medium">Belum ada topik diskusi</p>
            <p class="text-sm mt-1">Jadilah yang pertama memulai diskusi!</p>
            <button @click="showModal = true"
                class="mt-4 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                Buat Topik Pertama
            </button>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($topics->hasPages())
    <div class="bg-white rounded-lg shadow p-4">
        {{ $topics->links() }}
    </div>
    @endif

    {{-- =====================================================================
         MODAL BUAT TOPIK BARU
         ===================================================================== --}}
    <div x-show="showModal"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center px-4"
         x-cloak>
        <div @click="showModal = false" class="fixed inset-0 bg-black/50"></div>

        <div class="relative bg-white rounded-xl max-w-2xl w-full p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Buat Topik Diskusi Baru</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('dashboard.forum.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Topik <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required maxlength="255"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500"
                        placeholder="Tulis judul yang jelas dan informatif...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="hama_penyakit">Hama & Penyakit</option>
                        <option value="varietas_padi">Varietas Padi</option>
                        <option value="teknik_budidaya">Teknik Budidaya</option>
                        <option value="pemupukan">Pemupukan</option>
                        <option value="pengairan">Pengairan</option>
                        <option value="umum">Umum</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Isi Diskusi <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="5" required minlength="10"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 resize-none"
                        placeholder="Ceritakan masalah atau topik yang ingin didiskusikan..."></textarea>
                </div>

                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" @click="showModal = false"
                        class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        <i class="fas fa-paper-plane mr-1"></i> Posting
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection