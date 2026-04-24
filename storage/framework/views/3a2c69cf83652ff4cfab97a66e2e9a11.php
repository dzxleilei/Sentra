

<?php $__env->startSection('title', 'Riwayat Peminjaman'); ?>
<?php $__env->startSection('page_title', 'Riwayat'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $ticketBadge = [
            'Selesai' => 'bg-emerald-100 text-emerald-800',
            'Berlangsung' => 'bg-blue-100 text-blue-800',
            'Pelanggaran' => 'bg-rose-100 text-rose-800',
            'Dibatalkan' => 'bg-slate-200 text-slate-700',
            'Booking' => 'bg-amber-100 text-amber-800',
        ];

        $checkinBadge = [
            'Tidak Check-in' => 'bg-rose-100 text-rose-800',
        ];

        $checkoutBadge = [
            'Tidak Check-out' => 'bg-rose-100 text-rose-800',
        ];
    ?>

    <div class="space-y-3">
        <?php $__empty_1 = true; $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="rounded-2xl border border-slate-200 p-4">
                <div class="flex items-stretch justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-slate-500"><?php echo e($item->kode_booking); ?></p>
                        <h3 class="mt-1 font-semibold">
                            <?php if($item->tipe === 'Ruangan'): ?>
                                <?php echo e($item->room->nama ?? '-'); ?>

                            <?php else: ?>
                                <?php echo e($item->thing->nama ?? '-'); ?>

                            <?php endif; ?>
                        </h3>
                        <p class="mt-1 text-xs text-slate-500"><?php echo e($item->waktu_mulai_booking->format('d M Y H:i')); ?> - <?php echo e($item->waktu_selesai_booking->format('H:i')); ?></p>

                        <div class="mt-3">
                            <p class="mb-2 text-xs text-slate-500">
                                Check-in: <?php echo e($item->waktu_checkin ? $item->waktu_checkin->format('d M Y H:i') : '-'); ?>

                                <br>
                                Check-out: <?php echo e($item->waktu_checkout ? $item->waktu_checkout->format('d M Y H:i') : '-'); ?>

                            </p>
                            <?php if($item->status !== 'Selesai'): ?>
                                <div class="mb-2 flex flex-wrap gap-2">
                                    <?php if($item->status_checkin): ?>
                                        <span class="rounded-full px-2 py-1 text-xs font-semibold <?php echo e($checkinBadge[$item->status_checkin] ?? 'bg-slate-100 text-slate-700'); ?>"><?php echo e($item->status_checkin); ?></span>
                                    <?php endif; ?>
                                    <?php if($item->status_checkout): ?>
                                        <span class="rounded-full px-2 py-1 text-xs font-semibold <?php echo e($checkoutBadge[$item->status_checkout] ?? 'bg-slate-100 text-slate-700'); ?>"><?php echo e($item->status_checkout); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-col items-end">
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($ticketBadge[$item->status] ?? 'bg-slate-100 text-slate-700'); ?>"><?php echo e($item->status); ?></span>
                        <a href="<?php echo e(route('peminjam.tiket', $item->id)); ?>" class="mt-auto inline-flex items-center rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700">Buka Detail Tiket</a>
                    </div>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-2xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">Belum ada riwayat peminjaman.</div>
        <?php endif; ?>
    </div>

    <?php if($riwayat->hasPages()): ?>
        <div class="mt-4"><?php echo e($riwayat->links()); ?></div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.peminjam', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Sentra\resources\views/peminjam/riwayat.blade.php ENDPATH**/ ?>