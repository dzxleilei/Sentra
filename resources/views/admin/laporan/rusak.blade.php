@extends('layouts.admin')

@section('page_title', 'Laporan Barang Rusak')
@section('page_subtitle', 'Daftar laporan kerusakan barang yang dikirim peminjam')

@section('content')
    <div class="max-w-7xl mx-auto p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <span>Laporan Barang Rusak</span>
    </div>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900"><i class="fas fa-triangle-exclamation text-blue-600 mr-2"></i>Laporan Barang Rusak</h2>
        <p class="mt-1 text-sm text-slate-500">Daftar laporan kerusakan barang dari peminjam</p>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px]">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Pelapor</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Barang</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Lokasi</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Keterangan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Waktu</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3.5 text-sm font-semibold text-slate-700">{{ $report->user->name ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-sm text-slate-700">{{ $report->thing->kode_thing ?? '-' }} · {{ $report->thing->nama ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-sm text-slate-700">{{ $report->lokasi_barang }}</td>
                            <td class="px-6 py-3.5 text-sm text-slate-600">{{ $report->keterangan ?: '-' }}</td>
                            <td class="px-6 py-3.5 text-sm">
                                @php
                                    $statusClass = match($report->status) {
                                        'Sedang Ditinjau', 'Menunggu Verifikasi' => 'bg-amber-100 text-amber-800',
                                        'Ditolak' => 'bg-rose-100 text-rose-700',
                                        'Selesai Ditangani', 'Selesai' => 'bg-emerald-100 text-emerald-700',
                                        default => 'bg-slate-100 text-slate-700',
                                    };
                                @endphp
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $report->status }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-sm text-slate-500">{{ $report->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-3.5 text-sm">
                                <a href="{{ route('admin.laporan.rusak.detail', $report->id) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100" title="Lihat detail laporan" aria-label="Lihat detail laporan">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada laporan barang rusak.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-center">
            {{ $reports->links() }}
        </div>
    </section>
    </div>
@endsection
