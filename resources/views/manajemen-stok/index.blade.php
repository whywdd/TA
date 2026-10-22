@extends('Core.Sidebar')

@section('content')
<div class="p-6 animate-fade-in">
    <!-- Header -->
    <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold">Manajemen Stok</h1>
        <p class="text-sm mt-1">Kelola data persediaan barang</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div class="bg-white rounded-lg shadow-lg p-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full mr-4">
                    <i class="fas fa-boxes text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Barang</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stoks->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full mr-4">
                    <i class="fas fa-cubes text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Stok</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stoks->sum('stok') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-4">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full mr-4">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Stok Rendah</p>
                    <p class="text-xl font-bold text-gray-800">2</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-4">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full mr-4">
                    <i class="fas fa-calculator text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Nilai</p>
                    <p class="text-xl font-bold text-gray-800">Rp 5.250.000</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Header with Search and Add Button -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <input 
                        type="text" 
                        id="searchInput"
                        class="border rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Cari barang..."
                        onkeyup="searchTable()"
                    >
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <a href="{{ route('manajemen-stok.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Barang
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full" id="stokTable">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Kode Barang</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nama Barang</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Kategori</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Satuan</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Stok</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Min. Stok</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Harga Jual</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nilai Persediaan</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($stoks) && count($stoks) > 0)
                        @foreach($stoks as $index => $stok)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $stok->kode_barang ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $stok->nama_barang ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                    {{ $stok->kategori ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $stok->satuan ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs {{ ($stok->stok ?? 0) <= ($stok->reorder_point ?? 0) ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $stok->stok ?? 0 }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $stok->reorder_point ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">Rp {{ number_format($stok->harga_jual ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-green-600">
                                Rp {{ number_format(($stok->stok ?? 0) * ($stok->harga_beli ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('manajemen-stok.edit', $stok->id) }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteStok({{ $stok->id }})" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-box-open text-4xl mb-2"></i>
                                <p>Belum ada data barang</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination (Hanya jika menggunakan Paginator) -->
        {{-- @if(isset($stoks) && method_exists($stoks, 'hasPages') && $stoks->hasPages())
        <div class="mt-4">
            {{ $stoks->links() }}
        </div>
        @endif --}}
    </div>
</div>

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('stokTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                if (td[j].textContent.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? '' : 'none';
    }
}

function deleteStok(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data barang akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Akan dihubungkan dengan controller nanti
            window.location.href = `/manajemen-stok/${id}/delete`;
        }
    });
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
</style>
@endsection
