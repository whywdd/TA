@extends('Core.Sidebar')

@section('content')
    <title>Laporan Keuangan Akuntansi</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
                <h1 class="text-2xl font-bold">Laporan Keuangan</h1>
                <p class="text-sm mt-1">Halaman ini menampilkan semua laporan keuangan.</p>
            </div>

            <!-- Summary Cards -->
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
            </div>

            <!-- Enhanced Filter Section -->
            <div class="flex flex-wrap gap-4 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <label class="mr-2 text-sm font-medium text-gray-600">Periode:</label>
                    <select class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="daily">Harian</option>
                        <option value="weekly">Mingguan</option>
                        <option value="monthly" selected>Bulanan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="mr-2 text-sm font-medium text-gray-600">Dari:</label>
                    <input type="date" class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center">
                    <label class="mr-2 text-sm font-medium text-gray-600">Sampai:</label>
                    <input type="date" class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center">
                    <label class="mr-2 text-sm font-medium text-gray-600">Kategori:</label>
                    <select class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kategori</option>
                        <option value="penjualan">Penjualan</option>
                        <option value="pembelian">Pembelian</option>
                        <option value="gaji">Gaji & Upah</option>
                        <option value="operasional">Biaya Operasional</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <button class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                    <i class="fas fa-redo mr-2"></i>Reset
                </button>
            </div>

            <!-- Enhanced Table -->
            <div class="overflow-x-auto">
                <table class="w-full bg-white border border-gray-300 print-table">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-left">Tanggal</th>
                            <th class="py-3 px-4 text-left">Kode Akun</th>
                            <th class="py-3 px-4 text-left">Kategori</th>
                            <th class="py-3 px-4 text-left">Keterangan</th>
                            <th class="py-3 px-4 text-right">Debit</th>
                            <th class="py-3 px-4 text-right">Kredit</th>
                            <th class="py-3 px-4 text-right">Saldo</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Looping data dari database -->
                        @php 
                            $no = 1; 
                            $runningSaldo = 0;
                        @endphp
                        
                        @foreach($laporan as $item)
                            @php
                                $debit = $item->uang_masuk;
                                $kredit = $item->uang_keluar + $item->gaji;
                                $runningSaldo += $debit - $kredit;
                            @endphp
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4">{{ $no++ }}</td>
                                <td class="py-3 px-4">{{ $item->Tanggal->format('Y-m-d') }}</td>
                                <td class="py-3 px-4">{{ $item->kode }}</td>
                                <td class="py-3 px-4">{{ $item->kategori }}</td>
                                <td class="py-3 px-4">{{ $item->keterangan }}  {{ $item->nama_karyawan }}</td>
                                <td class="py-3 px-4 text-right text-green-600">
                                    @if($debit > 0)
                                        Rp {{ number_format($debit, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right text-red-600">
                                    @if($kredit > 0)
                                        Rp {{ number_format($kredit, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-medium">Rp {{ number_format($runningSaldo, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-green-600 hover:text-green-800" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-800" title="Hapus">
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
                            <td class="py-3 px-4 text-right text-green-600">Rp {{ number_format($totalUangMasuk, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-red-600">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right">Rp {{ number_format($saldo, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
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
</body>
@endsection