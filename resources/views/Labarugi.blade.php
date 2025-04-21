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
                        <td class="p-2 border border-gray-300 pl-8">{{ ucwords($item['kategori']) }} ({{ $item['kode_akun'] }})</td>
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
                        <td class="p-2 border border-gray-300 pl-8">{{ ucwords($item['kategori']) }} ({{ $item['kode_akun'] }})</td>
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
        </div>
    </div>
@endsection
