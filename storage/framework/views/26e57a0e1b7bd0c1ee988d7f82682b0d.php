

<?php $__env->startSection('title', 'Dashboard Peminjam'); ?>
<?php $__env->startSection('page_title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <?php if($bookingBlocked): ?>
        <section class="mb-4 rounded-2xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900">
            <?php if(!empty(Auth::user()->blocked_at)): ?>
                Akun Anda sedang diblokir oleh admin. Pengajuan peminjaman dinonaktifkan sampai blokir dibuka kembali.
            <?php else: ?>
                Poin penalti Anda sudah mencapai <?php echo e($penaltyPoints); ?>. Pengajuan peminjaman dinonaktifkan sampai akun dibuka kembali oleh admin.
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <section class="grid gap-3">
        <a href="<?php echo e(route('peminjam.riwayat')); ?>" class="group rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-4 text-white transition hover:-translate-y-0.5 hover:shadow-lg">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-wider text-blue-100">Tiket Hari Ini</p>
                    <p class="mt-2 text-2xl font-bold"><?php echo e($bookingHariIni); ?></p>
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

                <form id="quick-qr-form" action="<?php echo e(route('quick-borrow-qr')); ?>" method="POST" class="mt-4 w-full">
                    <?php echo csrf_field(); ?>
                    <input id="quick-qr-code" type="hidden" name="qr_code" value="">
                    <button id="open-quick-qr-scanner" type="button" class="w-full rounded-xl bg-blue-600 px-2 py-2 text-xs font-semibold text-white disabled:opacity-60 sm:text-sm" <?php echo e($bookingBlocked ? 'disabled' : ''); ?>>
                        Scan QR <span class="hidden lg:inline">dan Pinjam</span>
                    </button>
                </form>
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
                    
                </div>

                <form action="<?php echo e(route('borrow.report-damage')); ?>" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-rose-700">ID / Kode / Nama Barang</label>
                        <input type="text" name="thing_input" required value="<?php echo e(old('thing_input')); ?>" class="w-full rounded-xl border border-rose-300 px-3 py-2 text-sm" placeholder="Contoh: 4 atau T004 atau proyektor">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-rose-700">Lokasi Barang</label>
                        <input type="text" name="lokasi_barang" required value="<?php echo e(old('lokasi_barang')); ?>" class="w-full rounded-xl border border-rose-300 px-3 py-2 text-sm" placeholder="Contoh: Lab Komputer Lt.2">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-rose-700">Foto Barang Rusak</label>
                        <input type="file" name="foto_bukti" required accept="image/*" capture="environment" class="w-full rounded-xl border border-rose-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-rose-700">Keterangan (opsional)</label>
                        <textarea name="keterangan" rows="3" class="w-full rounded-xl border border-rose-300 px-3 py-2 text-sm" placeholder="Jelaskan kerusakan singkat"><?php echo e(old('keterangan')); ?></textarea>
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
            <a href="<?php echo e(route('peminjam.riwayat')); ?>" class="text-xs font-semibold text-blue-600">Lihat semua</a>
        </div>

        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $bookingAktif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <article class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs text-slate-500"><?php echo e($ticket->kode_booking); ?></p>
                            <h3 class="mt-1 font-semibold text-slate-900">
                                <?php if($ticket->tipe === 'Ruangan'): ?>
                                    <?php echo e($ticket->room->nama ?? '-'); ?>

                                <?php else: ?>
                                    <?php echo e($ticket->thing->nama ?? '-'); ?>

                                <?php endif; ?>
                            </h3>
                            <p class="mt-1 text-xs text-slate-500"><?php echo e($ticket->waktu_mulai_booking->format('H:i')); ?> - <?php echo e($ticket->waktu_selesai_booking->format('H:i')); ?></p>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($ticket->status === 'Booking' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'); ?>">
                            <?php echo e($ticket->status); ?>

                        </span>
                    </div>

                    <div class="mt-3 flex gap-2">
                        <a href="<?php echo e(route('peminjam.tiket', $ticket->id)); ?>" class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-center text-xs font-semibold text-slate-700">Buka Tiket</a>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="rounded-2xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">
                    Belum ada tiket aktif. Silakan booking barang atau ruangan terlebih dahulu.
                </div>
            <?php endif; ?>
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

    <?php if($showBlockedModal && $bookingBlocked): ?>
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
    <?php endif; ?>

    <div id="quick-qr-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Scan QR Barang</h3>
                    <p class="mt-1 text-xs text-slate-500">Arahkan kamera ke QR ID barang untuk pinjam cepat.</p>
                </div>
                <button id="quick-qr-close" type="button" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-semibold text-slate-700">Tutup</button>
            </div>
            <div id="quick-qr-reader" class="mt-3 min-h-64 overflow-hidden rounded-lg border border-slate-200 bg-slate-50"></div>
            <p id="quick-qr-error" class="mt-2 text-xs font-semibold text-rose-700"></p>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.peminjam', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Sentra\resources\views/peminjam/dashboard.blade.php ENDPATH**/ ?>