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
@endsection