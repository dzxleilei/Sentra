<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Verifikator - Sentra Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-orange-600 text-white p-6">
            <div class="mb-8">
                <h1 class="text-2xl font-bold">Sentra Booking</h1>
                <p class="text-sm text-orange-100 mt-1">Verifikator</p>
            </div>

            <nav class="space-y-4">
                <a href="{{ route('verifikator.dashboard') }}" class="block px-4 py-3 rounded-lg bg-orange-700 hover:bg-orange-800 transition">
                    📊 Dashboard
                </a>
                <a href="{{ route('verifikator.pelanggaran') }}" class="block px-4 py-3 rounded-lg hover:bg-orange-700 transition">
                    ⚠️ Laporan Pelanggaran
                </a>
                <a href="{{ route('verifikator.notifikasi') }}" class="block px-4 py-3 rounded-lg hover:bg-orange-700 transition">
                    🔔 Notifikasi
                </a>
            </nav>

            <div class="mt-8 pt-8 border-t border-orange-500">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 rounded-lg bg-red-600 hover:bg-red-700 transition text-left">
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
                    <h2 class="text-3xl font-bold text-gray-800">Verifikator Dashboard</h2>
                    <p class="text-gray-600 mt-2">Selamat datang, {{ Auth::user()->name }}!</p>
                </div>

                <!-- Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm">Booking Hari Ini</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $bookingHariIni->count() }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm">Sedang Berlangsung</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $sedangBerlangsung->count() }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-600 text-sm">Perlu Diverifikasi</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">{{ $perluDiverifikasi }}</p>
                    </div>
                </div>

                <!-- Booking Hari Ini -->
                <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Daftar Booking Hari Ini</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Peminjam</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Item/Ruangan</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Waktu Mulai</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($bookingHariIni as $booking)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">{{ $booking->user->name }}</td>
                                        <td class="px-6 py-4">{{ $booking->tipe === 'Barang' ? ($booking->thing->nama ?? '-') : ($booking->room->nama ?? '-') }}</td>
                                        <td class="px-6 py-4">{{ $booking->waktu_mulai_booking->format('H:i') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                                @if($booking->status === 'Berlangsung') bg-blue-100 text-blue-800
                                                @elseif($booking->status === 'Booking') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $booking->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('verifikator.booking.detail', $booking->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-600">
                                            Tidak ada booking hari ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Informasi Tanggung Jawab -->
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                    <h4 class="font-semibold text-orange-900 mb-3">📋 Tugas Verifikator</h4>
                    <ul class="text-sm text-orange-800 space-y-2">
                        <li>✓ Memantau dan validasi scan QR barang/ruangan</li>
                        <li>✓ Memeriksa kesesuaian dokumentasi foto (selfie) dengan ketentuan</li>
                        <li>✓ Membuat laporan pelanggaran jika ada ketidaksesuaian</li>
                        <li>✓ Memastikan booking yang tidak hadir atau tidak melakukan scan dilaporkan</li>
                        <li>✓ Meneruskan laporan pelanggaran ke Admin untuk tindak lanjut</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>