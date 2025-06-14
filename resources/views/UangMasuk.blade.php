@extends('Core.Sidebar')

@section('content')
<!-- Tambahkan CSS Select2 di bagian atas -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<div class="p-6 animate-fade-in">
    <!-- Header -->
    <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold">Bukti Transaksi</h1>
        <p class="text-sm mt-1">Formulir untuk menambahkan data Transaksi</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form id="uangMasukForm" action="{{ route('uangmasuk.store') }}" method="POST" class="space-y-4" onsubmit="return validateForm(event)">
            @csrf
            <!-- Tanggal -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal <span class="text-red-600">*</span>
                </label>
                <input 
                    type="date" 
                    name="Tanggal"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                    min="{{ date('Y-m-d') }}"
                    value="{{ date('Y-m-d') }}"
                >
            </div>

            <!-- Keterangan -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Keterangan <span class="text-red-600">*</span>
                </label>
                <div class="space-y-2">
                    <!-- Radio Button untuk memilih input -->
                    <div class="flex items-center space-x-4">
                        @if($tipe_pengguna == 'owner')
                        <label class="flex items-center">
                            <input type="radio" name="keterangan_type" value="karyawan" checked class="mr-2" onchange="toggleKeteranganInput()">
                            <span>Pilih Karyawan</span>
                        </label>
                        @endif
                        <label class="flex items-center">
                            <input type="radio" name="keterangan_type" value="manual" {{ $tipe_pengguna != 'owner' ? 'checked' : '' }} class="mr-2" onchange="toggleKeteranganInput()">
                            <span>Input Manual</span>
                        </label>
                    </div>

                    <!-- Dropdown Karyawan -->
                    <div id="karyawanInput" {{ $tipe_pengguna != 'owner' ? 'class="hidden"' : '' }}>
                        <div class="space-y-4">
                            <select 
                                id="keterangan"
                                name="keterangan"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                required
                            >
                                <option value="">Pilih Karyawan</option>
                                @foreach($karyawans as $karyawan)
                                    <option value="{{ $karyawan->nama }}" data-gaji="{{ $karyawan->gaji }}">{{ $karyawan->nama }} - {{ $karyawan->jabatan }}</option>
                                @endforeach
                            </select>

                            <!-- Input Keterangan Tambahan -->
                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Keterangan Tambahan
                                </label>
                                <textarea 
                                    name="keterangan_tambahan"
                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                    rows="3"
                                    placeholder="Tambahkan keterangan lain jika diperlukan"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Input Manual -->
                    <div id="manualInput" class="hidden">
                        <textarea 
                            name="keterangan_manual"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            rows="3"
                            placeholder="Masukkan keterangan"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Container untuk rekening-rekening -->
            <div id="rekening-container">
                <!-- Template Rekening -->
                <div class="rekening-entry border-b pb-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-medium text-gray-700">Rekening 1</h3>
                    </div>

                    <!-- Nama Akun -->
                    <div class="form-group mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Akun <span class="text-red-600">*</span>
                        </label>
                        <select 
                            name="kategori[]"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 kategori-select" 
                            required
                        >
                            <option value="" disabled selected>Pilih Kategori Akun</option>
                            <optgroup label="1. HARTA (ASET) (11)">
                            <!-- Aset Lancar -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Aset Lancar (1)">
                                <option value="kas" data-kode="1101">Kas/Kas Kecil (001) - Debit</option>
                                <option value="bank" data-kode="1102">Bank (002) - Debit</option>
                                <option value="piutang usaha" data-kode="1103">Piutang Usaha/Dagang (003) - Debit</option>
                                <option value="piutang wesel" data-kode="1104">Piutang Wesel (004) - Debit</option>
                                <option value="piutang karyawan" data-kode="1105">Piutang Karyawan (005) - Debit</option>
                                <option value="piutang lain" data-kode="1106">Piutang Lain-lain (006) - Debit</option>
                                <option value="persediaan barang" data-kode="1107">Persediaan Barang Dagang (007) - Debit</option>
                                <option value="persediaan bahan" data-kode="1108">Persediaan Bahan Baku/Supplies (008) - Debit</option>
                                <option value="sewa dibayar dimuka" data-kode="1109">Sewa Dibayar di Muka (009) - Debit</option>
                                <option value="asuransi dibayar_dimuka" data-kode="1110">Asuransi Dibayar di Muka (010) - Debit</option>
                                <option value="perlengkapan kantor" data-kode="1111">Perlengkapan Kantor (011) - Debit</option>
                                <option value="biaya dibayar dimuka" data-kode="1112">Biaya Dibayar di Muka (012) - Debit</option>
                                <option value="investasi pendek" data-kode="1113">Investasi Jangka Pendek (013) - Debit</option>
                            </optgroup>

                            <!-- Aset Tetap -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Aset Tetap (2)">
                                <option value="tanah" data-kode="1201">Tanah (001) - Debit</option>
                                <option value="gedung" data-kode="1202">Gedung/Bangunan (002) - Debit</option>
                                <option value="kendaraan" data-kode="1203">Kendaraan (003) - Debit</option>
                                <option value="mesin" data-kode="1204">Mesin dan Peralatan (004) - Debit</option>
                                <option value="perabotan" data-kode="1205">Perabotan Kantor (005) - Debit</option>
                                <option value="hak paten" data-kode="1206">Hak Paten (006) - Debit</option>
                                <option value="hak cipta" data-kode="1207">Hak Cipta (007) - Debit</option>
                                <option value="goodwill" data-kode="1208">Goodwill (008) - Debit</option>
                                <option value="merek dagang" data-kode="1209">Merek Dagang (009) - Debit</option>
                            </optgroup>
                        </optgroup>

                        <optgroup label="2. UTANG (KEWAJIBAN) (12)">
                            <!-- Utang Lancar -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Utang Lancar (1)">
                                <option value="utang usaha" data-kode="2101">Utang Usaha/Dagang (001) - Kredit</option>
                                <option value="utang wesel" data-kode="2102">Utang Wesel (002) - Kredit</option>
                                <option value="utang gaji" data-kode="2103">Utang Gaji (003) - Kredit</option>
                                <option value="utang bunga" data-kode="2104">Utang Bunga (004) - Kredit</option>
                                <option value="utang pajak" data-kode="2105">Utang Pajak (005) - Kredit</option>
                                <option value="utang dividen" data-kode="2106">Utang Dividen (006) - Kredit</option>
                            </optgroup>

                            <!-- Utang Jangka Panjang -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Utang Jangka Panjang (2)">
                                <option value="utang hipotek" data-kode="2201">Utang Hipotek (001) - Kredit</option>
                                <option value="utang obligasi" data-kode="2202">Utang Obligasi (002) - Kredit</option>
                                <option value="kredit investasi" data-kode="2203">Kredit Investasi (003) - Kredit</option>
                            </optgroup>
                        </optgroup>

                        <optgroup label="3. MODAL (EKUITAS) (131)">
                            <option value="modal pemilik" data-kode="3101">Modal Pemilik/Modal Disetor (001) - Kredit</option>
                            <option value="modal saham" data-kode="3102">Modal Saham (002) - Kredit</option>
                            <option value="laba ditahan" data-kode="3103">Laba Ditahan (003) - Kredit</option>
                            <option value="dividen" data-kode="3104">Dividen (004) - Kredit</option>
                            <option value="prive" data-kode="3105">Prive (Pengambilan Pribadi) (005) - Kredit</option>
                        </optgroup>

                        <optgroup label="4. PENDAPATAN (21)">
                            <!-- Pendapatan Operasional -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Pendapatan Operasional (1)">
                                <option value="pendapatan penjualan" data-kode="4101">Pendapatan Penjualan (001) - Kredit</option>
                                <option value="pendapatan jasa" data-kode="4102">Pendapatan Jasa (002) - Kredit</option>
                            </optgroup>

                            <!-- Pendapatan Non-Operasional -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Pendapatan Non-Operasional (2)">
                                <option value="pendapatan bunga" data-kode="4201">Pendapatan Bunga (001) - Kredit</option>
                                <option value="pendapatan sewa" data-kode="4202">Pendapatan Sewa (002) - Kredit</option>
                                <option value="pendapatan komisi" data-kode="4203">Pendapatan Komisi (003) - Kredit</option>
                                <option value="pendapatan lain" data-kode="4204">Pendapatan Lain-lain (004) - Kredit</option>
                            </optgroup>
                        </optgroup>

                        <optgroup label="5. BEBAN (22)">
                            <!-- Beban Operasional -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Beban Operasional (1)">
                                <option value="beban gaji" data-kode="5101">Beban Gaji (001) - Debit</option>
                                <option value="beban sewa" data-kode="5102">Beban Sewa (002) - Debit</option>
                                <option value="beban utilitas" data-kode="5103">Beban Listrik, Air, dan Telepon (003) - Debit</option>
                                <option value="beban penyusutan" data-kode="5104">Beban Penyusutan (004) - Debit</option>
                                <option value="beban supplies" data-kode="5105">Beban Supplies/Perlengkapan (005) - Debit</option>
                                <option value="beban iklan" data-kode="5106">Beban Iklan/Promosi (006) - Debit</option>
                            </optgroup>

                            <!-- Beban Non-Operasional -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Beban Non-Operasional (2)">
                                <option value="beban bunga" data-kode="5201">Beban Bunga (001) - Debit</option>
                                <option value="beban lain" data-kode="5202">Beban Lain-lain (002) - Debit</option>
                            </optgroup>
                        </optgroup>
                        </select>
                    </div>

                    <!-- Posisi -->
                    <div class="form-group mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Posisi <span class="text-red-600">*</span>
                        </label>
                        <select 
                            name="posisi[]"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            required
                        >
                            <option value="" disabled selected>Pilih Posisi</option>
                            <option value="debit">Debit</option>
                            <option value="kredit">Kredit</option>
                        </select>
                    </div>

                    <!-- Nominal -->
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nominal <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-600">Rp</span>
                            <input 
                                type="text"
                                name="nominal[]"
                                class="w-full border rounded-lg pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0"
                                required
                                oninput="formatNumber(this); validateBalance();"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Tambah Rekening -->
            <div class="flex justify-center">
                <button 
                    type="button" 
                    onclick="tambahRekening()"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
                >
                    <i class="fas fa-plus mr-2"></i>Tambah Rekening
                </button>
            </div>

            <!-- Tombol Submit -->
            <div class="flex justify-end space-x-3 pt-4 border-t mt-4">
                <button 
                    type="button" 
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition-colors"
                    onclick="window.history.back()"
                >
                    Kembali
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    id="submitBtn"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Tambahkan jQuery dan Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let rekeningCount = 1;
const MAX_REKENING = 5;

