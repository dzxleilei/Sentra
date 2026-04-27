@extends('layouts.peminjam')

@section('title', 'Dashboard Peminjam')
@section('page_title', 'Dashboard')

@section('content')
    @if($bookingBlocked)
        <section class="mb-4 rounded-2xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900">
            @if(!empty(Auth::user()->blocked_at))
                Akun Anda sedang diblokir oleh admin. Pengajuan peminjaman dinonaktifkan sampai blokir dibuka kembali.
            @else
                Poin penalti Anda sudah mencapai {{ $penaltyPoints }}. Pengajuan peminjaman dinonaktifkan sampai akun dibuka kembali oleh admin.
            @endif
        </section>
    @endif

    <section class="grid gap-3">
        <a href="{{ route('peminjam.riwayat') }}" class="group rounded-2xl bg-blue-600 p-4 text-white transition hover:-translate-y-0.5 hover:bg-blue-700 hover:shadow-lg">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-wider text-blue-100">Tiket Hari Ini</p>
                    <p class="mt-2 text-2xl font-bold">{{ $bookingHariIni }}</p>
                </div>
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/15 text-white">
                    <i class="fas fa-receipt"></i>
                </span>
            </div>
        </a>
    </section>

    <div x-data="{ openDamageModal: false }">
        <section class="mt-5 grid grid-cols-2 gap-3">
            <article class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-col items-start justify-between gap-3 sm:flex-row">
                    <div>
                        <p class="hidden text-xs uppercase tracking-wider text-slate-500 sm:block">Akses Cepat</p>
                        <h2 class="mt-1 text-sm font-bold leading-tight text-slate-900">Pinjam via QR</h2>
                        <p class="mt-1 hidden text-xs text-slate-500 sm:block">Scan QR ID barang untuk booking otomatis.</p>
                    </div>
                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white sm:h-10 sm:w-10 sm:rounded-2xl">
                        <i class="fas fa-qrcode"></i>
                    </span>
                </div>

                <button id="open-quick-qr-scanner" type="button" class="mt-4 w-full rounded-xl bg-blue-600 px-2 py-2 text-xs font-semibold text-white disabled:opacity-60 sm:text-sm" {{ $bookingBlocked ? 'disabled' : '' }}>
                    Scan QR <span class="hidden lg:inline">dan Pinjam / Kembalikan</span>
                </button>
            </article>

            <article class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-col items-start justify-between gap-3 sm:flex-row">
                    <div>
                        <p class="hidden text-xs uppercase tracking-wider text-slate-500 sm:block">Akses Cepat</p>
                        <h2 class="mt-1 text-sm font-bold leading-tight text-slate-900">Lapor Rusak</h2>
                        <p class="mt-1 hidden text-xs text-slate-500 sm:block">Lampirkan foto kerusakan barang.</p>
                    </div>
                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-rose-600 text-white sm:h-10 sm:w-10 sm:rounded-2xl">
                        <i class="fas fa-triangle-exclamation"></i>
                    </span>
                </div>

                <button type="button" @click="openDamageModal = true" class="mt-4 w-full rounded-xl border border-rose-200 bg-rose-50 px-2 py-2 text-xs font-semibold text-rose-700 sm:text-sm">
                    Form Laporan
                </button>
            </article>
        </section>

        <div x-cloak x-show="openDamageModal" class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/60 p-4 sm:items-center">
            <div @click.outside="openDamageModal = false" class="w-full max-w-lg rounded-3xl bg-white p-4 shadow-2xl">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Lapor Barang Rusak</h3>
                        <p class="mt-1 text-xs text-slate-500">Isi data barang, lokasi, dan foto bukti.</p>
                    </div>
                    {{-- Tombol Tutup dihilangkan dari sini --}}
                </div>

                <form action="{{ route('borrow.report-damage') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-rose-700">ID / Kode / Nama Barang</label>
                        <input type="text" name="thing_input" required value="{{ old('thing_input') }}" class="w-full rounded-xl border border-rose-300 px-3 py-2 text-sm" placeholder="Contoh: 4 atau T004 atau proyektor">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-rose-700">Lokasi Barang</label>
                        <input type="text" name="lokasi_barang" required value="{{ old('lokasi_barang') }}" class="w-full rounded-xl border border-rose-300 px-3 py-2 text-sm" placeholder="Contoh: Lab Komputer Lt.2">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-rose-700">Foto Barang Rusak</label>
                        <input type="file" name="foto_bukti" required accept="image/*" capture="environment" class="w-full rounded-xl border border-rose-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-rose-700">Keterangan (opsional)</label>
                        <textarea name="keterangan" rows="3" class="w-full rounded-xl border border-rose-300 px-3 py-2 text-sm" placeholder="Jelaskan kerusakan singkat">{{ old('keterangan') }}</textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="openDamageModal = false" class="flex-1 rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700">Batal</button>
                        <button type="submit" class="flex-1 rounded-xl bg-rose-600 px-3 py-2 text-sm font-semibold text-white">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <section class="mt-5">
        <div class="mb-2 flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">Tiket Aktif</h2>
            <a href="{{ route('peminjam.riwayat') }}" class="text-xs font-semibold text-blue-600">Lihat semua</a>
        </div>

        <div class="space-y-3">
            @forelse($bookingAktif as $ticket)
                <article class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs text-slate-500">{{ $ticket->kode_booking }}</p>
                            <h3 class="mt-1 font-semibold text-slate-900">
                                @if($ticket->tipe === 'Ruangan')
                                    {{ $ticket->room->nama ?? '-' }}
                                @else
                                    {{ $ticket->thing->nama ?? '-' }}
                                @endif
                            </h3>
                            <p class="mt-1 text-xs text-slate-500">{{ $ticket->waktu_mulai_booking->format('H:i') }} - {{ $ticket->waktu_selesai_booking->format('H:i') }}</p>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $ticket->status === 'Booking' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $ticket->status }}
                        </span>
                    </div>

                    <div class="mt-3 flex gap-2">
                        <a href="{{ route('peminjam.tiket', $ticket->id) }}" class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-center text-xs font-semibold text-slate-700">Buka Tiket</a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">
                    Belum ada tiket aktif. Silakan booking barang atau ruangan terlebih dahulu.
                </div>
            @endforelse
        </div>
    </section>

    <section class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <div class="flex items-center gap-2">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white">
                <i class="fas fa-circle-info"></i>
            </span>
            <div>
                <h2 class="text-sm font-bold text-slate-800">Aturan Singkat</h2>
                <p class="text-xs text-slate-500">Panduan cepat sebelum booking dan check-in.</p>
            </div>
        </div>
        <ul class="mt-3 space-y-1 text-xs leading-5 text-slate-600">
            <li>Booking hanya untuk hari ini dengan interval jam 1 jam.</li>
            <li>Tiket wajib ditunjukkan saat scan QR fisik barang/ruangan.</li>
            <li>Wajib upload selfie + foto kondisi awal saat check-in.</li>
            <li>Wajib scan QR ulang + upload foto akhir saat check-out.</li>
        </ul>
    </section>

    @if($showBlockedModal && $bookingBlocked)
        <div x-data="{ open: true }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
            <div class="w-full max-w-sm rounded-2xl bg-white p-5">
                <h3 class="text-base font-bold text-rose-700">Akun Diblokir</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Akun Anda sedang diblokir oleh admin. Silakan hubungi admin untuk membuka kembali akses peminjaman.
                </p>
                <div class="mt-4 flex justify-end">
                    <button type="button" @click="open = false" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white">Saya Mengerti</button>
                </div>
            </div>
        </div>
    @endif

    <div id="quick-qr-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 p-4">
        <div class="w-full max-w-2xl rounded-2xl bg-white p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Scan QR Barang</h3>
                    <p class="mt-1 text-xs text-slate-500">Scan QR untuk membuka detail barang dan proses pinjam atau kembalikan.</p>
                </div>
                <button id="quick-qr-close" type="button" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-semibold text-slate-700">Tutup</button>
            </div>
            <div id="quick-qr-scanner-pane" class="mt-3">
                <div id="quick-qr-reader" class="min-h-64 overflow-hidden rounded-lg border border-slate-200 bg-slate-50"></div>
                <p id="quick-qr-error" class="mt-2 text-xs font-semibold text-rose-700"></p>
            </div>

            <div id="quick-qr-preview-pane" class="mt-3 hidden">
                <div class="grid gap-3 md:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Data Diri</p>
                        <div class="mt-3 space-y-2 text-sm">
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Nama</label>
                                <input id="quick-qr-user-name" type="text" readonly class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Email</label>
                                <input id="quick-qr-user-email" type="text" readonly class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Detail Barang</p>
                        <div class="mt-3 space-y-2 text-sm">
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Kode Barang</label>
                                <input id="quick-qr-thing-code" type="text" readonly class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Nama Barang</label>
                                <input id="quick-qr-thing-name" type="text" readonly class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-slate-500">Lokasi</label>
                                <input id="quick-qr-thing-room" type="text" readonly class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <form id="quick-qr-process-form" action="{{ route('quick-borrow-qr.process') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3">
                    @csrf
                    <input id="quick-qr-code" type="hidden" name="qr_code" value="">
                    <input id="quick-qr-action" type="hidden" name="action" value="borrow">

                    <div id="quick-qr-borrow-fields" class="space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Alasan Peminjaman <span class="text-rose-600">*</span></label>
                            <select id="quick-qr-reason" name="alasan_peminjaman" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                <option value="Praktikum">Praktikum</option>
                                <option value="Perkuliahan">Perkuliahan</option>
                                <option value="Kegiatan Organisasi">Kegiatan Organisasi</option>
                                <option value="Riset">Riset</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div id="quick-qr-reason-other-wrap" class="hidden">
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Alasan Lainnya</label>
                            <textarea id="quick-qr-reason-other" name="alasan_lainnya" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Jelaskan alasan pinjam"></textarea>
                        </div>
                    </div>

                    <div>
                        <label id="quick-qr-photo-label" class="mb-1 block text-xs font-semibold text-slate-600">Foto Selfie + Barang <span class="text-rose-600">*</span></label>
                        <input id="quick-qr-photo" type="file" name="foto_awal" accept="image/*" capture="user" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>

                    <div class="flex gap-2">
                        <button id="quick-qr-rescan" type="button" class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700">Scan Ulang</button>
                        <button id="quick-qr-submit" type="submit" class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white">Pinjam Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        (function setupQuickQrFlow() {
            const openButton = document.getElementById('open-quick-qr-scanner');
            const modal = document.getElementById('quick-qr-modal');
            const closeButton = document.getElementById('quick-qr-close');
            const rescanButton = document.getElementById('quick-qr-rescan');
            const scannerPane = document.getElementById('quick-qr-scanner-pane');
            const previewPane = document.getElementById('quick-qr-preview-pane');
            const errorEl = document.getElementById('quick-qr-error');
            const readerId = 'quick-qr-reader';
            const csrfToken = '{{ csrf_token() }}';
            const previewUrl = '{{ route('quick-borrow-qr.preview') }}';
            const processUrl = '{{ route('quick-borrow-qr.process') }}';

            const form = document.getElementById('quick-qr-process-form');
            const qrCodeInput = document.getElementById('quick-qr-code');
            const actionInput = document.getElementById('quick-qr-action');
            const userName = document.getElementById('quick-qr-user-name');
            const userEmail = document.getElementById('quick-qr-user-email');
            const thingCode = document.getElementById('quick-qr-thing-code');
            const thingName = document.getElementById('quick-qr-thing-name');
            const thingRoom = document.getElementById('quick-qr-thing-room');
            const borrowFields = document.getElementById('quick-qr-borrow-fields');
            const reasonSelect = document.getElementById('quick-qr-reason');
            const reasonOtherWrap = document.getElementById('quick-qr-reason-other-wrap');
            const reasonOther = document.getElementById('quick-qr-reason-other');
            const photoInput = document.getElementById('quick-qr-photo');
            const photoLabel = document.getElementById('quick-qr-photo-label');
            const submitButton = document.getElementById('quick-qr-submit');

            if (!openButton || !modal || !closeButton || !scannerPane || !previewPane || !errorEl || !form || !photoInput) {
                return;
            }

            let scanner = null;

            function setMode(mode) {
                if (mode === 'checkout') {
                    actionInput.value = 'checkout';
                    borrowFields.classList.add('hidden');
                    photoInput.name = 'foto_akhir';
                    photoLabel.textContent = 'Foto Selfie + Barang Kembali *';
                    submitButton.textContent = 'Kembalikan';
                } else {
                    actionInput.value = 'borrow';
                    borrowFields.classList.remove('hidden');
                    photoInput.name = 'foto_awal';
                    photoLabel.textContent = 'Foto Selfie + Barang *';
                    submitButton.textContent = 'Pinjam Sekarang';
                }
            }

            function setFormVisible(previewVisible) {
                if (previewVisible) {
                    scannerPane.classList.add('hidden');
                    previewPane.classList.remove('hidden');
                } else {
                    previewPane.classList.add('hidden');
                    scannerPane.classList.remove('hidden');
                }
            }

            async function stopScanner() {
                if (!scanner) {
                    return;
                }

                try {
                    await scanner.stop();
                } catch (error) {
                    // noop
                }

                try {
                    await scanner.clear();
                } catch (error) {
                    // noop
                }

                scanner = null;
            }

            async function openScanner() {
                if (!window.Html5Qrcode) {
                    errorEl.textContent = 'Library scanner belum siap. Coba muat ulang halaman.';
                    return;
                }

                errorEl.textContent = '';
                setFormVisible(false);
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                scanner = new Html5Qrcode(readerId);

                try {
                    let cameraConfig = { facingMode: 'environment' };
                    if (window.Html5Qrcode.getCameras) {
                        const cameras = await window.Html5Qrcode.getCameras();
                        if (Array.isArray(cameras) && cameras.length > 0) {
                            const preferred = cameras.find((camera) => /back|rear|environment/i.test(camera.label)) || cameras[0];
                            cameraConfig = { deviceId: { exact: preferred.id } };
                        }
                    }

                    await scanner.start(cameraConfig, { fps: 10, qrbox: 220 }, async function (decodedText) {
                        const normalized = String(decodedText).trim().toUpperCase();
                        if (normalized === '') {
                            errorEl.textContent = 'QR tidak valid.';
                            return;
                        }

                        await stopScanner();

                        try {
                            const response = await fetch(previewUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ qr_code: normalized }),
                            });

                            const payload = await response.json();
                            if (!response.ok) {
                                throw new Error(payload.message || 'Barang tidak dapat diproses.');
                            }

                            qrCodeInput.value = normalized;
                            userName.value = payload.user?.name || '';
                            userEmail.value = payload.user?.email || '';
                            thingCode.value = payload.thing?.kode_thing || '';
                            thingName.value = payload.thing?.nama || '';
                            thingRoom.value = payload.thing?.room_name
                                ? payload.thing.room_name + (payload.thing.room_code ? ' (' + payload.thing.room_code + ')' : '')
                                : 'Tidak ada data lokasi';

                            setMode(payload.mode || 'borrow');
                            setFormVisible(true);
                        } catch (error) {
                            errorEl.textContent = error.message || 'Gagal membuka detail barang.';
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        }
                    });
                } catch (error) {
                    errorEl.textContent = error && error.message ? error.message : 'Kamera gagal dibuka. Pastikan izin kamera diaktifkan.';
                    await stopScanner();
                }
            }

            function closeModal() {
                stopScanner();
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                errorEl.textContent = '';
                form.reset();
                setMode('borrow');
                setFormVisible(false);
            }

            function updateReasonState() {
                if (reasonSelect && reasonOtherWrap) {
                    if (reasonSelect.value === 'Lainnya') {
                        reasonOtherWrap.classList.remove('hidden');
                    } else {
                        reasonOtherWrap.classList.add('hidden');
                    }
                }
            }

            function loadImageFromFile(file) {
                return new Promise(function (resolve, reject) {
                    const url = URL.createObjectURL(file);
                    const image = new Image();
                    image.onload = function () {
                        URL.revokeObjectURL(url);
                        resolve(image);
                    };
                    image.onerror = function () {
                        URL.revokeObjectURL(url);
                        reject(new Error('Gagal memuat gambar.'));
                    };
                    image.src = url;
                });
            }

            async function compressIfNeeded(file) {
                const maxBytes = 1800 * 1024;
                if (!file || file.size <= maxBytes) {
                    return file;
                }

                const image = await loadImageFromFile(file);
                const maxWidth = 1600;
                const ratio = image.width > maxWidth ? (maxWidth / image.width) : 1;
                const width = Math.max(1, Math.round(image.width * ratio));
                const height = Math.max(1, Math.round(image.height * ratio));

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const context = canvas.getContext('2d');
                if (!context) {
                    return file;
                }

                context.drawImage(image, 0, 0, width, height);

                let quality = 0.85;
                let blob = null;

                while (quality >= 0.4) {
                    // eslint-disable-next-line no-await-in-loop
                    blob = await new Promise(function (resolve) {
                        canvas.toBlob(function (result) {
                            resolve(result);
                        }, 'image/jpeg', quality);
                    });

                    if (!blob) {
                        break;
                    }

                    if (blob.size <= maxBytes) {
                        break;
                    }

                    quality -= 0.1;
                }

                if (!blob) {
                    return file;
                }

                return new File([blob], 'quick-qr-photo.jpg', {
                    type: 'image/jpeg',
                    lastModified: Date.now(),
                });
            }

            openButton.addEventListener('click', openScanner);
            closeButton.addEventListener('click', closeModal);
            rescanButton.addEventListener('click', async function () {
                setFormVisible(false);
                errorEl.textContent = '';
                await stopScanner();
                openScanner();
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            if (reasonSelect) {
                reasonSelect.addEventListener('change', updateReasonState);
                updateReasonState();
            }

            form.addEventListener('submit', async function (event) {
                if (reasonOther && actionInput.value === 'borrow') {
                    if (reasonSelect && reasonSelect.value !== 'Lainnya') {
                        reasonOther.value = '';
                    }
                }

                if (form.dataset.qrCompressing === '1') {
                    return;
                }

                if (!photoInput.files || photoInput.files.length === 0) {
                    return;
                }

                try {
                    event.preventDefault();
                    form.dataset.qrCompressing = '1';
                    submitButton.setAttribute('disabled', 'disabled');

                    const compressed = await compressIfNeeded(photoInput.files[0]);
                    const transfer = new DataTransfer();
                    transfer.items.add(compressed);
                    photoInput.files = transfer.files;

                    form.submit();
                } catch (error) {
                    form.dataset.qrCompressing = '0';
                    submitButton.removeAttribute('disabled');
                    alert('Foto gagal diproses. Coba ambil ulang foto dengan resolusi lebih rendah.');
                }
            });

            setMode('borrow');
            setFormVisible(false);
        })();
    </script>
@endpush