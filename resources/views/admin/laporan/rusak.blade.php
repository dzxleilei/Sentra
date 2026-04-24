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
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Pelapor</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Barang</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Lokasi</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Keterangan</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-700">{{ $report->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $report->thing->kode_thing ?? '-' }} · {{ $report->thing->nama ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $report->lokasi_barang }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $report->keterangan ?: '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $report->status === 'Menunggu Verifikasi' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">{{ $report->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $report->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada laporan barang rusak.</td>
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
