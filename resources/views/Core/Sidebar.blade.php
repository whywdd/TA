<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Budi - Financial Management System</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Custom Gradient Background for Sidebar */
        .sidebar {
            background: linear-gradient(to bottom, #00c6ff, #0072ff);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            z-index: 30;
            transition: transform 0.3s ease-in-out;
        }

        /* Main Content Spacing */
        .main-content {
            margin-left: 16rem; /* w-64 = 16rem */
            padding-top: 4rem; /* Space for fixed navbar */
            min-height: 100vh;
            background-color: #f3f4f6;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Fixed Navbar Styling */
        .navbar {
            background: linear-gradient(to bottom, #00c6ff, #0072ff);
            position: fixed;
            top: 0;
            right: 0;
            left: 16rem;
            z-index: 20;
            transition: left 0.3s ease-in-out;
        }

        /* Hover Effects */
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        /* Active Link Styling */
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 4px solid white;
        }

        /* Scrollbar Styling */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        /* Mobile Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .navbar {
                left: 0;
            }
        }

        /* Dropdown Menu Animation */
        .dropdown-menu {
            transform-origin: top;
            transition: transform 0.2s ease-in-out;
        }

        .dropdown-menu.hidden {
            transform: scaleY(0);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="sidebar w-64 p-5 text-white">
        <!-- Logo -->
        <div class="text-2xl font-bold mb-8 flex items-center">
            <i class="fas fa-book-open mr-3"></i>
            Buku Budi
        </div>

        <!-- Navigation Links -->
        <ul>
            <li class="mb-4">
                <a href="{{ route('home') }}" class="nav-link flex items-center p-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-home mr-3"></i>
                    <span>Home</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="{{ route('uang-masuk.index') }}" class="nav-link flex items-center p-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-money-bill-wave mr-3"></i>
                    <span>Uang Masuk</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="{{ route('uang-keluar.index') }}" class="nav-link flex items-center p-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-money-bill-wave-alt mr-3"></i>
                    <span>Uang Keluar</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="{{ route('input-gaji.index') }}" class="nav-link flex items-center p-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-money-bill-wave-alt mr-3"></i>
                    <span>Input Gaji</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="{{ route('gaji.index') }}" class="nav-link flex items-center p-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-user mr-3"></i>
                    <span>Data Karyawan</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="{{ route('Laporan.index') }}" class="nav-link flex items-center p-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-file-alt mr-3"></i>
                    <span>Laporan</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="{{ route('User.index') }}" class="nav-link flex items-center p-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-user mr-3"></i> <!-- Mengganti ikon dengan fa-user -->
                    <span>User</span>
                </a>
            </li>
            <li class="mt-8">
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="submit" class="nav-link w-full flex items-center p-2 rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Fixed Navbar -->
        <nav class="navbar bg-white shadow-md">
            <div class="max-w-full mx-auto px-4">
                <div class="flex justify-between h-16">
                    <!-- Left Side - Mobile Menu Button -->
                    <div class="flex items-center md:hidden">
                        <button id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                            <span class="sr-only">Open main menu</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>

                    <!-- Center - Desktop Navigation -->
                    <div class="hidden md:flex md:items-center md:space-x-8">
                    </div>

                    <!-- Right Side - User Menu & Notifications -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->


                        <!-- User Profile -->
                        <div class="relative" x-data="{ open: false }">
                            <div class="flex items-center space-x-3 cursor-pointer">
                                <div class="col-span-6 sm:col-span-3 lg:col-span-2 xl:col-span-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user block mx-auto">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg> 
                                    <div class="text-center text-xs mt-2"></div>
                                </div>
                                <span class="text-white font-medium">User Name</span> 
                                <i class="fas fa-chevron-down text-white"></i>
                            </div>
                        </div>

            <!-- Mobile Navigation Menu -->
            <div class="hidden md:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 border-t border-gray-200">
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-blue-600">Dashboard</a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-blue-600">Profil</a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-blue-600">Pengaturan</a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-blue-600">Bantuan</a>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="p-4">
            @yield('content')
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const sidebar = document.querySelector('.sidebar');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            sidebar.classList.toggle('active');
        });

        // Add active class to current nav link
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !mobileMenuButton.contains(e.target)) {
                sidebar.classList.remove('active');
                mobileMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>