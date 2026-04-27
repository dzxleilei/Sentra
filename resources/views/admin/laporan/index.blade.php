@extends('layouts.admin')

@section('page_title', 'Laporan Peminjaman')
@section('page_subtitle', 'Lihat riwayat peminjaman dan ekspor data per periode')

@section('content')
<div class="max-w-7xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <span>Laporan Peminjaman</span>
    </div>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900"><i class="fas fa-chart-column text-blue-600 mr-2"></i>Laporan Peminjaman</h2>
        <p class="mt-1 text-sm text-slate-500">Lihat riwayat peminjaman per periode</p>
    </div>

    <div class="mb-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-4">
        <div class="md:col-span-2">
            <label for="q" class="mb-1 block text-xs font-semibold text-slate-600">Cari Mahasiswa</label>
            <input type="text" id="q" name="q" form="laporan-filter-form" value="{{ $search ?? '' }}" placeholder="Cari nama atau email mahasiswa" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="bulan" class="mb-1 block text-xs font-semibold text-slate-600">Bulan</label>
            <select name="bulan" id="bulan" form="laporan-filter-form" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="">Semua bulan</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ (string) $bulan === (string) $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div>
            <label for="tahun" class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
            <select name="tahun" id="tahun" form="laporan-filter-form" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="">Semua tahun</option>
                @for($y = 2025; $y <= 2027; $y++)
                    <option value="{{ $y }}" {{ (string) $tahun === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="md:col-span-4 flex gap-2">
            <form id="laporan-filter-form" method="GET" class="flex gap-2">
                <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">
                    <i class="fas fa-filter mr-1"></i>Terapkan
                </button>
                <a href="{{ route('admin.laporan') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Reset</a>
            </form>
            <form action="{{ route('admin.laporan.export') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-800 hover:bg-emerald-100">
                    <i class="fas fa-file-export mr-1"></i>Export CSV
                </button>
            </form>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px]">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Nama User</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Email</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Jumlah Tiket</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Poin Penalti</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Lihat Tiket</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($laporanUsers as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3.5 text-sm font-semibold text-slate-800">{{ $user->name }}</td>
                            <td class="px-6 py-3.5 text-sm text-slate-600">{{ $user->email }}</td>
                            <td class="px-6 py-3.5 text-sm text-slate-700">{{ $user->total_tiket }}</td>
                            <td class="px-6 py-3.5 text-sm">
                                <span class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->penalty_points >= 20 ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800' }}">
                                    {{ (int) $user->penalty_points }} poin
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-sm">
                                <a href="{{ route('admin.laporan.tiket', ['userId' => $user->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100" title="Lihat tiket {{ $user->name }}" aria-label="Lihat tiket {{ $user->name }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">
                                <i class="fas fa-inbox mr-2"></i>Tidak ada data peminjaman untuk filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex justify-center">
        {{ $laporanUsers->links() }}
    </div>
</div>
@endsection
