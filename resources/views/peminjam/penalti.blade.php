@extends('layouts.peminjam')

@section('title', 'Status Penalti')
@section('page_title', 'Penalti')

@section('content')
    @php
        $checkinBadge = [
            'Tidak Check-in' => 'bg-rose-100 text-rose-800',
        ];

        $checkoutBadge = [
            'Tidak Check-out' => 'bg-rose-100 text-rose-800',
        ];
    @endphp

    <div class="mb-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
        <p>Total poin penalti Anda: <span class="font-bold">{{ $penaltyPoints }}</span></p>
        @if($bookingBlocked)
            <p class="mt-1 text-rose-700">Akun dibatasi untuk pengajuan peminjaman baru sampai admin membuka akses kembali.</p>
        @endif
    </div>

    <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900">
        Penalti muncul jika peminjam tidak scan QR, tidak upload bukti foto/selfie, terlambat pengembalian, atau melanggar aturan penggunaan.
    </div>

    <div class="space-y-3">
        @forelse($penalti as $item)
            <article class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                <p class="text-xs text-rose-700">{{ $item->kode_booking }} · {{ $item->created_at->format('d M Y') }}</p>
                <h3 class="mt-1 font-semibold text-rose-900">
                    @if($item->tipe === 'Ruangan')
                        {{ $item->room->nama ?? '-' }}
                    @else
                        {{ $item->thing->nama ?? '-' }}
                    @endif
                </h3>
                <p class="mt-2 text-sm text-rose-800">{{ $item->catatan_pelanggaran ?: 'Pelanggaran tercatat oleh verifikator/admin.' }}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @if($item->status_checkin)
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $checkinBadge[$item->status_checkin] ?? 'bg-slate-100 text-slate-700' }}">{{ $item->status_checkin }}</span>
                    @endif
                    @if($item->status_checkout)
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $checkoutBadge[$item->status_checkout] ?? 'bg-slate-100 text-slate-700' }}">{{ $item->status_checkout }}</span>
                    @endif
                </div>
                <p class="mt-1 text-xs text-rose-700">Poin penalti pada tiket ini: {{ (int) $item->penalty_points_applied }}</p>
                <p class="mt-2 text-xs font-semibold {{ $item->diverifikasi_admin ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $item->diverifikasi_admin ? 'Sudah diverifikasi admin' : 'Menunggu verifikasi admin' }}
                </p>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-emerald-300 bg-emerald-50 p-4 text-sm text-emerald-700">Tidak ada penalti. Pertahankan kepatuhan peminjaman Anda.</div>
        @endforelse
    </div>

    @if($penalti->hasPages())
        <div class="mt-4">{{ $penalti->links() }}</div>
    @endif
@endsection
