@extends('Core.Sidebar')
@section('content')
    <title>Data Akun User</title>
    <!-- ... existing code ... -->
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
                <button id="createUser" class="btn btn-primary">Buat Akun</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full bg-white border border-gray-300 print-table">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">Email</th>
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
        // Function to load users
        function loadUsers() {
            fetch('/users/data')
                .then(response => response.json())
                .then(data => {
                    console.log("Data dari API:", data);
                    const userTableBody = document.getElementById('userTableBody');
                    userTableBody.innerHTML = '';
                    
                    // Menambahkan data pengguna baru
                    userTableBody.innerHTML += `
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4">1</td>
                            <td class="py-3 px-4">Widodo</td>
                            <td class="py-3 px-4">widodo@gmail.com</td>
                            <td class="py-3 px-4 text-center">
                                <button onclick="editUser(1)" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteUser(1)" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4">2</td>
                            <td class="py-3 px-4">Siti</td>
                            <td class="py-3 px-4">siti@gmail.com</td>
                            <td class="py-3 px-4 text-center">
                                <button onclick="editUser(2)" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteUser(2)" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4">3</td>
                            <td class="py-3 px-4">Budi</td>
                            <td class="py-3 px-4">budi@gmail.com</td>
                            <td class="py-3 px-4 text-center">
                                <button onclick="editUser(3)" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteUser(3)" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    
                    // Menambahkan data pengguna dari API
                    data.forEach((user, index) => {
                        userTableBody.innerHTML += `
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4">${index + 4}</td>
                                <td class="py-3 px-4">${user.name}</td>
                                <td class="py-3 px-4">${user.email}</td>
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

        // Event listener for creating a user
        document.getElementById('createUser').addEventListener('click', () => {
            const name = document.getElementById('userName').value;
            const email = document.getElementById('userEmail').value;
            const password = document.getElementById('userPassword').value;

            fetch('/users/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ name, email, password }),
            })
            .then(response => response.json())
            .then(data => {
                loadUsers(); // Reload users after creation
                // Clear input fields
                document.getElementById('userName').value = '';
                document.getElementById('userEmail').value = '';
                document.getElementById('userPassword').value = '';
            });
        });

        // Initial load
        loadUsers();
    </script>
@endsection