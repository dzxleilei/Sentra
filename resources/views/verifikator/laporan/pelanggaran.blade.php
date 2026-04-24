@extends('layouts.verifikator')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Laporan Pelanggaran</h1>
        <p class="text-gray-600 mt-2">Lihat daftar pelanggaran yang telah diformulasikan</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-red-100 rounded-lg p-6 border-l-4 border-red-600">
            <p class="text-gray-600 text-sm font-semibold">Total Pelanggaran</p>
            <p class="text-4xl font-bold text-red-800">{{ $pelanggaran->total() }}</p>
        </div>
        <div class="bg-yellow-100 rounded-lg p-6 border-l-4 border-yellow-600">
            <p class="text-gray-600 text-sm font-semibold">Halaman Saat Ini</p>
            <p class="text-4xl font-bold text-yellow-800">{{ $pelanggaran->count() }}</p>
        </div>
        <div class="bg-blue-100 rounded-lg p-6 border-l-4 border-blue-600">
            <p class="text-gray-600 text-sm font-semibold">Total Peminjam Terkena Penalti</p>
            <p class="text-4xl font-bold text-blue-800">{{ $pelanggaran->groupBy('user_id')->count() }}</p>
        </div>
    </div>

    <!-- Tabel Pelanggaran -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Peminjam</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Kode Booking</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tipe</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Catatan Pelanggaran</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tanggal Laporan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($pelanggaran as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                            <span class="inline-block px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs">
                                {{ $item->user->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $item->kode_booking }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                @if($item->tipe === 'Ruangan')
                                    bg-purple-100 text-purple-800
                                @else
                                    bg-orange-100 text-orange-800
                                @endif">
                                {{ $item->tipe }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span title="{{ $item->catatan_pelanggaran }}" class="inline-block max-w-xs truncate">
                                {{ $item->catatan_pelanggaran ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $item->created_at->format('d M Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            ✓ Tidak ada pelanggaran
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $pelanggaran->links() }}
    </div>

    <!-- Info Box -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="font-bold text-blue-900 mb-2">ℹ️ Informasi Pelanggaran</h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li>• Pelanggaran dicatat ketika ada kerusakan atau keterlambatan pengembalian aset</li>
            <li>• Setiap pelanggaran akan dialihkan ke admin untuk tindak lanjut dan penalti</li>
            <li>• Peminjam dapat dilihat lagi status penaltinya dari halaman masing-masing</li>
        </ul>
    </div>
</div>
@endsection
