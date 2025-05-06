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
                <label class="block text-gray-700 text-sm font-bold mb-2">Usia</label>
                <input type="number" name="usia" value="{{ $karyawan->usia }}" 
                       class="border rounded w-full py-2 px-3" required>
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

// Form submission handling
document.getElementById('editGajiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validasi form di sini jika diperlukan
    const nama = document.querySelector('input[name="nama"]').value;
    const usia = document.querySelector('input[name="usia"]').value;
    const jabatan = document.querySelector('input[name="jabatan"]').value;
    const gaji = document.querySelector('input[name="gaji"]').value;

    if (!nama || !usia || !jabatan || !gaji) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Semua field harus diisi!',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Submit form jika validasi berhasil
    this.submit();
});
</script>
@endsection 