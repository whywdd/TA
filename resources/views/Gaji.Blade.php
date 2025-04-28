@extends('Core.Sidebar')

@section('content')
<div class="p-6 animate-fade-in">
    <!-- Header -->
    <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold">Gaji Karyawan</h1>
        <p class="text-sm mt-1">Tabel keterangan karyawan</p>
    </div>
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Tombol Tambah dan Filter dalam satu baris -->
        <div class="flex justify-between items-center mb-4">
            <!-- Tombol Tambah Karyawan -->
            <button onclick="window.location.href='{{ route('data-karyawan.index') }}'" 
                    class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Karyawan
            </button>
            
            <!-- Filter Keterangan -->
            <div class="flex items-center">
                <label class="mr-2 text-sm font-medium text-gray-600">Nama Karyawan:</label>
                <input type="text" id="filterKeterangan" 
                       placeholder="Masukkan nama karyawan" 
                       class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       onkeyup="filterTable()">
                <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors ml-2">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </div>
    
        <div class="overflow-x-auto bg-white p-4 rounded-md shadow-md">
    <!-- Table Section -->
    <table id="gajiTable" class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr class="bg-gray-50">
                <th class="py-2 px-4 border-b text-left">No</th>
                <th class="py-2 px-4 border-b text-left">Nama Karyawan</th>
                <th class="py-2 px-4 border-b text-left">Usia</th>
                <th class="py-2 px-4 border-b text-left">Jabatan</th>
                <th class="py-2 px-4 border-b text-left">Nominal Gaji</th>
                <th class="py-2 px-4 border-b text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gajiKaryawan as $index => $gaji)
            <tr>
                <td class="py-2 px-4 border-b">{{ $index + 1 }}</td>
                <td class="py-2 px-4 border-b">{{ $gaji->nama }}</td>
                <td class="py-2 px-4 border-b">{{ $gaji->usia }} tahun</td>
                <td class="py-2 px-4 border-b">{{ $gaji->jabatan }}</td>
                <td class="py-2 px-4 border-b">Rp {{ number_format($gaji->gaji, 0, ',', '.') }}</td>
                <td class="py-3 px-4 text-center">
                    <div class="flex justify-center space-x-2">
                        <a href="{{ route('gaji.edit', $gaji->id) }}" 
                           class="text-blue-600 hover:text-blue-800" 
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteGaji({{ $gaji->id }})" 
                                class="text-red-600 hover:text-red-800" 
                                title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                    Tidak ada data gaji karyawan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination Section -->
<div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center py-5 px-4 bg-white rounded-md shadow-md mt-4">
    <!-- Filter Rows per Page -->
    <div class="flex items-center space-x-2 flex-grow">
        <label for="rowsPerPage" class="text-sm font-medium">Rows per page:</label>
        <select id="rowsPerPage" class="border rounded-md py-3 px-6 ml-1 text-sm focus:outline-none focus:ring focus:border-blue-300">
            <option value="5" selected>5</option>
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="all">All</option>
        </select>
    </div>

    <!-- Pagination Controls -->
    <div class="flex items-center space-x-4 ml-2">
        <button id="prevPage" class="text-sm px-3 py-2 border rounded-md bg-gray-100 hover:bg-gray-200 focus:outline-none">Previous</button>
        <span id="pageIndicator" class="text-sm font-medium">Page 1</span>
        <button id="nextPage" class="text-sm px-3 py-2 border rounded-md bg-gray-100 hover:bg-gray-200 focus:outline-none">Next</button>
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

function filterTable() {
    const input = document.getElementById('filterKeterangan');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('gajiTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) { // Mulai dari 1 untuk melewati header
        const td = tr[i].getElementsByTagName('td')[1]; // Ambil kolom Keterangan
        if (td) {
            const txtValue = td.textContent || td.innerText;
            tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
        }
    }
}

function deleteGaji(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/gaji/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data gaji berhasil dihapus',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal menghapus data gaji',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menghapus data',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}
</script>

<style>
/* ... existing styles ... */
</style>
@endsection