@extends('Core.Sidebar')

@section('content')
<div class="p-6 animate-fade-in">
    <!-- Header -->
    <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold">Input Uang Masuk</h1>
        <p class="text-sm mt-1">Formulir untuk menambahkan data uang Masuk</p>
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

            <!-- Kategori -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kategori <span class="text-red-600">*</span>
                </label>
                <select 
                    name="kategori"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    required
                    onchange="updateTotalUang(this.value)"
                >
                    <option value="" disabled selected>Pilih Kategori Akun</option>
                    
                    <optgroup label="Akun Aset → Kategori yang mencatat kepemilikan perusahaan">
                        <option value="kas" data-kode="KAS">Kas</option>
                    </optgroup>
                    
                    <optgroup label="Akun Ekuitas → Kategori untuk modal pemilik">
                        <option value="modal pemilik" data-kode="MP">Modal pemilik</option>
                    </optgroup>
                    
                    <optgroup label="Akun Pendapatan → Kategori untuk pemasukan perusahaan">
                        <option value="pendapatan penjualan" data-kode="PP">Pendapatan penjualan</option>
                        <option value="pendapatan jasa" data-kode="PJ">Pendapatan jasa</option>
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
