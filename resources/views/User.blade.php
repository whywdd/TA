@extends('Core.Sidebar')
@section('content')
    <title>Data Akun User</title>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal.show {
            display: block;
        }

        .modal-slide-over {
            transform: translateX(100%);
            transition: transform 0.3s ease-out;
        }

        .modal.show .modal-slide-over {
            transform: translateX(0);
        }

        /* Animation */
        .intro-y {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Scrollbar */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .box {
                margin: 0.5rem;
                padding: 0.5rem;
            }

            .print-table {
                font-size: 0.875rem;
            }

            .print-table th,
            .print-table td {
                padding: 0.5rem;
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
            <!-- Form Section for Creating User -->
            <div class="flex flex-wrap gap-4 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <label class="mr-2 text-sm font-medium text-gray-600">Nama:</label>
                    <input type="text" id="userName" class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama User">
                </div>
                <div class="flex items-center">
                    <label class="mr-2 text-sm font-medium text-gray-600">Email:</label>
                    <input type="email" id="userEmail" class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Email User">
                </div>
                <div class="flex items-center">
                    <label class="mr-2 text-sm font-medium text-gray-600">Password:</label>
                    <input type="password" id="userPassword" class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Password User">
                </div>
                <div class="flex items-center">
                    <label class="mr-2 text-sm font-medium text-gray-600">Tipe Pengguna:</label>
                    <select id="userType" class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="karyawan">Karyawan</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                <button id="createUser" class="btn btn-primary">Buat Akun</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full bg-white border border-gray-300 print-table">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">Email</th>
                            <th class="py-3 px-4 text-left">Tipe Pengguna</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <!-- User data will be populated here -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center py-5 px-4">
                <!-- ... existing pagination code ... -->
            </div>
        </div>
    </div>

    <!-- JavaScript for handling user CRUD operations -->
    <script>
        // Notifikasi SweetAlert2
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

        // Function to load users
        function loadUsers() {
            fetch('/users/data')
                .then(response => response.json())
                .then(data => {
                    const userTableBody = document.getElementById('userTableBody');
                    userTableBody.innerHTML = '';
                    
                    data.forEach((user, index) => {
                        userTableBody.innerHTML += `
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4">${index + 1}</td>
                                <td class="py-3 px-4">${user.nama}</td>
                                <td class="py-3 px-4">${user.email}</td>
                                <td class="py-3 px-4">${user.tipe_pengguna}</td>
                                <td class="py-3 px-4 text-center">
                                    <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                })
                .catch(error => console.error("Error fetching data:", error));
        }

        // Function untuk mengedit user
        function editUser(id) {
            window.location.href = `/User/${id}/edit`;
        }

        // Event listener for creating a user
        document.getElementById('createUser').addEventListener('click', () => {
            const nama = document.getElementById('userName').value;
            const email = document.getElementById('userEmail').value;
            const password = document.getElementById('userPassword').value;
            const tipe_pengguna = document.getElementById('userType').value;

            // Redirect ke route User.create
            window.location.href = "{{ route('User.create') }}";
        });

        // Function untuk menghapus user
        function deleteUser(id) {
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
                    // Buat form untuk delete request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/User/${id}`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Initial load
        loadUsers();
    </script>
@endsection