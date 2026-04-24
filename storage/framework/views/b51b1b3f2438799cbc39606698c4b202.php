

<?php $__env->startSection('page_title', $barang ? 'Edit Barang' : 'Tambah Barang'); ?>
<?php $__env->startSection('page_subtitle', 'Atur data barang, lokasi, dan status ketersediaan'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <a href="<?php echo e(route('admin.barang')); ?>" class="hover:text-blue-600">Manajemen Barang</a>
        <span>/</span>
        <span><?php echo e($barang ? 'Edit Barang' : 'Tambah Barang'); ?></span>
    </div>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900">
            <i class="fas fa-boxes-stacked text-blue-600 mr-2"></i><?php echo e($barang ? 'Edit Barang' : 'Tambah Barang Baru'); ?>

        </h2>
        <p class="mt-1 text-sm text-slate-500">
            <?php echo e($barang ? 'Perbarui informasi barang' : 'Tambahkan barang baru ke sistem'); ?>

        </p>
    </div>

    <div class="max-w-5xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST">
            <?php echo csrf_field(); ?>

            <!-- Kode Barang -->
            <div class="mb-6">
                <label for="kode_thing" class="mb-2 block text-sm font-semibold text-slate-700">
                    Kode Barang <span class="text-red-600">*</span>
                </label>
                <input type="text" id="kode_thing" name="kode_thing" 
                    value="<?php echo e(old('kode_thing', $barang?->kode_thing)); ?>"
                    placeholder="Contoh: B001"
                    <?php echo e($barang ? 'readonly' : ''); ?>

                    class="w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo e($errors->has('kode_thing') ? 'border-red-500' : 'border-slate-300'); ?>"
                    required>
                <?php $__errorArgs = ['kode_thing'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Nama Barang -->
            <div class="mb-6">
                <label for="nama" class="mb-2 block text-sm font-semibold text-slate-700">
                    Nama Barang <span class="text-red-600">*</span>
                </label>
                <input type="text" id="nama" name="nama" 
                    value="<?php echo e(old('nama', $barang?->nama)); ?>"
                    placeholder="Contoh: Proyektor Epson EB-2140W"
                    class="w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo e($errors->has('nama') ? 'border-red-500' : 'border-slate-300'); ?>"
                    required>
                <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Lokasi Ruangan -->
            <div class="mb-6">
                <label for="room_id" class="mb-2 block text-sm font-semibold text-slate-700">
                    Lokasi Ruangan (Opsional)
                </label>
                <select id="room_id" name="room_id" 
                    class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Tidak terkait ruangan --</option>
                    <?php $__currentLoopData = $ruangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($room->id); ?>" 
                            <?php echo e(old('room_id', $barang?->room_id) == $room->id ? 'selected' : ''); ?>>
                            <?php echo e($room->nama); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">
                    Status <span class="text-red-600">*</span>
                </label>
                <select id="status" name="status" 
                    class="w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo e($errors->has('status') ? 'border-red-500' : 'border-slate-300'); ?>"
                    required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Tersedia" <?php echo e(old('status', $barang?->status) === 'Tersedia' ? 'selected' : ''); ?>>Tersedia</option>
                    <option value="Dipinjam" <?php echo e(old('status', $barang?->status) === 'Dipinjam' ? 'selected' : ''); ?>>Dipinjam</option>
                    <option value="Terpinjam Otomatis" <?php echo e(old('status', $barang?->status) === 'Terpinjam Otomatis' ? 'selected' : ''); ?>>Terpinjam Otomatis</option>
                    <option value="Rusak" <?php echo e(old('status', $barang?->status) === 'Rusak' ? 'selected' : ''); ?>>Rusak / Perlu Perbaikan</option>
                    <option value="Tidak Tersedia" <?php echo e(old('status', $barang?->status) === 'Tidak Tersedia' ? 'selected' : ''); ?>>Tidak Tersedia</option>
                </select>
                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 mt-8">
                        <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white transition hover:bg-blue-700">
                    <i class="fas fa-save mr-1"></i> <?php echo e($barang ? 'Perbarui' : 'Simpan'); ?>

                </button>
                        <a href="<?php echo e(route('admin.barang')); ?>" class="rounded-lg border border-slate-300 bg-white px-6 py-2 font-semibold text-slate-700 transition hover:bg-slate-100">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Sentra\resources\views/admin/sarana/barang/form.blade.php ENDPATH**/ ?>