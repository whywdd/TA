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
                <p class="text-sm mt-1">Entri Neraca Saldo Budivespaendut</p>
                <p class="text-sm">Anggrek</p>
            </div>

            <!-- Tabel Transaksi -->
            <div class="overflow-x-auto">
                <table class="w-full bg-white border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-4 text-left">Kode</th>
                            <th class="py-3 px-4 text-left">Kategori</th>
                            <th class="py-3 px-4 text-left">Keterangan</th>
                            <th class="py-3 px-4 text-right">Debit</th>
                            <th class="py-3 px-4 text-right">Kredit</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksis as $transaksi)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-4">{{ $transaksi['kode'] }}</td>
                                <td class="py-3 px-4">{{ $transaksi['kategori'] }}</td>
                                <td class="py-3 px-4">{{ $transaksi['keterangan'] }}</td>
                                <td class="py-3 px-4 text-right">
                                    @if($transaksi['debit'] > 0)
                                        {{ number_format($transaksi['debit'], 2) }}
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if($transaksi['kredit'] > 0)
                                        {{ number_format($transaksi['kredit'], 2) }}
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex item-center justify-center">
                                        <a href="{{ route('neracasaldo.edit', $transaksi['id']) }}" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('neracasaldo.destroy', $transaksi['id']) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
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
@endsection