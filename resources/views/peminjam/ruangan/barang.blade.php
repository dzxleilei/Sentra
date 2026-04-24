@extends('layouts.peminjam')

@section('title', 'Barang dalam Ruangan')
@section('page_title', 'Barang Ruangan')

@section('content')
    <div class="mb-4 rounded-2xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-700">
        <p class="font-semibold">Hari ini (GMT+7)</p>
        <p class="mt-1">{{ $todayLabel }}</p>
    </div>

    @if($bookingBlocked)
        <div class="mb-4 rounded-2xl border border-rose-300 bg-rose-50 p-3 text-xs text-rose-800">
            Pengajuan peminjaman dinonaktifkan karena poin penalti Anda mencapai 20 atau lebih. Silakan hubungi admin.
        </div>
    @endif

    <div class="mb-4 rounded-2xl border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Ruangan</p>
        <h2 class="mt-1 font-semibold">{{ $ruangan->nama }}</h2>
        <p class="text-xs text-slate-500">{{ $ruangan->kode_room }}</p>
    </div>

    <div class="space-y-3">
        @forelse($barang as $item)
            @php
                $isAvailable = $item->status === 'Tersedia';
                $statusLabel = $isAvailable ? 'Tersedia' : 'Tidak Tersedia';
                $statusClass = $isAvailable ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800';
            @endphp
            <article class="rounded-2xl border border-slate-200 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs text-slate-500">{{ $item->kode_thing }}</p>
                        <h3 class="mt-1 font-semibold">{{ $item->nama }}</h3>
                    </div>
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>

                @if($isAvailable)
                    <form action="{{ route('room.things.booking') }}" method="POST" class="mt-3 space-y-2" x-data="roomItemTimePicker({{ $item->id }})">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $ruangan->id }}">
                        <input type="hidden" name="thing_id" value="{{ $item->id }}">
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
                                        <template x-for="time in allOptions" :key="`start-${itemId}-${time}`">
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
                                        <template x-for="time in endOptions" :key="`end-${itemId}-${time}`">
                                            <button type="button" class="rounded-lg border px-3 py-2 text-sm font-semibold transition" :class="end === time ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-emerald-300'" @click="end = time" x-text="time" {{ $bookingBlocked ? 'disabled' : '' }}></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <p class="mt-3 text-xs text-slate-600">Sesi dipilih: <span class="font-semibold" x-text="start && end ? `${start} - ${end}` : 'Belum dipilih'"></span> <span class="ml-2 rounded-full bg-blue-100 px-2 py-1 text-[10px] font-semibold text-blue-800" x-show="start && end">1 sesi</span></p>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Detail waktu tersedia</p>
                            <div class="mt-2 flex flex-wrap gap-1.5 text-[10px]">
                                @php
                                    $availableTimes = collect($itemTimeOptions[$item->id] ?? []);
                                    $allTimes = collect($timeOptions->values());
                                    $unavailableTimes = $allTimes->reject(fn ($time) => $availableTimes->contains($time));
                                @endphp
                                @forelse($availableTimes as $time)
                                    <span class="rounded-full bg-emerald-100 px-2 py-1 font-semibold text-emerald-800">{{ $time }}</span>
                                @empty
                                    <span class="rounded-full bg-slate-100 px-2 py-1 font-semibold text-slate-500">Tidak ada sesi tersedia</span>
                                @endforelse
                            </div>
                            @if($unavailableTimes->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-1.5 text-[10px]">
                                    @foreach($unavailableTimes as $time)
                                        <span class="rounded-full border border-slate-300 px-2 py-1 font-semibold text-slate-400 line-through">{{ $time }}</span>
                                    @endforeach
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
                            <textarea name="alasan_lainnya" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" {{ $bookingBlocked ? 'disabled' : '' }}></textarea>
                        </div>

                        <button type="submit" class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white disabled:opacity-60" {{ $bookingBlocked ? 'disabled' : '' }}>Buat Tiket Booking Barang</button>
                    </form>
                @else
                    <p class="mt-3 text-xs text-slate-500">Barang ini belum bisa dibooking saat ini.</p>
                @endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">Ruangan belum memiliki daftar barang.</div>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script>
        function roomItemTimePicker(itemId) {
            const optionsByItem = @json($itemTimeOptions ?? []);
            const allOptions = Array.isArray(optionsByItem[itemId]) ? optionsByItem[itemId] : [];
            return {
                itemId,
                allOptions,
                start: '',
                end: '',
                alasan: '{{ old('alasan_peminjaman', 'Praktikum') }}',
                endOptions: [],
                syncEndOptions() {
                    this.endOptions = allOptions.filter((time) => this.start && time > this.start);
                    if (!this.endOptions.includes(this.end)) {
                        this.end = '';
                    }
                }
            };
        }
    </script>
@endpush