function toggleKeteranganInput() {
    const karyawanInput = document.getElementById('karyawanInput');
    const manualInput = document.getElementById('manualInput');
    const keteranganType = document.querySelector('input[name="keterangan_type"]:checked').value;

    if (keteranganType === 'karyawan' && '{{ $tipe_pengguna }}' === 'owner') {
        karyawanInput.classList.remove('hidden');
        manualInput.classList.add('hidden');
        document.getElementById('keterangan').required = true;
        document.querySelector('textarea[name="keterangan_manual"]').required = false;
    } else {
        karyawanInput.classList.add('hidden');
        manualInput.classList.remove('hidden');
        document.getElementById('keterangan').required = false;
        document.querySelector('textarea[name="keterangan_manual"]').required = true;
    }
}

function tambahRekening() {
    if (rekeningCount >= MAX_REKENING) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Maksimal 5 rekening per transaksi',
            confirmButtonText: 'OK'
        });
        return;
    }

    rekeningCount++;
    const container = document.getElementById('rekening-container');
    const template = document.querySelector('.rekening-entry');

    // Hapus Select2 sebelum clone
    const selectKategori = template.querySelector('select[name="kategori[]"]');
    if ($(selectKategori).hasClass("select2-hidden-accessible")) {
        $(selectKategori).select2('destroy');
    }

    // Clone node
    const newEntry = template.cloneNode(true);

    // Reset nilai-nilai form
    newEntry.querySelector('select[name="kategori[]"]').value = '';
    newEntry.querySelector('select[name="posisi[]"]').value = '';
    newEntry.querySelector('input[name="nominal[]"]').value = '';

    // Update judul rekening
    newEntry.querySelector('h3').textContent = `Rekening ${rekeningCount}`;

    // Tambahkan tombol hapus untuk rekening tambahan
    const headerDiv = newEntry.querySelector('.flex');
    const deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.className = 'text-red-500 hover:text-red-700';
    deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
    deleteButton.onclick = function() {
        newEntry.remove();
        rekeningCount--;
        validateBalance();
    };
    headerDiv.appendChild(deleteButton);

    container.appendChild(newEntry);

    // Inisialisasi Select2 ulang untuk semua select kategori
    initializeSelect2();
}

