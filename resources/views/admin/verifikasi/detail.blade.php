@extends('layouts.admin')

@section('page_title', 'Verifikasi Detail Booking')
@section('page_subtitle', 'Lihat dan verifikasi detail keadaan aset')

@section('content')
<div class="max-w-7xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <a href="{{ route('admin.notifikasi.booking') }}" class="hover:text-blue-600">Verifikasi Booking</a>
        <span>/</span>
        <span>Verifikasi Detail Booking</span>
    </div>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-slate-900"><i class="fas fa-file-check text-blue-600 mr-2"></i>Verifikasi Detail Booking</h2>
            <p class="mt-1 text-sm text-slate-500">Lihat dan verifikasi detail keadaan aset</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4">
            <p class="mb-2 font-semibold text-rose-900">Error:</p>
            <ul class="space-y-1 text-sm text-rose-800">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $bookingStatusClass = $booking->status === 'Selesai'
            ? 'bg-emerald-100 text-emerald-800'
            : ($booking->status === 'Berlangsung'
                ? 'bg-blue-100 text-blue-800'
                : 'bg-amber-100 text-amber-800');
    @endphp

    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-6 md:grid-cols-2 mb-8">
            <div>
                <h3 class="mb-4 text-lg font-bold text-slate-900"><i class="fas fa-user mr-2 text-blue-600"></i>Informasi Peminjam</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-slate-500">Nama</p>
                        <p class="text-lg font-semibold text-slate-900">{{ $booking->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Email</p>
                        <p class="text-lg font-semibold text-slate-900">{{ $booking->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Kode Booking</p>
                        <p class="inline-flex rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 font-mono text-sm text-slate-800">{{ $booking->kode_booking }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-4 text-lg font-bold text-slate-900"><i class="fas fa-box mr-2 text-blue-600"></i>Informasi Aset</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-slate-500">Tipe</p>
                        <p class="text-lg font-semibold text-slate-900">{{ $booking->tipe }}</p>
                    </div>
                    @if($booking->thing)
                        <div>
                            <p class="text-xs text-slate-500">Barang</p>
                            <p class="text-lg font-semibold text-slate-900">{{ $booking->thing->nama }} (ID: {{ $booking->thing->id }})</p>
                        </div>
                    @endif
                    @if($booking->room)
                        <div>
                            <p class="text-xs text-slate-500">Ruangan</p>
                            <p class="text-lg font-semibold text-slate-900">{{ $booking->room->nama }} (ID: {{ $booking->room->id }})</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-slate-500">Status</p>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $bookingStatusClass }}">
                            {{ $booking->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 border-t border-slate-200 pt-6 md:grid-cols-2 mb-8">
            <div>
                <h4 class="mb-2 font-bold text-slate-900"><i class="fas fa-clock mr-2 text-blue-600"></i>Waktu Mulai Booking</h4>
                <p class="text-slate-600">{{ $booking->waktu_mulai_booking->format('d M Y H:i') }}</p>
            </div>
            <div>
                <h4 class="mb-2 font-bold text-slate-900"><i class="fas fa-clock mr-2 text-blue-600"></i>Waktu Selesai Booking</h4>
                <p class="text-slate-600">{{ $booking->waktu_selesai_booking->format('d M Y H:i') }}</p>
            </div>
        </div>

        @if($booking->foto_awal || $booking->foto_akhir)
            <div class="border-t border-slate-200 pt-6 mb-8">
                <h3 class="mb-4 text-lg font-bold text-slate-900"><i class="fas fa-image mr-2 text-blue-600"></i>Dokumentasi</h3>
                <div class="grid gap-6 md:grid-cols-2">
                    @if($booking->foto_awal)
                        <div>
                            <p class="mb-2 font-semibold text-slate-700">Foto Awal</p>
                            <img src="{{ asset('storage/' . $booking->foto_awal) }}" alt="Foto Awal" class="h-64 w-full rounded-lg border border-slate-200 object-cover">
                        </div>
                    @endif
                    @if($booking->foto_akhir)
                        <div>
                            <p class="mb-2 font-semibold text-slate-700">Foto Akhir</p>
                            <img src="{{ asset('storage/' . $booking->foto_akhir) }}" alt="Foto Akhir" class="h-64 w-full rounded-lg border border-slate-200 object-cover">
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-6 text-2xl font-bold text-slate-900"><i class="fas fa-check-circle text-blue-600 mr-2"></i>Form Verifikasi</h2>
        <form action="{{ route('admin.verifikasi.scan', $booking->id) }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="waktu_checkin" class="mb-2 block text-sm font-semibold text-slate-700">
                        <i class="fas fa-door-open mr-1 text-blue-600"></i>Waktu Check-in
                    </label>
                    <input type="datetime-local" id="waktu_checkin" name="waktu_checkin"
                        value="{{ $booking->waktu_checkin ? \Carbon\Carbon::parse($booking->waktu_checkin)->format('Y-m-d\TH:i') : '' }}"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-slate-500">Waktu ketika peminjam mengambil aset</p>
                </div>

                <div>
                    <label for="waktu_checkout" class="mb-2 block text-sm font-semibold text-slate-700">
                        <i class="fas fa-sign-out-alt mr-1 text-blue-600"></i>Waktu Check-out
                    </label>
                    <input type="datetime-local" id="waktu_checkout" name="waktu_checkout"
                        value="{{ $booking->waktu_checkout ? \Carbon\Carbon::parse($booking->waktu_checkout)->format('Y-m-d\TH:i') : '' }}"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-slate-500">Waktu ketika peminjam mengembalikan aset</p>
                </div>
            </div>

            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="ada_pelanggaran" name="ada_pelanggaran" value="1"
                        {{ $booking->status === 'Pelanggaran' ? 'checked' : '' }}
                        class="h-5 w-5 rounded text-blue-600 focus:ring-2 focus:ring-blue-500">
                    <span class="font-semibold text-slate-700"><i class="fas fa-exclamation-triangle mr-1 text-rose-600"></i>Ada Pelanggaran / Kerusakan</span>
                </label>
            </div>

            <div>
                <label for="catatan_pelanggaran" class="mb-2 block text-sm font-semibold text-slate-700">
                    <i class="fas fa-clipboard mr-1 text-blue-600"></i>Catatan Pelanggaran (Jika Ada)
                </label>
                <textarea id="catatan_pelanggaran" name="catatan_pelanggaran" rows="4"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Deskripsikan pelanggaran atau kerusakan yang terjadi...">{{ $booking->catatan_pelanggaran }}</textarea>
            </div>

            <div class="flex gap-3 pt-6 border-t border-slate-200">
                <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white transition hover:bg-blue-700">
                    <i class="fas fa-check mr-1"></i> Verifikasi & Simpan
                </button>
                <a href="{{ route('admin.notifikasi.booking') }}" class="rounded-lg border border-slate-300 px-6 py-2 font-semibold text-slate-700 transition hover:bg-slate-100">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
