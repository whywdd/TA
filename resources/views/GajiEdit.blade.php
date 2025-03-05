@extends('Core.Sidebar')

@section('content')
<div class="p-6 animate-fade-in">
    <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold">Edit Gaji Karyawan</h1>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('gaji.update', $karyawan->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Karyawan</label>
                <input type="text" name="nama" value="{{ $karyawan->nama }}" 
                       class="border rounded w-full py-2 px-3" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Usia</label>
                <input type="number" name="usia" value="{{ $karyawan->usia }}" 
                       class="border rounded w-full py-2 px-3" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Jabatan</label>
                <input type="text" name="jabatan" value="{{ $karyawan->jabatan }}" 
                       class="border rounded w-full py-2 px-3" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Gaji</label>
                <input type="number" name="gaji" value="{{ $karyawan->gaji }}" 
                       class="border rounded w-full py-2 px-3" required>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="window.history.back()" 
                        class="bg-gray-500 text-white px-4 py-2 rounded mr-2">
                    Batal
                </button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 