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
        <!-- Tab Navigation -->
        <div class="mb-4 border-b">
            <nav class="flex space-x-4">
                <button type="button" onclick="switchTab('manual')" id="tabManual" class="px-4 py-2 border-b-2 border-blue-600 text-blue-600 font-medium">
                    Transaksi Manual
                </button>
                <button type="button" onclick="switchTab('penjualan')" id="tabPenjualan" class="px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                    Penjualan Anggrek
                </button>
            </nav>
        </div>

        <!-- Tab Manual -->
        <div id="contentManual" class="space-y-4">
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
                    id="tanggalInput"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                    readonly
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
                                <option value="kas" data-kode="111001">Kas/Kas Kecil (001) - Debit</option>
                                <option value="bank" data-kode="111002">Bank (002) - Debit</option>
                                <option value="piutang usaha" data-kode="111003">Piutang Usaha/Dagang (003) - Debit</option>
                                <option value="piutang wesel" data-kode="111004">Piutang Wesel (004) - Debit</option>
                                <option value="piutang karyawan" data-kode="111005">Piutang Karyawan (005) - Debit</option>
                                <option value="piutang lain" data-kode="111006">Piutang Lain-lain (006) - Debit</option>
                                <option value="persediaan barang" data-kode="111007">Persediaan Barang Dagang (007) - Debit</option>
                                <option value="persediaan bahan" data-kode="111008">Persediaan Bahan Baku/Supplies (008) - Debit</option>
                                <option value="sewa dibayar dimuka" data-kode="111009">Sewa Dibayar di Muka (009) - Debit</option>
                                <option value="asuransi dibayar_dimuka" data-kode="111010">Asuransi Dibayar di Muka (010) - Debit</option>
                                <option value="perlengkapan kantor" data-kode="111011">Perlengkapan Kantor (011) - Debit</option>
                                <option value="biaya dibayar dimuka" data-kode="111012">Biaya Dibayar di Muka (012) - Debit</option>
                                <option value="investasi pendek" data-kode="111013">Investasi Jangka Pendek (013) - Debit</option>
                            </optgroup>

                            <!-- Aset Tetap -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Aset Tetap (2)">
                                <option value="tanah" data-kode="112001">Tanah (001) - Debit</option>
                                <option value="gedung" data-kode="112002">Gedung/Bangunan (002) - Debit</option>
                                <option value="kendaraan" data-kode="112003">Kendaraan (003) - Debit</option>
                                <option value="mesin" data-kode="112004">Mesin dan Peralatan (004) - Debit</option>
                                <option value="perabotan" data-kode="112005">Perabotan Kantor (005) - Debit</option>
                                <option value="hak paten" data-kode="112006">Hak Paten (006) - Debit</option>
                                <option value="hak cipta" data-kode="112007">Hak Cipta (007) - Debit</option>
                                <option value="goodwill" data-kode="112008">Goodwill (008) - Debit</option>
                                <option value="merek dagang" data-kode="112009">Merek Dagang (009) - Debit</option>
                            </optgroup>
                        </optgroup>

                        <optgroup label="2. UTANG (KEWAJIBAN) (12)">
                            <!-- Utang Lancar -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Utang Lancar (1)">
                                <option value="utang usaha" data-kode="121001">Utang Usaha/Dagang (001) - Kredit</option>
                                <option value="utang wesel" data-kode="121002">Utang Wesel (002) - Kredit</option>
                                <option value="utang gaji" data-kode="121003">Utang Gaji (003) - Kredit</option>
                                <option value="utang bunga" data-kode="121004">Utang Bunga (004) - Kredit</option>
                                <option value="utang pajak" data-kode="121005">Utang Pajak (005) - Kredit</option>
                                <option value="utang dividen" data-kode="121006">Utang Dividen (006) - Kredit</option>
                            </optgroup>

                            <!-- Utang Jangka Panjang -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Utang Jangka Panjang (2)">
                                <option value="utang hipotek" data-kode="122001">Utang Hipotek (001) - Kredit</option>
                                <option value="utang obligasi" data-kode="122002">Utang Obligasi (002) - Kredit</option>
                                <option value="kredit investasi" data-kode="122003">Kredit Investasi (003) - Kredit</option>
                            </optgroup>
                        </optgroup>

                        <optgroup label="3. MODAL (EKUITAS) (31)">
                            <option value="modal pemilik" data-kode="311001">Modal Pemilik/Modal Disetor (001) - Kredit</option>
                            <option value="modal saham" data-kode="311002">Modal Saham (002) - Kredit</option>
                            <option value="laba ditahan" data-kode="311003">Laba Ditahan (003) - Kredit</option>
                            <option value="dividen" data-kode="311004">Dividen (004) - Kredit</option>
                            <option value="prive" data-kode="311005">Prive (Pengambilan Pribadi) (005) - Kredit</option>
                        </optgroup>

                        <optgroup label="4. PENDAPATAN (41)">
                            <!-- Pendapatan Operasional -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Pendapatan Operasional (1)">
                                <option value="pendapatan penjualan" data-kode="411001">Pendapatan Penjualan (001) - Kredit</option>
                                <option value="pendapatan jasa" data-kode="411002">Pendapatan Jasa (002) - Kredit</option>
                            </optgroup>

                            <!-- Pendapatan Non-Operasional -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Pendapatan Non-Operasional (2)">
                                <option value="pendapatan bunga" data-kode="412001">Pendapatan Bunga (001) - Kredit</option>
                                <option value="pendapatan sewa" data-kode="412002">Pendapatan Sewa (002) - Kredit</option>
                                <option value="pendapatan komisi" data-kode="412003">Pendapatan Komisi (003) - Kredit</option>
                                <option value="pendapatan lain" data-kode="412004">Pendapatan Lain-lain (004) - Kredit</option>
                            </optgroup>
                        </optgroup>

                        <optgroup label="5. BEBAN (22)">
                            <!-- Beban Operasional -->
                            <optgroup label="&nbsp;&nbsp;&nbsp;Beban Operasional (1)">
                                <option value="beban gaji" data-kode="221001">Beban Gaji (001) - Debit</option>
                                <option value="beban sewa" data-kode="221002">Beban Sewa (002) - Debit</option>
                                <option value="beban utilitas" data-kode="221003">Beban Listrik, Air, dan Telepon (003) - Debit</option>
                                <option value="beban penyusutan" data-kode="221004">Beban Penyusutan (004) - Debit</option>
                                <option value="beban supplies" data-kode="221005">Beban Supplies/Perlengkapan (005) - Debit</option>
                                <option value="beban iklan" data-kode="221006">Beban Iklan/Promosi (006) - Debit</option>
                                <option value="beban pokok penjualan" data-kode="221007">Beban Pokok Penjualan/HPP (007) - Debit</option>
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

        <!-- Tab Penjualan Anggrek -->
        <div id="contentPenjualan" class="hidden space-y-4">
            <form action="{{ route('uangmasuk.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Barang</label>
                        <select name="barang_id" id="barangSelectPenjualan" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required onchange="updatePenjualanInfo()">
                            <option value="">Pilih Barang</option>
                            @if(isset($manajemenStok))
                                @foreach($manajemenStok as $stok)
                                    <option value="{{ $stok->id }}" data-stok="{{ $stok->stok }}" data-harga="{{ $stok->harga_beli }}" data-satuan="{{ $stok->satuan }}">
                                        {{ $stok->kode_barang }} - {{ $stok->nama_barang }} ({{ ucfirst($stok->status_tahap) }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Jual</label>
                        <input type="number" name="jumlah_jual" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" min="1" required>
                    </div>
                    <div class="form-group md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                        <input type="text" name="harga_jual" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Rp" oninput="formatNumberInput(this)" required>
                    </div>
                </div>

                <!-- Info Stok dan Harga Beli -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Stok Tersedia:</span>
                            <span id="stokTersediaPenjualan" class="font-semibold text-gray-800">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Harga Beli:</span>
                            <span id="hargaBeliPenjualan" class="font-semibold text-gray-800">-</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Simpan Penjualan</button>
            </form>
        </div>
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
    const rekeningContainer = document.getElementById('rekening-container');
    const keteranganType = document.querySelector('input[name="keterangan_type"]:checked').value;

    if (keteranganType === 'karyawan' && '{{ $tipe_pengguna }}' === 'owner') {
        karyawanInput.classList.remove('hidden');
        manualInput.classList.add('hidden');
        rekeningContainer.classList.remove('hidden');
        document.getElementById('keterangan').required = true;
        document.querySelector('textarea[name="keterangan_manual"]').required = false;
    } else {
        karyawanInput.classList.add('hidden');
        manualInput.classList.remove('hidden');
        rekeningContainer.classList.remove('hidden');
        document.getElementById('keterangan').required = false;
        document.querySelector('textarea[name="keterangan_manual"]').required = true;
    }
}

function switchTab(tab) {
    const contentManual = document.getElementById('contentManual');
    const contentPenjualan = document.getElementById('contentPenjualan');
    const tabManual = document.getElementById('tabManual');
    const tabPenjualan = document.getElementById('tabPenjualan');

    if (tab === 'manual') {
        contentManual.classList.remove('hidden');
        contentPenjualan.classList.add('hidden');
        tabManual.classList.add('border-blue-600', 'text-blue-600');
        tabManual.classList.remove('border-transparent', 'text-gray-500');
        tabPenjualan.classList.remove('border-blue-600', 'text-blue-600');
        tabPenjualan.classList.add('border-transparent', 'text-gray-500');
    } else {
        contentManual.classList.add('hidden');
        contentPenjualan.classList.remove('hidden');
        tabPenjualan.classList.add('border-blue-600', 'text-blue-600');
        tabPenjualan.classList.remove('border-transparent', 'text-gray-500');
        tabManual.classList.remove('border-blue-600', 'text-blue-600');
        tabManual.classList.add('border-transparent', 'text-gray-500');
    }
}

function formatNumberInput(input) {
    let value = input.value.replace(/\D/g, '');
    if (value !== '') {
        value = new Intl.NumberFormat('id-ID').format(value);
    }
    input.value = value;
}

function updatePenjualanInfo() {
    const select = document.getElementById('barangSelectPenjualan');
    const selectedOption = select.options[select.selectedIndex];
    const stok = selectedOption.getAttribute('data-stok');
    const hargaBeli = selectedOption.getAttribute('data-harga');
    const satuan = selectedOption.getAttribute('data-satuan');

    if (stok && hargaBeli && satuan) {
        document.getElementById('stokTersediaPenjualan').textContent = stok + ' ' + satuan;
        document.getElementById('hargaBeliPenjualan').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(hargaBeli);
    } else {
        document.getElementById('stokTersediaPenjualan').textContent = '-';
        document.getElementById('hargaBeliPenjualan').textContent = '-';
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

    // Skip validasi jika penjualan anggrek
    const selectedKeteranganType = document.querySelector('input[name="keterangan_type"]:checked').value;
    if (selectedKeteranganType === 'penjualan_anggrek') {
        console.log('Penjualan Anggrek, submitting...');
        document.getElementById('uangMasukForm').submit();
        return false;
    }

    // Validasi jumlah rekening (flow lama)
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
    const selectedType = document.querySelector('input[name="keterangan_type"]:checked').value;
    if (selectedType === 'karyawan') {
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

function initializeSelect2() {
    // Inisialisasi Select2 hanya untuk kategori rekening
    $('select[name="kategori[]"]').not('#barangSelect').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('.rekening-entry').parent()
    });
}

// Fungsi untuk mengupdate tanggal secara realtime
function updateTanggal() {
    const today = new Date();
    const formattedDate = today.toISOString().split('T')[0];
    document.getElementById('tanggalInput').value = formattedDate;
}

// Update tanggal setiap detik
setInterval(updateTanggal, 1000);

// Set tanggal awal saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    updateTanggal();
    console.log('Page loaded');
    validateBalance();
    toggleKeteranganInput();
    initializeSelect2();

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

    // Event listener untuk penjualan anggrek
    if (document.getElementById('barangSelect')) {
        const barangSelect = document.getElementById('barangSelect');
        barangSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stok = selectedOption.getAttribute('data-stok');
            const hargaBeli = selectedOption.getAttribute('data-harga');
            const satuan = selectedOption.getAttribute('data-satuan');

            if (stok && hargaBeli && satuan) {
                document.getElementById('stokTersedia').textContent = stok + ' ' + satuan;
                document.getElementById('hargaBeli').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(hargaBeli);
                hitungHPP();
                // populateRekeningPenjualan(); // Matikan dulu untuk mencegah macet
            }
        });
    }
});

