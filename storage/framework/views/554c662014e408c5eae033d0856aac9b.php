

<?php $__env->startSection('title', 'Booking Detail Ruangan'); ?>
<?php $__env->startSection('page_title', 'Booking Ruangan'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-4 rounded-2xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-700">
        <p class="font-semibold">Hari ini (GMT+7)</p>
        <p class="mt-1"><?php echo e($todayLabel); ?></p>
    </div>

    <div class="mb-4 rounded-2xl border border-slate-200 p-4">
        <p class="text-xs text-slate-500"><?php echo e($ruangan->kode_room); ?></p>
        <h2 class="mt-1 font-semibold"><?php echo e($ruangan->nama); ?></h2>
        <p class="text-xs text-slate-500">Lantai: <?php echo e($ruangan->lantai ?? '-'); ?></p>
    </div>

    <section class="mb-4 rounded-2xl border border-slate-200 p-4">
        <h3 class="text-sm font-bold text-slate-800">Daftar Barang di Ruangan</h3>
        <div class="mt-3 space-y-2">
            <?php $__empty_1 = true; $__currentLoopData = $ruangan->things; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="rounded-xl border border-slate-200 px-3 py-2">
                    <p class="text-xs text-slate-500"><?php echo e($item->kode_thing); ?></p>
                    <p class="text-sm font-semibold text-slate-800"><?php echo e($item->nama); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-xs text-slate-500">Ruangan ini tidak memiliki barang terdaftar.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 p-4" x-data="timePicker()">
        <h3 class="text-sm font-bold text-slate-800">Jadwal dan Alasan Peminjaman</h3>

        <form action="<?php echo e(route('room.booking')); ?>" method="POST" class="mt-3 space-y-3" x-data="{ alasan: '<?php echo e(old('alasan_peminjaman', 'Praktikum')); ?>' }">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="room_id" value="<?php echo e($ruangan->id); ?>">

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
                                <button type="button" class="rounded-lg border px-3 py-2 text-sm font-semibold transition" :class="start === time ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-blue-300'" @click="start = time; syncEndOptions()" x-text="time" <?php echo e($bookingBlocked ? 'disabled' : ''); ?>></button>
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
                                <button type="button" class="rounded-lg border px-3 py-2 text-sm font-semibold transition" :class="end === time ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-emerald-300'" @click="end = time" x-text="time" <?php echo e($bookingBlocked ? 'disabled' : ''); ?>></button>
                            </template>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600">Sesi dipilih: <span class="font-semibold" x-text="start && end ? `${start} - ${end}` : 'Belum dipilih'"></span> <span class="ml-2 rounded-full bg-blue-100 px-2 py-1 text-[10px] font-semibold text-blue-800" x-show="start && end">1 sesi</span></p>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Alasan Peminjaman</label>
                <select name="alasan_peminjaman" x-model="alasan" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" <?php echo e($bookingBlocked ? 'disabled' : ''); ?>>
                    <option value="Praktikum">Praktikum</option>
                    <option value="Perkuliahan">Perkuliahan</option>
                    <option value="Kegiatan Organisasi">Kegiatan Organisasi</option>
                    <option value="Riset">Riset</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div x-show="alasan === 'Lainnya'">
                <label class="mb-1 block text-xs font-semibold text-slate-600">Tulis Alasan Lainnya</label>
                <textarea name="alasan_lainnya" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" <?php echo e($bookingBlocked ? 'disabled' : ''); ?>><?php echo e(old('alasan_lainnya')); ?></textarea>
            </div>

            <button type="submit" class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white disabled:opacity-60" <?php echo e($bookingBlocked ? 'disabled' : ''); ?>>
                Konfirmasi Booking Ruangan
            </button>
        </form>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function timePicker() {
            const allOptions = <?php echo json_encode($timeOptions->values(), 15, 512) ?>;
            return {
                allOptions,
                start: <?php echo json_encode(old('jam_mulai', ''), 512) ?>,
                end: <?php echo json_encode(old('jam_selesai', ''), 512) ?>,
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.peminjam', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Sentra\resources\views/peminjam/ruangan/booking.blade.php ENDPATH**/ ?>