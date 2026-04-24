<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Peminjam - Sentra Booking'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <?php
        $serverNowIso = \Illuminate\Support\Carbon::now()->toIso8601String();
    ?>

    <div class="mx-auto min-h-screen max-w-md bg-white shadow-sm md:max-w-full">
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur">
            <p class="text-xs uppercase tracking-widest text-blue-600">Sentra Booking</p>
            <div class="mt-1 flex items-center justify-between">
                <h1 class="text-lg font-bold"><?php echo $__env->yieldContent('page_title', 'Peminjam'); ?></h1>
                <a href="<?php echo e(route('peminjam.pengaturan')); ?>" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600">
                    Akun
                </a>
            </div>
            <p class="mt-1 text-xs text-slate-500"><?php echo e(Auth::user()->name); ?></p>
            <p id="header-live-clock" class="mt-1 text-xs font-semibold text-slate-700" data-server-now="<?php echo e($serverNowIso); ?>">Waktu server: --:--:--</p>
        </header>

        <main class="px-4 py-4 pb-24">
            <?php if(session('success')): ?>
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>

        <nav class="fixed bottom-0 left-0 right-0 z-30 mx-auto grid max-w-md grid-cols-4 border-t border-slate-200 bg-white md:max-w-full">
            <a href="<?php echo e(route('peminjam.dashboard')); ?>" class="px-2 py-3 text-center text-xs <?php echo e(request()->routeIs('peminjam.dashboard') ? 'font-bold text-blue-600' : 'text-slate-500'); ?>">
                <i class="fas fa-home block text-base"></i>
                Dashboard
            </a>
            <a href="<?php echo e(route('peminjam.barang')); ?>" class="px-2 py-3 text-center text-xs <?php echo e(request()->routeIs('peminjam.barang') || request()->routeIs('peminjam.ruangan.barang') ? 'font-bold text-blue-600' : 'text-slate-500'); ?>">
                <i class="fas fa-box block text-base"></i>
                Barang
            </a>
            <a href="<?php echo e(route('peminjam.ruangan')); ?>" class="px-2 py-3 text-center text-xs <?php echo e(request()->routeIs('peminjam.ruangan') ? 'font-bold text-blue-600' : 'text-slate-500'); ?>">
                <i class="fas fa-door-open block text-base"></i>
                Ruangan
            </a>
            <a href="<?php echo e(route('peminjam.riwayat')); ?>" class="px-2 py-3 text-center text-xs <?php echo e(request()->routeIs('peminjam.riwayat') || request()->routeIs('peminjam.penalti') || request()->routeIs('peminjam.tiket') ? 'font-bold text-blue-600' : 'text-slate-500'); ?>">
                <i class="fas fa-receipt block text-base"></i>
                Tiket
            </a>
        </nav>
    </div>

    <div id="outside-hours-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 p-4">
        <div class="w-full max-w-sm rounded-2xl bg-white p-5">
            <h3 class="text-base font-bold text-rose-700">Di Luar Jam Peminjaman</h3>
            <p class="mt-2 text-sm text-slate-600">Anda berada di luar jam peminjaman (21:00 - 06:00). Silakan coba lagi dalam:</p>
            <p id="outside-hours-countdown" class="mt-3 text-lg font-bold text-slate-900">--:--:--</p>
            <p class="mt-2 text-xs text-slate-500">Layanan booking aktif kembali pukul 06:00 WIB.</p>
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <script>
        (function () {
            const headerClock = document.getElementById('header-live-clock');

            const modal = document.getElementById('outside-hours-modal');
            const countdown = document.getElementById('outside-hours-countdown');
            if (!modal || !countdown || !headerClock) {
                return;
            }

            const base = new Date('<?php echo e($serverNowIso); ?>').getTime();
            const startClient = Date.now();
            window.sentraServerNowMs = function () {
                return base + (Date.now() - startClient);
            };

            function tick() {
                const nowMs = window.sentraServerNowMs();
                const now = new Date(nowMs);
                const yyyy = now.getFullYear();
                const mmDate = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                const hhNow = String(now.getHours()).padStart(2, '0');
                const mmNow = String(now.getMinutes()).padStart(2, '0');
                const ssNow = String(now.getSeconds()).padStart(2, '0');
                headerClock.textContent = 'Waktu server: ' + yyyy + '-' + mmDate + '-' + dd + ' ' + hhNow + ':' + mmNow + ':' + ssNow;

                const hour = now.getHours();
                const isOutside = hour >= 21 || hour < 6;

                if (!isOutside) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    setTimeout(tick, 1000);
                    return;
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                const target = new Date(nowMs);
                if (hour >= 21) {
                    target.setDate(target.getDate() + 1);
                }
                target.setHours(6, 0, 0, 0);

                const diff = Math.max(0, target.getTime() - now.getTime());
                const totalSeconds = Math.floor(diff / 1000);
                const hh = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
                const mm = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
                const ss = String(totalSeconds % 60).padStart(2, '0');
                countdown.textContent = hh + ':' + mm + ':' + ss;

                setTimeout(tick, 1000);
            }

            tick();
        })();
    </script>
</body>
</html>
<?php /**PATH D:\Projects\Sentra\resources\views/layouts/peminjam.blade.php ENDPATH**/ ?>