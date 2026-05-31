<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Dashboard PATANI</title>
    <link rel="icon" type="image/png" href="/images/Lambang_Kabupaten_Indramayu.png">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')
</head>

<body class="bg-gray-50" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden">

    <!-- ================= Sidebar ================= -->
    <aside
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex flex-col h-full">

            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 py-5 border-b">
                <div class="flex items-center justify-center w-10 h-10 bg-green-600 rounded-lg">
                    <i class="fas fa-seedling text-white text-xl"></i>
                </div>
                <span class="text-xl font-bold text-gray-800">PATANI</span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">

                @if(auth()->user()->role === 'admin')

                    <!-- ===== Admin Menu ===== -->

                    <a href="{{ route('admin.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.index') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-home w-5"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.petani') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.petani*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-users w-5"></i>
                        <span>Data Petani</span>
                    </a>

                    <a href="{{ route('admin.sawah') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.sawah*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-map w-5"></i>
                        <span>Data Sawah</span>
                    </a>

                    <a href="{{ route('admin.laporan') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.laporan*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-file-alt w-5"></i>
                        <span>Laporan</span>
                    </a>

                    <a href="{{ route('admin.forum') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.forum*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-comments w-5"></i>
                        <span>Forum Management</span>
                    </a>

                    <a href="{{ route('admin.transaksi') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.transaksi*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-credit-card w-5"></i>
                        <span>Transaksi Premium</span>
                    </a>

                    <a href="{{ route('admin.pengaturan') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.pengaturan*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-cog w-5"></i>
                        <span>Pengaturan</span>
                    </a>

                @else

                    <!-- ===== Petani Menu ===== -->

                    @php
                        $jumlahNotifBelumDibaca = \App\Models\AdminNotifikasi::where('user_id', auth()->id())
                            ->where('is_read', false)
                            ->count();
                    @endphp

                    <a href="{{ route('dashboard.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard.index') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-home w-5"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('dashboard.sawah') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard.sawah') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-map w-5"></i>
                        <span>Data Sawah</span>
                    </a>

                    <a href="{{ route('dashboard.prediksi') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard.prediksi') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Prediksi Panen</span>
                    </a>

                    <a href="{{ route('dashboard.perawatan') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard.perawatan') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-tasks w-5"></i>
                        <span>Perawatan</span>
                    </a>

                    <a href="{{ route('dashboard.cuaca') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard.cuaca') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-cloud-sun w-5"></i>
                        <span>Info Cuaca</span>
                    </a>

                    <a href="{{ route('dashboard.chatbot') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard.chatbot') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-robot w-5"></i>
                        <span>Chatbot AI</span>
                    </a>

                    <a href="{{ route('dashboard.transaksi') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard.transaksi*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-crown w-5"></i>
                        <span>Premium</span>
                    </a>

                    <a href="{{ route('dashboard.forum') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard.forum') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-comments w-5"></i>
                        <span>Forum Diskusi</span>
                    </a>

                    <a href="{{ route('dashboard.riwayat') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard.riwayat') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-history w-5"></i>
                        <span>Riwayat Panen</span>
                    </a>

                    <a href="{{ route('dashboard.notifikasi') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard.notifikasi') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-bell w-5"></i>
                        <span class="flex-1">Notifikasi</span>
                        @if($jumlahNotifBelumDibaca > 0)
                        <span class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-red-500 text-white text-xs font-bold">
                            {{ $jumlahNotifBelumDibaca > 99 ? '99+' : $jumlahNotifBelumDibaca }}
                        </span>
                        @endif
                    </a>

                    {{-- ===== PENGATURAN PETANI ===== --}}
                    <a href="{{ route('dashboard.pengaturan') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard.pengaturan*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                        <i class="fas fa-cog w-5"></i>
                        <span>Pengaturan</span>
                    </a>

                @endif

            </nav>

            <!-- User Profile -->
            <div class="px-4 py-4 border-t">

                {{-- Foto + nama user, klik ke profil/pengaturan --}}
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.profil') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.profil*') ? 'bg-green-50' : '' }}">
                @else
                    <a href="{{ route('dashboard.pengaturan') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard.pengaturan*') ? 'bg-green-50' : '' }}">
                @endif
                        @if(auth()->user()->foto_profil)
                            <img src="{{ Storage::url(auth()->user()->foto_profil) }}"
                                 alt="Foto"
                                 class="w-10 h-10 rounded-full object-cover ring-2 ring-green-200">
                        @else
                            <div class="flex items-center justify-center w-10 h-10 bg-green-600 rounded-full text-white font-semibold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ ucfirst(auth()->user()->role) }}
                                @if(auth()->user()->role === 'admin')
                                    · <span class="text-green-600">Edit Profil</span>
                                @else
                                    · <span class="text-green-600">Pengaturan</span>
                                @endif
                            </p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </a>

                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2 text-red-600 rounded-lg hover:bg-red-50">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>

        </div>
    </aside>

    <!-- ================= Main Content ================= -->

    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Topbar -->
        <header class="bg-white border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <h1 class="text-xl font-semibold text-gray-800">@yield('page-title')</h1>

                <div class="flex items-center gap-4">
                    @if(auth()->user()->role === 'petani' && isset($jumlahNotifBelumDibaca) && $jumlahNotifBelumDibaca > 0)
                    <a href="{{ route('dashboard.notifikasi') }}" class="relative text-gray-500 hover:text-green-600 transition-colors">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full bg-red-500 text-white text-[10px] font-bold">
                            {{ $jumlahNotifBelumDibaca > 9 ? '9+' : $jumlahNotifBelumDibaca }}
                        </span>
                    </a>
                    @endif

                    <span class="text-sm text-gray-600 hidden sm:block">
                        {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                    </span>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')

        </main>
    </div>

</div>

<!-- Overlay mobile -->
<div x-show="sidebarOpen"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
     x-cloak></div>

@stack('scripts')

</body>
</html>