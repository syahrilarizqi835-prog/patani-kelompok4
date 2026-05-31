@extends('layouts.dashboard')
@section('page-title', 'Data Sawah')

@section('content')
<div class="space-y-6" x-data="{
    showModal: false,
    editMode: false,
    editId: null,
    previewUrl: null,
    form: {
        nama_sawah: '', lokasi: '', desa: '', kecamatan: '',
        luas: '', jenis_padi: '', tanggal_tanam: '',
        kondisi_tanah: 'sedang', kondisi_air: 'baik',
        fase_tanam: 'vegetatif', status: 'aktif', catatan: ''
    },
    openTambah() {
        this.editMode = false;
        this.editId = null;
        this.previewUrl = null;
        this.form = {
            nama_sawah: '', lokasi: '', desa: '', kecamatan: '',
            luas: '', jenis_padi: '', tanggal_tanam: '',
            kondisi_tanah: 'sedang', kondisi_air: 'baik',
            fase_tanam: 'vegetatif', status: 'aktif', catatan: ''
        };
        this.showModal = true;
    },
    openEdit(sawah) {
        this.editMode = true;
        this.editId = sawah.id;
        this.previewUrl = sawah.foto_lahan ? '/storage/' + sawah.foto_lahan : null;
        this.form = {
            nama_sawah:    sawah.nama_sawah,
            lokasi:        sawah.lokasi,
            desa:          sawah.desa,
            kecamatan:     sawah.kecamatan,
            luas:          sawah.luas,
            jenis_padi:    sawah.jenis_padi,
            tanggal_tanam: sawah.tanggal_tanam ?? '',
            kondisi_tanah: sawah.kondisi_tanah,
            kondisi_air:   sawah.kondisi_air,
            fase_tanam:    sawah.fase_tanam,
            status:        sawah.status,
            catatan:       sawah.catatan ?? ''
        };
        this.showModal = true;
    },
    handleFoto(event) {
        const file = event.target.files[0];
        if (file) {
            this.previewUrl = URL.createObjectURL(file);
        }
    }
}">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Sawah</h1>
            <p class="text-gray-600">Kelola data sawah dan foto lahan Anda</p>
        </div>
        <button @click="openTambah()"
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Sawah
        </button>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2">
        <i class="fas fa-check-circle text-green-600"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Grid Kartu Sawah --}}
    @if($sawahList->count() > 0)
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($sawahList as $sawah)
        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden hover:shadow-md transition">

            {{-- Foto Lahan --}}
            <div class="relative h-48 bg-gray-100">
                @if($sawah->foto_lahan)
                    <img src="{{ asset('storage/' . $sawah->foto_lahan) }}"
                        alt="Foto {{ $sawah->nama_sawah }}"
                        class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-image text-5xl mb-2"></i>
                        <p class="text-sm">Belum ada foto lahan</p>
                    </div>
                @endif

                {{-- Badge Status --}}
                <div class="absolute top-3 right-3">
                    <span class="px-2 py-1 text-xs font-bold rounded-full
                        {{ $sawah->status === 'aktif' ? 'bg-green-500 text-white' :
                           ($sawah->status === 'panen' ? 'bg-yellow-500 text-white' : 'bg-gray-500 text-white') }}">
                        {{ ucfirst($sawah->status) }}
                    </span>
                </div>

                {{-- Badge Fase --}}
                <div class="absolute top-3 left-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-black bg-opacity-50 text-white capitalize">
                        {{ $sawah->fase_tanam }}
                    </span>
                </div>
            </div>

            {{-- Info --}}
            <div class="p-4">
                <h3 class="font-bold text-gray-800 text-lg mb-1">{{ $sawah->nama_sawah }}</h3>
                <p class="text-sm text-gray-500 mb-3">
                    <i class="fas fa-map-marker-alt text-red-400 mr-1"></i>
                    {{ $sawah->desa }}, {{ $sawah->kecamatan }}
                </p>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                        <p class="text-xs text-gray-500">Luas</p>
                        <p class="font-bold text-gray-800">{{ $sawah->luas }} Ha</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                        <p class="text-xs text-gray-500">Jenis Padi</p>
                        <p class="font-bold text-gray-800 text-sm">{{ $sawah->jenis_padi }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                        <p class="text-xs text-gray-500">Tanah</p>
                        <p class="font-semibold text-sm capitalize
                            {{ $sawah->kondisi_tanah === 'subur' ? 'text-green-700' :
                               ($sawah->kondisi_tanah === 'sedang' ? 'text-yellow-700' : 'text-red-700') }}">
                            {{ ucfirst($sawah->kondisi_tanah) }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                        <p class="text-xs text-gray-500">Air</p>
                        <p class="font-semibold text-sm capitalize
                            {{ $sawah->kondisi_air === 'baik' ? 'text-blue-700' :
                               ($sawah->kondisi_air === 'cukup' ? 'text-yellow-700' : 'text-red-700') }}">
                            {{ ucfirst($sawah->kondisi_air) }}
                        </p>
                    </div>
                </div>

                @if($sawah->estimasi_panen)
                <p class="text-xs text-gray-500 mb-3">
                    <i class="fas fa-calendar text-green-500 mr-1"></i>
                    Estimasi panen: {{ \Carbon\Carbon::parse($sawah->estimasi_panen)->format('d M Y') }}
                </p>
                @endif

                {{-- Aksi --}}
                <div class="flex gap-2 pt-2 border-t">
                    <button
                        @click="openEdit({{ $sawah->toJson() }})"
                        class="flex-1 bg-blue-50 text-blue-700 py-2 rounded-lg text-sm font-medium hover:bg-blue-100 transition flex items-center justify-center gap-1">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form action="{{ route('dashboard.sawah.destroy', $sawah->id) }}" method="POST"
                        onsubmit="return confirm('Yakin hapus sawah {{ $sawah->nama_sawah }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-50 text-red-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-100 transition flex items-center gap-1">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @else
    {{-- Empty state --}}
    <div class="bg-white rounded-xl shadow border border-gray-200 p-12 text-center">
        <i class="fas fa-seedling text-6xl text-gray-300 mb-4 block"></i>
        <h3 class="text-xl font-bold text-gray-600 mb-2">Belum ada data sawah</h3>
        <p class="text-gray-400 mb-6">Tambahkan sawah pertama Anda untuk mulai memantau lahan</p>
        <button @click="openTambah()"
            class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-medium">
            <i class="fas fa-plus mr-2"></i>Tambah Sawah Sekarang
        </button>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════
         MODAL TAMBAH / EDIT
    ═══════════════════════════════════════════════════ --}}
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div @click="showModal = false" class="fixed inset-0 bg-black opacity-50"></div>

            <div class="relative bg-white rounded-xl max-w-2xl w-full shadow-xl">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-xl font-bold text-gray-800">
                        
                        <span x-text="editMode ? 'Edit Data Sawah' : 'Tambah Sawah Baru'"></span>
                    </h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Form Tambah --}}
