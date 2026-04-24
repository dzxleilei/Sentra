@extends('layouts.verifikator')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Detail Booking</h1>
        <p class="text-gray-600 mt-2">Validasi dan verifikasi keadaan aset</p>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-lg shadow p-8 mb-6">
        <div class="grid grid-cols-2 gap-6 mb-8">
            <!-- Info Peminjam -->
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-user text-orange-600 mr-2"></i>Informasi Peminjam</h3>
                <div class="space-y-3">
                    <p><strong>Nama:</strong> {{ $booking->user->name }}</p>
                    <p><strong>Email:</strong> {{ $booking->user->email }}</p>
                    <p><strong>Kode Booking:</strong> <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $booking->kode_booking }}</span></p>
                </div>
            </div>

            <!-- Info Aset -->
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-crate text-orange-600 mr-2"></i>Informasi Aset</h3>
                <div class="space-y-3">
                    <p><strong>Tipe:</strong> {{ $booking->tipe }}</p>
                    @if($booking->thing)
                        <p><strong>Barang:</strong> {{ $booking->thing->nama }} ({{ $booking->thing->kode_thing }})</p>
                    @endif
                    @if($booking->room)
                        <p><strong>Ruangan:</strong> {{ $booking->room->nama }} ({{ $booking->room->kode_room }})</p>
                    @endif
                    <p><strong>Status:</strong> 
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            @if($booking->status === 'Selesai')
                                bg-green-100 text-green-800
                            @elseif($booking->status === 'Berlangsung')
                                bg-blue-100 text-blue-800
                            @elseif($booking->status === 'Pelanggaran')
                                bg-red-100 text-red-800
                            @else
                                bg-yellow-100 text-yellow-800
                            @endif">
                            {{ $booking->status }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Waktu Peminjaman -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div>
                <h4 class="font-bold text-gray-700 mb-2"><i class="fas fa-clock text-orange-600 mr-2"></i>Waktu Mulai Booking</h4>
                <p class="text-gray-600">{{ $booking->waktu_mulai_booking->format('d M Y H:i') }}</p>
            </div>
            <div>
                <h4 class="font-bold text-gray-700 mb-2"><i class="fas fa-clock text-orange-600 mr-2"></i>Waktu Selesai Booking</h4>
                <p class="text-gray-600">{{ $booking->waktu_selesai_booking->format('d M Y H:i') }}</p>
            </div>
        </div>

        <!-- Dokumentasi -->
        @if($booking->foto_awal || $booking->foto_akhir)
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-image text-orange-600 mr-2"></i>Dokumentasi</h3>
                <div class="grid grid-cols-2 gap-6">
                    @if($booking->foto_awal)
                        <div>
                            <p class="font-semibold text-gray-700 mb-2">Foto Awal</p>
                            <img src="{{ asset('storage/' . $booking->foto_awal) }}" alt="Foto Awal" class="w-full h-64 object-cover rounded-lg border border-gray-300">
                        </div>
                    @endif
                    @if($booking->foto_akhir)
                        <div>
                            <p class="font-semibold text-gray-700 mb-2">Foto Akhir</p>
                            <img src="{{ asset('storage/' . $booking->foto_akhir) }}" alt="Foto Akhir" class="w-full h-64 object-cover rounded-lg border border-gray-300">
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Form Validasi -->
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-check-circle text-orange-600 mr-2"></i>Form Validasi</h2>
        <form action="{{ route('verifikator.validasi.scan', $booking->id) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Waktu Checkin -->
            <div>
                <label for="waktu_checkin" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-door-open mr-1 text-orange-600"></i>Waktu Check-in
                </label>
                <input type="datetime-local" id="waktu_checkin" name="waktu_checkin" 
                    value="{{ $booking->waktu_checkin ? \Carbon\Carbon::parse($booking->waktu_checkin)->format('Y-m-d\TH:i') : '' }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                <p class="text-gray-500 text-xs mt-1">Waktu ketika peminjam mengambil barang/ruangan</p>
            </div>

            <!-- Waktu Checkout -->
            <div>
                <label for="waktu_checkout" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-sign-out-alt mr-1 text-orange-600"></i>Waktu Check-out
                </label>
                <input type="datetime-local" id="waktu_checkout" name="waktu_checkout"
                    value="{{ $booking->waktu_checkout ? \Carbon\Carbon::parse($booking->waktu_checkout)->format('Y-m-d\TH:i') : '' }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                <p class="text-gray-500 text-xs mt-1">Waktu ketika peminjam mengembalikan barang/ruangan</p>
            </div>

            <!-- Ada Pelanggaran -->
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="ada_pelanggaran" name="ada_pelanggaran" value="1"
                        {{ $booking->status === 'Pelanggaran' ? 'checked' : '' }}
                        class="w-5 h-5 text-red-600 rounded focus:ring-2 focus:ring-red-500">
                    <span class="font-semibold text-gray-700"><i class="fas fa-exclamation-triangle mr-1 text-red-600"></i>Ada Pelanggaran / Kerusakan</span>
                </label>
            </div>

            <!-- Catatan Pelanggaran -->
            <div>
                <label for="catatan_pelanggaran" class="block text-sm font-semibold text-gray-700 mb-2">
                    Catatan Pelanggaran (Jika Ada)
                </label>
                <textarea id="catatan_pelanggaran" name="catatan_pelanggaran" rows="4"
                    placeholder="Detail kerusakan, keterlambatan, atau pelanggaran lainnya..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">{{ $booking->catatan_pelanggaran }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 rounded-lg transition duration-200">
                    💾 Simpan Validasi
                </button>
                <a href="{{ route('verifikator.dashboard') }}" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-semibold py-3 rounded-lg transition duration-200 text-center">
                    ← Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
