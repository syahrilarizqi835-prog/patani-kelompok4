@extends('layouts.dashboard')
@section('page-title', 'Notifikasi')

@section('content')
<div class="space-y-5 max-w-3xl">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
        <p class="text-gray-500 text-sm mt-0.5">Pesan dan pemberitahuan dari Admin PATANI</p>
    </div>

    {{-- Daftar notifikasi --}}
    @if($notifikasi->isEmpty())
        <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400">
            <i class="fas fa-bell-slash text-5xl mb-3 block"></i>
            <p class="text-base font-medium">Belum ada notifikasi</p>
            <p class="text-sm mt-1">Notifikasi dari admin akan muncul di sini</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($notifikasi as $item)
            @php
                $info = $item->tipe_info;
                $colorMap = [
                    'green'  => ['bg' => 'bg-green-50',  'border' => 'border-green-300', 'icon' => 'bg-green-100 text-green-600'],
                    'red'    => ['bg' => 'bg-red-50',    'border' => 'border-red-300',   'icon' => 'bg-red-100 text-red-600'],
                    'orange' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-300','icon' => 'bg-orange-100 text-orange-600'],
                    'blue'   => ['bg' => 'bg-blue-50',   'border' => 'border-blue-300',  'icon' => 'bg-blue-100 text-blue-600'],
                    'purple' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-300','icon' => 'bg-purple-100 text-purple-600'],
                    'gray'   => ['bg' => 'bg-gray-50',   'border' => 'border-gray-300',  'icon' => 'bg-gray-100 text-gray-500'],
                ];
                $c = $colorMap[$info['color']] ?? $colorMap['gray'];
            @endphp

            <div class="bg-white rounded-xl shadow border-l-4 {{ $c['border'] }} p-5 flex gap-4 items-start">

                {{-- Icon --}}
                <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $c['icon'] }} flex items-center justify-center text-lg">
                    <i class="fas {{ $info['icon'] }}"></i>
                </div>

                {{-- Konten --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 flex-wrap">
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $c['bg'] }} {{ str_replace('bg-', 'text-', $c['border']) }} mb-1">
                                {{ $info['label'] }}
                            </span>
                            <h3 class="font-semibold text-gray-800 text-sm leading-snug">{{ $item->judul }}</h3>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0">
                            {{ $item->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 mt-1.5 leading-relaxed">{{ $item->pesan }}</p>

                    {{-- Info sawah terkait --}}
                    @if($item->sawah)
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-500">
                        <i class="fas fa-map-marker-alt text-green-500"></i>
                        <span>Sawah: <strong>{{ $item->sawah->nama_sawah }}</strong>
                            — {{ $item->sawah->desa }}, {{ $item->sawah->kecamatan }}
                        </span>
                    </div>
                    @endif

                    <p class="text-xs text-gray-400 mt-2">
                        {{ $item->created_at->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB
                        · <span class="text-green-600 font-medium">Admin PATANI</span>
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($notifikasi->hasPages())
        <div class="mt-4">{{ $notifikasi->links() }}</div>
        @endif
    @endif

</div>
@endsection