@extends('layouts.verifikator')

@section('content')
<div class="max-w-7xl">
    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Booking Hari Ini -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Booking Hari Ini</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $bookingHariIni->count() }}</p>
                </div>
                <div class="text-4xl text-gray-400"><i class="fas fa-calendar-day"></i></div>
            </div>
        </div>

        <!-- Sedang Berlangsung -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Sedang Berlangsung</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $sedangBerlangsung->count() }}</p>
                </div>
                <div class="text-4xl text-gray-400"><i class="fas fa-play-circle"></i></div>
            </div>
        </div>

        <!-- Perlu Diverifikasi -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Perlu Diverifikasi</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $perluDiverifikasi }}</p>
                </div>
                <div class="text-4xl text-gray-400"><i class="fas fa-tasks"></i></div>
            </div>
        </div>
    </div>

    <!-- Booking Hari Ini -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800"><i class="fas fa-calendar-alt mr-2"></i>Daftar Booking Hari Ini</h3>
        </div>
        
        @if($bookingHariIni->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Peminjam</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Item/Ruangan</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Waktu Mulai</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($bookingHariIni as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-semibold">{{ $booking->user->name }}</td>
                                <td class="px-6 py-4 text-sm">{{ $booking->tipe === 'Barang' ? ($booking->thing->nama ?? '-') : ($booking->room->nama ?? '-') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->waktu_mulai_booking->format('H:i') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($booking->status === 'Berlangsung') bg-blue-100 text-blue-800
                                        @elseif($booking->status === 'Booking') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $booking->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('verifikator.booking.detail', $booking->id) }}" class="text-orange-600 hover:text-orange-800 font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i>Validasi
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-8 text-center text-gray-600">
                <i class="fas fa-inbox mr-2"></i>Tidak ada booking hari ini
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="mt-8 bg-orange-50 border border-orange-200 rounded-lg p-6">
        <h3 class="font-bold text-orange-900 mb-2"><i class="fas fa-info-circle mr-2"></i>Tugas Verifikator</h3>
        <p class="text-sm text-orange-800 mb-4">
            Anda memiliki tanggung jawab untuk memvalidasi dan verifikasi setiap peminjaman aset sesuai dengan prosedur yang berlaku.
        </p>
        <ul class="text-sm text-orange-800 space-y-2">
            <li><i class="fas fa-check mr-2"></i>Validasi scan QR code barang/ruangan</li>
            <li><i class="fas fa-check mr-2"></i>Periksa dokumentasi foto (selfie) dengan ketentuan</li>
            <li><i class="fas fa-check mr-2"></i>Buat laporan pelanggaran jika ada ketidaksesuaian</li>
            <li><i class="fas fa-check mr-2"></i>Teruskan laporan pelanggaran ke Admin untuk tindak lanjut</li>
        </ul>
    </div>
</div>
@endsection
