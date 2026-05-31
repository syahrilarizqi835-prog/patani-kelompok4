@extends('layouts.dashboard')

@section('page-title', 'Data Petani')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Petani</h1>
            <p class="text-gray-600">Kelola data petani yang terdaftar</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid gap-4 md:grid-cols-3">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total Petani</span>
                <i class="fas fa-users text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $petaniList->total() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Petani Aktif</span>
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $petaniList->where('status', 'aktif')->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Petani Baru (Bulan Ini)</span>
                <i class="fas fa-user-plus text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $petaniList->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
        </div>
    </div>

    <!-- Petani List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <form method="GET" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari petani..." 
                       class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($petaniList as $petani)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                {{-- Foto profil: tampil foto jika ada, fallback ke inisial --}}
                                @if($petani->foto_profil)
                                    <img src="{{ $petani->foto_profil_url }}" 
                                         alt="{{ $petani->name }}"
                                         class="h-10 w-10 rounded-full object-cover flex-shrink-0 border border-gray-200">
                                @else
                                    <div class="h-10 w-10 bg-green-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                        {{ substr($petani->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900">{{ $petani->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $petani->nik ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $petani->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $petani->phone ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $petani->desa ?? '-' }}, {{ $petani->kecamatan ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $petani->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($petani->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-3">
                                {{-- Tombol Detail --}}
                                <a href="{{ route('admin.petani.show', $petani->id) }}" 
                                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium transition">
                                    <i class="fas fa-eye"></i> Detail
                                </a>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.petani.destroy', $petani->id) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus petani {{ addslashes($petani->name) }}?\nSemua data terkait akan ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 font-medium transition">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-users text-4xl mb-3 block opacity-30"></i>
                            Belum ada data petani
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($petaniList->hasPages())
        <div class="p-6 border-t">
            {{ $petaniList->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection