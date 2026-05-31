@extends('layouts.dashboard')

@section('page-title', 'Profil Admin')

@section('content')
<div class="space-y-6 max-w-4xl" x-data="{ activeTab: '{{ session('tab', 'profil') }}' }">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <div class="relative group">
            <img
                src="{{ $admin->foto_profil ? Storage::url($admin->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($admin->name) . '&background=16a34a&color=fff&size=96' }}"
                alt="Foto Profil"
                class="w-20 h-20 rounded-full object-cover ring-4 ring-green-100 shadow"
            >
            <label for="quick-foto" class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                <i class="fas fa-camera text-white text-lg"></i>
            </label>
            {{-- Quick upload foto --}}
            <form id="form-quick-foto" method="POST" action="{{ route('admin.profil.foto') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" id="quick-foto" name="foto_profil" class="hidden" accept="image/*"
                    onchange="document.getElementById('form-quick-foto').submit()">
            </form>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $admin->name }}</h1>
            <p class="text-green-600 font-medium text-sm flex items-center gap-1">
                <i class="fas fa-shield-alt"></i> Administrator
            </p>
            <p class="text-gray-500 text-sm mt-0.5">{{ $admin->email }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="flex gap-6 -mb-px text-sm font-medium">
            <button @click="activeTab = 'profil'"
                :class="activeTab === 'profil' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="pb-3 border-b-2 transition">
                <i class="fas fa-user mr-1.5"></i> Data Profil
            </button>
            <button @click="activeTab = 'foto'"
                :class="activeTab === 'foto' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="pb-3 border-b-2 transition">
                <i class="fas fa-camera mr-1.5"></i> Foto Profil
            </button>
            <button @click="activeTab = 'keamanan'"
                :class="activeTab === 'keamanan' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="pb-3 border-b-2 transition">
                <i class="fas fa-lock mr-1.5"></i> Keamanan
            </button>
        </nav>
    </div>

    {{-- Tab: Data Profil --}}
    <div x-show="activeTab === 'profil'" x-transition>
        <form action="{{ route('admin.profil.update') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="pb-2 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Informasi Akun</h2>
                <p class="text-sm text-gray-500">Data utama akun administrator</p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $admin->name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-300 focus:border-green-500 text-sm"
                        placeholder="Nama lengkap admin" required>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-300 focus:border-green-500 text-sm"
                        placeholder="admin@example.com" required>
                </div>

                {{-- No. Telepon --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $admin->phone) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-300 focus:border-green-500 text-sm"
                        placeholder="08xxxxxxxxxx">
                </div>

                {{-- NIK --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik', $admin->nik) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-300 focus:border-green-500 text-sm"
                        placeholder="Nomor Induk Kependudukan" maxlength="20">
                </div>

                {{-- Desa --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desa / Kelurahan</label>
                    <input type="text" name="desa" value="{{ old('desa', $admin->desa) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-300 focus:border-green-500 text-sm"
                        placeholder="Nama desa">
                </div>

                {{-- Kecamatan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $admin->kecamatan) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-300 focus:border-green-500 text-sm"
                        placeholder="Nama kecamatan">
                </div>
            </div>

            {{-- Alamat --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="alamat" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-300 focus:border-green-500 text-sm resize-none"
                    placeholder="Jl. Contoh No. 1, RT/RW ...">{{ old('alamat', $admin->alamat) }}</textarea>
            </div>

            {{-- Info readonly --}}
            <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-gray-500 block text-xs mb-0.5">Role</span>
                    <span class="font-semibold text-green-700 capitalize flex items-center gap-1">
                        <i class="fas fa-shield-alt text-xs"></i> {{ ucfirst($admin->role) }}
                    </span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs mb-0.5">Status Akun</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $admin->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        <i class="fas fa-circle text-xs"></i> {{ ucfirst($admin->status ?? 'aktif') }}
                    </span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs mb-0.5">Bergabung Sejak</span>
                    <span class="font-medium">{{ $admin->created_at->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs mb-0.5">Terakhir Diperbarui</span>
                    <span class="font-medium">{{ $admin->updated_at->format('d M Y, H:i') }}</span>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Tab: Foto Profil --}}
    <div x-show="activeTab === 'foto'" x-transition>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="pb-3 border-b border-gray-100 mb-5">
                <h2 class="text-base font-semibold text-gray-800">Foto Profil</h2>
                <p class="text-sm text-gray-500">Unggah foto profil baru. Format: JPG, PNG, WEBP. Maks. 2MB.</p>
            </div>

            <form action="{{ route('admin.profil.foto') }}" method="POST" enctype="multipart/form-data"
                x-data="{ preview: null, filename: '' }">
                @csrf

                <div class="flex flex-col sm:flex-row items-center gap-8">
                    {{-- Preview --}}
                    <div class="relative shrink-0">
                        <img
                            :src="preview || '{{ $admin->foto_profil ? Storage::url($admin->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($admin->name) . '&background=16a34a&color=fff&size=128' }}'"
                            alt="Foto Profil"
                            class="w-32 h-32 rounded-full object-cover ring-4 ring-green-100 shadow-lg"
                        >
                        <label for="foto_profil_input"
                            class="absolute bottom-1 right-1 bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center cursor-pointer hover:bg-green-700 shadow">
                            <i class="fas fa-pencil-alt text-xs"></i>
                        </label>
                    </div>

                    {{-- Upload area --}}
                    <div class="flex-1 w-full">
                        <label for="foto_profil_input"
                            class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-green-50 hover:border-green-400 transition">
                            <template x-if="!filename">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600 font-medium">Klik untuk pilih foto</p>
                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP – Maks. 2MB</p>
                                </div>
                            </template>
                            <template x-if="filename">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
                                    <p class="text-sm text-green-700 font-medium" x-text="filename"></p>
                                    <p class="text-xs text-gray-400 mt-1">Siap diunggah</p>
                                </div>
                            </template>
                        </label>
                        <input id="foto_profil_input" type="file" name="foto_profil" class="hidden" accept="image/*"
                            @change="
                                const file = $event.target.files[0];
                                if (file) {
                                    filename = file.name;
                                    const reader = new FileReader();
                                    reader.onload = e => preview = e.target.result;
                                    reader.readAsDataURL(file);
                                }
                            ">
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                        <i class="fas fa-upload"></i> Unggah Foto
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tab: Keamanan --}}
    <div x-show="activeTab === 'keamanan'" x-transition>
        <form action="{{ route('admin.profil.password') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="pb-2 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Ganti Password</h2>
                <p class="text-sm text-gray-500">Pastikan password baru minimal 8 karakter.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama <span class="text-red-500">*</span></label>
                    <input type="password" name="password_lama"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-300 focus:border-green-500 text-sm"
                        placeholder="Masukkan password lama" required>
                    @error('password_lama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru <span class="text-red-500">*</span></label>
                    <input type="password" name="password_baru"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-300 focus:border-green-500 text-sm"
                        placeholder="Minimal 8 karakter" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                    <input type="password" name="password_baru_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-300 focus:border-green-500 text-sm"
                        placeholder="Ulangi password baru" required>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800 flex items-start gap-2">
                <i class="fas fa-exclamation-triangle mt-0.5 shrink-0"></i>
                <span>Setelah mengganti password, Anda perlu login ulang dengan password baru.</span>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <i class="fas fa-key"></i> Ganti Password
                </button>
            </div>
        </form>
    </div>

</div>
@endsection