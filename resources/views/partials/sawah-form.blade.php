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