

<?php $__env->startSection('page_title', 'Manajemen Barang'); ?>
<?php $__env->startSection('page_subtitle', 'Kelola status barang dan ketersediaan sarana'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <span>Manajemen Barang</span>
    </div>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-slate-900"><i class="fas fa-boxes-stacked text-blue-600 mr-2"></i>Manajemen Barang</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola daftar barang dan status ketersediaan</p>
        </div>
        <a href="<?php echo e(route('admin.barang.tambah')); ?>" class="rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700">
            <i class="fas fa-plus mr-1"></i> Tambah Barang
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
            <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <form method="GET" class="mb-4 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-4">
        <div class="md:col-span-2">
            <label for="q" class="mb-1 block text-xs font-semibold text-slate-600">Cari Barang</label>
            <input type="text" id="q" name="q" value="<?php echo e($search ?? ''); ?>" placeholder="Cari nama, kode, atau ruangan" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="status" class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
            <select id="status" name="status" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="">Semua status</option>
                <option value="Tersedia" <?php echo e(($statusFilter ?? '') === 'Tersedia' ? 'selected' : ''); ?>>Tersedia</option>
                <option value="Dipinjam" <?php echo e(($statusFilter ?? '') === 'Dipinjam' ? 'selected' : ''); ?>>Dipinjam</option>
                <option value="Tidak Tersedia" <?php echo e(($statusFilter ?? '') === 'Tidak Tersedia' ? 'selected' : ''); ?>>Tidak Tersedia</option>
            </select>
        </div>
        <div>
            <label for="room_id" class="mb-1 block text-xs font-semibold text-slate-600">Ruangan</label>
            <select id="room_id" name="room_id" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="">Semua ruangan</option>
                <?php $__currentLoopData = $ruanganFilter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($room->id); ?>" <?php echo e((string) ($roomFilter ?? '') === (string) $room->id ? 'selected' : ''); ?>><?php echo e($room->nama); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label for="sort_by" class="mb-1 block text-xs font-semibold text-slate-600">Urutkan Berdasarkan</label>
            <select id="sort_by" name="sort_by" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="kode_thing" <?php echo e(($sortBy ?? 'kode_thing') === 'kode_thing' ? 'selected' : ''); ?>>Kode Barang</option>
                <option value="nama" <?php echo e(($sortBy ?? 'kode_thing') === 'nama' ? 'selected' : ''); ?>>Nama Barang</option>
                <option value="status" <?php echo e(($sortBy ?? 'kode_thing') === 'status' ? 'selected' : ''); ?>>Status</option>
            </select>
        </div>
        <div>
            <label for="sort_order" class="mb-1 block text-xs font-semibold text-slate-600">Urutan</label>
            <select id="sort_order" name="sort_order" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="asc" <?php echo e(($sortOrder ?? 'asc') === 'asc' ? 'selected' : ''); ?>>Ascending (↑)</option>
                <option value="desc" <?php echo e(($sortOrder ?? 'asc') === 'desc' ? 'selected' : ''); ?>>Descending (↓)</option>
            </select>
        </div>
        <div class="md:col-span-6 flex gap-2">
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">
                <i class="fas fa-filter mr-1"></i> Terapkan
            </button>
            <a href="<?php echo e(route('admin.barang')); ?>" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
        <table class="w-full min-w-[960px]">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Kode Barang</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Nama Barang</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Lokasi</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php $__empty_1 = true; $__currentLoopData = $barang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3.5 text-sm font-mono text-slate-700"><?php echo e($item->kode_thing); ?></td>
                        <td class="px-6 py-3.5 text-sm text-slate-700"><?php echo e($item->nama); ?></td>
                        <td class="px-6 py-3.5 text-sm text-slate-600">
                            <?php echo e($item->room?->nama ?? '-'); ?>

                        </td>
                        <td class="px-6 py-3.5 text-sm">
                            <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold
                                <?php echo e($item->status === 'Tersedia' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'Tidak Tersedia' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800')); ?>">
                                <?php echo e($item->status); ?>

                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-sm whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('admin.barang.edit', $item->id)); ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100" title="Edit <?php echo e($item->nama); ?>" aria-label="Edit <?php echo e($item->nama); ?>">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                                <button
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-100"
                                    onclick="openDeleteModal('<?php echo e(route('admin.barang.hapus', $item->id)); ?>', 'barang <?php echo e($item->nama); ?>')"
                                    title="Hapus <?php echo e($item->nama); ?>"
                                    aria-label="Hapus <?php echo e($item->nama); ?>"
                                >
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">
                            <i class="fas fa-box-open mr-2"></i>Belum ada data barang
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6 flex justify-center">
        <?php echo e($barang->links()); ?>

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
</div>

<script>
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
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Sentra\resources\views/admin/sarana/barang/daftar.blade.php ENDPATH**/ ?>