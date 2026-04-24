@extends('layouts.admin')

@section('page_title', 'Setting')
@section('page_subtitle', 'Kelola password dan batas blokir akun')

@section('content')
<div class="max-w-7xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <span>Setting</span>
    </div>

    <h2 class="mb-6 text-2xl font-bold text-slate-900">
        <i class="fas fa-gear mr-2 text-blue-600"></i>Setting
    </h2>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-900 font-semibold mb-2">Error:</p>
            <ul class="text-red-800 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($message = Session::get('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
            <i class="fas fa-check-circle mr-2"></i> {{ $message }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <form action="{{ route('update-password') }}" method="POST" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            @csrf

            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Ganti Password</h3>
                    <p class="text-sm text-slate-500">Perbarui password akun Anda secara aman</p>
                </div>
            </div>

            <div>
                <label for="current_password" class="mb-2 block text-sm font-semibold text-gray-700">Password Saat Ini</label>
                <input type="password" id="current_password" name="current_password" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="new_password" class="mb-2 block text-sm font-semibold text-gray-700">Password Baru</label>
                <input type="password" id="new_password" name="new_password" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('new_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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

        @if(auth()->user()->role === 'admin')
            <form action="{{ route('setting.penalty-threshold') }}" method="POST" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                @csrf

                <div>
                    <h3 class="text-lg font-bold text-slate-900">Setting Batas Blokir</h3>
                    <p class="text-sm text-slate-500">Akun peminjam akan terblokir jika poin penalti melebihi batas ini</p>
                </div>

                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                    Batas aktif saat ini: <span class="font-bold">{{ $penaltyBlockThreshold ?? 20 }}</span>
                </div>

                <div>
                    <label for="penalty_block_threshold" class="mb-2 block text-sm font-semibold text-gray-700">Batas Poin Penalti</label>
                    <input type="number" id="penalty_block_threshold" name="penalty_block_threshold" min="1" value="{{ old('penalty_block_threshold', $penaltyBlockThreshold ?? 20) }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('penalty_block_threshold')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full rounded-lg bg-emerald-600 px-6 py-2 font-semibold text-white transition hover:bg-emerald-700">
                    <i class="fas fa-save mr-1"></i> Simpan Setting
                </button>
            </form>
        @endif
    </div>

    <div class="mt-8 max-w-5xl rounded-2xl border border-blue-200 bg-blue-50 p-6">
        <h3 class="mb-2 font-semibold text-blue-900"><i class="fas fa-shield-alt mr-2"></i>Catatan</h3>
        <ul class="space-y-2 text-sm text-blue-800">
            <li>• Gunakan password yang kuat dengan kombinasi huruf, angka, dan simbol</li>
            <li>• Jika poin penalti peminjam melewati batas, akun akan ditandai tidak aktif dan terblokir</li>
        </ul>
    </div>
</div>
@endsection
