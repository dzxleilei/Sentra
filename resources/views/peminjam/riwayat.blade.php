@extends('layouts.peminjam')

@section('title', 'Riwayat Peminjaman')
@section('page_title', 'Riwayat')

@section('content')
    @php
        $ticketBadge = [
            'Selesai' => 'bg-emerald-100 text-emerald-800',
            'Berlangsung' => 'bg-blue-100 text-blue-800',
            'Pelanggaran' => 'bg-rose-100 text-rose-800',
            'Dibatalkan' => 'bg-slate-200 text-slate-700',
            'Booking' => 'bg-amber-100 text-amber-800',
        ];

        $checkinBadge = [
            'Tidak Check-in' => 'bg-rose-100 text-rose-800',
        ];

        $checkoutBadge = [
            'Tidak Check-out' => 'bg-rose-100 text-rose-800',
        ];

        $statusBadgeForReport = function ($status) {
            return match($status) {
                'Sedang Ditinjau', 'Menunggu Verifikasi' => 'bg-amber-100 text-amber-800',
                'Ditolak' => 'bg-rose-100 text-rose-700',
                'Selesai Ditangani', 'Selesai' => 'bg-emerald-100 text-emerald-700',
                default => 'bg-slate-100 text-slate-700',
            };
        };
    @endphp

    <section class="rounded-2xl border border-slate-200 bg-white p-4" x-data="{ openTicketGroup: '7-hari', openDamageGroup: '7-hari' }">
        <div class="mb-3 flex items-center justify-between gap-2">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">Riwayat Tiket Peminjaman</h2>
        </div>

        <div class="space-y-2">
            @forelse($riwayatGroups as $group)
                <div class="overflow-hidden rounded-xl border border-slate-200">
                    <button type="button" @click="openTicketGroup = openTicketGroup === '{{ $group['key'] }}' ? '' : '{{ $group['key'] }}'" class="flex w-full items-center justify-between bg-slate-50 px-3 py-2 text-left">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-600">{{ $group['label'] }} ({{ $group['items']->count() }})</span>
                        <i class="fas fa-chevron-down text-xs text-slate-500" :class="openTicketGroup === '{{ $group['key'] }}' ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openTicketGroup === '{{ $group['key'] }}'" x-cloak class="space-y-2 p-3">
                        @forelse($group['items'] as $item)
                            <article class="rounded-xl border border-slate-200 p-3">
                                <div class="flex items-stretch justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs text-slate-500">{{ $item->kode_booking }}</p>
                                        <h3 class="mt-1 font-semibold">
                                            @if($item->tipe === 'Ruangan')
                                                {{ $item->room->nama ?? '-' }}
                                            @else
                                                {{ $item->thing->nama ?? '-' }}
                                            @endif
                                        </h3>
                                        <p class="mt-1 text-xs text-slate-500">{{ $item->waktu_mulai_booking->format('d M Y H:i') }} - {{ $item->waktu_selesai_booking->format('H:i') }}</p>

                                        <div class="mt-2">
                                            <p class="mb-2 text-xs text-slate-500">
                                                Check-in: {{ $item->waktu_checkin ? $item->waktu_checkin->format('d M Y H:i') : '-' }}
                                                <br>
                                                Check-out: {{ $item->waktu_checkout ? $item->waktu_checkout->format('d M Y H:i') : '-' }}
                                            </p>
                                            @if($item->status !== 'Selesai')
                                                <div class="mb-2 flex flex-wrap gap-2">
                                                    @if($item->status_checkin)
                                                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $checkinBadge[$item->status_checkin] ?? 'bg-slate-100 text-slate-700' }}">{{ $item->status_checkin }}</span>
                                                    @endif
                                                    @if($item->status_checkout)
                                                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $checkoutBadge[$item->status_checkout] ?? 'bg-slate-100 text-slate-700' }}">{{ $item->status_checkout }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex shrink-0 flex-col items-end">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $ticketBadge[$item->status] ?? 'bg-slate-100 text-slate-700' }}">{{ $item->status }}</span>
                                        <a href="{{ route('peminjam.tiket', $item->id) }}" class="mt-auto inline-flex items-center rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700">Buka Detail Tiket</a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-300 p-3 text-sm text-slate-500">Tidak ada tiket pada kelompok ini.</div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">Belum ada riwayat peminjaman.</div>
            @endforelse
        </div>

        <div class="mb-3 mt-6 flex items-center justify-between gap-2">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">Status Laporan Barang Rusak</h2>
        </div>

        <div class="space-y-2">
            @forelse($laporanRusakGroups as $group)
                <div class="overflow-hidden rounded-xl border border-slate-200">
                    <button type="button" @click="openDamageGroup = openDamageGroup === '{{ $group['key'] }}' ? '' : '{{ $group['key'] }}'" class="flex w-full items-center justify-between bg-slate-50 px-3 py-2 text-left">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-600">{{ $group['label'] }} ({{ $group['items']->count() }})</span>
                        <i class="fas fa-chevron-down text-xs text-slate-500" :class="openDamageGroup === '{{ $group['key'] }}' ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openDamageGroup === '{{ $group['key'] }}'" x-cloak class="space-y-2 p-3">
                        @forelse($group['items'] as $report)
                            <article class="rounded-xl border border-slate-200 p-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs text-slate-500">{{ $report->created_at->format('d M Y H:i') }} · {{ $report->borrow->kode_booking ?? 'Tanpa tiket' }}</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $report->thing->nama ?? '-' }} ({{ $report->thing->kode_thing ?? '-' }})</p>
                                        <p class="mt-1 text-xs text-slate-500">Lokasi: {{ $report->lokasi_barang }}</p>
                                    </div>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadgeForReport($report->status) }}">{{ $report->status }}</span>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-300 p-3 text-sm text-slate-500">Tidak ada laporan rusak pada kelompok ini.</div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 p-3 text-sm text-slate-500">Belum ada laporan barang rusak.</div>
            @endforelse
        </div>
    </section>
@endsection
