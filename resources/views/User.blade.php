@extends('Core.Sidebar')
@section('content')
    <title>Data Akun User</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- Tambahkan variabel PHP untuk tipe pengguna -->
    @php
        $isOwner = auth()->user()->tipe_pengguna === 'owner';
    @endphp
    
    <style>
        /* Gradient Background */
        body {
            background: #f3f4f6;
        }

        /* Card Styling */
        .box {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Table Styling */
        .print-table {
            border-collapse: collapse;
            width: 100%;
        }

        .print-table th {
            background-color: #f9fafb;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .print-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .print-table tbody tr:hover {
            background-color: #f9fafb;
            transition: all 0.3s ease;
        }

        /* Button Styling */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        /* Filter Controls */
        .filter-controls {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-input {
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 0.5rem;
            min-width: 200px;
        }

        .filter-input:focus {
            outline: none;
            border-color: #3b82f6;
            ring: 2px;
            ring-color: #93c5fd;
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .filter-controls {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .filter-input {
                width: 100%;
            }
        }
    </style>

    <div class="box p-4 intro-y mt-5">
        <div class="intro-y">
            <!-- Header -->
            <div class="mb-4 bg-blue-600 text-white p-4 rounded-lg shadow-md">
                <h1 class="text-2xl font-bold">Akun User</h1>
                <p class="text-sm mt-1">Halaman ini menampilkan dan mengelola akun user.</p>
            </div>

            <!-- Filter dan Tombol Tambah -->
            <div class="filter-controls">
                <div class="filter-group">
                    <label for="userType" class="font-medium">Tipe User:</label>
                    <select id="userType" class="filter-input">
                        <option value="">Semua</option>
                        <option value="karyawan">Karyawan</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="searchUser" class="font-medium">Cari:</label>
                    <input type="text" id="searchUser" class="filter-input" placeholder="Cari nama/email...">
                </div>

                <div class="filter-group ml-auto">
                    @if($isOwner)
                        <a href="{{ route('User.create') }}" id="createUser" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            <span>Tambah User</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Tabel User -->
            <div class="overflow-x-auto">
                <table class="print-table">
                    <thead>
                        <tr>
                            <th class="text-left">No</th>
                            <th class="text-left">Nama</th>
                            <th class="text-left">Email</th>
                            <th class="text-left">Tipe User</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <!-- Data akan diisi melalui JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Tambahkan variabel JavaScript untuk tipe pengguna
        const isOwner = @json($isOwner);

        // Notifikasi SweetAlert2
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                confirmButtonText: 'OK'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session("error") }}',
                confirmButtonText: 'OK'
            });
        @endif

        document.addEventListener('DOMContentLoaded', function() {
            const userType = document.getElementById('userType');
            const searchUser = document.getElementById('searchUser');

            // Load initial data
            loadUsers();

            // Event listeners for filters
            userType.addEventListener('change', () => {
                loadUsers(userType.value, searchUser.value);
            });

            searchUser.addEventListener('input', () => {
                loadUsers(userType.value, searchUser.value);
            });
        });

        function loadUsers(typeFilter = '', searchFilter = '') {
            fetch(`/users/data?type=${typeFilter}&search=${searchFilter}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('userTableBody');
                    tbody.innerHTML = '';
                    
                    data.forEach((user, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${user.nama}</td>
                            <td>${user.email}</td>
                            <td>${user.tipe_pengguna}</td>
                            <td class="text-center">
                                ${isOwner ? `
                                    <a href="/User/${user.id}/edit" class="text-blue-600 hover:text-blue-800 mx-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-800 mx-1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                ` : ''}
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal memuat data user',
                        confirmButtonText: 'OK'
                    });
                });
        }

        function deleteUser(id) {
            if (!isOwner) {
                Swal.fire({
                    icon: 'error',
                    title: 'Akses Ditolak!',
                    text: 'Anda tidak memiliki akses untuk menghapus user',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    fetch(`/User/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Data user berhasil dihapus',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                loadUsers(
                                    document.getElementById('userType').value,
                                    document.getElementById('searchUser').value
                                );
                            });
                        } else {
                            throw new Error(data.message || 'Gagal menghapus user');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: error.message || 'Terjadi kesalahan saat menghapus data',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        }
    </script>
@endsection