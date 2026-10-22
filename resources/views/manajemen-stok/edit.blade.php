@extends('Core.Sidebar')

@section('content')
<div class="p-6 animate-fade-in">
    <!-- Header -->
    <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold">Edit Barang</h1>
        <p class="text-sm mt-1">Edit data persediaan barang</p>
    </div>

    <!-- Summary Widget - Total Nilai Persediaan -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm font-medium opacity-90">Total Nilai Persediaan</p>
                <p class="text-2xl font-bold mt-1">
                    Rp {{ number_format(($stok->stok ?? 0) * ($stok->harga_beli ?? 0), 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium opacity-90">Stok Saat Ini</p>
                <p class="text-2xl font-bold mt-1">{{ $stok->stok ?? 0 }} {{ $stok->satuan ?? '' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium opacity-90">Potensi Penjualan</p>
                <p class="text-2xl font-bold mt-1">
                    Rp {{ number_format(($stok->stok ?? 0) * ($stok->harga_jual ?? 0), 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('manajemen-stok.update', $stok->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Card 1: Informasi Barang -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-box mr-2 text-blue-600"></i>Informasi Barang
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kode Barang -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kode/SKU Barang <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="kode_barang"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: ANG-BLL-001"
                        value="{{ old('kode_barang', $stok->kode_barang ?? '') }}"
                        required
                    >
                    @error('kode_barang')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Barang -->
                <div class="form-group md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Barang <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama_barang"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan nama barang"
                        value="{{ old('nama_barang', $stok->nama_barang ?? '') }}"
                        required
                    >
                    @error('nama_barang')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori <span class="text-red-600">*</span>
                    </label>
                    <select 
                        name="kategori"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="">Pilih Kategori</option>
                        <option value="Seedling" {{ old('kategori', $stok->kategori ?? '') == 'Seedling' ? 'selected' : '' }}>Seedling</option>
                        <option value="Remaja" {{ old('kategori', $stok->kategori ?? '') == 'Remaja' ? 'selected' : '' }}>Remaja</option>
                        <option value="Indukan" {{ old('kategori', $stok->kategori ?? '') == 'Indukan' ? 'selected' : '' }}>Indukan</option>
                        <option value="Bunga Potong" {{ old('kategori', $stok->kategori ?? '') == 'Bunga Potong' ? 'selected' : '' }}>Bunga Potong</option>
                        <option value="Sarana Produksi/Pupuk" {{ old('kategori', $stok->kategori ?? '') == 'Sarana Produksi/Pupuk' ? 'selected' : '' }}>Sarana Produksi/Pupuk</option>
                    </select>
                    @error('kategori')
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
                        <option value="Pohon" {{ old('satuan', $stok->satuan ?? '') == 'Pohon' ? 'selected' : '' }}>Pohon</option>
                        <option value="Pot" {{ old('satuan', $stok->satuan ?? '') == 'Pot' ? 'selected' : '' }}>Pot</option>
                        <option value="Botol" {{ old('satuan', $stok->satuan ?? '') == 'Botol' ? 'selected' : '' }}>Botol</option>
                        <option value="Pack" {{ old('satuan', $stok->satuan ?? '') == 'Pack' ? 'selected' : '' }}>Pack</option>
                        <option value="Kg" {{ old('satuan', $stok->satuan ?? '') == 'Kg' ? 'selected' : '' }}>Kg</option>
                        <option value="Lusin" {{ old('satuan', $stok->satuan ?? '') == 'Lusin' ? 'selected' : '' }}>Lusin</option>
                    </select>
                    @error('satuan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Card 2: Kuantitas & Batas Stok -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-cubes mr-2 text-blue-600"></i>Kuantitas & Batas Stok
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Stok Saat Ini (Read-Only) -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Stok Saat Ini <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="stok"
                        class="w-full border rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed focus:outline-none"
                        placeholder="0"
                        min="0"
                        value="{{ $stok->stok ?? 0 }}"
                        readonly
                        disabled
                    >
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Stok hanya dapat diubah melalui transaksi atau penyesuaian/Opname
                    </p>
                </div>

                <!-- Reorder Point -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Reorder Point (Minimum Stok) <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="reorder_point"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: 10"
                        min="0"
                        value="{{ old('reorder_point', $stok->reorder_point ?? '') }}"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Jumlah stok minimum untuk pemesanan ulang</p>
                    @error('reorder_point')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Card 3: Penilaian Akuntansi & Harga -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-calculator mr-2 text-blue-600"></i>Penilaian Akuntansi & Harga
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Harga Beli / HPP -->
                <div class="form-group">
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
                            value="{{ old('harga_beli', $stok->harga_beli) ? number_format(old('harga_beli', $stok->harga_beli), 0, ',', '.') : '' }}"
                            oninput="formatNumber(this)"
                            required
                        >
                    </div>
                    @error('harga_beli')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Jual -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Harga Jual <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-600">Rp</span>
                        <input 
                            type="text" 
                            name="harga_jual"
                            class="w-full border rounded-lg pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="0"
                            value="{{ old('harga_jual', $stok->harga_jual) ? number_format(old('harga_jual', $stok->harga_jual), 0, ',', '.') : '' }}"
                            oninput="formatNumber(this)"
                            required
                        >
                    </div>
                    @error('harga_jual')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Akun COA Persediaan -->
                <div class="form-group md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Akun COA Persediaan <span class="text-red-600">*</span>
                    </label>
                    <select 
                        name="akun_coa_persediaan"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="">Pilih Akun COA Persediaan</option>
                        <option value="persediaan barang" {{ old('akun_coa_persediaan', $stok->akun_coa_persediaan ?? '') == 'persediaan barang' ? 'selected' : '' }}>Persediaan Barang Dagang (1107)</option>
                        <option value="persediaan bahan" {{ old('akun_coa_persediaan', $stok->akun_coa_persediaan ?? '') == 'persediaan bahan' ? 'selected' : '' }}>Persediaan Bahan Baku/Supplies (1108)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Akun untuk pencatatan persediaan di neraca</p>
                    @error('akun_coa_persediaan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Akun COA Penjualan -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Akun COA Penjualan <span class="text-red-600">*</span>
                    </label>
                    <select 
                        name="akun_coa_penjualan"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="">Pilih Akun COA Penjualan</option>
                        <option value="pendapatan penjualan" {{ old('akun_coa_penjualan', $stok->akun_coa_penjualan ?? '') == 'pendapatan penjualan' ? 'selected' : '' }}>Pendapatan Penjualan (4101)</option>
                        <option value="pendapatan jasa" {{ old('akun_coa_penjualan', $stok->akun_coa_penjualan ?? '') == 'pendapatan jasa' ? 'selected' : '' }}>Pendapatan Jasa (4102)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Akun untuk otomatis mencatat penjualan</p>
                    @error('akun_coa_penjualan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Akun COA HPP -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Akun COA HPP <span class="text-red-600">*</span>
                    </label>
                    <select 
                        name="akun_coa_hpp"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="">Pilih Akun COA HPP</option>
                        <option value="beban gaji" {{ old('akun_coa_hpp', $stok->akun_coa_hpp ?? '') == 'beban gaji' ? 'selected' : '' }}>Beban Gaji (5101)</option>
                        <option value="beban lain" {{ old('akun_coa_hpp', $stok->akun_coa_hpp ?? '') == 'beban lain' ? 'selected' : '' }}>Beban Lain-lain (5202)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Akun untuk otomatis mencatat HPP saat penjualan</p>
                    @error('akun_coa_hpp')
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
                >{{ old('deskripsi', $stok->deskripsi ?? '') }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Card 4: Lokasi & Foto Produk -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>Lokasi & Foto Produk
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Lokasi Greenhouse -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Lokasi Greenhouse
                    </label>
                    <input 
                        type="text" 
                        name="lokasi_greenhouse"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: GH-01"
                        value="{{ old('lokasi_greenhouse', $stok->lokasi_greenhouse ?? '') }}"
                    >
                    @error('lokasi_greenhouse')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rak -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Rak
                    </label>
                    <input 
                        type="text" 
                        name="rak"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: A-01"
                        value="{{ old('rak', $stok->rak ?? '') }}"
                    >
                    @error('rak')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Foto Produk -->
                <div class="form-group md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Foto Produk
                    </label>
                    <input 
                        type="file" 
                        name="foto_produk"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        accept="image/*"
                    >
                    @if($stok->foto_produk)
                        <div class="mt-2">
                            <p class="text-xs text-gray-500 mb-1">Foto saat ini:</p>
                            <img src="{{ asset('storage/' . $stok->foto_produk) }}" alt="Foto Produk" class="h-20 w-20 object-cover rounded border">
                        </div>
                    @endif
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG (Maks 2MB)</p>
                    @error('foto_produk')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
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
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function formatNumber(input) {
    let value = input.value.replace(/\D/g, '');
    if (value !== '') {
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
