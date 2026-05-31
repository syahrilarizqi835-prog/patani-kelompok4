@extends('layouts.dashboard')
@section('page-title', 'Pengaturan')
@section('content')

<div class="space-y-6" x-data="{ activeTab: '{{ session('active_tab', 'profil') }}' }">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengaturan</h1>
            <p class="text-gray-500 text-sm mt-0.5">Kelola profil admin dan konfigurasi sistem</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===== TABS ===== --}}
    <div class="border-b border-gray-200">
        <nav class="flex gap-1 -mb-px">
            <button @click="activeTab = 'profil'"
                :class="activeTab === 'profil'
                    ? 'border-green-600 text-green-700 bg-green-50'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                class="flex items-center gap-2 px-5 py-3 text-sm font-medium border-b-2 rounded-t-lg transition">
                <i class="fas fa-user-circle"></i> Profil Admin
            </button>
            <button @click="activeTab = 'sistem'"
                :class="activeTab === 'sistem'
                    ? 'border-green-600 text-green-700 bg-green-50'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                class="flex items-center gap-2 px-5 py-3 text-sm font-medium border-b-2 rounded-t-lg transition">
                <i class="fas fa-cog"></i> Pengaturan Sistem
            </button>
        </nav>
    </div>

    {{-- ===========================
         TAB: PROFIL ADMIN
    =========================== --}}
    <div x-show="activeTab === 'profil'" x-transition class="space-y-5">

        {{-- Kartu header profil --}}
        <div class="bg-white rounded-xl shadow p-5 flex flex-col sm:flex-row items-center sm:items-start gap-5">
            {{-- Foto profil + quick upload --}}
            <div class="relative group shrink-0">
                @if($admin->foto_profil && Storage::exists('public/' . $admin->foto_profil))
                    <img src="{{ Storage::url($admin->foto_profil) }}"
                         alt="Foto Profil"
                         class="w-24 h-24 rounded-full object-cover ring-4 ring-green-100 shadow">
                @else
                    <div class="w-24 h-24 rounded-full bg-green-600 flex items-center justify-center text-white text-3xl font-bold ring-4 ring-green-100 shadow">
                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                    </div>
                @endif
                <label for="quick-foto-admin"
                    class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer"
                    title="Ganti foto">
                    <i class="fas fa-camera text-white text-lg"></i>
                </label>
                <form id="form-quick-foto" method="POST"
                      action="{{ route('admin.pengaturan.foto') }}"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="file" id="quick-foto-admin" name="foto_profil"
                           class="hidden" accept="image/*"
                           onchange="document.getElementById('form-quick-foto').submit()">
                </form>
            </div>

            {{-- Info singkat --}}
            <div class="text-center sm:text-left">
                <h2 class="text-xl font-bold text-gray-800">{{ $admin->name }}</h2>
                <p class="text-green-600 text-sm font-medium flex items-center justify-center sm:justify-start gap-1 mt-0.5">
                    <i class="fas fa-shield-alt text-xs"></i> Administrator
                </p>
                <p class="text-gray-500 text-sm mt-1">{{ $admin->email }}</p>
                <div class="flex flex-wrap gap-3 mt-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-phone text-green-500"></i>
                        {{ $admin->phone ?: '-' }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-map-marker-alt text-green-500"></i>
                        {{ $admin->desa ? $admin->desa . ', ' . $admin->kecamatan : '-' }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-calendar text-green-500"></i>
                        Bergabung {{ $admin->created_at->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Form data diri --}}
        <div class="bg-white rounded-xl shadow p-6" x-data="{ section: 'data' }">

            {{-- Sub-tabs --}}
            <div class="flex gap-3 mb-5 border-b border-gray-100 pb-3">
                <button @click="section = 'data'"
                    :class="section === 'data' ? 'text-green-700 font-semibold' : 'text-gray-400'"
                    class="text-sm flex items-center gap-1.5 transition">
                    <i class="fas fa-id-card"></i> Data Diri
                </button>
                <button @click="section = 'foto'"
                    :class="section === 'foto' ? 'text-green-700 font-semibold' : 'text-gray-400'"
                    class="text-sm flex items-center gap-1.5 transition">
                    <i class="fas fa-camera"></i> Ganti Foto
                </button>
                <button @click="section = 'password'"
                    :class="section === 'password' ? 'text-green-700 font-semibold' : 'text-gray-400'"
                    class="text-sm flex items-center gap-1.5 transition">
                    <i class="fas fa-lock"></i> Password
                </button>
            </div>

            {{-- Sub-tab: Data Diri --}}
            <div x-show="section === 'data'" x-transition>
                <form action="{{ route('admin.pengaturan.profil') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $admin->name) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $admin->email) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $admin->phone) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500"
                                placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                            <input type="text" name="nik" value="{{ old('nik', $admin->nik) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500"
                                placeholder="Nomor Induk Kependudukan" maxlength="20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Desa / Kelurahan</label>
                            <input type="text" name="desa" value="{{ old('desa', $admin->desa) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500"
                                placeholder="Nama desa">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                            <input type="text" name="kecamatan" value="{{ old('kecamatan', $admin->kecamatan) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500"
                                placeholder="Nama kecamatan">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500 resize-none"
                            placeholder="Jl. Contoh No. 1 ...">{{ old('alamat', $admin->alamat) }}</textarea>
                    </div>

                    {{-- Info readonly --}}
                    <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm border border-gray-100">
                        <div>
                            <span class="text-gray-400 text-xs block mb-1">Role Akun</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                 ADMIN
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-xs block mb-1">Status</span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-circle text-xs"></i> {{ ucfirst($admin->status ?? 'aktif') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-xs block mb-0.5">Bergabung</span>
                            <span class="font-medium">{{ $admin->created_at->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-xs block mb-0.5">Diperbarui</span>
                            <span class="font-medium">{{ $admin->updated_at->format('d M Y') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan Data Diri
                        </button>
                    </div>
                </form>
            </div>

            {{-- Sub-tab: Ganti Foto --}}
            <div x-show="section === 'foto'" x-transition>
                <form action="{{ route('admin.pengaturan.foto') }}" method="POST"
                      enctype="multipart/form-data"
                      x-data="{ preview: null, filename: '' }">
                    @csrf

                    <div class="flex flex-col sm:flex-row items-center gap-8">
                        {{-- Preview --}}
                        <div class="relative shrink-0">
                            <img :src="preview || '{{ $admin->foto_profil ? Storage::url($admin->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($admin->name) . '&background=16a34a&color=fff&size=128' }}'"
                                 alt="Foto Profil"
                                 class="w-32 h-32 rounded-full object-cover ring-4 ring-green-100 shadow-lg">
                            <label for="foto-input"
                                class="absolute bottom-1 right-1 bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center cursor-pointer hover:bg-green-700 shadow">
                                <i class="fas fa-pencil-alt text-xs"></i>
                            </label>
                        </div>

                        {{-- Upload area --}}
                        <div class="flex-1 w-full">
                            <label for="foto-input"
                                class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-green-50 hover:border-green-400 transition">
                                <template x-if="!filename">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600 font-medium">Klik untuk pilih foto</p>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — Maks. 2MB</p>
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
                            <input id="foto-input" type="file" name="foto_profil"
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
                    </div>

                    <div class="flex justify-end mt-5">
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                            <i class="fas fa-upload"></i> Unggah Foto
                        </button>
                    </div>
                </form>
            </div>

            {{-- Sub-tab: Password --}}
            <div x-show="section === 'password'" x-transition>
                <form action="{{ route('admin.pengaturan.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password Lama <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_lama"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500"
                            placeholder="Password saat ini" required>
                        @error('password_lama')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_baru"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500"
                            placeholder="Minimal 8 karakter" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Konfirmasi Password Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_baru_confirmation"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500"
                            placeholder="Ulangi password baru" required>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800 flex items-start gap-2">
                        <i class="fas fa-exclamation-triangle mt-0.5 shrink-0"></i>
                        <span>Setelah mengganti password, Anda perlu login ulang.</span>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                            <i class="fas fa-key"></i> Ganti Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- ===========================
         TAB: PENGATURAN SISTEM
    =========================== --}}
    <div x-show="activeTab === 'sistem'" x-transition>
        <form action="{{ route('admin.pengaturan.update') }}" method="POST"
              class="bg-white rounded-xl shadow p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="pb-2 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">Konfigurasi Aplikasi</h3>
                <p class="text-sm text-gray-500">Pengaturan umum aplikasi PATANI</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Aplikasi</label>
                    <input type="text" name="app_name"
                        value="{{ $pengaturan['app_name'] ?? 'PATANI' }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Kontak</label>
                    <input type="email" name="contact_email"
                        value="{{ $pengaturan['contact_email'] ?? '' }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500"
                        placeholder="email@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telepon Kontak</label>
                    <input type="text" name="contact_phone"
                        value="{{ $pengaturan['contact_phone'] ?? '' }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-300 focus:border-green-500"
                        placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection