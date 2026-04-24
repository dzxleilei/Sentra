

<?php $__env->startSection('page_title', 'Laporan Peminjaman'); ?>
<?php $__env->startSection('page_subtitle', 'Daftar tiket peminjaman per mahasiswa'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl p-2">
    <?php
        $periodeLabel = ($bulan && $tahun)
            ? \Carbon\Carbon::createFromDate((int) $tahun, (int) $bulan, 1)->translatedFormat('F Y')
            : 'Semua Periode';
    ?>

    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <a href="<?php echo e(route('admin.laporan', ['bulan' => $bulan, 'tahun' => $tahun])); ?>" class="hover:text-blue-600">Laporan Peminjaman</a>
        <span>/</span>
        <span>Tiket <?php echo e($user->name); ?></span>
    </div>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-slate-900"><i class="fas fa-ticket text-blue-600 mr-2"></i>Tiket <?php echo e($user->name); ?></h2>
            <p class="mt-1 text-sm text-slate-500">Periode <?php echo e($periodeLabel); ?></p>
        </div>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white" x-data="{ openTicket: null }">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px]">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Kode Tiket</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Jenis</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Waktu</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Detail Barang / Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $groupedTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kodeBooking => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $first = $items->first();
                        ?>
                        <tr class="align-top hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono text-sm text-gray-700"><?php echo e($kodeBooking); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo e($first->tipe); ?></td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                <?php echo e($first->waktu_mulai_booking?->format('d M Y H:i') ?? '-'); ?><br>
                                <?php echo e($first->waktu_selesai_booking?->format('d M Y H:i') ?? '-'); ?>

                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($first->status === 'Selesai' ? 'bg-emerald-100 text-emerald-800' : ($first->status === 'Berlangsung' ? 'bg-blue-100 text-blue-800' : ($first->status === 'Pelanggaran' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800'))); ?>"><?php echo e($first->status); ?></span>
                                    <?php if($first->status === 'Selesai' && $first->catatan_pelanggaran): ?>
                                        <span class="inline-block rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Pelanggaran</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-700">
                                <button type="button" @click="openTicket === '<?php echo e($kodeBooking); ?>' ? openTicket = null : openTicket = '<?php echo e($kodeBooking); ?>'" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100" title="Buka detail tiket" aria-label="Buka detail tiket">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr x-show="openTicket === '<?php echo e($kodeBooking); ?>'" x-cloak>
                            <td colspan="5" class="bg-slate-50 px-6 py-4">
                                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                                    <table class="w-full min-w-[1200px] text-left text-xs text-slate-700">
                                        <thead class="bg-slate-50">
                                            <tr class="border-b border-slate-200 text-slate-500">
                                                <th class="px-4 py-3">Objek</th>
                                                <th class="px-4 py-3">Lokasi</th>
                                                <th class="px-4 py-3">Alasan</th>
                                                <th class="px-4 py-3">Check-in</th>
                                                <th class="px-4 py-3">Check-out</th>
                                                <th class="px-4 py-3">Catatan Pelanggaran</th>
                                                <th class="px-4 py-3">Bukti Awal</th>
                                                <th class="px-4 py-3">Bukti Akhir</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="border-b border-slate-100 align-top">
                                                    <td class="px-4 py-3"><?php echo e($item->room?->nama ?? $item->thing?->nama ?? '-'); ?></td>
                                                    <td class="px-4 py-3"><?php echo e($item->lokasi_penggunaan ?? '-'); ?></td>
                                                    <td class="px-4 py-3"><?php echo e($item->alasan_peminjaman ?? '-'); ?></td>
                                                    <td class="px-4 py-3"><?php echo e($item->waktu_checkin?->format('d M Y H:i') ?? '-'); ?></td>
                                                    <td class="px-4 py-3"><?php echo e($item->waktu_checkout?->format('d M Y H:i') ?? '-'); ?></td>
                                                    <td class="px-4 py-3"><?php echo e($item->catatan_pelanggaran ?? '-'); ?></td>
                                                    <td class="px-4 py-3">
                                                        <?php if($item->foto_awal): ?>
                                                            <a href="<?php echo e(asset('storage/' . $item->foto_awal)); ?>" target="_blank" class="font-semibold text-blue-700 hover:text-blue-900">Lihat</a>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <?php if($item->foto_akhir): ?>
                                                            <a href="<?php echo e(asset('storage/' . $item->foto_akhir)); ?>" target="_blank" class="font-semibold text-blue-700 hover:text-blue-900">Lihat</a>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada tiket untuk periode ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Sentra\resources\views/admin/laporan/tiket-user.blade.php ENDPATH**/ ?>