@extends('layouts.peminjam')

@section('title', 'Booking Detail Ruangan')
@section('page_title', 'Booking Ruangan')

@section('content')
    <div class="mb-4 rounded-2xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-700">
        <p class="font-semibold">Hari ini (GMT+7)</p>
        <p class="mt-1">{{ $todayLabel }}</p>
    </div>

    <div class="mb-4 rounded-2xl border border-slate-200 p-4">
        <p class="text-xs text-slate-500">{{ $ruangan->kode_room }}</p>
        <h2 class="mt-1 font-semibold">{{ $ruangan->nama }}</h2>
        <p class="text-xs text-slate-500">Lantai: {{ $ruangan->lantai ?? '-' }}</p>
    </div>

    <section class="mb-4 rounded-2xl border border-slate-200 p-4">
        <h3 class="text-sm font-bold text-slate-800">Daftar Barang di Ruangan</h3>
        <div class="mt-3 space-y-2">
            @forelse($ruangan->things as $item)
                <div class="rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500">{{ $item->kode_thing }}</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $item->nama }}</p>
                </div>
            @empty
                <p class="text-xs text-slate-500">Ruangan ini tidak memiliki barang terdaftar.</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 p-4" x-data="timePicker()">
        <h3 class="text-sm font-bold text-slate-800">Jadwal dan Alasan Peminjaman</h3>

        <form action="{{ route('room.booking') }}" method="POST" class="mt-3 space-y-3" x-data="{ alasan: '{{ old('alasan_peminjaman', 'Praktikum') }}' }">
            @csrf
            <input type="hidden" name="room_id" value="{{ $ruangan->id }}">

            <input type="hidden" name="jam_mulai" :value="start">
            <input type="hidden" name="jam_selesai" :value="end">

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs font-semibold text-slate-700">Pilih sesi per 1 jam</p>
                    <span class="rounded-full bg-slate-200 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-slate-700">1 sesi</span>
                </div>

                <div class="mt-3 space-y-3">
                    <div>
                        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-slate-500">Jam Mulai</p>
                        <div class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                            <template x-for="time in allOptions" :key="`start-${time}`">
                                <button type="button" class="rounded-lg border px-3 py-2 text-sm font-semibold transition" :class="start === time ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-blue-300'" @click="start = time; syncEndOptions()" x-text="time" {{ $bookingBlocked ? 'disabled' : '' }}></button>
                            </template>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Jam Selesai</p>
                            <span class="text-[11px] text-slate-500" x-show="start" x-text="start ? `Setelah ${start}` : ''"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                            <template x-for="time in endOptions" :key="`end-${time}`">
                                <button type="button" class="rounded-lg border px-3 py-2 text-sm font-semibold transition" :class="end === time ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-emerald-300'" @click="end = time" x-text="time" {{ $bookingBlocked ? 'disabled' : '' }}></button>
                            </template>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600">Sesi dipilih: <span class="font-semibold" x-text="start && end ? `${start} - ${end}` : 'Belum dipilih'"></span> <span class="ml-2 rounded-full bg-blue-100 px-2 py-1 text-[10px] font-semibold text-blue-800" x-show="start && end">1 sesi</span></p>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Alasan Peminjaman</label>
                <select name="alasan_peminjaman" x-model="alasan" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" {{ $bookingBlocked ? 'disabled' : '' }}>
                    <option value="Praktikum">Praktikum</option>
                    <option value="Perkuliahan">Perkuliahan</option>
                    <option value="Kegiatan Organisasi">Kegiatan Organisasi</option>
                    <option value="Riset">Riset</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div x-show="alasan === 'Lainnya'">
                <label class="mb-1 block text-xs font-semibold text-slate-600">Tulis Alasan Lainnya</label>
                <textarea name="alasan_lainnya" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" {{ $bookingBlocked ? 'disabled' : '' }}>{{ old('alasan_lainnya') }}</textarea>
            </div>

            <button type="submit" class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white disabled:opacity-60" {{ $bookingBlocked ? 'disabled' : '' }}>
                Konfirmasi Booking Ruangan
            </button>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        function timePicker() {
            const allOptions = @json($timeOptions->values());
            return {
                allOptions,
                start: @json(old('jam_mulai', '')),
                end: @json(old('jam_selesai', '')),
                endOptions: [],
                syncEndOptions() {
                    this.endOptions = allOptions.filter((time) => this.start && time > this.start);
                    if (!this.endOptions.includes(this.end)) {
                        this.end = '';
                    }
                },
                init() {
                    this.syncEndOptions();
                }
            };
        }
    </script>
@endpush
