@extends('layouts.admin')

@section('page_title', 'Dashboard Admin')
@section('page_subtitle', 'Ringkasan status ruangan, barang, penalti, dan laporan kerusakan')

@section('content')
<div class="max-w-7xl">
    <!-- Statistik Cards -->
    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <!-- Total Ruangan -->
        <a href="{{ route('admin.ruangan') }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Ruangan</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600">{{ $totalRuangan }}</p>
                </div>
                <div class="text-3xl text-blue-300"><i class="fas fa-door-open"></i></div>
            </div>
        </a>

        <!-- Total Barang -->
        <a href="{{ route('admin.barang') }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Barang</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600">{{ $totalBarang }}</p>
                </div>
                <div class="text-3xl text-blue-300"><i class="fas fa-boxes"></i></div>
            </div>
        </a>

        <!-- Total Peminjaman Hari Ini -->
        <a href="{{ route('admin.laporan') }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Peminjaman Hari Ini</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600">{{ $totalPeminjaman }}</p>
                </div>
                <div class="text-3xl text-blue-300"><i class="fas fa-calendar-check"></i></div>
            </div>
        </a>

        <!-- Booking Sedang Berlangsung -->
        <a href="{{ route('admin.laporan') }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Sedang Berlangsung</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-600">{{ $bookingSedangBerlangsung }}</p>
                </div>
                <div class="text-3xl text-emerald-300"><i class="fas fa-play-circle"></i></div>
            </div>
        </a>

        <!-- Total Pelanggaran -->
        <a href="{{ route('admin.laporan.rusak') }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Laporan Rusak</p>
                    <p class="mt-2 text-2xl font-bold text-amber-600">{{ $totalLaporanRusak }}</p>
                </div>
                <div class="text-3xl text-amber-300"><i class="fas fa-triangle-exclamation"></i></div>
            </div>
        </a>

        <a href="{{ route('admin.user.peminjam') }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Akun Dibatasi</p>
                    <p class="mt-2 text-2xl font-bold text-rose-600">{{ $totalPeminjamDibatasi }}</p>
                </div>
                <div class="text-3xl text-rose-300"><i class="fas fa-user-lock"></i></div>
            </div>
        </a>
    </div>

    <!-- Alert Section -->
    @if($bookingTanpaCheckin > 0)
        <div class="mb-8 bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-red-800 font-semibold"><i class="fas fa-exclamation-triangle mr-2"></i>Ada {{ $bookingTanpaCheckin }} booking yang belum check-in!</p>
        </div>
    @endif

    @if($bookingPerluVerifikasi > 0)
        <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-blue-800 font-semibold"><i class="fas fa-check-circle mr-2"></i>Ada {{ $bookingPerluVerifikasi }} booking yang perlu diverifikasi. <a href="{{ route('admin.laporan') }}" class="font-bold underline">Lihat Laporan →</a></p>
        </div>
    @endif

    @if($laporanRusakMenunggu > 0)
        <div class="mb-8 rounded-lg border border-amber-200 bg-amber-50 p-4">
            <p class="font-semibold text-amber-800"><i class="fas fa-triangle-exclamation mr-2"></i>Ada {{ $laporanRusakMenunggu }} laporan barang rusak aktif (sedang ditinjau) yang perlu tindak lanjut.</p>
        </div>
    @endif

    <!-- Booking yang Perlu Diverifikasi -->
    @if($bookingMenungguVerifikasi->count() > 0)
        <div class="mb-8 bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                <h3 class="text-lg font-semibold text-gray-800"><i class="fas fa-tasks text-blue-600 mr-2"></i>Booking Menunggu Verifikasi</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($bookingMenungguVerifikasi as $booking)
                    <div class="px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">{{ $booking->user->name }} - {{ $booking->kode_booking }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    @if($booking->thing)
                                        <i class="fas fa-cube mr-1 text-blue-600"></i>{{ $booking->thing->nama }}
                                    @elseif($booking->room)
                                        <i class="fas fa-door-open mr-1 text-blue-600"></i>{{ $booking->room->nama }}
                                    @endif
                                    | Selesai: {{ $booking->waktu_selesai_booking->format('d M - H:i') }}
                                </p>
                            </div>
                            <a href="{{ route('admin.laporan') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold whitespace-nowrap ml-4">
                                <i class="fas fa-check-circle mr-1"></i>Verifikasi
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white cursor-pointer"
            onclick="if (!event.target.closest('a')) { window.location='{{ route('admin.laporan') }}'; }"
        >
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600">Tiket Hari Ini</h3>
                <a href="{{ route('admin.laporan') }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($tiketHariIni as $ticket)
                    @php
                        $statusClass = match ($ticket->status) {
                            'Berlangsung' => 'bg-emerald-100 text-emerald-700',
                            'Selesai' => 'bg-slate-200 text-slate-700',
                            'Dibatalkan' => 'bg-rose-100 text-rose-700',
                            'Pelanggaran' => 'bg-amber-100 text-amber-700',
                            default => 'bg-blue-100 text-blue-700',
                        };
                    @endphp
                    <a href="{{ route('admin.laporan') }}" class="block px-5 py-3 text-sm hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $ticket->user->name }} · {{ $ticket->kode_booking }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $ticket->waktu_mulai_booking->format('H:i') }} - {{ $ticket->waktu_selesai_booking->format('H:i') }} ·
                                    {{ $ticket->thing->nama ?? $ticket->room->nama ?? '-' }}
                                </p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold whitespace-nowrap {{ $statusClass }}">{{ $ticket->status }}</span>
                        </div>
                    </a>
                @empty
                    <p class="px-5 py-4 text-sm text-slate-500">Tidak ada tiket booking untuk hari ini.</p>
                @endforelse
            </div>
        </section>

        <section
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white cursor-pointer"
            onclick="if (!event.target.closest('a')) { window.location='{{ route('admin.laporan') }}'; }"
        >
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600">Tiket Sedang Berlangsung</h3>
                <a href="{{ route('admin.laporan') }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($tiketBerlangsung as $ticket)
                    <a href="{{ route('admin.laporan') }}" class="block px-5 py-3 text-sm hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $ticket->user->name }} · {{ $ticket->kode_booking }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $ticket->waktu_mulai_booking->format('H:i') }} - {{ $ticket->waktu_selesai_booking->format('H:i') }} ·
                                    {{ $ticket->thing->nama ?? $ticket->room->nama ?? '-' }}
                                </p>
                            </div>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 whitespace-nowrap">Berlangsung</span>
                        </div>
                    </a>
                @empty
                    <p class="px-5 py-4 text-sm text-slate-500">Tidak ada tiket berstatus berlangsung saat ini.</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white cursor-pointer"
            onclick="if (!event.target.closest('a')) { window.location='{{ route('admin.laporan') }}'; }"
        >
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600">Pelanggaran Terbaru</h3>
                <a href="{{ route('admin.laporan') }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($pelanggaran as $item)
                    <a href="{{ route('admin.laporan') }}" class="block px-5 py-3 text-sm hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $item->user->name ?? '-' }} · {{ $item->kode_booking }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Tidak Check-in · Tidak Check-out · {{ $item->thing->nama ?? $item->room->nama ?? '-' }}
                                </p>
                            </div>
                            <span class="rounded-full bg-rose-100 px-2.5 py-1 text-[11px] font-semibold text-rose-700 whitespace-nowrap">Pelanggaran</span>
                        </div>
                    </a>
                @empty
                    <p class="px-5 py-4 text-sm text-slate-500">Tidak ada data pelanggaran check-in/check-out terbaru.</p>
                @endforelse
            </div>
        </section>

        <section
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white cursor-pointer"
            onclick="if (!event.target.closest('a')) { window.location='{{ route('admin.laporan.rusak') }}'; }"
        >
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600">Laporan Barang Rusak</h3>
                <a href="{{ route('admin.laporan.rusak') }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($laporanRusakTerbaru as $report)
                    @php
                        $reportStatus = $report->status ?: 'Sedang Ditinjau';
                        $reportStatusClass = match ($reportStatus) {
                            'Selesai Ditangani' => 'bg-emerald-100 text-emerald-700',
                            'Ditolak' => 'bg-rose-100 text-rose-700',
                            default => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <a href="{{ route('admin.laporan.rusak.detail', $report->id) }}" class="block px-5 py-3 text-sm hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $report->user->name ?? '-' }} · {{ $report->thing->kode_thing ?? '-' }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $report->thing->nama ?? 'Barang tidak ditemukan' }} · {{ $report->created_at->format('d M Y H:i') }}
                                </p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold whitespace-nowrap {{ $reportStatusClass }}">{{ $reportStatus }}</span>
                        </div>
                    </a>
                @empty
                    <p class="px-5 py-4 text-sm text-slate-500">Belum ada laporan barang rusak.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
