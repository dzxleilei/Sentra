<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sentra Booking</title>
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

        html[data-theme="dark"] .hover\:bg-slate-100:hover,
        html[data-theme="dark"] .hover\:bg-gray-50:hover,
        html[data-theme="dark"] .hover\:bg-blue-100:hover,
        html[data-theme="dark"] .hover\:bg-rose-100:hover {
            background-color: #1e293b !important;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden bg-slate-50 text-slate-900">
        <!-- Sidebar -->
        <aside class="h-screen w-72 overflow-y-auto border-r border-slate-200 bg-white p-6">
            <div class="mb-8">
                <p class="text-xs uppercase tracking-widest text-blue-600">Sentra Booking</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">Admin Panel</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola sarana dan operasional</p>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-chart-line mr-2"></i> Dashboard
                </a>
                <div class="my-4 border-t border-slate-200"></div>
                <p class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Kelola Sarana</p>
                <a href="{{ route('admin.barang') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.barang*') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-boxes mr-2"></i> Manajemen Barang
                </a>
                <a href="{{ route('admin.ruangan') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.ruangan*') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-door-open mr-2"></i> Manajemen Ruangan
                </a>

                <div class="my-4 border-t border-slate-200"></div>
                <p class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Kelola User</p>
                <a href="{{ route('admin.user.peminjam') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.user.peminjam') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-user-graduate mr-2"></i> User Peminjam
                </a>
                <a href="{{ route('admin.user.staff') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.user.staff') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-user-shield mr-2"></i> User Admin
                </a>

                <div class="my-4 border-t border-slate-200"></div>
                <p class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Monitoring</p>
                <a href="{{ route('admin.laporan') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.laporan') || request()->routeIs('admin.laporan.tiket') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-chart-bar mr-2"></i> Laporan Peminjaman
                </a>
                <a href="{{ route('admin.laporan.rusak') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.laporan.rusak') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-triangle-exclamation mr-2"></i> Laporan Barang Rusak
                </a>
            </nav>

            <div class="mt-8 border-t border-slate-200 pt-8">
                <div class="space-y-2 mb-4">
                    <a href="{{ route('setting') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('setting') || request()->routeIs('change-password') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        <i class="fas fa-gear mr-2"></i> Setting
                    </a>
                    <a href="{{ route('admin.faq') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.faq') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        <i class="fas fa-question-circle mr-2"></i> FAQ
                    </a>
                </div>
                <button type="button" onclick="openLogoutModal()" class="w-full rounded-xl bg-rose-600 px-4 py-3 text-left text-sm font-semibold text-white transition hover:bg-rose-700">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="h-screen flex-1 overflow-y-auto p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">@yield('page_title', 'Dashboard Admin')</h2>
                    <p class="mt-1 text-sm text-slate-600">@yield('page_subtitle', 'Kelola seluruh sistem booking sarana dan prasarana')</p>
                </div>

                @yield('content')
            </div>
        </main>
    </div>

    <div id="logout-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4">
        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-xl">
            <h3 class="text-lg font-bold text-slate-900">Konfirmasi Logout</h3>
            <p class="mt-2 text-sm text-slate-600">Yakin ingin logout sekarang?</p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeLogoutModal()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Batal</button>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
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

    function openLogoutModal() {
        const modal = document.getElementById('logout-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logout-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
    </script>

    @stack('scripts')
</body>
</html>
