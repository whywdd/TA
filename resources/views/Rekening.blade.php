@extends('Core.Sidebar')

@section('content')
    @php
        function getAccountTypePHP($kodeAkun) {
            $kode = (string)$kodeAkun;
            if (str_starts_with($kode, '111') || str_starts_with($kode, '112')) {
                return 'AKTIVA';
            } else if (str_starts_with($kode, '121') || str_starts_with($kode, '122') || str_starts_with($kode, '131')) {
                return 'PASIVA';
            } else if (str_starts_with($kode, '241') || str_starts_with($kode, '242')) {
                return 'PENDAPATAN';
            } else if (str_starts_with($kode, '251') || str_starts_with($kode, '252')) {
                return 'BEBAN';
            }
            return 'UNKNOWN';
        }

        function calculateBalancePHP($previousBalance, $debit, $kredit, $accountType) {
            $balance = $previousBalance;
            
            switch($accountType) {
                case 'AKTIVA':
                    // Aktiva: bertambah di debit, berkurang di kredit
                    $balance = $balance + $debit - $kredit;
                    break;
                case 'PASIVA':
                case 'PENDAPATAN':
                    // Pasiva & Pendapatan: bertambah di kredit, berkurang di debit
                    $balance = $balance - $debit + $kredit;
                    break;
                case 'BEBAN':
                    // Beban: bertambah di debit, berkurang di kredit
                    $balance = $balance + $debit - $kredit;
                    break;
                default:
                    $balance = $balance + $debit - $kredit;
            }
            
            return $balance;
        }
    @endphp
    <title>Buku Besar Perusahaan Dagang</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="box p-4 intro-y mt-5">
        <div class="intro-y">
            <!-- Header -->
            <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
                <h1 class="text-2xl font-bold">Buku Besar Perusahaan Dagang</h1>
                <p class="text-sm mt-1">Buku Besar Budivespaendut</p>
                <p class="text-sm">Periode: {{ date('F Y', strtotime($startDate)) }}</p>
            </div>

            <!-- Filter Section -->
            <div class="mb-4">
                <form action="{{ route('rekening.filter') }}" method="GET" class="flex items-center gap-4">
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

            @foreach($groupedLaporan as $kategori => $items)
                @php
                    $kodeAkun = $items->first()->kode ?? '-';
                    $runningBalance = 0;
                    $accountType = substr($kodeAkun, 0, 3);
                @endphp

                <div class="mb-8">
                    <!-- Informasi Akun -->
                    <div class="flex justify-between items-center mb-2 bg-gray-100 p-3 rounded-t-lg">
                        <div>
                            <span class="font-bold">Nama Akun: {{ $kategori }}</span>
                        </div>
                        <div>
                            <span class="font-bold">Kode Akun: {{ $kodeAkun }}</span>
                        </div>
                    </div>

                    <!-- Tabel Transaksi -->
                    <div class="overflow-x-auto">
                        <table class="w-full bg-white border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-4 text-left">Tanggal</th>
                                    <th class="py-3 px-4 text-left">Keterangan</th>
                                    <th class="py-3 px-4 text-center">Ref</th>
                                    <th class="py-3 px-4 text-right">Debit (Rp)</th>
                                    <th class="py-3 px-4 text-right">Kredit (Rp)</th>
                                    <th class="py-3 px-4 text-right">Saldo</th>
                                    <!-- <th class="py-3 px-4 text-center">Aksi</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $runningBalance = 0;
                                    $accountType = getAccountTypePHP($kodeAkun);
                                @endphp
                                @foreach($items->sortBy('Tanggal') as $item)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-4">{{ date('d/m/Y', strtotime($item->Tanggal)) }}</td>
                                        <td class="py-3 px-4">{{ $item->keterangan }}</td>
                                        <td class="py-3 px-4 text-center">-</td>
                                        <td class="py-3 px-4 text-right">
                                            @if($item->debit > 0)
                                                {{ number_format($item->debit, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            @if($item->kredit > 0)
                                                {{ number_format($item->kredit, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            @php
                                                $runningBalance = calculateBalancePHP($runningBalance, $item->debit ?? 0, $item->kredit ?? 0, $accountType);
                                                $displayBalance = $runningBalance < 0 ? '-' . number_format(abs($runningBalance), 0, ',', '.') : number_format($runningBalance, 0, ',', '.');
                                            @endphp
                                            {{ $displayBalance }}
                                        </td>
                                        <!-- <td class="py-3 px-4 text-center">
                                            <div class="flex justify-center space-x-2">
                                                <button class="text-blue-600 hover:text-blue-800" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="hapusData({{ $item->id }})" class="text-red-600 hover:text-red-800" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td> -->
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <!-- <tr class="bg-gray-50 font-bold">
                                    <td colspan="3" class="py-3 px-4 text-right">Total:</td>
                                    <td class="py-3 px-4 text-right">{{ number_format($totals[$kategori]['debit'], 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right">{{ number_format($totals[$kategori]['kredit'], 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right">{{ number_format($totals[$kategori]['saldo'], 0, ',', '.') }}</td>
                                    <td></td>
                                </tr> -->
                            </tfoot>
                        </table>
                    </div>
            <!-- Filter Rows per Page -->
            <div class="flex items-center justify-between mt-4 bg-white p-4 rounded-lg shadow-sm">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-700">Rows per page:</span>
                    <select id="rowsPerPage" class="border rounded-md py-1 px-2 text-sm focus:outline-none focus:ring focus:border-blue-300">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="15">15</option>
                        <option value="all">All</option>
                    </select>
                </div>

                <div class="flex items-center space-x-4">
                    <button id="prevPage" class="text-sm px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">Previous</button>
                    <span id="pageIndicator" class="text-sm text-gray-700">Page 1 of 14</span>
                    <button id="nextPage" class="text-sm px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
                </div>
            </div>
        </div>
                </div>
            @endforeach

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mt-4 mb-4">
                <div class="flex space-x-2">
                    <a href="{{ route('rekening.export-excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn bg-green-500 text-white hover:bg-green-600 px-4 py-2 rounded-md">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('rekening.export-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn bg-red-500 text-white hover:bg-red-600 px-4 py-2 rounded-md">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </a>
                    <button onclick="window.print()" class="btn bg-gray-500 text-white hover:bg-gray-600 px-4 py-2 rounded-md">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .btn, header, footer, .no-print, nav, .aside {
                display: none !important;
            }
            body {
                padding: 20px;
                font-size: 12px;
            }
            .box {
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            h1.text-2xl {
                text-align: center;
                font-size: 20px;
                margin: 10px 0 5px 0;
                font-weight: bold;
            }
            p.text-sm {
                text-align: center;
                margin: 5px 0 20px 0;
                font-size: 12px;
            }
            .account-info {
                background-color: #f2f2f2 !important;
                padding: 8px;
                margin-bottom: 10px;
                border-radius: 4px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                page-break-inside: avoid;
                font-size: 10px !important;
            }
            table, th, td {
                border: 1px solid #000 !important;
            }
            th, td {
                padding: 6px !important;
            }
            th {
                background-color: #f2f2f2 !important;
                font-weight: bold;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .text-right {
                text-align: right !important;
            }
            .text-center {
                text-align: center !important;
            }
            .bg-blue-600 {
                background-color: white !important;
                box-shadow: none !important;
            }
            .mb-8 {
                margin-bottom: 20px !important;
            }
            .page-break {
                page-break-after: always;
            }
        }

        /* Styling untuk pagination */
        .pagination-container {
            background-color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            margin-top: 1rem;
        }

        #rowsPerPage {
            background-color: white;
            border-color: #e2e8f0;
            padding: 0.25rem 2rem 0.25rem 0.5rem;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }

        .pagination-button {
            transition: all 0.2s;
            background-color: #f3f4f6;
            color: #374151;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
        }

        .pagination-button:hover:not(:disabled) {
            background-color: #e5e7eb;
        }

        .pagination-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        #pageIndicator {
            color: #374151;
            font-size: 0.875rem;
            padding: 0 1rem;
        }
    </style>

    <script>
    function getAccountType(kodeAkun) {
        const kode = kodeAkun.toString();
        if (kode.startsWith('111') || kode.startsWith('112')) {
            return 'AKTIVA';
        } else if (kode.startsWith('121') || kode.startsWith('122') || kode.startsWith('131')) {
            return 'PASIVA';
        } else if (kode.startsWith('241') || kode.startsWith('242')) {
            return 'PENDAPATAN';
        } else if (kode.startsWith('251') || kode.startsWith('252')) {
            return 'BEBAN';
        }
        return 'UNKNOWN';
    }

    function calculateBalance(previousBalance, debit, kredit, accountType) {
        let balance = previousBalance;
        
        switch(accountType) {
            case 'AKTIVA':
                // Aktiva: bertambah di debit, berkurang di kredit
                balance = balance + debit - kredit;
                break;
            case 'PASIVA':
            case 'PENDAPATAN':
                // Pasiva & Pendapatan: bertambah di kredit, berkurang di debit
                balance = balance - debit + kredit;
                break;
            case 'BEBAN':
                // Beban: bertambah di debit, berkurang di kredit
                balance = balance + debit - kredit;
                break;
            default:
                balance = balance + debit - kredit;
        }
        
        return balance;
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

    // Pagination Logic
    document.addEventListener('DOMContentLoaded', function() {
        const tables = document.querySelectorAll('table');
        
        tables.forEach(table => {
            const tbody = table.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr');
            const rowsPerPageSelect = table.parentElement.parentElement.querySelector('#rowsPerPage');
            const prevButton = table.parentElement.parentElement.querySelector('#prevPage');
            const nextButton = table.parentElement.parentElement.querySelector('#nextPage');
            const pageIndicator = table.parentElement.parentElement.querySelector('#pageIndicator');
            
            let currentPage = 1;
            let rowsPerPage = parseInt(rowsPerPageSelect.value);
            
            function updatePagination() {
                const totalRows = rows.length;
                const totalPages = rowsPerPage === 'all' ? 1 : Math.ceil(totalRows / rowsPerPage);
                
                // Reset all rows
                rows.forEach(row => {
                    row.classList.remove('active');
                    row.style.display = 'none';
                });
                
                if (rowsPerPage === 'all') {
                    // Show all rows
                    rows.forEach(row => {
                        row.classList.add('active');
                        row.style.display = 'table-row';
                    });
                } else {
                    // Show only rows for current page
                    const start = (currentPage - 1) * rowsPerPage;
                    const end = Math.min(start + rowsPerPage, totalRows);
                    
                    for (let i = start; i < end; i++) {
                        rows[i].classList.add('active');
                        rows[i].style.display = 'table-row';
                    }
                }
                
                // Update buttons state
                prevButton.disabled = currentPage === 1;
                nextButton.disabled = currentPage === totalPages;
                
                // Update page indicator
                if (rowsPerPage === 'all') {
                    pageIndicator.textContent = 'Showing all rows';
                } else {
                    pageIndicator.textContent = `Page ${currentPage} of ${totalPages}`;
                }
            }
            
            // Event Listeners
            rowsPerPageSelect.addEventListener('change', function() {
                rowsPerPage = this.value === 'all' ? 'all' : parseInt(this.value);
                currentPage = 1;
                updatePagination();
            });
            
            prevButton.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    updatePagination();
                }
            });
            
            nextButton.addEventListener('click', function() {
                const totalPages = rowsPerPage === 'all' ? 1 : Math.ceil(rows.length / rowsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    updatePagination();
                }
            });
            
            // Initialize pagination
            updatePagination();
        });
    });
    </script>
@endsection