// Fungsi untuk Penjualan Anggrek
function formatNumberInput(input) {
    let value = input.value.replace(/\D/g, '');
    if (value !== '') {
        value = new Intl.NumberFormat('id-ID').format(value);
    }
    input.value = value;
}

function hitungHPP() {
    const jumlahJual = parseInt(document.getElementById('jumlahJual').value) || 0;
    const barangSelect = document.getElementById('barangSelect');
    const selectedOption = barangSelect.options[barangSelect.selectedIndex];
    const hargaBeli = parseInt(selectedOption.getAttribute('data-harga')) || 0;

    const totalHPP = jumlahJual * hargaBeli;

    document.getElementById('hppPreview').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalHPP);

    // Update HPP dan Persediaan di rekening 3 dan 4
    const container = document.getElementById('rekening-container');
    const entries = container.querySelectorAll('.rekening-entry');
    if (entries.length >= 3) {
        const hppInput = entries[2].querySelector('input[name="nominal[]"]');
        const persediaanInput = entries[3].querySelector('input[name="nominal[]"]');
        hppInput.value = new Intl.NumberFormat('id-ID').format(totalHPP);
        persediaanInput.value = new Intl.NumberFormat('id-ID').format(totalHPP);
    }
}

function formatNumberInput(input) {
    let value = input.value.replace(/\D/g, '');
    if (value !== '') {
        value = new Intl.NumberFormat('id-ID').format(value);
    }
    input.value = value;
}

@if(session('success'))
    Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}' });
@endif

@if(session('error'))
    Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}' });
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