function formatNumber(input) {
    let value = input.value.replace(/\D/g, '');
    if (value !== '') {
        value = new Intl.NumberFormat('id-ID').format(value);
    }
    input.value = value;
}

function validateForm(event) {
    event.preventDefault();
    console.log('Validating form...');

    // Validasi jumlah rekening
    const nominals = document.getElementsByName('nominal[]');
    if (nominals.length < 2) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Minimal harus ada 2 rekening!',
            confirmButtonText: 'OK'
        });
        return false;
    }

    // Validasi input rekening
    let isValid = true;
    for (let i = 0; i < nominals.length; i++) {
        const nominal = nominals[i].value.replace(/\D/g, '');
        const posisi = document.getElementsByName('posisi[]')[i].value;
        const kategori = document.getElementsByName('kategori[]')[i].value;

        if (!nominal || !posisi || !kategori) {
            isValid = false;
            break;
        }
    }

    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Semua field rekening harus diisi!',
            confirmButtonText: 'OK'
        });
        return false;
    }

    // Set keterangan berdasarkan input yang dipilih
    const keteranganType = document.querySelector('input[name="keterangan_type"]:checked').value;
    if (keteranganType === 'karyawan') {
        const karyawan = document.getElementById('keterangan').value;
        if (!karyawan) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Silakan pilih karyawan!',
                confirmButtonText: 'OK'
            });
            return false;
        }
        const tambahan = document.querySelector('textarea[name="keterangan_tambahan"]').value;
        document.querySelector('textarea[name="keterangan_manual"]').value = '';
    } else {
        const manual = document.querySelector('textarea[name="keterangan_manual"]').value;
        if (!manual) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Silakan isi keterangan manual!',
                confirmButtonText: 'OK'
            });
            return false;
        }
    }

    console.log('Form validation passed, submitting...');
    document.getElementById('uangMasukForm').submit();
    return false;
}

