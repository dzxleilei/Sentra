<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sentra Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>[x-cloak]{display:none !important;}</style>
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
                <a href="{{ route('admin.ruangan') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.ruangan*') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-door-open mr-2"></i> Manajemen Ruangan
                </a>
                <a href="{{ route('admin.barang') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.barang*') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-boxes mr-2"></i> Manajemen Barang
                </a>

                <div class="my-4 border-t border-slate-200"></div>
                <p class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Kelola User</p>
                <a href="{{ route('admin.user.peminjam') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.user.peminjam') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-user-graduate mr-2"></i> User Peminjam
                </a>
                <a href="{{ route('admin.user.staff') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.user.staff') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-user-shield mr-2"></i> User Admin & Verifikator
                </a>

                <div class="my-4 border-t border-slate-200"></div>
                <p class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Monitoring</p>
                <a href="{{ route('admin.notifikasi.booking') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.notifikasi.booking') || request()->routeIs('admin.verifikasi.*') ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-check-circle mr-2"></i> Verifikasi Booking
                </a>
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
</body>
</html>
