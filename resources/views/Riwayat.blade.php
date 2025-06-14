@extends('Core.Sidebar')
@section('content')
    <title>Riwayat Aktivitas</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <style>
        /* Gradient Background */
        body {
            background: #f3f4f6;
        }

        /* Card Styling */
        .box {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Table Styling */
        .print-table {
            border-collapse: collapse;
            width: 100%;
        }

        .print-table th {
            background-color: #f9fafb;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .print-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .print-table tbody tr:hover {
            background-color: #f9fafb;
            transition: all 0.3s ease;
        }

        /* Filter Controls */
        .filter-controls {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-input {
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 0.5rem;
            min-width: 200px;
        }

        .filter-input:focus {
            outline: none;
            border-color: #3b82f6;
            ring: 2px;
            ring-color: #93c5fd;
        }

        /* Action Badge */
        .action-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .action-delete {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .action-edit {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .action-create {
            background-color: #dcfce7;
            color: #16a34a;
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .filter-controls {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .filter-input {
                width: 100%;
            }
        }

        /* Keterangan Styling */
        .keterangan {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .keterangan:hover {
            white-space: normal;
            overflow: visible;
            position: relative;
            z-index: 1;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 0.5rem;
            border-radius: 0.25rem;
        }

        /* Pagination Controls */
        .pagination-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-top: 1rem;
        }

        .pagination-controls button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-controls button:hover:not(:disabled) {
            background-color: #e5e7eb;
        }

        .rows-per-page {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .rows-per-page select {
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 0.5rem;
            font-size: 0.875rem;
        }

        .rows-per-page select:focus {
            outline: none;
            border-color: #3b82f6;
            ring: 2px;
            ring-color: #93c5fd;
        }
    </style>

    <div class="box p-4 intro-y mt-5">
        <div class="intro-y">
            <!-- Header -->
            <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
                <h1 class="text-2xl font-bold">Riwayat Aktivitas</h1>
                <p class="text-sm mt-1">Halaman ini menampilkan riwayat aktivitas pengguna dalam sistem.</p>
            </div>

            <!-- Filter -->
            <div class="filter-controls">
                <div class="filter-group">
                    <label for="searchRiwayat" class="font-medium">Cari:</label>
                    <input type="text" id="searchRiwayat" class="filter-input" placeholder="Cari nama/aksi...">
                </div>

                <div class="filter-group">
                    <label for="dateFilter" class="font-medium">Tanggal:</label>
                    <input type="date" id="dateFilter" class="filter-input">
                </div>
            </div>

            <!-- Tabel Riwayat -->
            <div class="overflow-x-auto">
                <table class="print-table">
                    <thead>
                        <tr>
                            <th class="text-left">No</th>
                            <th class="text-left">Nama User</th>
                            <th class="text-left">Tanggal</th>
                            <th class="text-left">Waktu (WIB)</th>
                            <th class="text-left">Aksi</th>
                            <th class="text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayat as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama_user }}</td>
                                <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->waktu)->setTimezone('Asia/Jakarta')->format('H:i:s') }} WIB</td>
                                <td>
                                    <span class="action-badge 
                                        @if(str_contains(strtolower($item->aksi), 'hapus')) action-delete
                                        @elseif(str_contains(strtolower($item->aksi), 'edit')) action-edit
                                        @else action-create
                                        @endif">
                                        {{ $item->aksi }}
                                    </span>
                                </td>
                                <td class="keterangan" title="{{ $item->keterangan }}">{{ $item->keterangan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
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
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchRiwayat');
            const dateFilter = document.getElementById('dateFilter');
            const tableRows = document.querySelectorAll('tbody tr');
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
                Array.from(tableRows).forEach((row, index) => {
                    if (index >= start && index < end) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Update page indicator
                const totalPages = Math.ceil(tableRows.length / rowsPerPage);
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
            
            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedDate = dateFilter.value;
                
                tableRows.forEach(row => {
                    const namaUser = row.cells[1].textContent.toLowerCase();
                    const aksi = row.cells[4].textContent.toLowerCase();
                    const tanggal = row.cells[2].textContent;
                    const rowDate = tanggal.split('/').reverse().join('-');
                    
                    const matchesSearch = namaUser.includes(searchTerm) || aksi.includes(searchTerm);
                    const matchesDate = !selectedDate || rowDate === selectedDate;
                    
                    row.style.display = matchesSearch && matchesDate ? '' : 'none';
                });
                
                // Reset ke halaman pertama setelah filter
                currentPage = 1;
                displayTableRows();
            }
            
            // Event listener untuk rows per page
            rowsPerPageSelect.addEventListener('change', function() {
                if (this.value === 'all') {
                    rowsPerPage = tableRows.length;
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
                if (currentPage < Math.ceil(tableRows.length / rowsPerPage)) {
                    currentPage++;
                    displayTableRows();
                }
            });
            
            // Event listener untuk pencarian dan filter tanggal
            searchInput.addEventListener('input', filterTable);
            dateFilter.addEventListener('change', filterTable);
            
            // Load saved preference dari localStorage
            const savedRowsPerPage = localStorage.getItem('preferredRowsPerPage');
            if (savedRowsPerPage) {
                rowsPerPageSelect.value = savedRowsPerPage;
                if (savedRowsPerPage === 'all') {
                    rowsPerPage = tableRows.length;
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
    </script>
@endsection
