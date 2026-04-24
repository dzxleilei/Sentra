

<?php $__env->startSection('page_title', 'Verifikasi Booking'); ?>
<?php $__env->startSection('page_subtitle', 'Daftar booking yang perlu diverifikasi'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <span>Verifikasi Booking</span>
    </div>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-slate-900"><i class="fas fa-tasks text-blue-600 mr-2"></i>Verifikasi Booking</h2>
            <p class="mt-1 text-sm text-slate-500">Booking yang sedang berlangsung dan perlu diverifikasi</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-sm text-slate-500 font-semibold"><i class="fas fa-hourglass-half mr-1 text-blue-600"></i>Menunggu Verifikasi</p>
            <p class="mt-2 text-2xl font-bold text-blue-600"><?php echo e($notifikasi->total()); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-sm text-slate-500 font-semibold"><i class="fas fa-list mr-1 text-blue-600"></i>Halaman Ini</p>
            <p class="mt-2 text-2xl font-bold text-blue-600"><?php echo e($notifikasi->count()); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-sm text-slate-500 font-semibold"><i class="fas fa-building mr-1 text-blue-600"></i>Total Booking</p>
            <p class="mt-2 text-2xl font-bold text-blue-600"><?php echo e($notifikasi->total()); ?></p>
        </div>
    </div>

    <?php if($message = Session::get('success')): ?>
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            <i class="fas fa-check-circle mr-2"></i> <?php echo e($message); ?>

        </div>
    <?php endif; ?>

    <!-- Notifikasi List -->
    <?php $__empty_1 = true; $__currentLoopData = $notifikasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $tipeBadgeClass = $item->tipe === 'Ruangan' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800';
            $statusBadgeClass = $item->status === 'Berlangsung' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800';
        ?>

        <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
            <div class="grid gap-6 md:grid-cols-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Peminjam</p>
                    <p class="mt-1 text-lg font-bold text-slate-900"><?php echo e($item->user->name); ?></p>
                    <p class="text-sm text-slate-600"><?php echo e($item->user->email); ?></p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Aset</p>
                    <p class="mt-1 text-lg font-bold text-slate-900">
                        <?php echo e($item->thing->nama ?? $item->room->nama ?? '-'); ?>

                    </p>
                    <p class="mt-2">
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($tipeBadgeClass); ?>"><?php echo e($item->tipe); ?></span>
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Waktu</p>
                    <p class="mt-1 text-sm text-slate-700">
                        <?php echo e($item->waktu_mulai_booking->format('d M Y')); ?><br>
                        <?php echo e($item->waktu_mulai_booking->format('H:i')); ?> - <?php echo e($item->waktu_selesai_booking->format('H:i')); ?>

                    </p>
                    <p class="mt-2">
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($statusBadgeClass); ?>"><?php echo e($item->status); ?></span>
                    </p>
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <a href="<?php echo e(route('admin.verifikasi.booking', $item->id)); ?>" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                    <i class="fas fa-check-circle mr-1"></i> Verifikasi Sekarang
                </a>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center">
            <p class="text-6xl mb-4"><i class="fas fa-check-circle text-green-600"></i></p>
            <h2 class="mb-2 text-2xl font-bold text-slate-900">Tidak Ada Booking Pending</h2>
            <p class="text-slate-600">Semua booking sudah terverifikasi. Kembali lagi nanti.</p>
        </div>
    <?php endif; ?>

    <div class="mt-8">
        <?php echo e($notifikasi->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Sentra\resources\views/admin/verifikasi/notifikasi.blade.php ENDPATH**/ ?>