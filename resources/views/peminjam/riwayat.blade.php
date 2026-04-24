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
    @endphp

    <div class="space-y-3">
        @forelse($riwayat as $item)
            <article class="rounded-2xl border border-slate-200 p-4">
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

                        <div class="mt-3">
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
            <div class="rounded-2xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">Belum ada riwayat peminjaman.</div>
        @endforelse
    </div>

    @if($riwayat->hasPages())
        <div class="mt-4">{{ $riwayat->links() }}</div>
    @endif
@endsection
