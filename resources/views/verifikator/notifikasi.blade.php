@extends('layouts.verifikator')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800"><i class="fas fa-bell text-orange-600 mr-2"></i>Notifikasi & Tugas Validasi</h1>
        <p class="text-gray-600 mt-2">Daftar peminjaman yang menunggu validasi dari Anda</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-orange-100 rounded-lg p-6 border-l-4 border-orange-600">
            <p class="text-gray-600 text-sm font-semibold"><i class="fas fa-hourglass-half mr-1"></i>Menunggu Validasi</p>
            <p class="text-4xl font-bold text-orange-800">{{ $notifikasi->total() }}</p>
        </div>
        <div class="bg-blue-100 rounded-lg p-6 border-l-4 border-blue-600">
            <p class="text-gray-600 text-sm font-semibold"><i class="fas fa-list mr-1"></i>Halaman Saat Ini</p>
            <p class="text-4xl font-bold text-blue-800">{{ $notifikasi->count() }}</p>
        </div>
        <div class="bg-yellow-100 rounded-lg p-6 border-l-4 border-yellow-600">
            <p class="text-gray-600 text-sm font-semibold"><i class="fas fa-exclamation mr-1"></i>Prioritas Validasi</p>
            <p class="text-sm text-yellow-800 mt-2">Fokus pada peminjaman yang sudah dimulai</p>
        </div>
    </div>

    <!-- Notifikasi List -->
    @forelse($notifikasi as $item)
        <div class="bg-white rounded-lg shadow p-6 mb-4 border-l-4 border-orange-500 hover:shadow-lg transition">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <!-- Info Peminjam -->
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold"><i class="fas fa-user mr-1 text-orange-600"></i>Peminjam</p>
                    <p class="text-lg font-bold text-gray-800">{{ $item->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $item->user->email }}</p>
                </div>

                <!-- Info Aset -->
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold"><i class="fas fa-crate mr-1 text-orange-600"></i>Aset</p>
                    <p class="text-lg font-bold text-gray-800">
                        @if($item->thing)
                            {{ $item->thing->nama }}
                        @else
                            {{ $item->room->nama }}
                        @endif
                    </p>
                    <p class="text-sm text-gray-600">
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            @if($item->tipe === 'Ruangan')
                                bg-purple-100 text-purple-800
                            @else
                                bg-orange-100 text-orange-800
                            @endif">
                            {{ $item->tipe }}
                        </span>
                    </p>
                </div>

                <!-- Waktu -->
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold"><i class="fas fa-clock mr-1 text-orange-600"></i>Waktu Peminjaman</p>
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-calendar-alt mr-1 text-orange-600"></i> {{ $item->waktu_mulai_booking->format('d M Y') }}<br>
                        <i class="fas fa-hourglass-start mr-1 text-orange-600"></i> {{ $item->waktu_mulai_booking->format('H:i') }} - {{ $item->waktu_selesai_booking->format('H:i') }}
                    </p>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold mb-2"><i class="fas fa-flag mr-1 text-orange-600"></i>Status Saat Ini</p>
                <div class="flex gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($item->status === 'Menunggu Verifikasi')
                            bg-yellow-100 text-yellow-800
                        @else
                            bg-blue-100 text-blue-800
                        @endif">
                        {{ $item->status }}
                    </span>
                    @if(!$item->diverifikasi_admin)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                            <i class="fas fa-bell mr-1"></i>Belum Divalidasi
                        </span>
                    @endif
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-gray-200 flex gap-3">
                <a href="{{ route('verifikator.booking.detail', $item->id) }}" 
                    class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 rounded-lg transition text-center">
                    <i class="fas fa-check-circle mr-1"></i>Validasi Sekarang
                </a>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-6xl mb-4"><i class="fas fa-check-circle text-green-600"></i></p>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Tidak Ada Tugas Validasi</h2>
            <p class="text-gray-600">Semua peminjaman sudah tervalidasi. Kembali lagi nanti untuk melihat tugas baru.</p>
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="mt-6">
        {{ $notifikasi->links() }}
    </div>

    <!-- Info Box -->
    <div class="mt-8 bg-orange-50 border border-orange-200 rounded-lg p-6">
        <h3 class="font-bold text-orange-900 mb-4"><i class="fas fa-info-circle mr-2 text-orange-600"></i>Panduan Validasi</h3>
        <ul class="text-sm text-orange-800 space-y-2">
            <li><i class="fas fa-check text-green-600 mr-2"></i> Klik "Validasi Sekarang" untuk melihat detail peminjaman dan dokumentasi</li>
            <li><i class="fas fa-check text-green-600 mr-2"></i> Verifikasi QR code, foto awal & akhir, serta kondisi aset saat check-in dan check-out</li>
            <li><i class="fas fa-check text-green-600 mr-2"></i> Catat waktu check-in dan check-out yang akurat</li>
            <li><i class="fas fa-check text-green-600 mr-2"></i> Jika ada kerusakan atau keterlambatan, tandai sebagai "Ada Pelanggaran"</li>
            <li><i class="fas fa-check text-green-600 mr-2"></i> Admin akan menerima laporan pelanggaran untuk tindak lanjut penalti</li>
        </ul>
    </div>
</div>
@endsection
