@extends('layouts.admin')

@section('page_title', 'Detail Laporan Barang Rusak')
@section('page_subtitle', 'Lihat bukti laporan dan ubah status penanganan')

@section('content')
<div class="max-w-6xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <a href="{{ route('admin.laporan.rusak') }}" class="hover:text-blue-600">Laporan Barang Rusak</a>
        <span>/</span>
        <span>Detail</span>
    </div>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900"><i class="fas fa-file-circle-check text-blue-600 mr-2"></i>Detail Laporan Barang Rusak</h2>
        <p class="mt-1 text-sm text-slate-500">Kode barang {{ $report->thing->kode_thing ?? '-' }} · Dilaporkan {{ $report->created_at->format('d M Y H:i') }}</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white lg:col-span-2">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600">Data Laporan</h3>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                <div>
                    <p class="text-xs text-slate-500">Pelapor</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $report->user->name ?? '-' }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $report->user->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Barang</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $report->thing->nama ?? '-' }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $report->thing->kode_thing ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Lokasi Barang</p>
                    <p class="mt-1 text-sm text-slate-800">{{ $report->lokasi_barang }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Tiket Terkait</p>
                    <p class="mt-1 text-sm text-slate-800">{{ $report->borrow->kode_booking ?? '-' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-slate-500">Keterangan</p>
                    <p class="mt-1 text-sm text-slate-800">{{ $report->keterangan ?: '-' }}</p>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600">Atur  Status Laporan</h3>
            </div>
            <div class="p-5">
                @php
                    $statusClass = match($report->status) {
                        'Sedang Ditinjau', 'Menunggu Verifikasi' => 'bg-amber-100 text-amber-800',
                        'Ditolak' => 'bg-rose-100 text-rose-700',
                        'Selesai Ditangani', 'Selesai' => 'bg-emerald-100 text-emerald-700',
                        default => 'bg-slate-100 text-slate-700',
                    };
                @endphp
                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $report->status }}</span>

                <form action="{{ route('admin.laporan.rusak.status', $report->id) }}" method="POST" class="mt-4 space-y-2">
                    @csrf
                    <button type="submit" name="status" value="Sedang Ditinjau" class="w-full rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-800 hover:bg-amber-100">
                        Sedang Ditinjau
                    </button>
                    <button type="submit" name="status" value="Ditolak" class="w-full rounded-lg border border-rose-300 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                        Ditolak
                    </button>
                    <button type="submit" name="status" value="Selesai Ditangani" class="w-full rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">
                        Selesai Ditangani
                    </button>
                </form>
            </div>
        </section>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600">Bukti Foto</h3>
        </div>
        <div class="p-5">
            @if($report->foto_bukti)
                <a href="{{ route('admin.laporan.rusak.foto', $report->id) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                    <i class="fas fa-up-right-from-square"></i>
                    Buka Foto Asli
                </a>
                <img src="{{ route('admin.laporan.rusak.foto', $report->id) }}" alt="Bukti laporan kerusakan" class="mt-4 max-h-[540px] w-full rounded-xl border border-slate-200 object-contain bg-slate-50">
            @else
                <p class="text-sm text-slate-500">Bukti foto tidak ditemukan.</p>
            @endif
        </div>
    </section>
</div>
@endsection
