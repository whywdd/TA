@extends('Core.Sidebar')

@section('content')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<style>
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}
</style>

<div class="max-w-full bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg shadow-md dark:bg-gray-800 p-6">
  <!-- Filter Section -->
  <div class="flex flex-wrap items-center gap-4 mb-6">
    <h2 class="text-xl font-bold text-indigo-700 dark:text-white">Dashboard Keuangan</h2>
    <div class="flex flex-wrap items-center gap-4 sm:ml-auto">
      <!-- Switch Toggle -->
      <div class="flex items-center gap-2">
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" id="showAllSwitch" class="sr-only peer" {{ request()->routeIs('home.all') ? 'checked' : '' }}>
          <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
          <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-300">Tampilkan Semua</span>
        </label>
      </div>
      <form action="{{ route('home.filter') }}" method="GET" class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-600">Tanggal Awal</span>
          <input type="date" name="start_date" class="form-input box bg-white border border-indigo-200 rounded-lg text-gray-600" value="{{ request('start_date', $startDate ?? '') }}">
        </div>
        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-600">Tanggal Akhir</span>
          <input type="date" name="end_date" class="form-input box bg-white border border-indigo-200 rounded-lg text-gray-600" value="{{ request('end_date', $endDate ?? '') }}">
        </div>
        <button type="submit" class="btn bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600">
          Filter
        </button>
      </form>
    </div>
  </div>

  <!-- Main Metrics -->
  <div class="grid grid-cols-2 gap-4 mb-6">
    <!-- Total Pendapatan Card -->
    <div class="bg-white rounded-xl p-6 shadow-sm">
      <div class="flex items-center">
        <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center me-3">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
        </div>
        <div>
          <p class="text-sm text-gray-500 mb-1">Total Pendapatan</p>
          <h3 class="text-2xl font-bold text-gray-800">
            @if($totalPendapatan < 0)
              -Rp {{ number_format(abs($totalPendapatan), 0, ',', '.') }}
            @else
              Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            @endif
          </h3>
          <p class="text-sm text-gray-500">Periode: {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}</p>
        </div>
      </div>
    </div>

    <!-- Total Beban Card -->
    <div class="bg-white rounded-xl p-6 shadow-sm">
      <div class="flex items-center">
        <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center me-3">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
          </svg>
        </div>
        <div>
          <p class="text-sm text-gray-500 mb-1">Total Beban</p>
          <h3 class="text-2xl font-bold text-gray-800">
            @if($totalBeban < 0)
              -Rp {{ number_format(abs($totalBeban), 0, ',', '.') }}
            @else
              Rp {{ number_format($totalBeban, 0, ',', '.') }}
            @endif
          </h3>
          <p class="text-sm text-gray-500">Periode: {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Detail Section -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <!-- Rincian Pendapatan -->
    <div class="bg-white rounded-xl p-6 shadow-sm">
      <h4 class="text-lg font-semibold text-gray-700 mb-4">Rincian Pendapatan</h4>
      @foreach($pendapatan as $item)
      <div class="flex justify-between items-center mb-2">
        <div class="flex items-center">
          <span class="text-sm text-gray-600">{{ ucwords($item['kategori']) }}</span>
          <span class="text-xs text-gray-400 ml-2">({{ $item['kode_akun'] }})</span>
        </div>
        <span class="text-sm font-medium {{ $item['nominal'] < 0 ? 'text-red-600' : 'text-green-600' }}">
          @if($item['nominal'] < 0)
            -Rp {{ number_format(abs($item['nominal']), 0, ',', '.') }}
          @else
            Rp {{ number_format($item['nominal'], 0, ',', '.') }}
          @endif
        </span>
      </div>
      @endforeach
      <div class="mt-4 pt-4 border-t">
        <div class="flex justify-between items-center">
          <span class="font-semibold">Total Pendapatan</span>
          <span class="font-semibold {{ $totalPendapatan < 0 ? 'text-red-600' : 'text-green-600' }}">
            @if($totalPendapatan < 0)
              -Rp {{ number_format(abs($totalPendapatan), 0, ',', '.') }}
            @else
              Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            @endif
          </span>
        </div>
      </div>
    </div>

    <!-- Rincian Beban -->
    <div class="bg-white rounded-xl p-6 shadow-sm">
      <h4 class="text-lg font-semibold text-gray-700 mb-4">Rincian Beban</h4>
      @foreach($beban as $item)
      <div class="flex justify-between items-center mb-2">
        <div class="flex items-center">
          <span class="text-sm text-gray-600">{{ ucwords($item['kategori']) }}</span>
          <span class="text-xs text-gray-400 ml-2">({{ $item['kode_akun'] }})</span>
        </div>
        <span class="text-sm font-medium {{ $item['nominal'] < 0 ? 'text-green-600' : 'text-red-600' }}">
          @if($item['nominal'] < 0)
            -Rp {{ number_format(abs($item['nominal']), 0, ',', '.') }}
          @else
            Rp {{ number_format($item['nominal'], 0, ',', '.') }}
          @endif
        </span>
      </div>
      @endforeach
      <div class="mt-4 pt-4 border-t">
        <div class="flex justify-between items-center">
          <span class="font-semibold">Total Beban</span>
          <span class="font-semibold {{ $totalBeban < 0 ? 'text-green-600' : 'text-red-600' }}">
            @if($totalBeban < 0)
              -Rp {{ number_format(abs($totalBeban), 0, ',', '.') }}
            @else
              Rp {{ number_format($totalBeban, 0, ',', '.') }}
            @endif
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Profit/Loss Section -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <!-- Total Laba/Rugi Card -->
    <div class="bg-white rounded-xl p-6 shadow-sm">
      <h4 class="text-lg font-semibold text-gray-700 mb-4">Laba/Rugi Total</h4>
      <div class="flex items-center">
        <div class="w-12 h-12 rounded-lg {{ $labaRugiTotal >= 0 ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center me-3">
          <svg class="w-6 h-6 {{ $labaRugiTotal >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $labaRugiTotal >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"></path>
          </svg>
        </div>
        <div>
          <h3 class="text-2xl font-bold {{ $labaRugiTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
            @if($labaRugiTotal < 0)
              -Rp {{ number_format(abs($labaRugiTotal), 0, ',', '.') }}
            @else
              Rp {{ number_format($labaRugiTotal, 0, ',', '.') }}
            @endif
          </h3>
          <p class="text-sm text-gray-500">{{ $labaRugiTotal >= 0 ? 'Laba' : 'Rugi' }}</p>
        </div>
      </div>
    </div>

    <!-- Laba/Rugi Bulan Ini Card -->
    <div class="bg-white rounded-xl p-6 shadow-sm">
      <h4 class="text-lg font-semibold text-gray-700 mb-4">Laba/Rugi Periode Ini</h4>
      <div class="flex items-center">
        <div class="w-12 h-12 rounded-lg {{ $labaRugiBulanIni >= 0 ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center me-3">
          <svg class="w-6 h-6 {{ $labaRugiBulanIni >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $labaRugiBulanIni >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"></path>
          </svg>
        </div>
        <div>
          <h3 class="text-2xl font-bold {{ $labaRugiBulanIni >= 0 ? 'text-green-600' : 'text-red-600' }}">
            @if($labaRugiBulanIni < 0)
              -Rp {{ number_format(abs($labaRugiBulanIni), 0, ',', '.') }}
            @else
              Rp {{ number_format($labaRugiBulanIni, 0, ',', '.') }}
            @endif
          </h3>
          <p class="text-sm text-gray-500">{{ $labaRugiBulanIni >= 0 ? 'Laba' : 'Rugi' }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Bar Chart -->
    <div class="max-w-sm w-full bg-white rounded-lg shadow-sm dark:bg-gray-800 p-4 md:p-6">
      <div class="flex justify-between">
        <div>
          <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">
            @if($labaRugiTotal < 0)
              -Rp {{ number_format(abs($labaRugiTotal), 0, ',', '.') }}
            @else
              Rp {{ number_format($labaRugiTotal, 0, ',', '.') }}
            @endif
          </h5>
          <p class="text-base font-normal text-gray-500 dark:text-gray-400">Laba/Rugi Total</p>
        </div>
        <div class="flex items-center px-2.5 py-0.5 text-base font-semibold {{ $growthPercentage >= 0 ? 'text-green-500' : 'text-red-500' }} dark:text-green-500 text-center">
          {{ number_format($growthPercentage, 1) }}%
          <svg class="w-3 h-3 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4"/>
          </svg>
        </div>
      </div>
      <div class="chart-container">
        <canvas id="financial-trend-chart"></canvas>
      </div>
      <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
        <div class="flex justify-between items-center pt-5">
          <!-- Button -->
          <button
            id="dropdownDefaultButton"
            data-dropdown-toggle="lastDaysdropdown"
            data-dropdown-placement="bottom"
            class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 text-center inline-flex items-center dark:hover:text-white"
            type="button">
            {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}
            <svg class="w-2.5 m-2.5 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
          </button>
          <!-- Dropdown menu -->
          <div id="lastDaysdropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
              <li>
                <a href="{{ route('home.filter', ['start_date' => Carbon\Carbon::yesterday()->format('Y-m-d'), 'end_date' => Carbon\Carbon::yesterday()->format('Y-m-d')]) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Kemarin</a>
              </li>
              <li>
                <a href="{{ route('home.filter', ['start_date' => Carbon\Carbon::today()->format('Y-m-d'), 'end_date' => Carbon\Carbon::today()->format('Y-m-d')]) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Hari Ini</a>
              </li>
              <li>
                <a href="{{ route('home.filter', ['start_date' => Carbon\Carbon::now()->subDays(7)->format('Y-m-d'), 'end_date' => Carbon\Carbon::now()->format('Y-m-d')]) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">7 Hari Terakhir</a>
              </li>
              <li>
                <a href="{{ route('home.filter', ['start_date' => Carbon\Carbon::now()->subDays(30)->format('Y-m-d'), 'end_date' => Carbon\Carbon::now()->format('Y-m-d')]) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">30 Hari Terakhir</a>
              </li>
              <li>
                <a href="{{ route('home.filter', ['start_date' => Carbon\Carbon::now()->subDays(90)->format('Y-m-d'), 'end_date' => Carbon\Carbon::now()->format('Y-m-d')]) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">90 Hari Terakhir</a>
              </li>
            </ul>
          </div>
          <div class="flex items-center space-x-2">
            <button onclick="updateChartType('line')" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white px-3 py-1 rounded-lg bg-gray-100 dark:bg-gray-700">Garis</button>
            <button onclick="updateChartType('bar')" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white px-3 py-1 rounded-lg">Bar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Category Chart -->
    <div class="max-w-sm w-full bg-white rounded-lg shadow-sm dark:bg-gray-800 p-4 md:p-6">
      <div class="flex flex-col w-full">
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center">
            <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Distribusi Neraca Saldo</h5>
            <svg data-popover-target="chart-info" data-popover-placement="bottom" class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white cursor-pointer ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm0 16a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Zm1-5.034V12a1 1 0 0 1-2 0v-1.418a1 1 0 0 1 1.038-.999 1.436 1.436 0 0 0 1.488-1.441 1.501 1.501 0 1 0-3-.116.986.986 0 0 1-1.037.961 1 1 0 0 1-.96-1.037A3.5 3.5 0 1 1 11 11.466Z"/>
            </svg>
          </div>
          <button id="dateRangeButton" data-dropdown-toggle="dateRangeDropdown" data-dropdown-ignore-click-outside-class="datepicker" type="button" class="inline-flex items-center text-blue-700 dark:text-blue-600 font-medium hover:underline">
            {{ date('d M', strtotime($startDate)) }} - {{ date('d M', strtotime($endDate)) }}
            <svg class="w-3 h-3 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
          </button>
        </div>
        <div class="flex flex-col items-center justify-center py-4">
          <div class="chart-container" style="height: 260px; width: 260px;">
            <canvas id="category-distribution-chart"></canvas>
          </div>
        </div>
        <div class="flex flex-wrap justify-center gap-4 mt-4">
          @php
            $pieColors = ['#FF5733','#33FF57','#3357FF','#FF33A1','#FFBD33','#10b981','#f472b6','#6366f1','#f59e42','#e11d48'];
          @endphp
          @foreach($pieLabels as $i => $label)
            <div class="flex items-center space-x-2">
              <span class="w-3 h-3 rounded-full" style="background: {{ $pieColors[$i % count($pieColors)] }}"></span>
              <span class="text-sm">{{ $label }}</span>
            </div>
          @endforeach
        </div>
        <div class="flex justify-between items-center border-t border-gray-200 dark:border-gray-700 mt-6 pt-4">
          <div class="flex items-center">
            <button id="dropdownDefaultButton" data-dropdown-toggle="lastDaysdropdown" data-dropdown-placement="bottom" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 text-center inline-flex items-center dark:hover:text-white" type="button">
              Last 7 days
              <svg class="w-2.5 m-2.5 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
              </svg>
            </button>
            <div id="lastDaysdropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700">
              <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Kemarin</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Hari Ini</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">7 Hari Terakhir</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">30 Hari Terakhir</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">90 Hari Terakhir</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Transactions Table -->
  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100">
      <h4 class="text-lg font-semibold text-gray-700">Transaksi Terbaru</h4>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beban</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @foreach($recentTransactions as $transaction)
          <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ date('d/m/Y', strtotime($transaction->Tanggal)) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ $transaction->kode }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ $transaction->kategori }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ $transaction->keterangan }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
              {{ $transaction->uang_masuk > 0 ? 'Rp ' . number_format($transaction->uang_masuk, 0, ',', '.') : '-' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
              {{ $transaction->uang_keluar > 0 ? 'Rp ' . number_format($transaction->uang_keluar, 0, ',', '.') : '-' }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
// Debugging untuk memastikan script berjalan
console.log('Script started');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Format number to Rupiah
    function formatRupiah(number) {
        return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    try {
        // Financial Trend Chart
        const trendCtx = document.getElementById('financial-trend-chart');
        if (trendCtx) {
            const monthlyData = @json($monthlyTotals);
            const labels = monthlyData.map(item => {
                const date = new Date(item.periode);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            });
            const pendapatanData = monthlyData.map(item => parseFloat(item.total_debit) || 0);
            const bebanData = monthlyData.map(item => parseFloat(item.total_kredit) || 0);

            // Create gradient for area fill
            const gradientFill = trendCtx.getContext('2d').createLinearGradient(0, 0, 0, 400);
            gradientFill.addColorStop(0, 'rgba(34, 197, 94, 0.2)');
            gradientFill.addColorStop(1, 'rgba(34, 197, 94, 0)');

            window.trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: pendapatanData,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: gradientFill,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: 'rgb(34, 197, 94)',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                        cubicInterpolationMode: 'monotone',
                        stepped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.y;
                                    return formatRupiah(value);
                                },
                                title: function(context) {
                                    return `Tanggal: ${context[0].label}`;
                                }
                            },
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 7
                            }
                        }
                    },
                    elements: {
                        line: {
                            tension: 0.4
                        }
                    }
                }
            });

            // Debug untuk memastikan data terisi
            console.log('Chart Data:', {
                labels: labels,
                data: pendapatanData
            });
        }

        // Pie Chart Neraca Saldo
        var ctx = document.getElementById('category-distribution-chart').getContext('2d');
        var pieLabels = @json($pieLabels);
        var pieData = @json($pieData);
        var pieColors = [
            '#FF5733','#33FF57','#3357FF','#FF33A1','#FFBD33','#10b981','#f472b6','#6366f1','#f59e42','#e11d48'
        ];
        var total = pieData.reduce((a, b) => a + b, 0);
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: pieColors,
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        color: '#fff',
                        font: { weight: 'bold', size: 16 },
                        formatter: function(value) {
                            var percent = total ? (value / total * 100) : 0;
                            return percent.toFixed(1) + '%';
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed || 0;
                                var percent = total ? (value / total * 100) : 0;
                                return label + ': ' + value.toLocaleString('id-ID', {style:'currency', currency:'IDR'}) + ' (' + percent.toFixed(1) + '%)';
                            }
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    } catch (error) {
        console.error('Error creating charts:', error);
    }
});

// Function to update chart type
function updateChartType(type) {
    if (window.trendChart) {
        window.trendChart.config.type = type;
        window.trendChart.update();
    }
}

// Script untuk menangani toggle switch
document.addEventListener('DOMContentLoaded', function() {
  const showAllSwitch = document.getElementById('showAllSwitch');
  
  showAllSwitch.addEventListener('change', function() {
    if (this.checked) {
      // Redirect ke halaman tampilkan semua
      window.location.href = "{{ route('home.all') }}";
    } else {
      // Redirect ke halaman dengan filter tanggal bulan ini
      const today = new Date();
      const startDate = today.toISOString().split('T')[0].substring(0, 8) + '01';
      const endDate = today.toISOString().split('T')[0];
      
      window.location.href = `{{ route('home.filter') }}?start_date=${startDate}&end_date=${endDate}`;
    }
  });
});
</script>

@endsection