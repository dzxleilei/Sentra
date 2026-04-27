

<?php $__env->startSection('page_title', 'Setting'); ?>
<?php $__env->startSection('page_subtitle', 'Kelola password dan batas blokir akun'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <span>Setting</span>
    </div>

    <h2 class="mb-6 text-2xl font-bold text-slate-900">
        <i class="fas fa-gear mr-2 text-blue-600"></i>Setting
    </h2>

    <?php if($errors->any()): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-900 font-semibold mb-2">Error:</p>
            <ul class="text-red-800 text-sm space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>• <?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if($message = Session::get('success')): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
            <i class="fas fa-check-circle mr-2"></i> <?php echo e($message); ?>

        </div>
    <?php endif; ?>

    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-end gap-3">
            <div class="min-w-[220px] flex-1">
                <label for="theme-selector" class="mb-2 block text-sm font-semibold text-gray-700">Tema Tampilan</label>
                <select id="theme-selector" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="light">Light</option>
                    <option value="dark">Dark</option>
                </select>
            </div>
            <button type="button" id="save-theme-btn" class="rounded-lg bg-slate-800 px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-900">
                <i class="fas fa-palette mr-1"></i> Simpan Tema
            </button>
        </div>
    </div>

    <div class="grid items-start gap-6 lg:grid-cols-2">
        <form action="<?php echo e(route('update-password')); ?>" method="POST" class="flex flex-col gap-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <?php echo csrf_field(); ?>

            <div>
                <h3 class="text-lg font-bold text-slate-900">Ganti Password</h3>
                <p class="text-sm text-slate-500">Perbarui password akun Anda secara aman</p>
            </div>

            <div>
                <label for="current_password" class="mb-2 block text-sm font-semibold text-gray-700">Password Saat Ini</label>
                <input type="password" id="current_password" name="current_password" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="new_password" class="mb-2 block text-sm font-semibold text-gray-700">Password Baru</label>
                <input type="password" id="new_password" name="new_password" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="mt-2 text-xs text-slate-500">Minimal 6 karakter</p>
            </div>

            <div>
                <label for="new_password_confirmation" class="mb-2 block text-sm font-semibold text-gray-700">Konfirmasi Password Baru</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <button type="submit" class="w-full rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white transition hover:bg-blue-700">
                <i class="fas fa-save mr-1"></i> Simpan Password
            </button>
        </form>

        <?php if(auth()->user()->role === 'admin'): ?>
            <form action="<?php echo e(route('setting.penalty-threshold')); ?>" method="POST" class="flex flex-col gap-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <?php echo csrf_field(); ?>

                <div>
                    <h3 class="text-lg font-bold text-slate-900">Atur Batas Blokir</h3>
                    <p class="text-sm text-slate-500">Akun peminjam akan terblokir jika poin penalti melebihi batas ini</p>
                </div>

                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                    Batas aktif saat ini: <span class="font-bold"><?php echo e($penaltyBlockThreshold ?? 20); ?></span>
                </div>

                <div>
                    <label for="penalty_block_threshold" class="mb-2 block text-sm font-semibold text-gray-700">Batas Poin Penalti</label>
                    <input type="number" id="penalty_block_threshold" name="penalty_block_threshold" min="1" value="<?php echo e(old('penalty_block_threshold', $penaltyBlockThreshold ?? 20)); ?>" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <?php $__errorArgs = ['penalty_block_threshold'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit" class="w-full rounded-lg bg-emerald-600 px-6 py-2 font-semibold text-white transition hover:bg-emerald-700">
                    <i class="fas fa-save mr-1"></i> Simpan Setting
                </button>
            </form>
        <?php endif; ?>
    </div>

    <div class="mt-8 max-w-5xl rounded-2xl border border-blue-200 bg-blue-50 p-6">
        <h3 class="mb-2 font-semibold text-blue-900"><i class="fas fa-shield-alt mr-2"></i>Catatan</h3>
        <ul class="space-y-2 text-sm text-blue-800">
            <li>• Gunakan password yang kuat dengan kombinasi huruf, angka, dan simbol</li>
            <li>• Jika poin penalti peminjam melewati batas, akun akan ditandai tidak aktif dan terblokir</li>
        </ul>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const selector = document.getElementById('theme-selector');
        const saveBtn = document.getElementById('save-theme-btn');
        if (!selector || !saveBtn || !window.SentraTheme) {
            return;
        }

        selector.value = window.SentraTheme.get();

        saveBtn.addEventListener('click', function () {
            const selected = selector.value === 'dark' ? 'dark' : 'light';
            window.SentraTheme.set(selected);
        });
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Sentra\resources\views/auth/change-password.blade.php ENDPATH**/ ?>