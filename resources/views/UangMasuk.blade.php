@extends('Core.Sidebar')

@section('content')
<div class="p-6 animate-fade-in">
    <!-- Header -->
    <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold">Bukti Transaksi</h1>
        <p class="text-sm mt-1">Formulir untuk menambahkan data Transaksi</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form id="uangMasukForm" action="{{ route('uangmasuk.store') }}" method="POST" class="space-y-4">
            @csrf <!-- Token CSRF untuk keamanan -->
            <!-- Tanggal -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal <span class="text-red-600">*</span>
                </label>
                <input 
                    type="date" 
                    name="Tanggal"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
            </div>

            <!-- Nama Akun -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Akun <span class="text-red-600">*</span>
                </label>
                <select 
                    name="kategori"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    required
                    onchange="updateTotalUang(this.value)"
                >
                    <option value="" disabled selected>Pilih Kategori Akun</option>
                    
                    <optgroup label="1. HARTA (ASET) (11)">
                    <!-- Aset Lancar -->
                    <optgroup label="&nbsp;&nbsp;&nbsp;Aset Lancar (1)">
                        <option value="kas" data-kode="1101">Kas/Kas Kecil (001) - Debit</option>
                        <option value="bank" data-kode="1102">Bank (002) - Debit</option>
                        <option value="piutang usaha" data-kode="1103">Piutang Usaha/Dagang (003) - Debit</option>
                        <option value="piutang wesel" data-kode="1104">Piutang Wesel (004) - Debit</option>
                        <option value="piutang karyawan" data-kode="1105">Piutang Karyawan (005) - Debit</option>
                        <option value="piutang lain" data-kode="1106">Piutang Lain-lain (006) - Debit</option>
                        <option value="persediaan barang" data-kode="1107">Persediaan Barang Dagang (007) - Debit</option>
                        <option value="persediaan bahan" data-kode="1108">Persediaan Bahan Baku/Supplies (008) - Debit</option>
                        <option value="sewa dibayar dimuka" data-kode="1109">Sewa Dibayar di Muka (009) - Debit</option>
                        <option value="asuransi dibayar_dimuka" data-kode="1110">Asuransi Dibayar di Muka (010) - Debit</option>
                        <option value="perlengkapan kantor" data-kode="1111">Perlengkapan Kantor (011) - Debit</option>
                        <option value="biaya dibayar dimuka" data-kode="1112">Biaya Dibayar di Muka (012) - Debit</option>
                        <option value="investasi pendek" data-kode="1113">Investasi Jangka Pendek (013) - Debit</option>
                    </optgroup>

                    <!-- Aset Tetap -->
                    <optgroup label="&nbsp;&nbsp;&nbsp;Aset Tetap (2)">
                        <option value="tanah" data-kode="1201">Tanah (001) - Debit</option>
                        <option value="gedung" data-kode="1202">Gedung/Bangunan (002) - Debit</option>
                        <option value="kendaraan" data-kode="1203">Kendaraan (003) - Debit</option>
                        <option value="mesin" data-kode="1204">Mesin dan Peralatan (004) - Debit</option>
                        <option value="perabotan" data-kode="1205">Perabotan Kantor (005) - Debit</option>
                        <option value="hak paten" data-kode="1206">Hak Paten (006) - Debit</option>
                        <option value="hak cipta" data-kode="1207">Hak Cipta (007) - Debit</option>
                        <option value="goodwill" data-kode="1208">Goodwill (008) - Debit</option>
                        <option value="merek dagang" data-kode="1209">Merek Dagang (009) - Debit</option>
                    </optgroup>
                </optgroup>

                <optgroup label="2. UTANG (KEWAJIBAN) (12)">
                    <!-- Utang Lancar -->
                    <optgroup label="&nbsp;&nbsp;&nbsp;Utang Lancar (1)">
                        <option value="utang usaha" data-kode="2101">Utang Usaha/Dagang (001) - Kredit</option>
                        <option value="utang wesel" data-kode="2102">Utang Wesel (002) - Kredit</option>
                        <option value="utang gaji" data-kode="2103">Utang Gaji (003) - Kredit</option>
                        <option value="utang bunga" data-kode="2104">Utang Bunga (004) - Kredit</option>
                        <option value="utang pajak" data-kode="2105">Utang Pajak (005) - Kredit</option>
                        <option value="utang dividen" data-kode="2106">Utang Dividen (006) - Kredit</option>
                    </optgroup>

                    <!-- Utang Jangka Panjang -->
                    <optgroup label="&nbsp;&nbsp;&nbsp;Utang Jangka Panjang (2)">
                        <option value="utang hipotek" data-kode="2201">Utang Hipotek (001) - Kredit</option>
                        <option value="utang obligasi" data-kode="2202">Utang Obligasi (002) - Kredit</option>
                        <option value="kredit investasi" data-kode="2203">Kredit Investasi (003) - Kredit</option>
                    </optgroup>
                </optgroup>

                <optgroup label="3. MODAL (EKUITAS) (131)">
                    <option value="modal pemilik" data-kode="3101">Modal Pemilik/Modal Disetor (001) - Kredit</option>
                    <option value="modal saham" data-kode="3102">Modal Saham (002) - Kredit</option>
                    <option value="laba ditahan" data-kode="3103">Laba Ditahan (003) - Kredit</option>
                    <option value="dividen" data-kode="3104">Dividen (004) - Kredit</option>
                    <option value="prive" data-kode="3105">Prive (Pengambilan Pribadi) (005) - Kredit</option>
                </optgroup>

                <optgroup label="4. PENDAPATAN (21)">
                    <!-- Pendapatan Operasional -->
                    <optgroup label="&nbsp;&nbsp;&nbsp;Pendapatan Operasional (1)">
                        <option value="pendapatan penjualan" data-kode="4101">Pendapatan Penjualan (001) - Kredit</option>
                        <option value="pendapatan jasa" data-kode="4102">Pendapatan Jasa (002) - Kredit</option>
                    </optgroup>

                    <!-- Pendapatan Non-Operasional -->
                    <optgroup label="&nbsp;&nbsp;&nbsp;Pendapatan Non-Operasional (2)">
                        <option value="pendapatan bunga" data-kode="4201">Pendapatan Bunga (001) - Kredit</option>
                        <option value="pendapatan sewa" data-kode="4202">Pendapatan Sewa (002) - Kredit</option>
                        <option value="pendapatan komisi" data-kode="4203">Pendapatan Komisi (003) - Kredit</option>
                        <option value="pendapatan lain" data-kode="4204">Pendapatan Lain-lain (004) - Kredit</option>
                    </optgroup>
                </optgroup>

                <optgroup label="5. BEBAN (22)">
                    <!-- Beban Operasional -->
                    <optgroup label="&nbsp;&nbsp;&nbsp;Beban Operasional (1)">
                        <option value="beban gaji" data-kode="5101">Beban Gaji (001) - Debit</option>
                        <option value="beban sewa" data-kode="5102">Beban Sewa (002) - Debit</option>
                        <option value="beban utilitas" data-kode="5103">Beban Listrik, Air, dan Telepon (003) - Debit</option>
                        <option value="beban penyusutan" data-kode="5104">Beban Penyusutan (004) - Debit</option>
                        <option value="beban supplies" data-kode="5105">Beban Supplies/Perlengkapan (005) - Debit</option>
                        <option value="beban iklan" data-kode="5106">Beban Iklan/Promosi (006) - Debit</option>
                    </optgroup>

                    <!-- Beban Non-Operasional -->
                    <optgroup label="&nbsp;&nbsp;&nbsp;Beban Non-Operasional (2)">
                        <option value="beban bunga" data-kode="5201">Beban Bunga (001) - Debit</option>
                        <option value="beban lain" data-kode="5202">Beban Lain-lain (002) - Debit</option>
                    </optgroup>
                </optgroup>
                </select>
            </div>

            <!-- Keterangan -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Keterangan <span class="text-red-600">*</span>
                </label>
                <textarea 
                    name="keterangan"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    rows="3"
                    placeholder="Masukkan keterangan uang masuk"
                    required
                ></textarea>
            </div>

            <!-- Total Uang -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Total Uang <span class="text-red-600">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-600">Rp</span>
                    <input 
                        type="text"
                        name="uang_masuk"
                        class="w-full border rounded-lg pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="0"
                        required
                        oninput="formatNumber(this)"
                    >
                </div>
                <p class="text-xs text-gray-500 mt-1">Masukkan angka tanpa tanda titik atau koma</p>
            </div>

            <!-- Input Tersembunyi untuk Kode -->
            <input type="hidden" name="kode" id="kodeInput" value="">

            <!-- Tombol Submit -->
            <div class="flex justify-end space-x-3 pt-4">
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
</div>

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function formatNumber(input) {
    // Hapus semua karakter non-digit
    let value = input.value.replace(/\D/g, '');
    
    // Format angka dengan pemisah ribuan
    if (value !== '') {
        value = new Intl.NumberFormat('id-ID').format(value);
    }
    
    input.value = value;
}

// Handle form submission
document.getElementById('uangMasukForm').addEventListener('submit', function(e) {
    // Jangan prevent default agar form bisa disubmit
    const uangMasukInput = document.querySelector('input[name="uang_masuk"]');
    // Biarkan nilai dengan format untuk diproses di controller
});

// Set default date to today
document.querySelector('input[type="date"]').valueAsDate = new Date();

// Menampilkan notifikasi jika ada pesan sukses atau error
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

function updateTotalUang(kategori) {
    const kodeInput = document.getElementById('kodeInput');
    const selectedOption = document.querySelector(`select[name="kategori"] option:checked`);
    
    if (selectedOption) {
        kodeInput.value = selectedOption.getAttribute('data-kode'); // Ini tidak lagi digunakan, karena kode dihasilkan di server
    }
}
</script>

<style>
/* Animasi fade-in saat halaman dimuat */
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

/* Custom styling untuk input date */
input[type="date"] {
    appearance: none;
    -webkit-appearance: none;
    padding-right: 2rem;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E") no-repeat right 0.75rem center/1.5rem;
}

/* Hover effects */
.form-group input:hover,
.form-group textarea:hover {
    border-color: #93C5FD;
}

/* Button animations */
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
