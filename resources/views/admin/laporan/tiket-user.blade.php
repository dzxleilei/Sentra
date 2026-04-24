@extends('layouts.admin')

@section('page_title', 'Laporan Peminjaman')
@section('page_subtitle', 'Daftar tiket peminjaman per mahasiswa')

@section('content')
<div class="max-w-7xl p-2">
    @php
        $periodeLabel = ($bulan && $tahun)
            ? \Carbon\Carbon::createFromDate((int) $tahun, (int) $bulan, 1)->translatedFormat('F Y')
            : 'Semua Periode';
    @endphp

    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <a href="{{ route('admin.laporan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="hover:text-blue-600">Laporan Peminjaman</a>
        <span>/</span>
        <span>Tiket {{ $user->name }}</span>
    </div>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-slate-900"><i class="fas fa-ticket text-blue-600 mr-2"></i>Tiket {{ $user->name }}</h2>
            <p class="mt-1 text-sm text-slate-500">Periode {{ $periodeLabel }}</p>
        </div>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white" x-data="{ openTicket: null }">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px]">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Kode Tiket</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Jenis</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Waktu</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Detail Barang / Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($groupedTickets as $kodeBooking => $items)
                        @php
                            $first = $items->first();
                        @endphp
                        <tr class="align-top hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono text-sm text-gray-700">{{ $kodeBooking }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $first->tipe }}</td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                {{ $first->waktu_mulai_booking?->format('d M Y H:i') ?? '-' }}<br>
                                {{ $first->waktu_selesai_booking?->format('d M Y H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $first->status === 'Selesai' ? 'bg-emerald-100 text-emerald-800' : ($first->status === 'Berlangsung' ? 'bg-blue-100 text-blue-800' : ($first->status === 'Pelanggaran' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800')) }}">{{ $first->status }}</span>
                                    @if($first->status === 'Selesai' && $first->catatan_pelanggaran)
                                        <span class="inline-block rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Pelanggaran</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-700">
                                <button type="button" @click="openTicket === '{{ $kodeBooking }}' ? openTicket = null : openTicket = '{{ $kodeBooking }}'" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100" title="Buka detail tiket" aria-label="Buka detail tiket">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr x-show="openTicket === '{{ $kodeBooking }}'" x-cloak>
                            <td colspan="5" class="bg-slate-50 px-6 py-4">
                                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                                    <table class="w-full min-w-[1200px] text-left text-xs text-slate-700">
                                        <thead class="bg-slate-50">
                                            <tr class="border-b border-slate-200 text-slate-500">
                                                <th class="px-4 py-3">Objek</th>
                                                <th class="px-4 py-3">Lokasi</th>
                                                <th class="px-4 py-3">Alasan</th>
                                                <th class="px-4 py-3">Check-in</th>
                                                <th class="px-4 py-3">Check-out</th>
                                                <th class="px-4 py-3">Catatan Pelanggaran</th>
                                                <th class="px-4 py-3">Bukti Awal</th>
                                                <th class="px-4 py-3">Bukti Akhir</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $item)
                                                <tr class="border-b border-slate-100 align-top">
                                                    <td class="px-4 py-3">{{ $item->room?->nama ?? $item->thing?->nama ?? '-' }}</td>
                                                    <td class="px-4 py-3">{{ $item->lokasi_penggunaan ?? '-' }}</td>
                                                    <td class="px-4 py-3">{{ $item->alasan_peminjaman ?? '-' }}</td>
                                                    <td class="px-4 py-3">{{ $item->waktu_checkin?->format('d M Y H:i') ?? '-' }}</td>
                                                    <td class="px-4 py-3">{{ $item->waktu_checkout?->format('d M Y H:i') ?? '-' }}</td>
                                                    <td class="px-4 py-3">{{ $item->catatan_pelanggaran ?? '-' }}</td>
                                                    <td class="px-4 py-3">
                                                        @if($item->foto_awal)
                                                            <a href="{{ asset('storage/' . $item->foto_awal) }}" target="_blank" class="font-semibold text-blue-700 hover:text-blue-900">Lihat</a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if($item->foto_akhir)
                                                            <a href="{{ asset('storage/' . $item->foto_akhir) }}" target="_blank" class="font-semibold text-blue-700 hover:text-blue-900">Lihat</a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada tiket untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
