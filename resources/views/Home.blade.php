@extends('Core.Sidebar')

@section('content')

<div class="max-w-full bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg shadow-md dark:bg-gray-800 p-6">
  <!-- Menambahkan elemen select di atas tampilan dengan desain yang lebih menarik -->
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-indigo-700 dark:text-white">Dashboard</h2>
    <select class="sm:ml-auto mt-3 sm:mt-0 sm:w-auto form-select box bg-white border border-indigo-200 rounded-lg text-indigo-600 font-medium focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
      <option value="daily">Daily</option>
      <option value="weekly">Weekly</option>
      <option value="monthly">Monthly</option>
      <option value="yearly">Yearly</option>
      <option value="custom-date">Custom Date</option>
    </select>
  </div>

  <!-- Card dengan desain yang lebih modern dan berwarna -->
  <div class="flex justify-between pb-4 mb-4 border-b border-indigo-100 dark:border-gray-700 bg-white rounded-xl p-4 shadow-sm">
    <div class="flex items-center">
      <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-700 flex items-center justify-center me-3">
        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 19">
          <path d="M14.5 0A3.987 3.987 0 0 0 11 2.1a4.977 4.977 0 0 1 3.9 5.858A3.989 3.989 0 0 0 14.5 0ZM9 13h2a4 4 0 0 1 4 4v2H5v-2a4 4 0 0 1 4-4Z"/>
          <path d="M5 19h10v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2ZM5 7a5.008 5.008 0 0 1 4-4.9 3.988 3.988 0 1 0-3.9 5.859A4.974 4.974 0 0 1 5 7Zm5 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm5-1h-.424a5.016 5.016 0 0 1-1.942 2.232A6.007 6.007 0 0 1 17 17h2a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5ZM5.424 9H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h2a6.007 6.007 0 0 1 4.366-5.768A5.016 5.016 0 0 1 5.424 9Z"/>
        </svg>
      </div>
      <div>
        <h5 class="leading-none text-2xl font-bold text-indigo-800 dark:text-white pb-1">3.4k</h5>
        <p class="text-sm font-medium text-indigo-500 dark:text-indigo-300">Leads generated per week</p>
      </div>
    </div>
    <div>
      <span class="bg-emerald-100 text-emerald-600 text-xs font-bold inline-flex items-center px-3 py-1.5 rounded-lg dark:bg-emerald-900 dark:text-emerald-300">
        <svg class="w-3 h-3 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4"/>
        </svg>
        42.5%
      </span>
    </div>
  </div>

  <!-- Grid dengan cards metrics yang lebih menarik -->
  <div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-4 rounded-xl shadow-sm">
      <dl class="flex items-center">
        <dt class="text-purple-500 dark:text-purple-300 text-sm font-medium me-1">Money spent:</dt>
        <dd class="text-purple-700 text-sm dark:text-white font-bold">$3,232</dd>
      </dl>
    </div>
    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-4 rounded-xl shadow-sm">
      <dl class="flex items-center justify-end">
        <dt class="text-blue-500 dark:text-blue-300 text-sm font-medium me-1">Conversion rate:</dt>
        <dd class="text-blue-700 text-sm dark:text-white font-bold">1.2%</dd>
      </dl>
    </div>
  </div>

  <div class="flex mt-6 flex-col md:flex-row gap-4">
    <!-- Chart dengan background yang lebih menarik -->
    <div class="flex-1 bg-white p-4 rounded-xl shadow-sm">
      <h4 class="text-lg font-bold text-gray-700 mb-2">Sales Analytics</h4>
      <canvas id="vertical-bar-chart-widget" height="200" class="mt-4"></canvas>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const ctx = document.getElementById('vertical-bar-chart-widget').getContext('2d');
          const myChart = new Chart(ctx, {
              type: 'bar',
              data: {
                  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                  datasets: [{
                      label: 'Sales',
                      data: [12, 19, 14, 24, 16, 28],
                      backgroundColor: [
                        'rgba(79, 70, 229, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(236, 72, 153, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(139, 92, 246, 0.7)'
                      ],
                      borderColor: [
                        'rgba(79, 70, 229, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(139, 92, 246, 1)'
                      ],
                      borderWidth: 1,
                      borderRadius: 6
                  }]
              },
              options: {
                  responsive: true,
                  plugins: {
                    legend: {
                      position: 'top',
                    }
                  },
                  scales: {
                      y: {
                          beginAtZero: true,
                          grid: {
                            drawBorder: false,
                            color: 'rgba(226, 232, 240, 0.7)'
                          }
                      },
                      x: {
                        grid: {
                          display: false
                        }
                      }
                  }
              }
          });
        });
      </script>
    </div>

    <!-- Card laporan penjualan dengan design yang lebih menarik -->
    <div class="md:w-1/3 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl p-6 flex flex-col justify-center text-white shadow-lg">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag w-12 h-12 text-white/80 mb-4">
        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <path d="M16 10a4 4 0 0 1-8 0"></path>
      </svg> 
      <div class="relative text-4xl font-bold mb-2"> 
        <span class="absolute text-2xl font-medium top-0 left-0 -ml-1">$</span> 54.143 
      </div>
      <div class="bg-white/20 text-white px-3 py-1 rounded-full inline-flex items-center text-sm font-bold mt-2 w-fit"> 
        47% 
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-up w-4 h-4 ml-1">
          <polyline points="18 15 12 9 6 15"></polyline>
        </svg> 
      </div>
      <div class="mt-4 text-white/80 text-sm">Sales earnings this month after associated author fees, &amp; before taxes.</div>
      <button class="bg-white text-indigo-700 hover:bg-indigo-50 font-bold px-6 py-3 rounded-full mt-6 transition flex items-center justify-between">
        Download Reports 
        <span class="w-8 h-8 flex justify-center items-center bg-indigo-600 text-white rounded-full ml-2"> 
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right w-4 h-4">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg> 
        </span>
      </button>
    </div>
  </div>

  <!-- Footer element dengan design yang lebih baik -->
  <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between mt-6 pt-4">
    <div class="flex justify-between items-center">
      <!-- Button dengan design yang lebih menarik -->
      <button
        id="dropdownDefaultButton"
        data-dropdown-toggle="lastDaysdropdown"
        data-dropdown-placement="bottom"
        class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 text-center inline-flex items-center dark:hover:text-white bg-indigo-50 px-4 py-2 rounded-lg hover:bg-indigo-100 transition"
        type="button">
        Last 7 days
        <svg class="w-3 h-3 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </button>
      <!-- Dropdown menu -->
      <div id="lastDaysdropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-md w-44 dark:bg-gray-700">
          <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-600 dark:hover:text-white">Yesterday</a>
            </li>
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-600 dark:hover:text-white">Today</a>
            </li>
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-600 dark:hover:text-white">Last 7 days</a>
            </li>
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-600 dark:hover:text-white">Last 30 days</a>
            </li>
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-600 dark:hover:text-white">Last 90 days</a>
            </li>
          </ul>
      </div>
      <a
        href="#"
        class="uppercase text-sm font-bold inline-flex items-center rounded-lg text-indigo-600 hover:text-indigo-700 dark:hover:text-indigo-400 bg-indigo-50 hover:bg-indigo-100 dark:hover:bg-indigo-700 dark:bg-indigo-800 px-4 py-2 transition">
        Leads Report
        <svg class="w-3 h-3 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
        </svg>
      </a>
    </div>
  </div>
</div>

@endsection