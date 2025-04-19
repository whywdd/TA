@extends('Core.Sidebar')

@section('content')
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
    <form action="{{ route('home.filter') }}" method="GET" class="flex flex-wrap items-center gap-4 sm:ml-auto">
      <div class="flex items-center gap-2">
        <span class="text-sm text-gray-600">Tanggal Awal</span>
        <input type="date" name="start_date" class="form-input box bg-white border border-indigo-200 rounded-lg text-gray-600" value="{{ $startDate }}">
      </div>
      <div class="flex items-center gap-2">
        <span class="text-sm text-gray-600">Tanggal Akhir</span>
        <input type="date" name="end_date" class="form-input box bg-white border border-indigo-200 rounded-lg text-gray-600" value="{{ $endDate }}">
      </div>
      <button type="submit" class="btn bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600">
        Filter
      </button>
    </form>
  </div>

  <!-- Main Metrics -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <!-- Total Saldo Card -->
    <!-- <div class="bg-white rounded-xl p-6 shadow-sm">
      <div class="flex items-center">
        <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center me-3">
          <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div>
          <p class="text-sm text-gray-500 mb-1">Total Saldo</p>
          <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</h3>
          <p class="text-sm {{ $growthPercentage >= 0 ? 'text-green-500' : 'text-red-500' }}">
            {{ $growthPercentage >= 0 ? '↑' : '↓' }} {{ abs(round($growthPercentage, 1)) }}% dari bulan lalu
          </p>
        </div>
      </div>
    </div> -->

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
          <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalUangMasuk, 0, ',', '.') }}</h3>
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
          <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalUangKeluar, 0, ',', '.') }}</h3>
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
        <span class="text-sm font-medium text-green-600">Rp {{ number_format(abs($item['nominal']), 0, ',', '.') }}</span>
      </div>
      @endforeach
      <div class="mt-4 pt-4 border-t">
        <div class="flex justify-between items-center">
          <span class="font-semibold">Total Pendapatan</span>
          <span class="font-semibold text-green-600">Rp {{ number_format($totalUangMasuk, 0, ',', '.') }}</span>
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
        <span class="text-sm font-medium text-red-600">Rp {{ number_format(abs($item['nominal']), 0, ',', '.') }}</span>
      </div>
      @endforeach
      <div class="mt-4 pt-4 border-t">
        <div class="flex justify-between items-center">
          <span class="font-semibold">Total Beban</span>
          <span class="font-semibold text-red-600">Rp {{ number_format($totalUangKeluar, 0, ',', '.') }}</span>
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
            Rp {{ number_format(abs($labaRugiTotal), 0, ',', '.') }}
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
            Rp {{ number_format(abs($labaRugiBulanIni), 0, ',', '.') }}
          </h3>
          <p class="text-sm text-gray-500">{{ $labaRugiBulanIni >= 0 ? 'Laba' : 'Rugi' }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Bar Chart -->
    <div class="bg-white p-6 rounded-xl shadow-sm">
      <h4 class="text-lg font-semibold text-gray-700 mb-4">Tren Pendapatan & Beban</h4>
      <div class="chart-container">
        <canvas id="financial-trend-chart"></canvas>
      </div>
    </div>

    <!-- Category Chart -->
    <div class="bg-white p-6 rounded-xl shadow-sm">
      <h4 class="text-lg font-semibold text-gray-700 mb-4">Distribusi per Kategori</h4>
      <div class="chart-container">
        <canvas id="category-distribution-chart"></canvas>
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

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
                const [year, month] = item.periode.split('-');
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                return `${monthNames[parseInt(month)-1]} ${year}`;
            });
            const pendapatanData = monthlyData.map(item => parseFloat(item.total_debit) || 0);
            const bebanData = monthlyData.map(item => parseFloat(item.total_kredit) || 0);

            new Chart(trendCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: pendapatanData,
                        backgroundColor: 'rgba(34, 197, 94, 0.5)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1,
                        order: 2
                    }, {
                        label: 'Beban',
                        data: bebanData,
                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1,
                        order: 2
                    }, {
                        label: 'Laba/Rugi',
                        data: pendapatanData.map((pendapatan, index) => pendapatan - bebanData[index]),
                        type: 'line',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2,
                        fill: false,
                        order: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatRupiah(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + formatRupiah(context.parsed.y);
                                }
                            }
                        }
                    }
                }
            });
        }

        // Category Distribution Chart
        const categoryCtx = document.getElementById('category-distribution-chart');
        if (categoryCtx) {
            const categoryData = @json($categoryTotals);
            const categories = categoryData.map(item => item.kategori);
            const pendapatanPerKategori = categoryData.map(item => parseFloat(item.total_debit) || 0);
            const bebanPerKategori = categoryData.map(item => parseFloat(item.total_kredit) || 0);

            new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: categories,
                    datasets: [{
                        label: 'Pendapatan',
                        data: pendapatanPerKategori,
                        backgroundColor: 'rgba(34, 197, 94, 0.5)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1
                    }, {
                        label: 'Beban',
                        data: bebanPerKategori,
                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatRupiah(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + formatRupiah(context.parsed.x);
                                }
                            }
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error creating charts:', error);
    }
});
</script>
@endpush

@endsection