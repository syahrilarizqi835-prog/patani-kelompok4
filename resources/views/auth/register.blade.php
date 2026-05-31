@extends('layouts.app')

@section('title', 'Register - PATANI')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-green-50 via-white to-white p-4 py-10">
    <div class="w-full max-w-lg">

        {{-- Logo --}}
        <div class="mb-8 text-center">
            <a href="/" class="inline-flex items-center gap-2">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-600">
                    <i class="fas fa-seedling text-white text-2xl"></i>
                </div>
                <span class="text-3xl font-bold text-gray-800">PATANI</span>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h2>
                <p class="text-gray-600 mt-2">Daftar untuk mulai menggunakan platform PATANI</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST"
                  enctype="multipart/form-data"
                  class="space-y-4"
                  x-data="{ preview: null, filename: '' }">
                @csrf

                {{-- ===== FOTO PROFIL ===== --}}
                <div class="flex flex-col items-center gap-3 pb-4 border-b border-gray-100">
                    <div class="relative group">
                        <img :src="preview || 'https://ui-avatars.com/api/?name=?&background=e5e7eb&color=9ca3af&size=96'"
                             alt="Foto Profil"
                             class="w-24 h-24 rounded-full object-cover ring-4 ring-gray-100">
                        <label for="foto_profil"
                            class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                            <i class="fas fa-camera text-white text-lg"></i>
                        </label>
                    </div>
                    <div class="text-center">
                        <label for="foto_profil"
                            class="cursor-pointer text-sm text-green-600 font-medium hover:underline flex items-center gap-1 justify-center">
                            <i class="fas fa-upload text-xs"></i>
                            <span x-text="filename || 'Upload Foto Profil (opsional)'"></span>
                        </label>
                        <p class="text-xs text-gray-400 mt-0.5">JPG, PNG, WEBP — Maks. 2MB</p>
                    </div>
                    <input type="file" id="foto_profil" name="foto_profil"
                           class="hidden" accept="image/*"
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

                {{-- ===== DATA AKUN ===== --}}
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Data Akun</p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                               placeholder="Masukkan nama lengkap">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                               placeholder="nama@email.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                               placeholder="08xxxxxxxxxx">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Pilih Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                            <option value="">Pilih role Anda</option>
                            <option value="petani" {{ old('role') == 'petani' ? 'selected' : '' }}>🌾 Petani</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>🛡️ Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                               placeholder="16 digit NIK KTP" maxlength="20">
                    </div>
                </div>

                {{-- ===== DATA WILAYAH ===== --}}
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider pt-1">Data Wilayah</p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Desa / Kelurahan</label>
                        <input type="text" name="desa" value="{{ old('desa') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                               placeholder="Nama desa">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                        <input type="text" name="kecamatan" value="{{ old('kecamatan') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                               placeholder="Nama kecamatan">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" rows="2"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm resize-none"
                               placeholder="Jl. Contoh No. 1, RT/RW ...">{{ old('alamat') }}</textarea>
                    </div>
                </div>

                {{-- ===== PASSWORD ===== --}}
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider pt-1">Keamanan</p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                               placeholder="Min. 8 karakter">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                               placeholder="Ulangi password">
                    </div>
                </div>

                {{-- Syarat & Ketentuan --}}
                <div class="flex items-start gap-2 pt-1">
                    <input type="checkbox" id="terms" required
                           class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <label for="terms" class="text-sm text-gray-600">
                        Saya menyetujui
                        <a href="#" class="text-green-600 hover:underline">Syarat & Ketentuan</a>
                        dan
                        <a href="#" class="text-green-600 hover:underline">Kebijakan Privasi</a>
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-medium text-green-600 hover:underline">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection