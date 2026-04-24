<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Verifikator - Sentra Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 text-white p-6" style="background-color: #317EFB;">
            <div class="mb-8">
                <h1 class="text-2xl font-bold">Sentra Booking</h1>
                <p class="text-sm text-blue-100 mt-1">Verifikator</p>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('verifikator.dashboard') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('verifikator.dashboard') ? 'hover:opacity-80' : 'hover:opacity-80' }} transition" style="{{ request()->routeIs('verifikator.dashboard') ? 'background-color: rgba(255,255,255,0.1);' : '' }}">
                    <i class="fas fa-chart-line mr-2"></i> Dashboard
                </a>
                <div class="border-t border-blue-300 my-4"></div>
                <p class="text-xs font-semibold text-blue-100 px-4 py-2">TUGAS ANDA</p>
                <a href="{{ route('verifikator.notifikasi') }}" class="block px-4 py-3 rounded-lg hover:opacity-80 transition">
                    <i class="fas fa-tasks mr-2"></i> Daftar Validasi
                </a>
                <a href="{{ route('verifikator.pelanggaran') }}" class="block px-4 py-3 rounded-lg hover:opacity-80 transition">
                    <i class="fas fa-flag mr-2"></i> Laporan Pelanggaran
                </a>
            </nav>

            <div class="mt-8 pt-8 border-t border-blue-300">
                <div class="space-y-2 mb-4">
                    <a href="{{ route('setting') }}" class="block px-4 py-3 rounded-lg hover:opacity-80 transition text-sm">
                        <i class="fas fa-gear mr-2"></i> Setting
                    </a>
                    <a href="{{ route('verifikator.faq') }}" class="block px-4 py-3 rounded-lg hover:opacity-80 transition text-sm">
                        <i class="fas fa-question-circle mr-2"></i> FAQ
                    </a>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 rounded-lg hover:opacity-80 transition text-left" style="background-color: rgba(255,255,255,0.15);">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-800">Dashboard Verifikator</h2>
                    <p class="text-gray-600 mt-2">Validasi dan verifikasi setiap peminjaman aset</p>
                </div>

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
