<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Peminjam - Sentra Booking'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        (function () {
            try {
                var savedTheme = localStorage.getItem('sentra-theme') || 'light';
                document.documentElement.setAttribute('data-theme', savedTheme);
            } catch (error) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    <style>[x-cloak]{display:none !important;}</style>
    <style>
        html[data-theme="dark"] body {
            background-color: #0b1220;
            color: #e2e8f0;
        }

        html[data-theme="dark"] .bg-white {
            background-color: #111827 !important;
        }

        html[data-theme="dark"] .bg-slate-50,
        html[data-theme="dark"] .bg-gray-50 {
            background-color: #0f172a !important;
        }

        html[data-theme="dark"] .border-slate-200,
        html[data-theme="dark"] .border-gray-200,
        html[data-theme="dark"] .border-slate-300 {
            border-color: #334155 !important;
        }

        html[data-theme="dark"] .text-slate-900,
        html[data-theme="dark"] .text-gray-900,
        html[data-theme="dark"] .text-gray-800,
        html[data-theme="dark"] .text-slate-800 {
            color: #f1f5f9 !important;
        }

        html[data-theme="dark"] .text-slate-700,
        html[data-theme="dark"] .text-gray-700,
        html[data-theme="dark"] .text-slate-600,
        html[data-theme="dark"] .text-gray-600,
        html[data-theme="dark"] .text-slate-500,
        html[data-theme="dark"] .text-gray-500 {
            color: #cbd5e1 !important;
        }

        html[data-theme="dark"] input,
        html[data-theme="dark"] select,
        html[data-theme="dark"] textarea {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }
    </style>
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

    <script>
        window.SentraTheme = {
            get: function () {
                try {
                    return localStorage.getItem('sentra-theme') || 'light';
                } catch (error) {
                    return 'light';
                }
            },
            set: function (theme) {
                var safeTheme = theme === 'dark' ? 'dark' : 'light';
                try {
                    localStorage.setItem('sentra-theme', safeTheme);
                } catch (error) {
                    // Ignore storage error, but still apply theme for current session.
                }
                document.documentElement.setAttribute('data-theme', safeTheme);
                return safeTheme;
            }
        };

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

    <script>
        (function setupDamageReportImageCompression() {
            const targetActionSuffix = '/report-damage';
            const maxUploadBytes = 1800 * 1024;
            const maxWidth = 1600;

            function blobToImage(blob) {
                return new Promise(function (resolve, reject) {
                    const url = URL.createObjectURL(blob);
                    const image = new Image();
                    image.onload = function () {
                        URL.revokeObjectURL(url);
                        resolve(image);
                    };
                    image.onerror = function () {
                        URL.revokeObjectURL(url);
                        reject(new Error('Gagal memuat gambar.'));
                    };
                    image.src = url;
                });
            }

            async function compressImageFile(file) {
                const image = await blobToImage(file);
                const scale = image.width > maxWidth ? (maxWidth / image.width) : 1;
                const width = Math.max(1, Math.round(image.width * scale));
                const height = Math.max(1, Math.round(image.height * scale));

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const context = canvas.getContext('2d');
                if (!context) {
                    throw new Error('Browser tidak mendukung kompresi gambar.');
                }

                context.drawImage(image, 0, 0, width, height);

                let quality = 0.85;
                let resultBlob = null;

                while (quality >= 0.4) {
                    // eslint-disable-next-line no-await-in-loop
                    resultBlob = await new Promise(function (resolve) {
                        canvas.toBlob(function (blob) {
                            resolve(blob);
                        }, 'image/jpeg', quality);
                    });

                    if (!resultBlob) {
                        break;
                    }

                    if (resultBlob.size <= maxUploadBytes) {
                        break;
                    }

                    quality -= 0.1;
                }

                if (!resultBlob) {
                    throw new Error('Gagal mengompres gambar.');
                }

                return new File([
                    resultBlob
                ], 'foto-bukti.jpg', {
                    type: 'image/jpeg',
                    lastModified: Date.now(),
                });
            }

            function shouldHandleForm(form) {
                const action = (form.getAttribute('action') || '').trim();
                return action.endsWith(targetActionSuffix) || action.includes(targetActionSuffix + '?');
            }

            document.addEventListener('submit', async function (event) {
                const form = event.target;
                if (!(form instanceof HTMLFormElement) || !shouldHandleForm(form)) {
                    return;
                }

                if (form.dataset.damageCompressing === '1') {
                    return;
                }

                const input = form.querySelector('input[type="file"][name="foto_bukti"]');
                if (!input || !input.files || input.files.length === 0) {
                    return;
                }

                const originalFile = input.files[0];
                if (!originalFile || originalFile.size <= maxUploadBytes) {
                    return;
                }

                event.preventDefault();
                form.dataset.damageCompressing = '1';

                const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                submitButtons.forEach(function (button) {
                    button.setAttribute('disabled', 'disabled');
                });

                try {
                    const compressedFile = await compressImageFile(originalFile);
                    const transfer = new DataTransfer();
                    transfer.items.add(compressedFile);
                    input.files = transfer.files;

                    form.submit();
                } catch (error) {
                    form.dataset.damageCompressing = '0';
                    submitButtons.forEach(function (button) {
                        button.removeAttribute('disabled');
                    });

                    alert('Foto terlalu besar dan gagal dikompres otomatis. Silakan ambil ulang foto dengan resolusi lebih rendah.');
                }
            }, true);
        })();
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\Projects\Sentra\resources\views/layouts/peminjam.blade.php ENDPATH**/ ?>