function validateBalance() {
    const nominals = document.getElementsByName('nominal[]');
    const posisis = document.getElementsByName('posisi[]');
    const kategoris = document.getElementsByName('kategori[]');
    
    let isValid = true;
    for (let i = 0; i < nominals.length; i++) {
        const nominal = nominals[i].value.replace(/\D/g, '');
        const posisi = posisis[i].value;
        const kategori = kategoris[i].value;
        
        if (!nominal || !posisi || !kategori) {
            isValid = false;
            break;
        }
    }

    const submitBtn = document.getElementById('submitBtn');
    if (isValid) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-gray-400');
        submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
    } else {
        submitBtn.disabled = true;
        submitBtn.classList.add('bg-gray-400');
        submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
    }
}

// Set default date to today
document.querySelector('input[type="date"]').valueAsDate = new Date();

// Tambahkan fungsi untuk inisialisasi Select2
function initializeSelect2() {
    $('.kategori-select').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih Kategori Akun',
        allowClear: true,
        language: {
            noResults: function() {
                return "Tidak ada hasil yang ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded');
    validateBalance();
    toggleKeteranganInput();
    initializeSelect2(); // Inisialisasi Select2 saat halaman dimuat

    // Event listener untuk input nominal
    document.addEventListener('input', function(e) {
        if (e.target.name === 'nominal[]') {
            formatNumber(e.target);
            validateBalance();
        }
    });

    // Event listener untuk perubahan posisi dan kategori
    document.addEventListener('change', function(e) {
        if (e.target.name === 'posisi[]' || e.target.name === 'kategori[]') {
            validateBalance();
        }
    });

    // Event listener untuk perubahan karyawan
    document.getElementById('keterangan').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const gaji = selectedOption.getAttribute('data-gaji');
        if (gaji) {
            const nominalInputs = document.getElementsByName('nominal[]');
            if (nominalInputs.length > 0) {
                nominalInputs[0].value = new Intl.NumberFormat('id-ID').format(gaji);
                validateBalance();
            }
        }
    });
});

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        confirmButtonText: 'OK'
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK'
    });
@endif
</script>

<style>
/* Tambahkan style untuk Select2 */
.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
}

.select2-container--bootstrap-5 .select2-selection--single {
    padding: 0.375rem 0.75rem;
}

.select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    padding-left: 0;
}

.select2-container--bootstrap-5 .select2-dropdown {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
}

.select2-container--bootstrap-5 .select2-results__option--highlighted {
    background-color: #3b82f6;
}

/* Existing styles */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

.form-group input:hover,
.form-group select:hover,
.form-group textarea:hover {
    border-color: #93C5FD;
}

button {
    transition: all 0.2s ease-in-out;
}

button:hover {
    transform: translateY(-1px);
}

button:active {
    transform: translateY(0);
}
</style>
@endsection