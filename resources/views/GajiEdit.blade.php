@extends('Core.Sidebar')

@section('content')
<div class="p-6 animate-fade-in">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold">Edit Data Karyawan</h1>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form id="editGajiForm" action="{{ route('gaji.update', $karyawan->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Karyawan</label>
                <input type="text" name="nama" value="{{ $karyawan->nama }}" 
                       class="border rounded w-full py-2 px-3" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" 
                       value="{{ $karyawan->tanggal_lahir }}" 
                       class="border rounded w-full py-2 px-3" 
                       required
                       max="{{ date('Y-m-d') }}"
                       onchange="hitungUsia(this.value)">
                <div class="mt-2">
                    <span class="text-sm font-medium text-gray-700">Usia: </span>
                    <span id="usia" class="text-sm text-gray-600">{{ Carbon\Carbon::createFromTimestamp($karyawan->tanggal_lahir)->age }}</span>
                    <span class="text-sm text-gray-600">tahun</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Usia minimal 17 tahun dan maksimal 65 tahun</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Jabatan</label>
                <input type="text" name="jabatan" value="{{ $karyawan->jabatan }}" 
                       class="border rounded w-full py-2 px-3" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Gaji</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-600">Rp</span>
                    <input type="text" name="gaji" value="{{ number_format($karyawan->gaji, 0, ',', '.') }}" 
                           class="border rounded w-full py-2 pl-10 pr-3" required
                           oninput="formatNumber(this)">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="window.history.back()" 
                        class="bg-gray-500 text-white px-4 py-2 rounded mr-2">
                    Batal
                </button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Notifikasi SweetAlert2
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

// Form submission handling
document.getElementById('editGajiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validasi form
    const nama = document.querySelector('input[name="nama"]').value;
    const tanggalLahir = document.querySelector('input[name="tanggal_lahir"]').value;
    const jabatan = document.querySelector('input[name="jabatan"]').value;
    const gaji = document.querySelector('input[name="gaji"]').value;

    if (!nama || !tanggalLahir || !jabatan || !gaji) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Semua field harus diisi!',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Validasi usia
    const usia = parseInt(document.getElementById('usia').textContent);
    if (isNaN(usia) || usia < 17 || usia > 65) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Usia harus antara 17-65 tahun',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Submit form jika validasi berhasil
    this.submit();
});
</script>
@endsection 