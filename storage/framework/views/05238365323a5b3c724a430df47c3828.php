

<?php
    $isPeminjamPage = ($pageType ?? 'peminjam') === 'peminjam';
?>

<?php $__env->startSection('page_title', $isPeminjamPage ? 'User Peminjam' : 'User Admin'); ?>
<?php $__env->startSection('page_subtitle', $isPeminjamPage ? 'Kelola akun mahasiswa peminjam' : 'Kelola akun internal sistem'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <span><?php echo e($isPeminjamPage ? 'User Peminjam' : 'User Admin'); ?></span>
    </div>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-slate-900"><i class="fas <?php echo e($isPeminjamPage ? 'fa-user-graduate' : 'fa-user-shield'); ?> text-blue-600 mr-2"></i><?php echo e($isPeminjamPage ? 'User Peminjam' : 'User Admin'); ?></h2>
            <p class="mt-1 text-sm text-slate-500">Kelola user dan role di sistem</p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.user.tambah', ['scope' => $isPeminjamPage ? 'peminjam' : 'staff'])); ?>" class="rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700">
                <i class="fas fa-plus mr-1"></i> Tambah User
            </a>
            <a href="<?php echo e(route('admin.user.import', ['scope' => $isPeminjamPage ? 'peminjam' : 'staff'])); ?>" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 transition hover:bg-slate-100">
                <i class="fas fa-upload mr-1"></i> Import CSV
            </a>
        </div>
    </div>

    <?php if($message = Session::get('success')): ?>
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
            <i class="fas fa-check-circle mr-2"></i> <?php echo e($message); ?>

        </div>
    <?php endif; ?>

    <form method="GET" class="mb-4 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-3">
        <div>
            <label for="sort_by" class="mb-1 block text-xs font-semibold text-slate-600">Urutkan Berdasarkan</label>
            <select id="sort_by" name="sort_by" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <?php if($isPeminjamPage): ?>
                    <option value="name" <?php echo e(($sortBy ?? 'name') === 'name' ? 'selected' : ''); ?>>Nama</option>
                    <option value="email" <?php echo e(($sortBy ?? 'name') === 'email' ? 'selected' : ''); ?>>Email</option>
                    <option value="created_at" <?php echo e(($sortBy ?? 'name') === 'created_at' ? 'selected' : ''); ?>>Dibuat</option>
                    <option value="penalty_points" <?php echo e(($sortBy ?? 'name') === 'penalty_points' ? 'selected' : ''); ?>>Penalti</option>
                <?php else: ?>
                    <option value="name" <?php echo e(($sortBy ?? 'name') === 'name' ? 'selected' : ''); ?>>Nama</option>
                    <option value="email" <?php echo e(($sortBy ?? 'name') === 'email' ? 'selected' : ''); ?>>Email</option>
                    <option value="role" <?php echo e(($sortBy ?? 'name') === 'role' ? 'selected' : ''); ?>>Role</option>
                    <option value="created_at" <?php echo e(($sortBy ?? 'name') === 'created_at' ? 'selected' : ''); ?>>Dibuat</option>
                <?php endif; ?>
            </select>
        </div>
        <div>
            <label for="sort_order" class="mb-1 block text-xs font-semibold text-slate-600">Urutan</label>
            <select id="sort_order" name="sort_order" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="asc" <?php echo e(($sortOrder ?? 'asc') === 'asc' ? 'selected' : ''); ?>>Ascending (↑)</option>
                <option value="desc" <?php echo e(($sortOrder ?? 'asc') === 'desc' ? 'selected' : ''); ?>>Descending (↓)</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">
                <i class="fas fa-sort mr-1"></i> Urutkan
            </button>
        </div>
    </form>

    <?php
        $threshold = $penaltyBlockThreshold ?? 20;
    ?>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1080px]">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Nama</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Email</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Role</th>
                        <?php if($isPeminjamPage): ?>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Penalti</th>
                        <?php endif; ?>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Dibuat</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isBlockedByPenalty = $isPeminjamPage && (int) $user->penalty_points > $threshold;
                            $isBlocked = $isPeminjamPage && ($user->blocked_at || $isBlockedByPenalty);
                            $roleBadgeClass = match ($user->role) {
                                'admin' => 'bg-rose-100 text-rose-800',
                                default => 'bg-blue-100 text-blue-800',
                            };

                            $accountStatusClass = $isBlocked ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800';
                            $penaltyBadgeClass = (int) $user->penalty_points === 0
                                ? 'bg-emerald-100 text-emerald-800'
                                : ((int) $user->penalty_points > $threshold || $user->blocked_at ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800');
                        ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3.5 text-sm font-semibold text-slate-800"><?php echo e($user->name); ?></td>
                            <td class="px-6 py-3.5 text-sm text-slate-600"><?php echo e($user->email); ?></td>
                            <td class="px-6 py-3.5 text-sm">
                                <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold <?php echo e($roleBadgeClass); ?>"><?php echo e(ucfirst($user->role)); ?></span>
                            </td>
                            <?php if($isPeminjamPage): ?>
                                <td class="px-6 py-3.5 text-sm">
                                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold <?php echo e($accountStatusClass); ?>">
                                        <?php echo e($isBlocked ? 'Tidak Aktif' : 'Aktif'); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-sm">
                                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold <?php echo e($penaltyBadgeClass); ?>"><?php echo e((int) $user->penalty_points); ?> poin</span>
                                </td>
                            <?php endif; ?>
                            <td class="px-6 py-3.5 text-sm text-slate-600"><?php echo e($user->created_at->format('d M Y H:i')); ?></td>
                            <td class="px-6 py-3.5 <?php echo e($isPeminjamPage ? '' : 'whitespace-nowrap'); ?>">
                                <?php if($isPeminjamPage): ?>
                                    <button type="button" onclick="toggleBorrowerActionRow(<?php echo e($user->id); ?>)" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100" title="Lihat detail & aksi <?php echo e($user->name); ?>" aria-label="Lihat detail & aksi <?php echo e($user->name); ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                <?php else: ?>
                                    <a href="<?php echo e(route('admin.user.edit', ['id' => $user->id, 'scope' => 'staff'])); ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100" title="Edit <?php echo e($user->name); ?>" aria-label="Edit <?php echo e($user->name); ?>">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if($isPeminjamPage): ?>
                            <tr id="borrower-action-row-<?php echo e($user->id); ?>" class="hidden">
                                <td colspan="7" class="bg-slate-50 px-6 py-5">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-sm font-bold text-slate-900">Detail Pengguna</h3>
                                                <p class="text-xs text-slate-500"><?php echo e($user->name); ?> · <?php echo e($user->email); ?></p>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <a href="<?php echo e(route('admin.user.edit', ['id' => $user->id, 'scope' => 'peminjam'])); ?>" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                                                    <i class="fas fa-user-pen mr-1"></i> Edit User
                                                </a>
                                                <?php if($isBlocked): ?>
                                                    <button type="button" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700" onclick="openStatusModal('<?php echo e(route('admin.user.buka-blokir', $user->id)); ?>', 'Buka blokir peminjam', 'Buka blokir untuk <?php echo e($user->name); ?>?', 'Buka Blokir', 'bg-emerald-600 hover:bg-emerald-700')">
                                                        <i class="fas fa-unlock mr-1"></i> Buka Blokir
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" class="rounded-lg bg-amber-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-amber-700" onclick="openStatusModal('<?php echo e(route('admin.user.blokir', $user->id)); ?>', 'Blokir peminjam', 'Blokir <?php echo e($user->name); ?> sekarang?', 'Blokir', 'bg-amber-600 hover:bg-amber-700')">
                                                        <i class="fas fa-lock mr-1"></i> Blokir
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                                            <div class="rounded-xl border border-slate-200 bg-white p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Rincian Penalti</p>
                                                <p class="mt-2 text-3xl font-bold text-slate-900"><?php echo e((int) $user->penalty_points); ?></p>
                                                <p class="mt-1 text-xs text-slate-500">Batas blokir aktif: <?php echo e($threshold); ?> poin</p>
                                            </div>
                                            <div class="rounded-xl border border-slate-200 bg-white p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Perubahan Penalti</p>
                                                <form id="penalty-form-<?php echo e($user->id); ?>" method="POST" class="mt-3 space-y-2">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="user_id" value="<?php echo e($user->id); ?>">
                                                    <select name="penalty_action" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" onchange="setPenaltyFormAction(<?php echo e($user->id); ?>)" required>
                                                        <option value="">-- Pilih Aksi --</option>
                                                        <option value="add">Tambah Penalti</option>
                                                        <option value="subtract">Kurangi Penalti</option>
                                                    </select>
                                                    <input type="number" name="points" min="1" value="1" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Sebanyak berapa poin" required>
                                                    <input type="text" name="reason" placeholder="Alasan perubahan" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                                                    <button id="penalty-submit-<?php echo e($user->id); ?>" type="submit" class="w-full rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700" disabled>Simpan Penalti</button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4">
                                            <div class="flex items-center justify-between gap-2">
                                                <div>
                                                    <h3 class="text-sm font-bold text-slate-900">Riwayat Penalti</h3>
                                                    <p class="text-sm text-slate-500">10 perubahan terakhir</p>
                                                </div>
                                            </div>
                                            <div class="mt-3 space-y-2">
                                                <?php $__empty_2 = true; $__currentLoopData = $user->penaltyLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                    <div class="rounded-xl border border-slate-200 px-3 py-2">
                                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                                            <div>
                                                                <p class="text-sm font-semibold text-slate-900">
                                                                    <?php echo e(match ($log->action) {
                                                                        'add' => 'Tambah',
                                                                        'subtract' => 'Kurangi',
                                                                        'set' => 'Set',
                                                                        'reset' => 'Reset',
                                                                        default => ucfirst($log->action),
                                                                    }); ?> <?php echo e($log->points); ?> poin
                                                                </p>
                                                                <p class="text-xs text-slate-500"><?php echo e($log->created_at?->format('d M Y H:i')); ?> · oleh <?php echo e($log->admin?->name ?? '-'); ?></p>
                                                            </div>
                                                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($log->action === 'add' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800'); ?>">
                                                                <?php echo e(match ($log->action) {
                                                                    'add' => 'Tambah',
                                                                    'subtract' => 'Kurangi',
                                                                    'set' => 'Set',
                                                                    'reset' => 'Reset',
                                                                    default => ucfirst($log->action),
                                                                }); ?>

                                                            </span>
                                                        </div>
                                                        <p class="mt-1 text-xs text-slate-600"><?php echo e($log->reason); ?></p>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                    <p class="text-sm text-slate-500">Belum ada riwayat penalti.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($isPeminjamPage ? 7 : 5); ?>" class="px-6 py-8 text-center text-sm text-slate-500">
                                <i class="fas fa-users mr-2"></i> Belum ada user terdaftar
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex justify-center">
        <?php echo e($users->links()); ?>

    </div>

    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4">
        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-xl">
            <h3 class="text-lg font-bold text-slate-900">Konfirmasi Hapus</h3>
            <p id="delete-modal-text" class="mt-2 text-sm text-slate-600">Data akan dihapus permanen.</p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeDeleteModal()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Batal</button>
                <form id="delete-modal-form" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <div id="status-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4">
        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-xl">
            <h3 id="status-modal-title" class="text-lg font-bold text-slate-900"></h3>
            <p id="status-modal-text" class="mt-2 text-sm text-slate-600"></p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeStatusModal()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Batal</button>
                <form id="status-modal-form" method="POST">
                    <?php echo csrf_field(); ?>
                    <button id="status-modal-button" type="submit" class="rounded-lg px-4 py-2 text-sm font-semibold text-white"></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openStatusModal(action, title, text, button, buttonClass) {
    const modal = document.getElementById('status-modal');
    document.getElementById('status-modal-title').textContent = title;
    document.getElementById('status-modal-text').textContent = text;
    document.getElementById('status-modal-button').textContent = button;
    document.getElementById('status-modal-button').className = `rounded-lg px-4 py-2 text-sm font-semibold text-white ${buttonClass}`;
    document.getElementById('status-modal-form').action = action;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeStatusModal() {
    const modal = document.getElementById('status-modal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

function openDeleteModal(action, label) {
    const modal = document.getElementById('delete-modal');
    document.getElementById('delete-modal-form').action = action;
    document.getElementById('delete-modal-text').textContent = `Yakin ingin menghapus ${label}?`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

function toggleBorrowerActionRow(userId) {
    const row = document.getElementById(`borrower-action-row-${userId}`);
    if (!row) {
        return;
    }

    const currentlyHidden = row.classList.contains('hidden');

    document.querySelectorAll('[id^="borrower-action-row-"]').forEach((item) => {
        item.classList.add('hidden');
    });

    if (currentlyHidden) {
        row.classList.remove('hidden');
    }
}

function setPenaltyFormAction(userId) {
    const form = document.getElementById(`penalty-form-${userId}`);
    const submitButton = document.getElementById(`penalty-submit-${userId}`);
    const action = form?.querySelector('select[name="penalty_action"]')?.value;

    if (!form || !submitButton) {
        return;
    }

    if (action === 'add') {
        form.action = `<?php echo e(url('/admin/user')); ?>/${userId}/penalty/add`;
        submitButton.textContent = 'Tambah Penalti';
        submitButton.className = 'w-full rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700';
        submitButton.disabled = false;
        return;
    }

    if (action === 'subtract') {
        form.action = `<?php echo e(url('/admin/user')); ?>/${userId}/penalty/subtract`;
        submitButton.textContent = 'Kurangi Penalti';
        submitButton.className = 'w-full rounded-lg bg-amber-600 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-700';
        submitButton.disabled = false;
        return;
    }

    form.removeAttribute('action');
    submitButton.textContent = 'Simpan Penalti';
    submitButton.className = 'w-full rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700';
    submitButton.disabled = true;
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Sentra\resources\views/admin/user/daftar.blade.php ENDPATH**/ ?>