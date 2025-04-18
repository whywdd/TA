@extends('Core.Sidebar')

@section('content')
    <title>Laporan Laba Rugi</title>
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
                <h1 class="text-2xl font-bold text-center">CV. Budivespaendut</h1>
                <h2 class="text-xl font-semibold text-center mt-2">Laporan Laba Rugi</h2>
                <p class="text-center">Periode: {{ date('d F Y', strtotime($startDate)) }} - {{ date('d F Y', strtotime($endDate)) }}</p>
            </div>

            <!-- Filter Tanggal -->
            <!-- <div class="mb-4">
                <form method="GET" action="{{ url('/laporan-laba-rugi') }}" class="flex gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div class="self-end">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Filter
                        </button>
                    </div>
                </form>
            </div> -->

            <!-- Tabel Laporan Laba Rugi -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border-collapse">
                    <!-- Pendapatan Operasional -->
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="3" class="border p-2">Pendapatan Operasional</td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Pendapatan Penjualan</td>
                        <td class="border p-2 text-right">{{ number_format($pendapatanPenjualan ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Pendapatan Jasa</td>
                        <td class="border p-2 text-right">{{ number_format($pendapatanJasa ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr class="font-bold bg-gray-50">
                        <td class="border p-2">Total Pendapatan Operasional</td>
                        <td class="border p-2"></td>
                        <td class="border p-2 text-right">{{ number_format($totalPendapatanOperasional ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Pendapatan Non-Operasional -->
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="3" class="border p-2">Pendapatan Non-Operasional</td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Pendapatan Bunga</td>
                        <td class="border p-2 text-right">{{ number_format($pendapatanBunga ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Pendapatan Sewa</td>
                        <td class="border p-2 text-right">{{ number_format($pendapatanSewa ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Pendapatan Komisi</td>
                        <td class="border p-2 text-right">{{ number_format($pendapatanKomisi ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Pendapatan Lain</td>
                        <td class="border p-2 text-right">{{ number_format($pendapatanLain ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr class="font-bold bg-gray-50">
                        <td class="border p-2">Total Pendapatan Non-Operasional</td>
                        <td class="border p-2"></td>
                        <td class="border p-2 text-right">{{ number_format($totalPendapatanNonOperasional ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Total Seluruh Pendapatan -->
                    <tr class="font-bold bg-blue-50">
                        <td class="border p-2">Total Pendapatan</td>
                        <td class="border p-2"></td>
                        <td class="border p-2 text-right">{{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Beban Operasional -->
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="3" class="border p-2">Beban Operasional</td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Beban Gaji</td>
                        <td class="border p-2 text-right">{{ number_format($bebanGaji ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Beban Sewa</td>
                        <td class="border p-2 text-right">{{ number_format($bebanSewa ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Beban Utilitas</td>
                        <td class="border p-2 text-right">{{ number_format($bebanUtilitas ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Beban Penyusutan</td>
                        <td class="border p-2 text-right">{{ number_format($bebanPenyusutan ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Beban Supplies</td>
                        <td class="border p-2 text-right">{{ number_format($bebanSupplies ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Beban Iklan</td>
                        <td class="border p-2 text-right">{{ number_format($bebanIklan ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr class="font-bold bg-gray-50">
                        <td class="border p-2">Total Beban Operasional</td>
                        <td class="border p-2"></td>
                        <td class="border p-2 text-right">{{ number_format($totalBebanOperasional ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Beban Non-Operasional -->
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="3" class="border p-2">Beban Non-Operasional</td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Beban Bunga</td>
                        <td class="border p-2 text-right">{{ number_format($bebanBunga ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-4">Beban Lain</td>
                        <td class="border p-2 text-right">{{ number_format($bebanLain ?? 0, 0, ',', '.') }}</td>
                        <td class="border p-2"></td>
                    </tr>
                    <tr class="font-bold bg-gray-50">
                        <td class="border p-2">Total Beban Non-Operasional</td>
                        <td class="border p-2"></td>
                        <td class="border p-2 text-right">{{ number_format($totalBebanNonOperasional ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Total Seluruh Beban -->
                    <tr class="font-bold bg-red-50">
                        <td class="border p-2">Total Beban</td>
                        <td class="border p-2"></td>
                        <td class="border p-2 text-right">{{ number_format($totalBeban ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Total Laba/Rugi -->
                    <tr class="font-bold text-lg {{ $totalLabaRugi >= 0 ? 'bg-green-100' : 'bg-red-100' }}">
                        <td class="border p-2">Total Laba/Rugi</td>
                        <td class="border p-2"></td>
                        <td class="border p-2 text-right">{{ number_format($totalLabaRugi ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
