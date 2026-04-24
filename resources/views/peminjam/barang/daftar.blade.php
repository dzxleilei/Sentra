@extends('layouts.peminjam')

@section('title', 'Daftar Barang')
@section('page_title', 'Booking Barang')

@section('content')
    <form id="clear-cart-on-leave-form" action="{{ route('thing.cart.clear') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="redirect_to" id="clear-cart-redirect-to" value="">
    </form>

    <form method="GET" action="{{ route('peminjam.barang') }}" class="mb-4 grid grid-cols-3 gap-2">
        <div class="col-span-3">
            <label class="mb-1 block text-xs font-semibold text-slate-600">Cari Barang</label>
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama atau ID barang" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
            <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Semua</option>
                <option value="Tersedia" {{ $filterStatus === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="Dipinjam" {{ $filterStatus === 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="Tidak Tersedia" {{ $filterStatus === 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">Lokasi Simpan</label>
            <select name="lokasi" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Semua</option>
                <option value="Sarpras" {{ $filterLokasi === 'Sarpras' ? 'selected' : '' }}>Sarpras</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full rounded-lg bg-slate-800 px-3 py-2 text-sm font-semibold text-white">Filter</button>
        </div>
    </form>

    @if($bookingBlocked)
        <div class="mb-4 rounded-2xl border border-rose-300 bg-rose-50 p-3 text-xs text-rose-800">
            Pengajuan peminjaman dinonaktifkan karena poin penalti Anda mencapai 20 atau lebih. Silakan hubungi admin.
        </div>
    @endif

    <p class="mb-4 text-xs text-slate-500">Pilih sesi terlebih dahulu sebelum menambahkan barang ke keranjang. Interval pilihan waktu per 1 jam.</p>

    <div x-data="thingBookingPage()">
    <section class="mb-4 rounded-2xl border border-slate-200 p-4">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">Keranjang Barang</h2>
            @if($cartBarang->count() > 0)
                <form action="{{ route('thing.cart.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-700">Kosongkan</button>
                </form>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <p class="text-sm font-bold text-slate-800">Pilih Sesi Peminjaman</p>
                    <p class="text-xs text-slate-500">Pilih jam mulai, lalu jam selesai. Semua barang di keranjang mengikuti sesi yang sama.</p>
                </div>
                <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-blue-800">1 sesi</span>
            </div>

            <div class="mt-4 space-y-4">
                <div>
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Jam Mulai</p>
                        <span class="text-[11px] text-slate-500" x-show="start" x-text="start ? `Dipilih ${start}` : ''"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 lg:grid-cols-6">
                        <template x-for="time in allTimes" :key="`start-${time}`">
                            <button type="button" class="rounded-lg border px-3 py-2 text-sm font-semibold transition" :class="start === time ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-blue-300'" @click="start = time; syncEndOptions()" x-text="time"></button>
                        </template>
                    </div>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Jam Selesai</p>
                        <span class="text-[11px] text-slate-500" x-show="start" x-text="start ? `Setelah ${start}` : ''"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 lg:grid-cols-6">
                        <template x-for="time in endOptions" :key="`end-${time}`">
                            <button type="button" class="rounded-lg border px-3 py-2 text-sm font-semibold transition" :class="end === time ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-emerald-300'" @click="end = time" x-text="time"></button>
                        </template>
                    </div>
                </div>

                <p class="text-xs text-slate-600">Sesi dipilih: <span class="font-semibold" x-text="start && end ? `${start} - ${end}` : 'Belum dipilih'"></span> <span class="ml-2 rounded-full bg-blue-100 px-2 py-1 text-[10px] font-semibold text-blue-800" x-show="start && end">1 sesi</span></p>
            </div>
        </div>

        @if($cartBarang->count() > 0)
            <div class="space-y-2">
                @foreach($cartBarang as $cartItem)
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2">
                        <div>
                            <p class="text-xs text-slate-500">{{ $cartItem->kode_thing }}</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $cartItem->nama }}</p>
                            <p class="text-[11px] text-slate-500" x-show="start && end">Sesi aktif: <span class="font-semibold text-slate-700" x-text="`${start} - ${end}`"></span></p>
                        </div>
                        <form action="{{ route('thing.cart.remove', $cartItem->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-semibold text-slate-700">Hapus</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <form action="{{ route('thing.cart.checkout') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <input type="hidden" name="jam_mulai" :value="start">
                <input type="hidden" name="jam_selesai" :value="end">

                <div class="rounded-xl border border-slate-200 bg-white p-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Sesi checkout</p>
                    <p class="mt-1 text-sm text-slate-700">Keranjang akan mengikuti sesi yang dipilih di atas.</p>
                    <p class="mt-1 text-xs text-slate-500">Sesi aktif: <span class="font-semibold text-slate-700" x-text="start && end ? `${start} - ${end}` : 'Belum dipilih'"></span></p>
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

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Lokasi Penggunaan Barang</label>
                    <select name="lokasi_penggunaan" x-model="lokasi" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" {{ $bookingBlocked ? 'disabled' : '' }}>
                        @foreach($lokasiOptions as $lokasi)
                            <option value="{{ $lokasi }}" {{ old('lokasi_penggunaan', 'Laboratorium Komputer') === $lokasi ? 'selected' : '' }}>{{ $lokasi }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="lokasi === 'Lainnya'">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tulis Lokasi Lainnya</label>
                    <textarea name="lokasi_lainnya" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" {{ $bookingBlocked ? 'disabled' : '' }}>{{ old('lokasi_lainnya') }}</textarea>
                </div>

                <button type="submit" class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="!start || !end || {{ $bookingBlocked ? 'true' : 'false' }}">Checkout Keranjang</button>
            </form>
        @else
            <div class="rounded-xl border border-dashed border-slate-300 p-3 text-xs text-slate-500">Keranjang masih kosong. Tambahkan barang dari daftar di bawah.</div>
        @endif
    </section>

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

                <div class="mt-3 rounded-xl border border-slate-200 p-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Detail waktu tersedia</p>
                    <div class="mt-2 flex flex-wrap gap-1.5 text-[10px]">
                        @php
                            $availableTimes = collect($itemTimeOptions[$item->id] ?? []);
                            $allTimes = collect($allTimeOptions->values());
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
                    @endif
                </div>

                @if($isAvailable)
                    <form action="{{ route('thing.cart.add') }}" method="POST" class="mt-3 flex items-center justify-between gap-3">
                        @csrf
                        <input type="hidden" name="thing_id" value="{{ $item->id }}">
                        <input type="hidden" name="jam_mulai" :value="start">
                        <input type="hidden" name="jam_selesai" :value="end">
                        <p class="text-[11px] text-slate-500">Pilih sesi di atas dulu.</p>
                        <button type="submit" title="Tambah ke keranjang" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white disabled:opacity-60" :disabled="!start || !end || {{ $bookingBlocked ? 'true' : 'false' }}">
                            <i class="fas fa-cart-plus"></i>
                            Tambah ke Keranjang
                        </button>
                    </form>
                @else
                    <p class="mt-3 text-xs text-slate-500">Barang ini belum bisa dibooking saat ini.</p>
                @endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">Belum ada data barang.</div>
        @endforelse
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        function thingBookingPage() {
            const allTimes = @json($allTimeOptions->values());
            return {
                allTimes,
                start: @json($cartWindow['jam_mulai'] ?? old('jam_mulai', '')),
                end: @json($cartWindow['jam_selesai'] ?? old('jam_selesai', '')),
                alasan: '{{ old('alasan_peminjaman', 'Praktikum') }}',
                lokasi: '{{ old('lokasi_penggunaan', 'Laboratorium Komputer') }}',
                endOptions: [],
                hasSession() {
                    return this.start !== '' && this.end !== '';
                },
                syncEndOptions() {
                    this.endOptions = allTimes.filter((time) => this.start && time > this.start);
                    if (!this.endOptions.includes(this.end)) {
                        this.end = '';
                    }
                },
                init() {
                    this.syncEndOptions();
                }
            };
        }

        (function () {
            const hasCartItems = {{ $cartBarang->count() > 0 ? 'true' : 'false' }};
            if (!hasCartItems) {
                return;
            }

            const clearForm = document.getElementById('clear-cart-on-leave-form');
            const redirectInput = document.getElementById('clear-cart-redirect-to');
            if (!clearForm || !redirectInput) {
                return;
            }

            document.addEventListener('click', function (event) {
                const anchor = event.target.closest('a[href]');
                if (!anchor) {
                    return;
                }

                if (anchor.target === '_blank' || anchor.hasAttribute('download')) {
                    return;
                }

                const href = anchor.getAttribute('href') || '';
                if (!href || href.startsWith('#')) {
                    return;
                }

                const isSamePage = href === window.location.pathname || href === window.location.href;
                if (isSamePage) {
                    return;
                }

                event.preventDefault();
                const confirmLeave = window.confirm('Jika pindah halaman, keranjang akan dikosongkan. Lanjutkan?');
                if (!confirmLeave) {
                    return;
                }

                redirectInput.value = href;
                clearForm.submit();
            });
        })();
    </script>
@endpush
