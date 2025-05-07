@extends('Core.Sidebar')

@section('content')
    <title>Laporan Laba Rugi</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- <div class="p-4"> -->
        <!-- Header Controls -->
        <!-- <form action="{{ route('labarugi.filter') }}" method="GET" class="flex items-center gap-4 mb-4">
            <div class="flex items-center gap-2">
                <span>Tanggal Awal</span>
                <input type="date" name="start_date" class="border rounded px-2 py-1" value="{{ $startDate }}">
            </div>
            <div class="flex items-center gap-2">
                <span>Tanggal Akhir</span>
                <input type="date" name="end_date" class="border rounded px-2 py-1" value="{{ $endDate }}">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">Filter</button>
        </form> -->

        <div class="box p-4 intro-y mt-5">
            <div class="intro-y">
                <!-- Header -->
                <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
                    <h1 class="text-2xl font-bold">Laporan Laba Rugi</h1>
                    <p class="text-sm mt-1">Budivespaendut</p>
                    <p class="text-sm">Periode: {{ date('F Y', strtotime($startDate)) }}</p>
                </div>

            <div class="p-4">
        <!-- Header Controls -->
        <form action="{{ route('labarugi.filter') }}" method="GET" class="flex items-center gap-4 mb-4">
            <div class="flex items-center gap-2">
                <span>Tanggal Awal</span>
                <input type="date" name="start_date" class="border rounded px-2 py-1" value="{{ $startDate }}">
            </div>
            <div class="flex items-center gap-2">
                <span>Tanggal Akhir</span>
                <input type="date" name="end_date" class="border rounded px-2 py-1" value="{{ $endDate }}">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">Filter</button>
        </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <tr class="bg-purple-600 text-white">
                        <th class="text-left p-2 border border-gray-300">Keterangan</th>
                        <th class="text-right p-2 border border-gray-300">Nominal</th>
                    </tr>

                    <!-- Pendapatan -->
                    <tr class="bg-gray-100">
                        <td class="p-2 border border-gray-300 font-bold" colspan="2">Pendapatan</td>
                    </tr>
                    @foreach($pendapatan as $item)
                    <tr class="border-b border-gray-300">
                        <td class="p-2 border border-gray-300 pl-8">{{ ucwords($item['kategori']) }}</td>
                        <td class="text-right p-2 border border-gray-300">
                            {{ number_format(abs($item['nominal']), 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                    <tr class="border-b border-gray-300 font-semibold bg-gray-50">
                        <td class="p-2 border border-gray-300">Total Pendapatan</td>
                        <td class="text-right p-2 border border-gray-300">{{ number_format(abs($total_pendapatan), 0, ',', '.') }}</td>
                    </tr>

                    <!-- Beban -->
                    <tr class="bg-gray-100">
                        <td class="p-2 border border-gray-300 font-bold" colspan="2">Beban</td>
                    </tr>
                    @foreach($beban as $item)
                    <tr class="border-b border-gray-300">
                        <td class="p-2 border border-gray-300 pl-8">{{ ucwords($item['kategori']) }}</td>
                        <td class="text-right p-2 border border-gray-300">
                            {{ number_format(abs($item['nominal']), 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                    <tr class="border-b border-gray-300 font-semibold bg-gray-50">
                        <td class="p-2 border border-gray-300">Total Beban</td>
                        <td class="text-right p-2 border border-gray-300">{{ number_format($total_beban, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Laba/Rugi -->
                    <tr class="bg-purple-100">
                        <td class="p-2 border border-gray-300 font-bold">Laba/Rugi Bersih</td>
                        <td class="text-right p-2 border border-gray-300 font-bold">
                            @php
                                $labaRugi = -($total_pendapatan) - $total_beban;
                            @endphp
                            @if($labaRugi < 0)
                                -Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
                            @else
                                Rp {{ number_format($labaRugi, 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mt-4 mb-4">
                <div class="flex space-x-2">
                    <a href="{{ route('labarugi.export-excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn bg-green-500 text-white hover:bg-green-600 px-4 py-2 rounded">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('labarugi.export-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn bg-red-500 text-white hover:bg-red-600 px-4 py-2 rounded">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </a>
                    <button onclick="window.print()" class="btn bg-gray-500 text-white hover:bg-gray-600 px-4 py-2 rounded">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>

            <!-- Style untuk print -->
            <style>
                @media print {
                    .btn, header, footer, .no-print, nav, .aside {
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
                    }
                    table, th, td {
                        border: 1px solid #000 !important;
                    }
                    th {
                        background-color: #6b46c1 !important;
                        color: white !important;
                        -webkit-print-color-adjust: exact;
                        color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    .text-right {
                        text-align: right !important;
                    }
                    .bg-purple-100 {
                        background-color: #f3e8ff !important;
                        -webkit-print-color-adjust: exact;
                        color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    .bg-gray-100 {
                        background-color: #f3f4f6 !important;
                        -webkit-print-color-adjust: exact;
                        color-adjust: exact;
                        print-color-adjust: exact;
                    }
                }
            </style>
        </div>
    </div>
@endsection
