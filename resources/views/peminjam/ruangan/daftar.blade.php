@extends('layouts.peminjam')

@section('title', 'Daftar Ruangan')
@section('page_title', 'Booking Ruangan')

@section('content')
    @if($bookingBlocked)
        <div class="mb-4 rounded-2xl border border-rose-300 bg-rose-50 p-3 text-xs text-rose-800">
            Pengajuan peminjaman dinonaktifkan karena poin penalti Anda mencapai 20 atau lebih. Silakan hubungi admin.
        </div>
    @endif

    <form method="GET" action="{{ route('peminjam.ruangan') }}" class="mb-4">
        <label class="mb-1 block text-xs font-semibold text-slate-600">Cari Ruangan</label>
        <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama/kode/lantai ruangan" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </form>

    <p class="mb-4 text-xs text-slate-500">Pilih satu ruangan terlebih dahulu. Setelah klik booking, Anda akan diarahkan ke halaman detail ruangan untuk memilih jadwal dan alasan.</p>

    <div x-data="roomSelector()" class="space-y-3">
        @forelse($ruangan as $room)
            @php
                $isAvailable = $room->status === 'Tersedia';
                $statusLabel = $room->status === 'Maintenance' ? 'Dalam Perbaikan' : ($isAvailable ? 'Tersedia' : 'Tidak Tersedia');
                $statusClass = $room->status === 'Maintenance'
                    ? 'bg-orange-100 text-orange-800'
                    : ($isAvailable ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800');
            @endphp
            <label class="block rounded-2xl border p-4" :class="selectedRoomId == {{ $room->id }} ? 'border-blue-500 bg-blue-50' : 'border-slate-200'">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <input type="radio" name="selected_room" value="{{ $room->id }}" @change="selectedRoomId = {{ $room->id }}" {{ ! $isAvailable ? 'disabled' : '' }}>
                        <div>
                            <p class="text-xs text-slate-500">{{ $room->kode_room }}</p>
                            <h3 class="mt-1 font-semibold">{{ $room->nama }}</h3>
                            <p class="text-xs text-slate-500">Lantai: {{ $room->lantai ?? '-' }}</p>
                        </div>
                    </div>
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>
            </label>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">Belum ada data ruangan.</div>
        @endforelse

        <button type="button" @click="goBooking()" class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="!selectedRoomId || {{ $bookingBlocked ? 'true' : 'false' }}">
            Buat Tiket Booking Ruangan
        </button>
    </div>
@endsection

@push('scripts')
    <script>
        function roomSelector() {
            return {
                selectedRoomId: null,
                goBooking() {
                    if (!this.selectedRoomId) {
                        return;
                    }

                    window.location.href = '/peminjam/ruangan/' + this.selectedRoomId + '/booking';
                }
            };
        }
    </script>
@endpush
