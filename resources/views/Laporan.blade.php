@extends('Core.Sidebar')

@section('content')
    <title>Laporan Keuangan Akuntansi</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Include the same CSS as before -->
    <style>
        /* Previous CSS styles remain the same */
    </style>    
</head>
<body class="bg-gray-100">
    <div class="box p-4 intro-y mt-5">
        <div class="intro-y">
            <!-- Header -->
            <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
                <h1 class="text-2xl font-bold">Jurnal Umum</h1>
                <p class="text-sm mt-1">jurnal umum Budivespaendut.</p>
                <p class="text-sm">Periode: {{ date('F Y', strtotime($startDate)) }}</p>
            </div>

            <!-- Filter Section -->
            <div class="mb-4">
                <form action="{{ route('laporan.filter') }}" method="GET" class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span>Tanggal Awal</span>
                        <input type="date" name="start_date" class="border rounded px-2 py-1" value="{{ $startDate ?? '' }}">
                    </div>
                    <div class="flex items-center gap-2">
                        <span>Tanggal Akhir</span>
                        <input type="date" name="end_date" class="border rounded px-2 py-1" value="{{ $endDate ?? '' }}">
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">Filter</button>
                </form>
            </div>

            <!-- Summary Cards
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-gray-500 text-sm">Total Pendapatan</h3>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalUangMasuk, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-400">Data realtime keuangan</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-gray-500 text-sm">Total Pengeluaran</h3>
                    <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalKredit, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-400">Data realtime keuangan</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-gray-500 text-sm">Laba Bersih</h3>
                    <p class="text-2xl font-bold text-blue-600">Rp {{ number_format(($saldo > 0) ? $saldo : 0, 0, ',', '.') }}</p>
                    
                    @if(isset($persentasePeningkatan))
                        <p class="text-sm {{ $persentasePeningkatan >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $persentasePeningkatan >= 0 ? '+' : '' }}{{ number_format($persentasePeningkatan, 0) }}% dari bulan lalu
                        </p>
                    @else
                        <p class="text-sm text-gray-400">Data realtime keuangan</p>
                    @endif
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-gray-500 text-sm">Rugi</h3>
                    <p class="text-2xl font-bold text-gray-700">Rp {{ number_format(($saldo < 0) ? $saldo : 0, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-400">Data realtime keuangan</p>
                </div>
            </div> -->

            <!-- Enhanced Filter Section -->
            <!-- <div class="flex flex-wrap gap-4 px-6 py-4 border-b border-gray-200">
                <form id="filterForm" class="flex flex-wrap gap-4 w-full" onsubmit="applyFilter(event)">
                    <div class="flex items-center">
                        <label for="periode" class="mr-2 text-sm font-medium text-gray-600">Periode:</label>
                        <select id="periode" name="periode" class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="handlePeriodeChange()">
                            <option value="daily">Harian</option>
                            <option value="weekly">Mingguan</option>
                            <option value="monthly" selected>Bulanan</option>
                            <option value="yearly">Tahunan</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <label for="startDate" class="mr-2 text-sm font-medium text-gray-600">Dari:</label>
                        <input type="date" id="startDate" name="startDate" class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center">
                        <label for="endDate" class="mr-2 text-sm font-medium text-gray-600">Sampai:</label>
                        <input type="date" id="endDate" name="endDate" class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center">
                        <label for="category" class="mr-2 text-sm font-medium text-gray-600">Kategori:</label>
                        <select id="category" name="category" class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            <option value="penjualan">Penjualan</option>
                            <option value="pembelian">Pembelian</option>
                            <option value="gaji">Gaji & Upah</option>
                            <option value="operasional">Biaya Operasional</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors" onclick="resetFilter()">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </button>
                </form>
            </div> -->

            <!-- Enhanced Table -->
            <div class="overflow-x-auto">
                <table class="w-full bg-white border border-gray-300 print-table">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-left">Tanggal</th>
                            <th class="py-3 px-4 text-left">Kode Akun</th>
                            <th class="py-3 px-4 text-left">Nama Akun</th>
                            <th class="py-3 px-4 text-left">Keterangan</th>
                            <th class="py-3 px-4 text-right">Debit</th>
                            <th class="py-3 px-4 text-right">Kredit</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @php 
                            $no = 1;
                            $totalDebitTable = 0;
                            $totalKreditTable = 0;
                        @endphp
                        
                        @foreach($laporan as $item)
                            @php
                                // Cek apakah semua nilai adalah debit
                                $allDebit = true;
                                $debitValues = [
                                    $item->uang_masuk,
                                    $item->uang_masuk2,
                                    $item->uang_masuk3,
                                    $item->uang_masuk4,
                                    $item->uang_masuk5
                                ];
                                
                                // Hitung total debit yang valid (tidak null dan lebih dari 0)
                                $validDebitValues = array_filter($debitValues, function($value) {
                                    return $value !== null && $value > 0;
                                });
                                
                                // Jika ada nilai di uang_keluar, berarti bukan semua debit
                                if ($item->uang_keluar > 0 || 
                                    ($item->uang_keluar2 ?? 0) > 0 || 
                                    ($item->uang_keluar3 ?? 0) > 0 || 
                                    ($item->uang_keluar4 ?? 0) > 0 || 
                                    ($item->uang_keluar5 ?? 0) > 0) {
                                    $allDebit = false;
                                }
                                
                                // Hitung total untuk baris ini
                                $rowDebit = 0;
                                $rowKredit = 0;
                                
                                if ($allDebit) {
                                    // Jika semua debit, ambil nilai terakhir untuk kredit
                                    $lastDebitValue = end($validDebitValues);
                                    $firstDebitValue = reset($validDebitValues);
                                    
                                    if (count($validDebitValues) > 1) {
                                        // Jika ada lebih dari satu nilai debit
                                        $rowDebit = $firstDebitValue;
                                        $rowKredit = $lastDebitValue;
                                    } else {
                                        // Jika hanya ada satu nilai debit
                                        $rowDebit = $firstDebitValue;
                                        $rowKredit = $firstDebitValue;
                                    }
                                } else {
                                    // Jika ada kredit, hitung normal
                                    $rowDebit = $item->uang_masuk + 
                                              ($item->uang_masuk2 ?? 0) + 
                                              ($item->uang_masuk3 ?? 0) + 
                                              ($item->uang_masuk4 ?? 0) + 
                                              ($item->uang_masuk5 ?? 0);
                                              
                                    $rowKredit = $item->uang_keluar + 
                                               ($item->uang_keluar2 ?? 0) + 
                                               ($item->uang_keluar3 ?? 0) + 
                                               ($item->uang_keluar4 ?? 0) + 
                                               ($item->uang_keluar5 ?? 0);
                                }
                                
                                // Update total untuk tabel
                                $totalDebitTable += $rowDebit;
                                $totalKreditTable += $rowKredit;
                            @endphp
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4">{{ $no++ }}</td>
                                <td class="py-3 px-4">{{ date('d/m/Y', strtotime($item->Tanggal)) }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex flex-col">
                                        <span>{{ $item->kode }}</span>
                                        @if(isset($item->kode2))
                                            <span class="text-sm text-gray-500">{{ $item->kode2 }}</span>
                                        @endif
                                        @if(isset($item->kode3))
                                            <span class="text-sm text-gray-500">{{ $item->kode3 }}</span>
                                        @endif
                                        @if(isset($item->kode4))
                                            <span class="text-sm text-gray-500">{{ $item->kode4 }}</span>
                                        @endif
                                        @if(isset($item->kode5))
                                            <span class="text-sm text-gray-500">{{ $item->kode5 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex flex-col">
                                        @if($item->uang_masuk > 0)
                                            <span>{{ $item->kategori }}</span>
                                        @else
                                            <span class="ml-8">{{ $item->kategori }}</span>
                                        @endif
                                        @if(isset($item->kategori2))
                                            <span class="text-sm text-gray-500 ml-8">{{ $item->kategori2 }}</span>
                                        @endif
                                        @if(isset($item->kategori3))
                                            <span class="text-sm text-gray-500 ml-8">{{ $item->kategori3 }}</span>
                                        @endif
                                        @if(isset($item->kategori4))
                                            <span class="text-sm text-gray-500 ml-8">{{ $item->kategori4 }}</span>
                                        @endif
                                        @if(isset($item->kategori5))
                                            <span class="text-sm text-gray-500 ml-8">{{ $item->kategori5 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">{{ $item->keterangan }} {{ $item->nama_karyawan }}</td>
                                <td class="py-3 px-4 text-right text-green-600">
                                    <div class="flex flex-col">
                                        @if($item->uang_masuk > 0)
                                            <span>Rp {{ number_format($item->uang_masuk, 0, ',', '.') }}</span>
                                        @else
                                            <span>-</span>
                                        @endif
                                        @if($allDebit)
                                            {{-- Jika semua debit, tampilkan semua kecuali yang terakhir --}}
                                            @if(isset($item->kategori2) && isset($item->uang_masuk2) && $item->uang_masuk2 > 0 && count($validDebitValues) > 2)
                                                <span class="text-sm">Rp {{ number_format($item->uang_masuk2, 0, ',', '.') }}</span>
                                            @endif
                                            @if(isset($item->kategori3) && isset($item->uang_masuk3) && $item->uang_masuk3 > 0 && count($validDebitValues) > 3)
                                                <span class="text-sm">Rp {{ number_format($item->uang_masuk3, 0, ',', '.') }}</span>
                                            @endif
                                            @if(isset($item->kategori4) && isset($item->uang_masuk4) && $item->uang_masuk4 > 0 && count($validDebitValues) > 4)
                                                <span class="text-sm">Rp {{ number_format($item->uang_masuk4, 0, ',', '.') }}</span>
                                            @endif
                                        @else
                                            {{-- Tampilkan normal jika ada kredit --}}
                                            @if(isset($item->kategori2) && isset($item->uang_masuk2) && $item->uang_masuk2 > 0)
                                                <span class="text-sm">Rp {{ number_format($item->uang_masuk2, 0, ',', '.') }}</span>
                                            @endif
                                            @if(isset($item->kategori3) && isset($item->uang_masuk3) && $item->uang_masuk3 > 0)
                                                <span class="text-sm">Rp {{ number_format($item->uang_masuk3, 0, ',', '.') }}</span>
                                            @endif
                                            @if(isset($item->kategori4) && isset($item->uang_masuk4) && $item->uang_masuk4 > 0)
                                                <span class="text-sm">Rp {{ number_format($item->uang_masuk4, 0, ',', '.') }}</span>
                                            @endif
                                            @if(isset($item->kategori5) && isset($item->uang_masuk5) && $item->uang_masuk5 > 0)
                                                <span class="text-sm">Rp {{ number_format($item->uang_masuk5, 0, ',', '.') }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right text-red-600">
                                    <div class="flex flex-col">
                                        @if($allDebit)
                                            {{-- Jika semua debit, tampilkan nilai terakhir di kredit --}}
                                            @if(count($validDebitValues) > 1)
                                                @if(isset($item->uang_masuk5) && $item->uang_masuk5 > 0)
                                                    <span>Rp {{ number_format($item->uang_masuk5, 0, ',', '.') }}</span>
                                                @elseif(isset($item->uang_masuk4) && $item->uang_masuk4 > 0)
                                                    <span>Rp {{ number_format($item->uang_masuk4, 0, ',', '.') }}</span>
                                                @elseif(isset($item->uang_masuk3) && $item->uang_masuk3 > 0)
                                                    <span>Rp {{ number_format($item->uang_masuk3, 0, ',', '.') }}</span>
                                                @elseif(isset($item->uang_masuk2) && $item->uang_masuk2 > 0)
                                                    <span>Rp {{ number_format($item->uang_masuk2, 0, ',', '.') }}</span>
                                                @endif
                                            @else
                                                <span>-</span>
                                            @endif
                                        @else
                                            {{-- Tampilkan normal jika ada kredit --}}
                                            @if($item->uang_keluar > 0)
                                                <span>Rp {{ number_format($item->uang_keluar, 0, ',', '.') }}</span>
                                            @else
                                                <span>-</span>
                                            @endif
                                            @if(isset($item->kategori2) && isset($item->uang_keluar2) && $item->uang_keluar2 > 0)
                                                <span class="text-sm">Rp {{ number_format($item->uang_keluar2, 0, ',', '.') }}</span>
                                            @endif
                                            @if(isset($item->kategori3) && isset($item->uang_keluar3) && $item->uang_keluar3 > 0)
                                                <span class="text-sm">Rp {{ number_format($item->uang_keluar3, 0, ',', '.') }}</span>
                                            @endif
                                            @if(isset($item->kategori4) && isset($item->uang_keluar4) && $item->uang_keluar4 > 0)
                                                <span class="text-sm">Rp {{ number_format($item->uang_keluar4, 0, ',', '.') }}</span>
                                            @endif
                                            @if(isset($item->kategori5) && isset($item->uang_keluar5) && $item->uang_keluar5 > 0)
                                                <span class="text-sm">Rp {{ number_format($item->uang_keluar5, 0, ',', '.') }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <!-- <button class="text-blue-600 hover:text-blue-800" title="Edit">
                                            <i class="fas fa-edit"></i> -->
                                        </button>
                                        <button onclick="hapusData({{ $item->id }})" class="text-red-600 hover:text-red-800" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-bold">
                            <td colspan="5" class="py-3 px-4 text-right">Total:</td>
                            <td class="py-3 px-4 text-right text-green-600">Rp {{ number_format($totalDebitTable, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-red-600">Rp {{ number_format($totalKreditTable, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center py-5 px-4 bg-white rounded-md shadow-md">
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

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mt-4 mb-4">
                <div class="flex space-x-2">
                    <a href="{{ route('laporan.export-excel') }}" class="btn bg-green-500 text-white hover:bg-green-600">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('laporan.export-pdf') }}" class="btn bg-red-500 text-white hover:bg-red-600">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </a>
                    <button onclick="window.print()" class="btn bg-gray-500 text-white hover:bg-gray-600">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>

            <!-- Style untuk print -->
            <style>
                @media print {
                    .btn, header, footer, .no-print {
                        display: none !important;
                    }
                    body {
                        padding: 20px;
                        font-size: 14px;
                    }
                    .print-table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    .print-table th,
                    .print-table td {
                        border: 1px solid #000;
                        padding: 8px;
                    }
                }
            </style>

            <!-- Pagination (same as before) -->
            <!-- Modal (same as before) -->
            <!-- JavaScript (same as before) -->
        </div>
    </div>

<script>
// Fungsi pagination yang sudah ada (jika ada)...

document.addEventListener('DOMContentLoaded', function() {
    const table = document.querySelector('table');
    const tbody = table.querySelector('tbody');
    const rows = tbody.getElementsByTagName('tr');
    const rowsPerPageSelect = document.getElementById('rowsPerPage');
    const prevButton = document.getElementById('prevPage');
    const nextButton = document.getElementById('nextPage');
    const pageIndicator = document.getElementById('pageIndicator');
    
    let currentPage = 1;
    let rowsPerPage = parseInt(rowsPerPageSelect.value);
    
    function displayTableRows() {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        let visibleRows = 0;
        
        // Sembunyikan semua baris terlebih dahulu
        Array.from(rows).forEach((row, index) => {
            if (index >= start && index < end) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update page indicator
        const totalPages = Math.ceil(rows.length / rowsPerPage);
        pageIndicator.textContent = `Page ${currentPage} of ${totalPages}`;
        
        // Update button states
        prevButton.disabled = currentPage === 1;
        nextButton.disabled = currentPage >= totalPages;
        
        // Tambahkan class untuk styling button disabled
        if (prevButton.disabled) {
            prevButton.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            prevButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        
        if (nextButton.disabled) {
            nextButton.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    
    // Event listener untuk rows per page
    rowsPerPageSelect.addEventListener('change', function() {
        if (this.value === 'all') {
            rowsPerPage = rows.length;
        } else {
            rowsPerPage = parseInt(this.value);
        }
        currentPage = 1;
        displayTableRows();
        
        // Simpan preferensi di localStorage
        localStorage.setItem('preferredRowsPerPage', this.value);
    });
    
    // Event listener untuk previous button
    prevButton.addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            displayTableRows();
        }
    });
    
    // Event listener untuk next button
    nextButton.addEventListener('click', function() {
        if (currentPage < Math.ceil(rows.length / rowsPerPage)) {
            currentPage++;
            displayTableRows();
        }
    });
    
    // Load saved preference dari localStorage
    const savedRowsPerPage = localStorage.getItem('preferredRowsPerPage');
    if (savedRowsPerPage) {
        rowsPerPageSelect.value = savedRowsPerPage;
        if (savedRowsPerPage === 'all') {
            rowsPerPage = rows.length;
        } else {
            rowsPerPage = parseInt(savedRowsPerPage);
        }
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft' && !prevButton.disabled) {
            prevButton.click();
        } else if (e.key === 'ArrowRight' && !nextButton.disabled) {
            nextButton.click();
        }
    });
    
    // Initial display
    displayTableRows();
});

// Tambahkan ini ke dalam script yang sudah ada
function updateTable() {
    // Panggil displayTableRows setelah filter diterapkan
    displayTableRows();
}

// Update fungsi applyFilter yang sudah ada
function applyFilter(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('filterForm'));
    const queryString = new URLSearchParams(formData).toString();
    
    // Tambahkan callback untuk update table setelah filter
    fetch(`${window.location.pathname}?${queryString}`)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTbody = doc.querySelector('tbody');
            document.querySelector('tbody').innerHTML = newTbody.innerHTML;
            updateTable();
        });
}

function hapusData(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        fetch(`/laporan/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data berhasil dihapus');
                location.reload();
            } else {
                alert('Gagal menghapus data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
        });
    }
}
</script>
</body>
@endsection