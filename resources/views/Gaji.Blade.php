@extends('Core.Sidebar')

@section('content')
<div class="p-6 animate-fade-in">
    <!-- Header -->
    <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold">Gaji Karyawan</h1>
        <p class="text-sm mt-1">Tabel keterangan gaji karyawan</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Filter Keterangan -->
        <div class="flex flex-wrap gap-4 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <label class="mr-2 text-sm font-medium text-gray-600">Nama Karyawan:</label>
                <input type="text" id="filterKeterangan" placeholder="Masukkan nama karyawan" class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onkeyup="filterTable()">
                <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors ml-2">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </div>
    
        <div class="overflow-x-auto">
            <table id="gajiTable" class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="py-2 px-4 border-b text-left">No</th>
                        <th class="py-2 px-4 border-b text-left">Nama Karyawan</th>
                        <th class="py-2 px-4 border-b text-left">Nominal Gaji</th>
                        <th class="py-2 px-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gajiKaryawan as $index => $gaji)
                    <tr>
                        <td class="py-2 px-4 border-b">{{ $index + 1 }}</td>
                        <td class="py-2 px-4 border-b">{{ $gaji->nama_karyawan }}</td>
                        <td class="py-2 px-4 border-b">Rp {{ number_format($gaji->gaji, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <button class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data gaji karyawan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
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
</script>

<style>
/* ... existing styles ... */
</style>
@endsection