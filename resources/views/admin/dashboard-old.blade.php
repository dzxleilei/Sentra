<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sentra Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-red-600 text-white p-6">
            <div class="mb-8">
                <h1 class="text-2xl font-bold">Sentra Booking</h1>
                <p class="text-sm text-red-100 mt-1">Administrator</p>
            </div>

            <nav class="space-y-4 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-lg bg-red-700 hover:bg-red-800 transition">
                    📊 Dashboard
                </a>
                <div class="border-t border-red-500 my-4"></div>
                <p class="text-xs font-semibold text-red-100 px-4 py-2">MANAJEMEN SARANA</p>
                <a href="{{ route('admin.ruangan') }}" class="block px-4 py-3 rounded-lg hover:bg-red-700 transition">
                    🏠 Kelola Ruangan
                </a>
                <a href="{{ route('admin.barang') }}" class="block px-4 py-3 rounded-lg hover:bg-red-700 transition">
                    📦 Kelola Barang
                </a>
                <div class="border-t border-red-500 my-4"></div>
                <p class="text-xs font-semibold text-red-100 px-4 py-2">MONITORING & PENALTI</p>
                <a href="{{ route('admin.pelanggaran') }}" class="block px-4 py-3 rounded-lg hover:bg-red-700 transition">
                    ⚠️ Kelola Penalti
                </a>
                <a href="{{ route('admin.laporan') }}" class="block px-4 py-3 rounded-lg hover:bg-red-700 transition">
                    📈 Laporan
                </a>
                <a href="{{ route('admin.notifikasi') }}" class="block px-4 py-3 rounded-lg hover:bg-red-700 transition">
                    🔔 Notifikasi
                </a>
            </nav>

            <div class="mt-8 pt-8 border-t border-red-500">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 rounded-lg bg-yellow-600 hover:bg-yellow-700 transition text-left">
                        🚪 Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-800">Admin Dashboard</h2>
                    <p class="text-gray-600 mt-2">Selamat datang, {{ Auth::user()->name }}!</p>
                </div>

                <!-- Statistik Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm">Booking Hari Ini</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalBookingHariIni }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm">Total Pelanggaran</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">{{ $totalPelanggaran }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm">Total Ruangan</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $totalRuangan }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm">Total Barang</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $totalBarang }}</p>
                    </div>
                </div>

                <!-- Alert Section -->
                @if($bookingTanpaCheckin > 0)
                    <div class="mb-8 bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-red-800 font-semibold">⚠️ Ada {{ $bookingTanpaCheckin }} booking yang belum check-in!</p>
                    </div>
                @endif

                <!-- Pelanggaran Terbaru -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Pelanggaran Terbaru yang Perlu Ditindak</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($pelanggaram as $pelanggaran)
                            <div class="px-6 py-4 hover:bg-gray-50 transition">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $pelanggaran->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $pelanggaran->catatan_pelanggaran }}</p>
                                    </div>
                                    <a href="{{ route('admin.pelanggaran') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                        Tindak Lanjut
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-600">
                                <p>✓ Tidak ada pelanggaran yang perlu ditindak</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>