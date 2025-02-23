@extends('Core.Sidebar')

@section('content')

<div class="max-w-full bg-white rounded-lg shadow-sm dark:bg-gray-800 p-6">
  <!-- Menambahkan elemen select di atas tampilan -->
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold">Dashboard</h2>
    <select class="sm:ml-auto mt-3 sm:mt-0 sm:w-auto form-select box">
      <option value="daily">Daily</option>
      <option value="weekly">Weekly</option>
      <option value="monthly">Monthly</option>
      <option value="yearly">Yearly</option>
      <option value="custom-date">Custom Date</option>
    </select>
  </div>

  <div class="flex justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center">
      <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center me-3">
        <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 19">
          <path d="M14.5 0A3.987 3.987 0 0 0 11 2.1a4.977 4.977 0 0 1 3.9 5.858A3.989 3.989 0 0 0 14.5 0ZM9 13h2a4 4 0 0 1 4 4v2H5v-2a4 4 0 0 1 4-4Z"/>
          <path d="M5 19h10v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2ZM5 7a5.008 5.008 0 0 1 4-4.9 3.988 3.988 0 1 0-3.9 5.859A4.974 4.974 0 0 1 5 7Zm5 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm5-1h-.424a5.016 5.016 0 0 1-1.942 2.232A6.007 6.007 0 0 1 17 17h2a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5ZM5.424 9H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h2a6.007 6.007 0 0 1 4.366-5.768A5.016 5.016 0 0 1 5.424 9Z"/>
        </svg>
      </div>
      <div>
        <h5 class="leading-none text-2xl font-bold text-gray-900 dark:text-white pb-1">3.4k</h5>
        <p class="text-sm font-normal text-gray-500 dark:text-gray-400">Leads generated per week</p>
      </div>
    </div>
    <div>
      <span class="bg-green-100 text-green-800 text-xs font-medium inline-flex items-center px-2.5 py-1 rounded-md dark:bg-green-900 dark:text-green-300">
        <svg class="w-2.5 h-2.5 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4"/>
        </svg>
        42.5%
      </span>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-4 mb-6">
    <dl class="flex items-center">
      <dt class="text-gray-500 dark:text-gray-400 text-sm font-normal me-1">Money spent:</dt>
      <dd class="text-gray-900 text-sm dark:text-white font-semibold">$3,232</dd>
    </dl>
    <dl class="flex items-center justify-end">
      <dt class="text-gray-500 dark:text-gray-400 text-sm font-normal me-1">Conversion rate:</dt>
      <dd class="text-gray-900 text-sm dark:text-white font-semibold">1.2%</dd>
    </dl>
  </div>

  <div class="flex mt-6">
    <!-- Menambahkan elemen canvas untuk chart -->
    <div class="flex-1">
      <canvas id="vertical-bar-chart-widget" height="200" class="mt-4"></canvas>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const ctx = document.getElementById('vertical-bar-chart-widget').getContext('2d');
          const myChart = new Chart(ctx, {
              type: 'bar',
              data: {
                  labels: ['Label 1', 'Label 2', 'Label 3'],
                  datasets: [{
                      label: 'Dataset 1',
                      data: [12, 19, 3],
                      backgroundColor: 'rgba(75, 192, 192, 0.2)',
                      borderColor: 'rgba(75, 192, 192, 1)',
                      borderWidth: 1
                  }]
              },
              options: {
                  scales: {
                      y: {
                          beginAtZero: true
                      }
                  }
              }
          });
        });
      </script>
    </div>

    <!-- Menambahkan elemen baru untuk laporan penjualan di samping chart -->
    <div class="px-6 py-4 flex flex-col justify-center">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag w-10 h-10 text-theme-12 mb-4">
        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <path d="M16 10a4 4 0 0 1-8 0"></path>
      </svg> 
      <div class="relative text-3xl font-medium"> 
        <span class="absolute text-2xl font-medium top-0 left-0 -ml-0.5">$</span> 54.143 
      </div>
      <div class="report-box-2__indicator bg-theme-9 tooltip cursor-pointer mt-2"> 
        47% 
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-up w-4 h-4 ml-0.5">
          <polyline points="18 15 12 9 6 15"></polyline>
        </svg> 
      </div>
      <div class="mt-4 text-gray-600 dark:text-gray-600">Sales earnings this month after associated author fees, &amp; before taxes.</div>
      <button class="btn btn-outline-secondary relative justify-start rounded-full mt-4">
        Download Reports 
        <span class="w-8 h-8 absolute flex justify-center items-center bg-theme-1 text-white rounded-full right-0 top-0 bottom-0 my-auto ml-auto mr-0.5"> 
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right w-4 h-4">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg> 
        </span>
      </button>
    </div>
  </div>

  <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between mt-6">
    <div class="flex justify-between items-center pt-5">
      <!-- Button -->
      <button
        id="dropdownDefaultButton"
        data-dropdown-toggle="lastDaysdropdown"
        data-dropdown-placement="bottom"
        class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 text-center inline-flex items-center dark:hover:text-white"
        type="button">
        Last 7 days
        <svg class="w-2.5 m-2.5 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </button>
      <!-- Dropdown menu -->
      <div id="lastDaysdropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700">
          <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Yesterday</a>
            </li>
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Today</a>
            </li>
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last 7 days</a>
            </li>
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last 30 days</a>
            </li>
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last 90 days</a>
            </li>
          </ul>
      </div>
      <a
        href="#"
        class="uppercase text-sm font-semibold inline-flex items-center rounded-lg text-blue-600 hover:text-blue-700 dark:hover:text-blue-500  hover:bg-gray-100 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 px-3 py-2">
        Leads Report
        <svg class="w-2.5 h-2.5 ms-1.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
        </svg>
      </a>
    </div>
  </div>
</div>

@endsection