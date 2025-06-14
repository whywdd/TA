@extends('Core.Sidebar')

@section('content')
<div class="p-6 animate-fade-in">
    <!-- Header -->
    <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold">Tambah Data Karyawan</h1>
        <p class="text-sm mt-1">Formulir untuk menambahkan data karyawan</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form id="karyawanForm" action="{{ route('karyawan.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- Nama Karyawan -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Karyawan <span class="text-red-600">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama" 
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan nama karyawan"
                    required
                >
            </div>

            <!-- Tanggal Lahir -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Lahir <span class="text-red-600">*</span>
                </label>
                <input 
                    type="date" 
                    name="tanggal_lahir"
                    id="tanggal_lahir"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                    max="{{ date('Y-m-d') }}"
                    onchange="hitungUsia(this.value)"
                >
                <div class="mt-2">
                    <span class="text-sm font-medium text-gray-700">Usia: </span>
                    <span id="usia" class="text-sm text-gray-600">-</span>
                    <span class="text-sm text-gray-600">tahun</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Usia minimal 17 tahun dan maksimal 65 tahun</p>
            </div>

            <!-- Jabatan -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Jabatan <span class="text-red-600">*</span>
                </label>
                <input 
                    type="text" 
                    name="jabatan"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan jabatan"
                    required
                >
            </div>

            <!-- Gaji -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Gaji <span class="text-red-600">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-600">Rp</span>
                    <input 
                        type="text" 
                        name="gaji"
                        class="w-full border rounded-lg pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="0"
                        required
                        oninput="formatNumber(this)"
                    >
                </div>
                <p class="text-xs text-gray-500 mt-1">Masukkan gaji tanpa tanda titik atau koma</p>
            </div>

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

function hitungUsia(tanggalLahir) {
    const today = new Date();
    const birthDate = new Date(tanggalLahir);
    
    let usia = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        usia--;
    }
    
    // Validasi usia
    if (usia < 17) {
        Swal.fire({
            icon: 'error',
            title: 'Usia Tidak Memenuhi Syarat',
            text: 'Usia minimal harus 17 tahun',
            confirmButtonText: 'OK'
        });
        document.getElementById('tanggal_lahir').value = '';
        document.getElementById('usia').textContent = '-';
        return;
    }
    
    if (usia > 65) {
        Swal.fire({
            icon: 'error',
            title: 'Usia Tidak Memenuhi Syarat',
            text: 'Usia maksimal harus 65 tahun',
            confirmButtonText: 'OK'
        });
        document.getElementById('tanggal_lahir').value = '';
        document.getElementById('usia').textContent = '-';
        return;
    }
    
    document.getElementById('usia').textContent = usia;
}

// Tambahkan validasi form sebelum submit
document.getElementById('karyawanForm').addEventListener('submit', function(e) {
    const tanggalLahir = document.getElementById('tanggal_lahir').value;
    if (!tanggalLahir) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Silakan isi tanggal lahir',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    const usia = parseInt(document.getElementById('usia').textContent);
    if (isNaN(usia) || usia < 17 || usia > 65) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Usia harus antara 17-65 tahun',
            confirmButtonText: 'OK'
        });
        return;
    }
});

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
</script>

<style>
/* Animasi dan styling yang sudah ada tetap sama */
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

/* Hover effects */
.form-group input:hover,
.form-group select:hover {
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