<div x-show="!editMode">
    <form action="{{ route('dashboard.sawah.store') }}" method="POST"
        enctype="multipart/form-data" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
        @csrf

        {{-- Foto Upload --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-camera text-green-600 mr-1"></i>Foto Lahan
            </label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                <div x-show="!previewUrl">
                    <i class="fas fa-image text-4xl text-gray-300 mb-2 block"></i>
                    <p class="text-sm text-gray-400 mb-2">Upload foto lahan sawah Anda</p>
                </div>
                <img x-show="previewUrl" :src="previewUrl" class="h-40 mx-auto object-cover rounded-lg mb-3">
                <input type="file" name="foto_lahan" accept="image/*"
                    @change="handleFoto($event)"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — Maks 2MB</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sawah *</label>
                <input type="text" name="nama_sawah" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Luas (Ha) *</label>
                <input type="number" name="luas" step="0.01" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi *</label>
            <input type="text" name="lokasi" required
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Desa *</label>
                <input type="text" name="desa" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan *</label>
                <input type="text" name="kecamatan" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Padi *</label>
                <input type="text" name="jenis_padi" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tanam</label>
                <input type="date" name="tanggal_tanam"
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Tanah *</label>
                <select name="kondisi_tanah" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="subur">Subur</option>
                    <option value="sedang" selected>Sedang</option>
                    <option value="kurang">Kurang</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Air *</label>
                <select name="kondisi_air" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="baik" selected>Baik</option>
                    <option value="cukup">Cukup</option>
                    <option value="kurang">Kurang</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <textarea name="catatan" rows="2"
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
        </div>

        <div class="flex gap-3 pt-2 sticky bottom-0 bg-white pb-2">
            <button type="button" @click="showModal = false"
                class="flex-1 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50">
                Batal
            </button>
            <button type="submit"
                class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
        </div>
    </form>
</div>

                {{-- Form Edit (per sawah, pakai loop tersembunyi) --}}
                <div x-show="editMode">
                    @foreach($sawahList as $sawah)
                    <form
                        x-show="editId === {{ $sawah->id }}"
                        action="{{ route('dashboard.sawah.update', $sawah->id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                        @csrf
                        @method('PUT')

                        {{-- Foto Preview & Upload --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-camera text-green-600 mr-1"></i>Foto Lahan
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                @if($sawah->foto_lahan)
                                <img src="{{ asset('storage/' . $sawah->foto_lahan) }}"
                                    class="h-40 mx-auto object-cover rounded-lg mb-3"
                                    alt="Foto lahan saat ini">
                                <p class="text-xs text-gray-500 mb-2">Foto saat ini. Upload baru untuk mengganti.</p>
                                @else
                                <i class="fas fa-image text-4xl text-gray-300 mb-2 block"></i>
                                <p class="text-sm text-gray-400 mb-2">Belum ada foto. Upload foto lahan Anda.</p>
                                @endif
                                <input type="file" name="foto_lahan" accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — Maks 2MB</p>
                            </div>
                        </div>

                        {{-- Field Data --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sawah *</label>
                                <input type="text" name="nama_sawah" value="{{ $sawah->nama_sawah }}" required
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Luas (Ha) *</label>
                                <input type="number" name="luas" step="0.01" value="{{ $sawah->luas }}" required
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi *</label>
                            <input type="text" name="lokasi" value="{{ $sawah->lokasi }}" required
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Desa *</label>
                                <input type="text" name="desa" value="{{ $sawah->desa }}" required
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan *</label>
                                <input type="text" name="kecamatan" value="{{ $sawah->kecamatan }}" required
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Padi *</label>
                                <input type="text" name="jenis_padi" value="{{ $sawah->jenis_padi }}" required
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tanam</label>
                                <input type="date" name="tanggal_tanam"
                                    value="{{ $sawah->tanggal_tanam ? $sawah->tanggal_tanam->format('Y-m-d') : '' }}"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Tanah *</label>
                                <select name="kondisi_tanah" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="subur" {{ $sawah->kondisi_tanah === 'subur' ? 'selected' : '' }}>Subur</option>
                                    <option value="sedang" {{ $sawah->kondisi_tanah === 'sedang' ? 'selected' : '' }}>Sedang</option>
                                    <option value="kurang" {{ $sawah->kondisi_tanah === 'kurang' ? 'selected' : '' }}>Kurang</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Air *</label>
                                <select name="kondisi_air" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="baik" {{ $sawah->kondisi_air === 'baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="cukup" {{ $sawah->kondisi_air === 'cukup' ? 'selected' : '' }}>Cukup</option>
                                    <option value="kurang" {{ $sawah->kondisi_air === 'kurang' ? 'selected' : '' }}>Kurang</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fase Tanam *</label>
                                <select name="fase_tanam" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                                    @foreach(['persiapan','vegetatif','generatif','pematangan','panen'] as $fase)
                                    <option value="{{ $fase }}" {{ $sawah->fase_tanam === $fase ? 'selected' : '' }}>
                                        {{ ucfirst($fase) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                                <select name="status" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="aktif" {{ $sawah->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="panen" {{ $sawah->status === 'panen' ? 'selected' : '' }}>Panen</option>
                                    <option value="istirahat" {{ $sawah->status === 'istirahat' ? 'selected' : '' }}>Istirahat</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea name="catatan" rows="2"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">{{ $sawah->catatan }}</textarea>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showModal = false"
                                class="flex-1 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-medium">
                                <i class="fas fa-save mr-1"></i> Update Sawah
                            </button>
                        </div>
                    </form>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

</div>
@endsection