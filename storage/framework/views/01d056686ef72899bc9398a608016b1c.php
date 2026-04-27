

<?php $__env->startSection('page_title', 'Dashboard Admin'); ?>
<?php $__env->startSection('page_subtitle', 'Ringkasan status ruangan, barang, penalti, dan laporan kerusakan'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl">
    <!-- Statistik Cards -->
    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <!-- Total Ruangan -->
        <a href="<?php echo e(route('admin.ruangan')); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Ruangan</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600"><?php echo e($totalRuangan); ?></p>
                </div>
                <div class="text-3xl text-blue-300"><i class="fas fa-door-open"></i></div>
            </div>
        </a>

        <!-- Total Barang -->
        <a href="<?php echo e(route('admin.barang')); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Barang</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600"><?php echo e($totalBarang); ?></p>
                </div>
                <div class="text-3xl text-blue-300"><i class="fas fa-boxes"></i></div>
            </div>
        </a>

        <!-- Total Peminjaman Hari Ini -->
        <a href="<?php echo e(route('admin.laporan')); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Peminjaman Hari Ini</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600"><?php echo e($totalPeminjaman); ?></p>
                </div>
                <div class="text-3xl text-blue-300"><i class="fas fa-calendar-check"></i></div>
            </div>
        </a>

        <!-- Booking Sedang Berlangsung -->
        <a href="<?php echo e(route('admin.laporan')); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Sedang Berlangsung</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-600"><?php echo e($bookingSedangBerlangsung); ?></p>
                </div>
                <div class="text-3xl text-emerald-300"><i class="fas fa-play-circle"></i></div>
            </div>
        </a>

        <!-- Total Pelanggaran -->
        <a href="<?php echo e(route('admin.laporan.rusak')); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Laporan Rusak</p>
                    <p class="mt-2 text-2xl font-bold text-amber-600"><?php echo e($totalLaporanRusak); ?></p>
                </div>
                <div class="text-3xl text-amber-300"><i class="fas fa-triangle-exclamation"></i></div>
            </div>
        </a>

        <a href="<?php echo e(route('admin.user.peminjam')); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Akun Dibatasi</p>
                    <p class="mt-2 text-2xl font-bold text-rose-600"><?php echo e($totalPeminjamDibatasi); ?></p>
                </div>
                <div class="text-3xl text-rose-300"><i class="fas fa-user-lock"></i></div>
            </div>
        </a>
    </div>

    <!-- Alert Section -->
    <?php if($bookingTanpaCheckin > 0): ?>
        <div class="mb-8 bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-red-800 font-semibold"><i class="fas fa-exclamation-triangle mr-2"></i>Ada <?php echo e($bookingTanpaCheckin); ?> booking yang belum check-in!</p>
        </div>
    <?php endif; ?>

    <?php if($bookingPerluVerifikasi > 0): ?>
        <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-blue-800 font-semibold"><i class="fas fa-check-circle mr-2"></i>Ada <?php echo e($bookingPerluVerifikasi); ?> booking yang perlu diverifikasi. <a href="<?php echo e(route('admin.laporan')); ?>" class="font-bold underline">Lihat Laporan →</a></p>
        </div>
    <?php endif; ?>

    <?php if($laporanRusakMenunggu > 0): ?>
        <div class="mb-8 rounded-lg border border-amber-200 bg-amber-50 p-4">
            <p class="font-semibold text-amber-800"><i class="fas fa-triangle-exclamation mr-2"></i>Ada <?php echo e($laporanRusakMenunggu); ?> laporan barang rusak aktif (sedang ditinjau) yang perlu tindak lanjut.</p>
        </div>
    <?php endif; ?>

    <!-- Booking yang Perlu Diverifikasi -->
    <?php if($bookingMenungguVerifikasi->count() > 0): ?>
        <div class="mb-8 bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                <h3 class="text-lg font-semibold text-gray-800"><i class="fas fa-tasks text-blue-600 mr-2"></i>Booking Menunggu Verifikasi</h3>
            </div>
            <div class="divide-y divide-gray-200">
                <?php $__currentLoopData = $bookingMenungguVerifikasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800"><?php echo e($booking->user->name); ?> - <?php echo e($booking->kode_booking); ?></p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <?php if($booking->thing): ?>
                                        <i class="fas fa-cube mr-1 text-blue-600"></i><?php echo e($booking->thing->nama); ?>

                                    <?php elseif($booking->room): ?>
                                        <i class="fas fa-door-open mr-1 text-blue-600"></i><?php echo e($booking->room->nama); ?>

                                    <?php endif; ?>
                                    | Selesai: <?php echo e($booking->waktu_selesai_booking->format('d M - H:i')); ?>

                                </p>
                            </div>
                            <a href="<?php echo e(route('admin.laporan')); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold whitespace-nowrap ml-4">
                                <i class="fas fa-check-circle mr-1"></i>Verifikasi
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white cursor-pointer"
            onclick="if (!event.target.closest('a')) { window.location='<?php echo e(route('admin.laporan')); ?>'; }"
        >
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600">Tiket Hari Ini</h3>
                <a href="<?php echo e(route('admin.laporan')); ?>" class="text-xs font-semibold text-blue-700 hover:text-blue-900">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $tiketHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $statusClass = match ($ticket->status) {
                            'Berlangsung' => 'bg-emerald-100 text-emerald-700',
                            'Selesai' => 'bg-slate-200 text-slate-700',
                            'Dibatalkan' => 'bg-rose-100 text-rose-700',
                            'Pelanggaran' => 'bg-amber-100 text-amber-700',
                            default => 'bg-blue-100 text-blue-700',
                        };
                    ?>
                    <a href="<?php echo e(route('admin.laporan')); ?>" class="block px-5 py-3 text-sm hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-800"><?php echo e($ticket->user->name); ?> · <?php echo e($ticket->kode_booking); ?></p>
                                <p class="mt-1 text-xs text-slate-500">
                                    <?php echo e($ticket->waktu_mulai_booking->format('H:i')); ?> - <?php echo e($ticket->waktu_selesai_booking->format('H:i')); ?> ·
                                    <?php echo e($ticket->thing->nama ?? $ticket->room->nama ?? '-'); ?>

                                </p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold whitespace-nowrap <?php echo e($statusClass); ?>"><?php echo e($ticket->status); ?></span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="px-5 py-4 text-sm text-slate-500">Tidak ada tiket booking untuk hari ini.</p>
                <?php endif; ?>
            </div>
        </section>

        <section
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white cursor-pointer"
            onclick="if (!event.target.closest('a')) { window.location='<?php echo e(route('admin.laporan')); ?>'; }"
        >
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600">Tiket Sedang Berlangsung</h3>
                <a href="<?php echo e(route('admin.laporan')); ?>" class="text-xs font-semibold text-blue-700 hover:text-blue-900">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $tiketBerlangsung; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('admin.laporan')); ?>" class="block px-5 py-3 text-sm hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-800"><?php echo e($ticket->user->name); ?> · <?php echo e($ticket->kode_booking); ?></p>
                                <p class="mt-1 text-xs text-slate-500">
                                    <?php echo e($ticket->waktu_mulai_booking->format('H:i')); ?> - <?php echo e($ticket->waktu_selesai_booking->format('H:i')); ?> ·
                                    <?php echo e($ticket->thing->nama ?? $ticket->room->nama ?? '-'); ?>

                                </p>
                            </div>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 whitespace-nowrap">Berlangsung</span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="px-5 py-4 text-sm text-slate-500">Tidak ada tiket berstatus berlangsung saat ini.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white cursor-pointer"
            onclick="if (!event.target.closest('a')) { window.location='<?php echo e(route('admin.laporan')); ?>'; }"
        >
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600">Pelanggaran Terbaru</h3>
                <a href="<?php echo e(route('admin.laporan')); ?>" class="text-xs font-semibold text-blue-700 hover:text-blue-900">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $pelanggaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('admin.laporan')); ?>" class="block px-5 py-3 text-sm hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-800"><?php echo e($item->user->name ?? '-'); ?> · <?php echo e($item->kode_booking); ?></p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Tidak Check-in · Tidak Check-out · <?php echo e($item->thing->nama ?? $item->room->nama ?? '-'); ?>

                                </p>
                            </div>
                            <span class="rounded-full bg-rose-100 px-2.5 py-1 text-[11px] font-semibold text-rose-700 whitespace-nowrap">Pelanggaran</span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="px-5 py-4 text-sm text-slate-500">Tidak ada data pelanggaran check-in/check-out terbaru.</p>
                <?php endif; ?>
            </div>
        </section>

        <section
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white cursor-pointer"
            onclick="if (!event.target.closest('a')) { window.location='<?php echo e(route('admin.laporan.rusak')); ?>'; }"
        >
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600">Laporan Barang Rusak</h3>
                <a href="<?php echo e(route('admin.laporan.rusak')); ?>" class="text-xs font-semibold text-blue-700 hover:text-blue-900">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $laporanRusakTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $reportStatus = $report->status ?: 'Sedang Ditinjau';
                        $reportStatusClass = match ($reportStatus) {
                            'Selesai Ditangani' => 'bg-emerald-100 text-emerald-700',
                            'Ditolak' => 'bg-rose-100 text-rose-700',
                            default => 'bg-amber-100 text-amber-700',
                        };
                    ?>
                    <a href="<?php echo e(route('admin.laporan.rusak.detail', $report->id)); ?>" class="block px-5 py-3 text-sm hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-800"><?php echo e($report->user->name ?? '-'); ?> · <?php echo e($report->thing->kode_thing ?? '-'); ?></p>
                                <p class="mt-1 text-xs text-slate-500">
                                    <?php echo e($report->thing->nama ?? 'Barang tidak ditemukan'); ?> · <?php echo e($report->created_at->format('d M Y H:i')); ?>

                                </p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold whitespace-nowrap <?php echo e($reportStatusClass); ?>"><?php echo e($reportStatus); ?></span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="px-5 py-4 text-sm text-slate-500">Belum ada laporan barang rusak.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Sentra\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>