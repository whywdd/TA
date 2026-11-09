@extends('Core.Sidebar')

@section('content')
<div class="p-6 animate-fade-in">
    <!-- Header -->
    <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold">Tambah Barang</h1>
        <p class="text-sm mt-1">Formulir untuk menambahkan data barang baru</p>
    </div>

    <form action="{{ route('manajemen-stok.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Card 1: Informasi Barang -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-box mr-2 text-blue-600"></i>Informasi Barang
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Jenis Anggrek -->
                <div class="form-group md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jenis Anggrek <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        name="nama_barang"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: Phalaenopsis White, Dendrobium Red, dll"
                        value="{{ old('nama_barang') }}"
                        required
                    >
                    @error('nama_barang')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Satuan -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Satuan <span class="text-red-600">*</span>
                    </label>
                    <select
                        name="satuan"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="">Pilih Satuan</option>
                        <option value="Pot" {{ old('satuan') == 'Pot' ? 'selected' : '' }}>Pot (Anggrek siap jual di pot)</option>
                        <option value="Bibit" {{ old('satuan') == 'Bibit' ? 'selected' : '' }}>Bibit (Tanaman masih kecil)</option>
                        <option value="Botol" {{ old('satuan') == 'Botol' ? 'selected' : '' }}>Botol (Bibit kultur jaringan)</option>
                        <option value="Batang" {{ old('satuan') == 'Batang' ? 'selected' : '' }}>Batang (Bunga potong/tangkai)</option>
                        <option value="Pohon" {{ old('satuan') == 'Pohon' ? 'selected' : '' }}>Pohon (Tanaman per individu)</option>
                        <option value="Tray" {{ old('satuan') == 'Tray' ? 'selected' : '' }}>Tray (Disusun dalam baki)</option>
                        <option value="Pack" {{ old('satuan') == 'Pack' ? 'selected' : '' }}>Pack (Kemasan)</option>
                        <option value="Rumpun" {{ old('satuan') == 'Rumpun' ? 'selected' : '' }}>Rumpun (Kelompok)</option>
                    </select>
                    @error('satuan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Tahap -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Status Tahap <span class="text-red-600">*</span>
                    </label>
                    <select
                        name="status_tahap"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="">Pilih Status Tahap</option>
                        <option value="bibit" {{ old('status_tahap') == 'bibit' ? 'selected' : '' }}>Bibit</option>
                        <option value="remaja" {{ old('status_tahap') == 'remaja' ? 'selected' : '' }}>Remaja</option>
                        <option value="indukan" {{ old('status_tahap') == 'indukan' ? 'selected' : '' }}>Indukan</option>
                        <option value="siap_bunga" {{ old('status_tahap') == 'siap_bunga' ? 'selected' : '' }}>Siap Bunga</option>
                        <option value="bunga_potong" {{ old('status_tahap') == 'bunga_potong' ? 'selected' : '' }}>Bunga Potong</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Tahap pertumbuhan anggrek saat ini</p>
                    @error('status_tahap')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Card 2: Kuantitas Stok -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-cubes mr-2 text-blue-600"></i>Kuantitas Stok
            </h2>

            <!-- Stok -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Stok <span class="text-red-600">*</span>
                </label>
                <input 
                    type="number" 
                    name="stok"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0"
                    min="0"
                    value="{{ old('stok') }}"
                    required
                >
                @error('stok')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Card 3: Penilaian Akuntansi & Harga -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-calculator mr-2 text-blue-600"></i>Penilaian Akuntansi & Harga
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Harga Beli / HPP -->
                <div class="form-group md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Harga Beli / HPP <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-600">Rp</span>
                        <input
                            type="text"
                            name="harga_beli"
                            class="w-full border rounded-lg pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="0"
                            value="{{ old('harga_beli') }}"
                            oninput="formatNumberInput(this)"
                            required
                        >
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Harga beli ini akan digunakan untuk menghitung laba saat penjualan</p>
                    @error('harga_beli')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Opsi Pembayaran -->
                <div class="form-group md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Opsi Pembayaran <span class="text-red-600">*</span>
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="opsi_pembayaran" value="tunai" checked class="mr-2">
                            <span>Bayar Tunai</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="opsi_pembayaran" value="belum_bayar" class="mr-2">
                            <span>Belum Bayar (Utang)</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pilih metode pembayaran untuk pembelian barang</p>
                    @error('opsi_pembayaran')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Card 5: Informasi Tambahan -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>Informasi Tambahan
            </h2>
            
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi / Keterangan
                </label>
                <textarea 
                    name="deskripsi"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="3"
                    placeholder="Tambahkan deskripsi atau keterangan tambahan..."
                >{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Tombol Submit -->
        <div class="flex justify-end space-x-3">
            <button 
                type="button" 
                class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition-colors"
                onclick="window.history.back()"
            >
                Kembali
            </button>
            <button 
                type="submit" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
                Simpan
            </button>
        </div>
    </form>
</div>

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function formatNumberInput(input) {
    // Hapus semua karakter kecuali angka
    let value = input.value.replace(/\D/g, '');
    if (value !== '') {
        // Format dengan titik ribuan
        value = new Intl.NumberFormat('id-ID').format(value);
    }
    input.value = value;
}

// Notifikasi
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        confirmButtonText: 'OK'
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK'
    });
@endif
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

.form-group input:hover,
.form-group select:hover {
    border-color: #93C5FD;
}

button {
    transition: all 0.2s ease-in-out;
}

button:hover {
    transform: translateY(-1px);
}

button:active {
    transform: translateY(0);
}
</style>
@endsection
