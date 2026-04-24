@extends('layouts.peminjam')

@section('title', 'Pengaturan Akun')
@section('page_title', 'Pengaturan')

@section('content')
    <article class="mb-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <h2 class="text-sm font-bold text-slate-800">Info Akun</h2>
        <p class="mt-2 text-xs text-slate-600">Nama: {{ Auth::user()->name }}</p>
        <p class="mt-1 text-xs text-slate-600">Email: {{ Auth::user()->email }}</p>
        <p class="mt-1 text-xs text-slate-600">Poin Penalti: {{ (int) Auth::user()->penalty_points }}</p>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="mt-4">
            @csrf
            <button id="open-logout-confirm" type="button" class="w-full rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white">Logout</button>
        </form>
    </article>

    <article class="rounded-2xl border border-slate-200 p-4">
        <h2 class="text-sm font-bold text-slate-800">Ganti Password</h2>
        <p class="mt-1 text-xs text-slate-500">Gunakan password baru yang kuat untuk keamanan akun Anda.</p>

        <form id="change-password-form" action="{{ route('update-password') }}" method="POST" class="mt-4 space-y-3">
            @csrf
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Password Saat Ini</label>
                <input type="password" name="current_password" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Password Baru</label>
                <input type="password" name="new_password" minlength="6" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirmation" minlength="6" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <button id="open-password-confirm" type="button" class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white">Simpan Password Baru</button>
        </form>
    </article>

    <div id="logout-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 px-4">
        <div class="w-full max-w-sm rounded-2xl bg-white p-5 shadow-xl">
            <h3 class="text-base font-bold text-slate-900">Konfirmasi Logout</h3>
            <p class="mt-2 text-sm text-slate-600">Yakin ingin logout sekarang?</p>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" data-close-modal="logout-confirm-modal" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700">Batal</button>
                <button id="confirm-logout-btn" type="button" class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white">Ya, Logout</button>
            </div>
        </div>
    </div>

    <div id="password-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 px-4">
        <div class="w-full max-w-sm rounded-2xl bg-white p-5 shadow-xl">
            <h3 class="text-base font-bold text-slate-900">Konfirmasi Ganti Password</h3>
            <p class="mt-2 text-sm text-slate-600">Simpan perubahan password sekarang?</p>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" data-close-modal="password-confirm-modal" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700">Batal</button>
                <button id="confirm-password-btn" type="button" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white">Ya, Simpan</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const logoutForm = document.getElementById('logout-form');
        const passwordForm = document.getElementById('change-password-form');
        const logoutModal = document.getElementById('logout-confirm-modal');
        const passwordModal = document.getElementById('password-confirm-modal');
        const openLogoutBtn = document.getElementById('open-logout-confirm');
        const openPasswordBtn = document.getElementById('open-password-confirm');
        const confirmLogoutBtn = document.getElementById('confirm-logout-btn');
        const confirmPasswordBtn = document.getElementById('confirm-password-btn');

        if (!logoutForm || !passwordForm || !logoutModal || !passwordModal || !openLogoutBtn || !openPasswordBtn || !confirmLogoutBtn || !confirmPasswordBtn) {
            return;
        }

        function showModal(modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function hideModal(modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.querySelectorAll('[data-close-modal]').forEach((button) => {
            button.addEventListener('click', function () {
                const modalId = button.getAttribute('data-close-modal');
                const modal = document.getElementById(modalId);
                if (modal) {
                    hideModal(modal);
                }
            });
        });

        [logoutModal, passwordModal].forEach((modal) => {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    hideModal(modal);
                }
            });
        });

        openLogoutBtn.addEventListener('click', function () {
            showModal(logoutModal);
        });

        openPasswordBtn.addEventListener('click', function () {
            showModal(passwordModal);
        });

        confirmLogoutBtn.addEventListener('click', function () {
            logoutForm.submit();
        });

        confirmPasswordBtn.addEventListener('click', function () {
            passwordForm.submit();
        });
    })();
</script>
@endpush
