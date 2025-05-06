@extends('Core.Sidebar')

@section('content')
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
                <h1 class="text-2xl font-bold">Neraca Saldo Perusahaan Dagang</h1>
                <p class="text-sm mt-1">Neraca Saldo Budivespaendut</p>
                <p class="text-sm">Periode: {{ date('F Y', strtotime($startDate)) }}</p>
            </div>

            <!-- Filter Section -->
            <div class="mb-4">
                <form action="{{ route('neracasaldo.filter') }}" method="GET" class="flex items-center gap-4">
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

            <!-- Tabel Transaksi -->
            <div class="overflow-x-auto">
                <table class="w-full bg-white border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-4 text-left">Kode</th>
                            <th class="py-3 px-4 text-left">Nama Akun</th>
                            <th class="py-3 px-4 text-right">Debit</th>
                            <th class="py-3 px-4 text-right">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalDebit = 0;
                            $totalKredit = 0;
                        @endphp
                        @foreach($transaksis as $transaksi)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-4">{{ $transaksi['kode'] }}</td>
                                <td class="py-3 px-4">{{ $transaksi['kategori'] }}</td>
                                <td class="py-3 px-4 text-right">
                                    @php $totalDebit += $transaksi['debit']; @endphp
                                    {{ $transaksi['debit'] != 0 ? ($transaksi['debit'] < 0 ? '-' : '') . number_format(abs($transaksi['debit']), 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @php $totalKredit += $transaksi['kredit']; @endphp
                                    {{ $transaksi['kredit'] != 0 ? ($transaksi['kredit'] < 0 ? '-' : '') . number_format(abs($transaksi['kredit']), 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                        <!-- Total Row -->
                        <tr class="bg-gray-50 font-bold">
                            <td class="py-3 px-4" colspan="2">Total</td>
                            <td class="py-3 px-4 text-right">
                                {{ $totalDebit != 0 ? ($totalDebit < 0 ? '-' : '') . number_format(abs($totalDebit), 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                {{ $totalKredit != 0 ? ($totalKredit < 0 ? '-' : '') . number_format(abs($totalKredit), 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
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
                    <a href="{{ route('neracasaldo.export-excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn bg-green-500 text-white hover:bg-green-600">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('neracasaldo.export-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn bg-red-500 text-white hover:bg-red-600">
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
                    .btn, header, footer, .no-print, nav, .aside, #prevPage, #nextPage, #rowsPerPage {
                        display: none !important;
                    }
                    body {
                        padding: 20px;
                        font-size: 12px;
                        background-color: white !important;
                    }
                    .box, .p-4, .intro-y {
                        padding: 0 !important;
                        margin: 0 !important;
                        background-color: white !important;
                    }
                    .bg-gray-100 {
                        background-color: white !important;
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
                    table {
                        width: 100%;
                        border-collapse: collapse !important;
                        margin-bottom: 20px;
                        font-size: 10px !important;
                    }
                    table th,
                    table td {
                        border: 1px solid #000 !important;
                        padding: 6px !important;
                    }
                    table th {
                        text-align: left;
                        background-color: #f2f2f2 !important;
                        font-weight: bold;
                        -webkit-print-color-adjust: exact;
                        color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    table td:nth-child(3),
                    table td:nth-child(4),
                    table th:nth-child(3),
                    table th:nth-child(4) {
                        text-align: right !important;
                    }
                    table tr:last-child td {
                        font-weight: bold !important;
                        background-color: #f9f9f9 !important;
                        -webkit-print-color-adjust: exact;
                        color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    .bg-blue-600 {
                        background-color: white !important;
                        box-shadow: none !important;
                    }
                }
            </style>
@endsection