@extends('layouts.admin')

@section('page_title', 'Kelola Penalti')
@section('page_subtitle', 'Pantau riwayat pelanggaran booking peminjam')

@section('content')
    <div class="max-w-7xl mx-auto p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <span>Kelola Penalti</span>
    </div>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-slate-900"><i class="fas fa-shield-halved text-blue-600 mr-2"></i>Kelola Penalti</h2>
            <p class="mt-1 text-sm text-slate-500">Riwayat pelanggaran booking. Pengelolaan poin penalti ditampilkan pada halaman Laporan Peminjaman.</p>
        </div>
        <a href="{{ route('admin.laporan') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
            <i class="fas fa-chart-column mr-1"></i> Buka Laporan Peminjaman
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <h3 class="text-base font-semibold text-slate-800">Riwayat pelanggaran booking dipindahkan ke Laporan Peminjaman</h3>
        <p class="mt-2 text-sm text-slate-600">Untuk melihat data pelanggaran berdasarkan filter periode yang sama dengan data tiket, gunakan halaman Laporan Peminjaman.</p>
        <a href="{{ route('admin.laporan') }}" class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
            <i class="fas fa-arrow-right mr-1"></i> Buka Laporan Peminjaman
        </a>
    </section>
    </div>
@endsection
