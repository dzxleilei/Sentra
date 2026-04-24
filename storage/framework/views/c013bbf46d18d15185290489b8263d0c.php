

<?php $__env->startSection('title', 'Tiket Booking'); ?>
<?php $__env->startSection('page_title', 'Tiket Booking'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $checkinBadge = [
            'Tidak Check-in' => 'bg-rose-100 text-rose-800',
        ];
        $checkoutBadge = [
            'Tidak Check-out' => 'bg-rose-100 text-rose-800',
        ];
        $earliestCheckin = $borrow->waktu_mulai_booking->copy()->subMinutes(5)->format('H:i');
        $latestCheckout = $borrow->waktu_selesai_booking->copy()->addMinutes(5)->format('H:i');
        $hasBookingItems = $ticketItems->contains(fn($item) => $item->status === 'Booking');
        $hasRunningItems = $ticketItems->contains(fn($item) => $item->status === 'Berlangsung');
        $allBookingItems = $ticketItems->every(fn($item) => $item->status === 'Booking');
        $firstStart = $ticketItems->min('waktu_mulai_booking');
        $firstEnd = $ticketItems->max('waktu_selesai_booking');
        $isItemTicket = in_array($borrow->tipe, ['Barang', 'Barang_Dari_Ruangan'], true);
        $nowServer = \Illuminate\Support\Carbon::now();
        $cancelDeadline = $firstStart ? $firstStart->copy()->subMinutes(15) : null;
        $checkinStartAt = $firstStart ? $firstStart->copy()->subMinutes(5) : null;
        $checkinDeadline = $firstStart ? $firstStart->copy()->addMinutes(15) : null;
        $checkoutDeadline = $firstEnd ? $firstEnd->copy()->addMinutes(15) : null;
        $canCancelNow = $cancelDeadline ? $nowServer->lessThan($cancelDeadline) : false;
        $canCheckinNow = $checkinStartAt ? $nowServer->greaterThanOrEqualTo($checkinStartAt) : false;
        $bookingTicketItems = $ticketItems->filter(fn($item) => $item->status === 'Booking');
        $runningTicketItems = $ticketItems->filter(fn($item) => $item->status === 'Berlangsung');
    ?>

    <article class="rounded-2xl border border-slate-200 p-4">
        <p class="text-xs text-slate-500">Kode Tiket</p>
        <p class="mt-1 font-mono text-lg font-bold"><?php echo e($borrow->kode_booking); ?></p>

        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
            <div>
                <p class="text-xs text-slate-500">Tipe</p>
                <p class="font-semibold">Tiket Barang</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Jumlah Barang</p>
                <p class="font-semibold"><?php echo e($ticketItems->count()); ?> item</p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-slate-500">Jadwal</p>
                <p class="font-semibold"><?php echo e(optional($firstStart)->format('d M Y H:i')); ?> - <?php echo e(optional($firstEnd)->format('H:i')); ?></p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-slate-500">Alasan</p>
                <p class="font-semibold"><?php echo e($borrow->alasan_peminjaman); ?><?php if($borrow->alasan_peminjaman === 'Lainnya' && $borrow->alasan_lainnya): ?> - <?php echo e($borrow->alasan_lainnya); ?><?php endif; ?></p>
            </div>
        </div>
    </article>

    <?php if($allBookingItems): ?>
        <section class="mt-4 rounded-2xl border border-slate-200 p-4">
            <h2 class="text-sm font-bold">Pembatalan Tiket</h2>
            <p class="mt-1 text-xs text-slate-500">Tiket dapat dibatalkan maksimal 15 menit sebelum jam mulai.</p>
            <p id="cancel-countdown" data-target="<?php echo e(optional($cancelDeadline)->toIso8601String()); ?>" class="mt-2 text-xs font-semibold text-amber-700"></p>
            <form action="<?php echo e(route('booking.cancel')); ?>" method="POST" class="mt-3">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="kode_booking" value="<?php echo e($borrow->kode_booking); ?>">
                <button id="cancel-ticket-btn" type="submit" class="w-full rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white disabled:opacity-60" <?php echo e($canCancelNow ? '' : 'disabled'); ?>>Batalkan Tiket</button>
            </form>
        </section>
    <?php endif; ?>

    <?php if($hasBookingItems): ?>
        <section class="mt-4 rounded-2xl border border-slate-200 p-4">
            <h2 class="text-sm font-bold">Scan untuk Check-in</h2>
            <p class="mt-1 text-xs text-slate-500">
                <?php if($isItemTicket): ?>
                    Scan QR tiap barang satu per satu.
                <?php else: ?>
                    Scan QR ruangan sesuai kode fisik ruangan.
                <?php endif; ?>
                Check-in paling cepat jam <?php echo e($earliestCheckin); ?>.
            </p>
            <p id="checkin-countdown" data-target="<?php echo e(optional($checkinStartAt)->toIso8601String()); ?>" class="mt-2 text-xs font-semibold text-blue-700"></p>
            <p id="checkin-deadline-countdown" data-target="<?php echo e(optional($checkinDeadline)->toIso8601String()); ?>" class="mt-1 hidden text-xs font-semibold text-rose-700"></p>
            <?php if($isItemTicket): ?>
                <p class="mt-2 text-xs text-slate-500">Semua barang wajib discan dari daftar barang di atas sebelum proses check-in.</p>
                <p id="checkin-scan-progress" class="mt-2 text-xs font-semibold text-slate-700">Progress scan: 0 / <?php echo e($bookingTicketItems->count()); ?></p>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <section class="mt-4 rounded-2xl border border-slate-200 p-4">
        <h2 class="text-sm font-bold">Daftar Barang Dalam Tiket</h2>
        <div class="mt-3 space-y-2">
            <?php $__currentLoopData = $ticketItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 p-3" x-data="{ openReport: false }">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-slate-500">ID Barang: <?php echo e($item->thing->kode_thing ?? '-'); ?></p>
                            <p class="mt-1 text-sm font-semibold text-slate-800"><?php echo e($item->thing->nama ?? '-'); ?></p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($item->status === 'Selesai' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'Berlangsung' ? 'bg-blue-100 text-blue-800' : ($item->status === 'Dibatalkan' ? 'bg-slate-200 text-slate-700' : 'bg-amber-100 text-amber-800'))); ?>"><?php echo e($item->status); ?></span>
                                <?php if($item->status !== 'Selesai'): ?>
                                    <?php if($item->status_checkin): ?>
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($checkinBadge[$item->status_checkin] ?? 'bg-slate-100 text-slate-700'); ?>"><?php echo e($item->status_checkin); ?></span>
                                    <?php endif; ?>
                                    <?php if($item->status_checkout): ?>
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($checkoutBadge[$item->status_checkout] ?? 'bg-slate-100 text-slate-700'); ?>"><?php echo e($item->status_checkout); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if(($isItemTicket && $item->status === 'Booking') || $item->waktu_checkin): ?>
                            <div class="mt-0.5 flex shrink-0 flex-col items-end gap-2">
                                <?php if($isItemTicket && $item->status === 'Booking'): ?>
                                    <div class="flex items-center gap-2">
                                        <span id="scan-state-checkin-<?php echo e($item->thing->kode_thing); ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-700" title="Belum discan">
                                            <i class="fas fa-xmark"></i>
                                        </span>
                                        <button
                                            type="button"
                                            class="open-qr-modal rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-60"
                                            data-mode="checkin"
                                            data-scan-code="<?php echo e(strtoupper((string) ($item->thing->kode_thing ?? ''))); ?>"
                                            data-label="<?php echo e($item->thing->nama ?? 'Barang'); ?>"
                                            <?php echo e($canCheckinNow ? '' : 'disabled'); ?>

                                        >
                                            Scan QR
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <?php if($item->waktu_checkin): ?>
                                    <button type="button" @click="openReport = !openReport" class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white">
                                        Laporkan Barang Rusak
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if($item->waktu_checkin): ?>
                        <form x-show="openReport" action="<?php echo e(route('borrow.report-damage')); ?>" method="POST" enctype="multipart/form-data" class="mt-3 space-y-2 rounded-xl border border-rose-200 bg-rose-50 p-3">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="borrow_id" value="<?php echo e($item->id); ?>">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-rose-700">Lokasi Barang</label>
                                <input type="text" name="lokasi_barang" required class="w-full rounded-lg border border-rose-300 px-3 py-2 text-sm" placeholder="Contoh: Ruang 302, meja depan">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-rose-700">Foto Barang Rusak</label>
                                <input type="file" name="foto_bukti" required accept="image/*" capture="environment" class="w-full rounded-lg border border-rose-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-rose-700">Keterangan (opsional)</label>
                                <textarea name="keterangan" rows="2" class="w-full rounded-lg border border-rose-300 px-3 py-2 text-sm"></textarea>
                            </div>
                            <button type="submit" class="w-full rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white">Kirim Laporan</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <?php if($hasBookingItems): ?>
        <section class="mt-4 rounded-2xl border border-slate-200 p-4">
            <h2 class="text-sm font-bold">Proses Check-in</h2>
            <form action="<?php echo e(route('borrow.checkin')); ?>" method="POST" enctype="multipart/form-data" class="mt-3 space-y-2">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="kode_booking" value="<?php echo e($borrow->kode_booking); ?>">
                <?php if($isItemTicket): ?>
                    <input id="scanned_item_ids_checkin" type="hidden" name="scanned_values" required>
                    <p class="text-xs text-slate-500">Semua barang wajib discan dari daftar barang di atas.</p>
                <?php else: ?>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Hasil Scan QR <span class="text-rose-600">*</span></label>
                        <input id="scanned_item_id_checkin" type="text" name="scanned_value" readonly required class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm" placeholder="Belum ada hasil scan">
                    </div>
                    <div class="rounded-xl border border-slate-200 p-3">
                        <p class="text-xs text-slate-500">Scan QR via kamera HP</p>
                        <button type="button" id="start-checkin-scan" class="mt-2 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-60" <?php echo e($canCheckinNow ? '' : 'disabled'); ?>>Buka Kamera</button>
                    </div>
                <?php endif; ?>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Foto Selfie + Kondisi Awal (Wajib via Kamera) <span class="text-rose-600">*</span></label>
                    <input id="foto-awal-input" type="file" name="foto_awal" accept="image/*" class="hidden">
                    <div class="rounded-xl border border-slate-200 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span id="camera-state-awal" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-700" title="Belum ada foto">
                                    <i class="fas fa-xmark"></i>
                                </span>
                                <p class="text-xs text-slate-500">Selfie awal belum diambil.</p>
                            </div>
                            <button id="open-camera-awal" type="button" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700">Buka Kamera</button>
                        </div>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">Foto harus diambil langsung dari kamera, tidak bisa pilih file galeri/manual.</p>
                </div>
                <button id="checkin-submit-btn" type="submit" class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white disabled:opacity-60" <?php echo e($canCheckinNow ? '' : 'disabled'); ?>>Proses Check-in</button>
            </form>
        </section>
    <?php endif; ?>

    <?php if($hasRunningItems): ?>
        <section class="mt-4 rounded-2xl border border-slate-200 p-4">
            <h2 class="text-sm font-bold">Scan untuk Check-out</h2>
            <p class="mt-1 text-xs text-slate-500">
                <?php if($isItemTicket): ?>
                    Scan QR tiap barang satu per satu.
                <?php else: ?>
                    Scan QR ruangan sesuai kode fisik ruangan.
                <?php endif; ?>
                Check-out paling lambat jam <?php echo e($latestCheckout); ?>.
            </p>
            <p id="checkout-deadline-countdown" data-target="<?php echo e(optional($checkoutDeadline)->toIso8601String()); ?>" class="mt-1 hidden text-xs font-semibold text-rose-700"></p>
            <form action="<?php echo e(route('borrow.checkout')); ?>" method="POST" enctype="multipart/form-data" class="mt-3 space-y-2">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="kode_booking" value="<?php echo e($borrow->kode_booking); ?>">
                <?php if($isItemTicket): ?>
                    <input id="scanned_item_ids_checkout" type="hidden" name="scanned_values" required>
                    <div class="rounded-xl border border-slate-200 p-3">
                        <p class="text-xs text-slate-500">Semua barang wajib discan sebelum proses check-out.</p>
                        <p id="checkout-scan-progress" class="mt-2 text-xs font-semibold text-slate-700">Progress scan: 0 / <?php echo e($runningTicketItems->count()); ?></p>
                        <div class="mt-3 space-y-2">
                            <?php $__currentLoopData = $runningTicketItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800"><?php echo e($item->thing->nama ?? '-'); ?></p>
                                        <p class="text-xs text-slate-500">ID Barang: <?php echo e($item->thing->kode_thing ?? '-'); ?></p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span id="scan-state-checkout-<?php echo e($item->thing->kode_thing); ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-700" title="Belum discan">
                                            <i class="fas fa-xmark"></i>
                                        </span>
                                        <button
                                            type="button"
                                            class="open-qr-modal rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white"
                                            data-mode="checkout"
                                            data-scan-code="<?php echo e(strtoupper((string) ($item->thing->kode_thing ?? ''))); ?>"
                                            data-label="<?php echo e($item->thing->nama ?? 'Barang'); ?>"
                                        >
                                            Scan QR
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Hasil Scan QR <span class="text-rose-600">*</span></label>
                        <input id="scanned_item_id_checkout" type="text" name="scanned_value" readonly required class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm" placeholder="Belum ada hasil scan">
                    </div>
                    <div class="rounded-xl border border-slate-200 p-3">
                        <p class="text-xs text-slate-500">Scan QR via kamera HP</p>
                        <button type="button" id="start-checkout-scan" class="mt-2 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white">Buka Kamera</button>
                    </div>
                <?php endif; ?>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Foto Selfie + Kondisi Akhir (Wajib via Kamera) <span class="text-rose-600">*</span></label>
                    <input id="foto-akhir-input" type="file" name="foto_akhir" accept="image/*" class="hidden">
                    <div class="rounded-xl border border-slate-200 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span id="camera-state-akhir" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-700" title="Belum ada foto">
                                    <i class="fas fa-xmark"></i>
                                </span>
                                <p class="text-xs text-slate-500">Selfie akhir belum diambil.</p>
                            </div>
                            <button id="open-camera-akhir" type="button" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700">Buka Kamera</button>
                        </div>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">Foto harus diambil langsung dari kamera, tidak bisa pilih file galeri/manual.</p>
                </div>
                <button type="submit" class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white">Proses Check-out</button>
            </form>
        </section>
    <?php endif; ?>

    <div id="qr-scan-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 id="qr-modal-title" class="text-sm font-bold text-slate-900">Scan QR</h3>
                    <p class="mt-1 text-xs text-slate-500">Arahkan kamera ke QR barang yang dipilih.</p>
                </div>
                <button id="qr-modal-close" type="button" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-semibold text-slate-700">Tutup</button>
            </div>
            <div id="qr-reader-modal" class="mt-3 min-h-64 overflow-hidden rounded-lg border border-slate-200 bg-slate-50"></div>
            <p id="qr-modal-error" class="mt-2 text-xs font-semibold text-rose-700"></p>
        </div>
    </div>

    <div id="camera-capture-modal" class="fixed inset-0 z-60 hidden items-center justify-center bg-slate-900/70 p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 id="camera-modal-title" class="text-sm font-bold text-slate-900">Ambil Foto Selfie</h3>
                    <p class="mt-1 text-xs text-slate-500">Gunakan kamera depan, lalu simpan hasil foto.</p>
                </div>
                <button id="camera-modal-close" type="button" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-semibold text-slate-700">Tutup</button>
            </div>
            <video id="camera-modal-preview" class="mt-3 hidden w-full rounded-lg bg-black" autoplay playsinline muted></video>
            <img id="camera-modal-result" class="mt-3 hidden w-full rounded-lg border border-slate-200 object-cover" alt="Hasil selfie">
            <p id="camera-modal-error" class="mt-2 text-xs font-semibold text-rose-700"></p>
            <div class="mt-3 flex gap-2">
                <button id="camera-modal-capture" type="button" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white">Ambil Foto</button>
                <button id="camera-modal-retake" type="button" class="hidden rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700">Ambil Ulang</button>
                <button id="camera-modal-use" type="button" class="hidden rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white">Gunakan Foto</button>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        function getNowMs() {
            return typeof window.sentraServerNowMs === 'function' ? window.sentraServerNowMs() : Date.now();
        }

        function startCountdown({ elementId, targetTime, onUnlock, lockedText, unlockedText }) {
            const el = document.getElementById(elementId);
            if (!el || !targetTime) {
                return;
            }

            function tick() {
                const now = getNowMs();
                const diff = targetTime - now;

                if (diff <= 0) {
                    el.textContent = unlockedText;
                    if (typeof onUnlock === 'function') {
                        onUnlock();
                    }
                    return;
                }

                const totalSeconds = Math.floor(diff / 1000);
                const hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
                const seconds = String(totalSeconds % 60).padStart(2, '0');
                el.textContent = lockedText + ' ' + hours + ':' + minutes + ':' + seconds;
                setTimeout(tick, 1000);
            }

            tick();
        }

        function startDeadlineCountdown({ elementId, targetText }) {
            const el = document.getElementById(elementId);
            if (!el) {
                return;
            }

            const targetRaw = el.dataset?.target;
            if (!targetRaw) {
                return;
            }

            const targetTime = new Date(targetRaw).getTime();

            (function tickDeadline() {
                const diff = targetTime - getNowMs();

                if (diff <= 0) {
                    el.textContent = 'Batas waktu sudah berakhir.';
                    return;
                }

                const totalSeconds = Math.floor(diff / 1000);
                const hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
                const seconds = String(totalSeconds % 60).padStart(2, '0');
                el.textContent = targetText + ' ' + hours + ':' + minutes + ':' + seconds;
                setTimeout(tickDeadline, 1000);
            })();
        }

        const checkinTarget = document.getElementById('checkin-countdown')?.dataset?.target;
        if (checkinTarget) {
            const checkinButton = document.getElementById('checkin-submit-btn');
            const checkinScanButton = document.getElementById('start-checkin-scan');
            const checkinCountdownText = document.getElementById('checkin-countdown');
            const checkinDeadlineText = document.getElementById('checkin-deadline-countdown');
            startCountdown({
                elementId: 'checkin-countdown',
                targetTime: new Date(checkinTarget).getTime(),
                lockedText: 'Check-in bisa dilakukan dalam',
                unlockedText: 'Check-in sudah bisa dilakukan sekarang.',
                onUnlock: function () {
                    if (checkinButton) {
                        checkinButton.removeAttribute('disabled');
                    }
                    if (checkinScanButton) {
                        checkinScanButton.removeAttribute('disabled');
                    }

                    if (checkinCountdownText) {
                        checkinCountdownText.classList.add('hidden');
                    }

                    if (checkinDeadlineText) {
                        checkinDeadlineText.classList.remove('hidden');
                        startDeadlineCountdown({
                            elementId: 'checkin-deadline-countdown',
                            targetText: 'Sisa waktu sampai batas akhir check-in:'
                        });
                    }
                }
            });
        }

        const checkoutDeadlineText = document.getElementById('checkout-deadline-countdown');
        if (checkoutDeadlineText) {
            checkoutDeadlineText.classList.remove('hidden');
            startDeadlineCountdown({
                elementId: 'checkout-deadline-countdown',
                targetText: 'Sisa waktu sampai batas akhir check-out:'
            });
        }

        const cancelTarget = document.getElementById('cancel-countdown')?.dataset?.target;
        if (cancelTarget) {
            const cancelButton = document.getElementById('cancel-ticket-btn');
            const target = new Date(cancelTarget).getTime();

            (function cancelTick() {
                const diff = target - getNowMs();
                const el = document.getElementById('cancel-countdown');
                if (!el) {
                    return;
                }

                if (diff <= 0) {
                    el.textContent = 'Waktu pembatalan sudah berakhir.';
                    if (cancelButton) {
                        cancelButton.setAttribute('disabled', 'disabled');
                    }
                    return;
                }

                const totalSeconds = Math.floor(diff / 1000);
                const hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
                const seconds = String(totalSeconds % 60).padStart(2, '0');
                el.textContent = 'Sisa waktu pembatalan: ' + hours + ':' + minutes + ':' + seconds;
                setTimeout(cancelTick, 1000);
            })();
        }

        (function setupScanModal() {
            const modal = document.getElementById('qr-scan-modal');
            const modalClose = document.getElementById('qr-modal-close');
            const modalTitle = document.getElementById('qr-modal-title');
            const modalError = document.getElementById('qr-modal-error');
            const readerId = 'qr-reader-modal';
            const openButtons = document.querySelectorAll('.open-qr-modal');

            if (!modal || !modalClose || !modalTitle || !modalError) {
                return;
            }

            const checkinHidden = document.getElementById('scanned_item_ids_checkin');
            const checkoutHidden = document.getElementById('scanned_item_ids_checkout');
            const checkinProgress = document.getElementById('checkin-scan-progress');
            const checkoutProgress = document.getElementById('checkout-scan-progress');

            const checkinExpected = Array.from(document.querySelectorAll('.open-qr-modal[data-mode="checkin"]'))
                .map((el) => String(el.dataset.scanCode || '').trim().toUpperCase())
                .filter((code) => code !== '');

            const checkoutExpected = Array.from(document.querySelectorAll('.open-qr-modal[data-mode="checkout"]'))
                .map((el) => String(el.dataset.scanCode || '').trim().toUpperCase())
                .filter((code) => code !== '');

            const scanState = {
                checkin: new Set(),
                checkout: new Set(),
            };

            let scanner = null;
            let activeMode = null;
            let activeScanCode = null;

            function updateProgress(mode) {
                const expected = mode === 'checkin' ? checkinExpected : checkoutExpected;
                const progressEl = mode === 'checkin' ? checkinProgress : checkoutProgress;
                const hiddenInput = mode === 'checkin' ? checkinHidden : checkoutHidden;

                if (hiddenInput) {
                    hiddenInput.value = Array.from(scanState[mode]).sort().join(',');
                }

                if (progressEl) {
                    progressEl.textContent = 'Progress scan: ' + scanState[mode].size + ' / ' + expected.length;
                }
            }

            function markScanned(mode, scanCode) {
                const chip = document.getElementById('scan-state-' + mode + '-' + scanCode);
                if (chip) {
                    chip.innerHTML = '<i class="fas fa-check"></i>';
                    chip.className = 'inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700';
                    chip.setAttribute('title', 'Sudah discan');
                }
                scanState[mode].add(scanCode);
                updateProgress(mode);
            }

            async function stopScanner() {
                if (!scanner) {
                    return;
                }

                try {
                    await scanner.stop();
                } catch (e) {
                    // noop
                }

                try {
                    await scanner.clear();
                } catch (e) {
                    // noop
                }

                scanner = null;
            }

            async function closeModal() {
                await stopScanner();
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modalError.textContent = '';
            }

            modalClose.addEventListener('click', function () {
                closeModal();
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            async function startScanner() {
                if (!window.Html5Qrcode) {
                    modalError.textContent = 'Library scanner belum siap. Coba muat ulang halaman.';
                    return;
                }

                if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                    modalError.textContent = 'Kamera hanya bisa diakses di HTTPS atau localhost.';
                    return;
                }

                scanner = new Html5Qrcode(readerId);

                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: 'environment' }
                        }
                    });
                    stream.getTracks().forEach((track) => track.stop());

                    let cameraConfig = { facingMode: 'environment' };
                    if (window.Html5Qrcode.getCameras) {
                        const cameras = await window.Html5Qrcode.getCameras();
                        if (Array.isArray(cameras) && cameras.length > 0) {
                            const preferred = cameras.find((camera) => /back|rear|environment/i.test(camera.label)) || cameras[0];
                            cameraConfig = { deviceId: { exact: preferred.id } };
                        }
                    }

                    await scanner.start(
                        cameraConfig,
                        { fps: 10, qrbox: 220 },
                        async function (decodedText) {
                            const normalized = String(decodedText).trim().toUpperCase();
                            if (normalized === '') {
                                modalError.textContent = 'QR tidak valid.';
                                return;
                            }

                            if (normalized !== activeScanCode) {
                                modalError.textContent = 'QR tidak cocok. Silahkan ulangi!';
                                return;
                            }

                            markScanned(activeMode, normalized);
                            await closeModal();
                        }
                    );
                } catch (e) {
                    modalError.textContent = e && e.message
                        ? e.message
                        : 'Kamera gagal dibuka. Pastikan izin kamera diaktifkan.';
                    await stopScanner();
                }
            }

            openButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    activeMode = button.dataset.mode;
                    activeScanCode = String(button.dataset.scanCode || '').trim().toUpperCase();
                    const label = button.dataset.label || 'Barang';

                    if (activeScanCode === '') {
                        return;
                    }

                    modalTitle.textContent = 'Scan QR - ' + label + ' (Kode ' + activeScanCode + ')';
                    modalError.textContent = '';
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    startScanner();
                });
            });

            const roomCheckinButton = document.getElementById('start-checkin-scan');
            const roomCheckoutButton = document.getElementById('start-checkout-scan');
            const roomCheckinInput = document.getElementById('scanned_item_id_checkin');
            const roomCheckoutInput = document.getElementById('scanned_item_id_checkout');

            function openRoomScan(mode) {
                activeMode = mode;
                activeScanCode = null;
                modalTitle.textContent = mode === 'checkin' ? 'Scan QR Ruangan (Check-in)' : 'Scan QR Ruangan (Check-out)';
                modalError.textContent = '';
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                if (!window.Html5Qrcode) {
                    modalError.textContent = 'Library scanner belum siap. Coba muat ulang halaman.';
                    return;
                }

                scanner = new Html5Qrcode(readerId);
                (async function startRoom() {
                    try {
                        let cameraConfig = { facingMode: 'environment' };
                        if (window.Html5Qrcode.getCameras) {
                            const cameras = await window.Html5Qrcode.getCameras();
                            if (Array.isArray(cameras) && cameras.length > 0) {
                                const preferred = cameras.find((camera) => /back|rear|environment/i.test(camera.label)) || cameras[0];
                                cameraConfig = { deviceId: { exact: preferred.id } };
                            }
                        }

                        await scanner.start(
                            cameraConfig,
                            { fps: 10, qrbox: 220 },
                            async function (decodedText) {
                                const normalized = String(decodedText).trim();
                                if (mode === 'checkin' && roomCheckinInput) {
                                    roomCheckinInput.value = normalized;
                                }
                                if (mode === 'checkout' && roomCheckoutInput) {
                                    roomCheckoutInput.value = normalized;
                                }
                                await closeModal();
                            }
                        );
                    } catch (e) {
                        modalError.textContent = e && e.message
                            ? e.message
                            : 'Kamera gagal dibuka. Pastikan izin kamera diaktifkan.';
                        await stopScanner();
                    }
                })();
            }

            if (roomCheckinButton) {
                roomCheckinButton.addEventListener('click', function () {
                    openRoomScan('checkin');
                });
            }

            if (roomCheckoutButton) {
                roomCheckoutButton.addEventListener('click', function () {
                    openRoomScan('checkout');
                });
            }

            updateProgress('checkin');
            updateProgress('checkout');
        })();

        (function setupCameraCaptureModal() {
            const modal = document.getElementById('camera-capture-modal');
            const modalTitle = document.getElementById('camera-modal-title');
            const modalClose = document.getElementById('camera-modal-close');
            const modalError = document.getElementById('camera-modal-error');
            const videoEl = document.getElementById('camera-modal-preview');
            const imageEl = document.getElementById('camera-modal-result');
            const captureBtn = document.getElementById('camera-modal-capture');
            const retakeBtn = document.getElementById('camera-modal-retake');
            const useBtn = document.getElementById('camera-modal-use');

            if (!modal || !modalTitle || !modalClose || !modalError || !videoEl || !imageEl || !captureBtn || !retakeBtn || !useBtn) {
                return;
            }

            let stream = null;
            let previewUrl = '';
            let activeConfig = null;

            const cameraConfigMap = {
                awal: {
                    openId: 'open-camera-awal',
                    fileId: 'foto-awal-input',
                    stateId: 'camera-state-awal',
                    title: 'Ambil Foto Selfie + Kondisi Awal',
                    fileName: 'foto-awal.jpg',
                },
                akhir: {
                    openId: 'open-camera-akhir',
                    fileId: 'foto-akhir-input',
                    stateId: 'camera-state-akhir',
                    title: 'Ambil Foto Selfie + Kondisi Akhir',
                    fileName: 'foto-akhir.jpg',
                },
            };

            function stopStream() {
                if (stream) {
                    stream.getTracks().forEach((track) => track.stop());
                    stream = null;
                }
                videoEl.srcObject = null;
            }

            function resetPreview() {
                stopStream();
                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                    previewUrl = '';
                }
                imageEl.src = '';
                modalError.textContent = '';
                imageEl.classList.add('hidden');
                videoEl.classList.remove('hidden');
                captureBtn.classList.remove('hidden');
                retakeBtn.classList.add('hidden');
                useBtn.classList.add('hidden');
            }

            async function startCamera() {
                if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                    modalError.textContent = 'Kamera hanya bisa diakses di HTTPS atau localhost.';
                    return;
                }

                try {
                    stopStream();
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: 'user' } },
                        audio: false,
                    });
                    videoEl.srcObject = stream;
                    await videoEl.play();
                    modalError.textContent = '';
                } catch (error) {
                    modalError.textContent = 'Kamera tidak bisa diakses. Pastikan izin kamera diaktifkan.';
                }
            }

            async function openModal(config) {
                activeConfig = config;
                modalTitle.textContent = config.title;
                resetPreview();
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                await startCamera();
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                resetPreview();
                activeConfig = null;
            }

            function markStateDone(stateId) {
                const chip = document.getElementById(stateId);
                if (!chip) {
                    return;
                }

                chip.innerHTML = '<i class="fas fa-check"></i>';
                chip.className = 'inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700';
                chip.setAttribute('title', 'Foto sudah diambil');
            }

            Object.values(cameraConfigMap).forEach((config) => {
                const openBtn = document.getElementById(config.openId);
                if (!openBtn) {
                    return;
                }

                openBtn.addEventListener('click', function () {
                    openModal(config);
                });
            });

            modalClose.addEventListener('click', closeModal);

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            captureBtn.addEventListener('click', function () {
                if (!activeConfig || !videoEl.videoWidth || !videoEl.videoHeight) {
                    return;
                }

                const canvas = document.createElement('canvas');
                canvas.width = videoEl.videoWidth;
                canvas.height = videoEl.videoHeight;
                const ctx = canvas.getContext('2d');
                if (!ctx) {
                    return;
                }

                ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);

                canvas.toBlob(function (blob) {
                    if (!blob || !activeConfig) {
                        return;
                    }

                    const fileInput = document.getElementById(activeConfig.fileId);
                    if (!fileInput) {
                        modalError.textContent = 'Input file tidak ditemukan.';
                        return;
                    }

                    const file = new File([blob], activeConfig.fileName, { type: 'image/jpeg' });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    fileInput.files = dt.files;

                    if (previewUrl) {
                        URL.revokeObjectURL(previewUrl);
                    }
                    previewUrl = URL.createObjectURL(blob);
                    imageEl.src = previewUrl;

                    imageEl.classList.remove('hidden');
                    videoEl.classList.add('hidden');
                    captureBtn.classList.add('hidden');
                    retakeBtn.classList.remove('hidden');
                    useBtn.classList.remove('hidden');
                    stopStream();
                }, 'image/jpeg', 0.92);
            });

            retakeBtn.addEventListener('click', function () {
                if (!activeConfig) {
                    return;
                }

                const fileInput = document.getElementById(activeConfig.fileId);
                if (fileInput) {
                    fileInput.value = '';
                }
                imageEl.classList.add('hidden');
                videoEl.classList.remove('hidden');
                captureBtn.classList.remove('hidden');
                useBtn.classList.add('hidden');
                startCamera();
            });

            useBtn.addEventListener('click', function () {
                if (!activeConfig) {
                    return;
                }

                markStateDone(activeConfig.stateId);
                closeModal();
            });
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.peminjam', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Sentra\resources\views/peminjam/tiket.blade.php ENDPATH**/ ?>