@extends('layouts.admin')

@php
    $scope = request('scope', 'peminjam');
    $isCreate = !isset($user);
    $isStaffScope = $scope === 'staff';
    $backRoute = $scope === 'staff' ? route('admin.user.staff') : route('admin.user.peminjam');
    $selectedRole = $user->role ?? old('role') ?? ($scope === 'staff' ? 'admin' : 'peminjam');
@endphp

@section('page_title', isset($user) ? 'Edit User' : 'Tambah User')
@section('page_subtitle', 'Kelola data akun dan role pengguna')

@section('content')
<div class="max-w-7xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <a href="{{ $backRoute }}" class="hover:text-blue-600">{{ $scope === 'staff' ? 'User Admin' : 'User Peminjam' }}</a>
        <span>/</span>
        <span>{{ isset($user) ? 'Edit User' : 'Tambah User' }}</span>
    </div>

    <h2 class="mb-6 text-2xl font-bold text-slate-900">
        <i class="fas fa-user-plus text-blue-600 mr-2"></i>
        {{ isset($user) ? 'Edit User' : 'Tambah User Baru' }}
    </h2>

    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4">
            <p class="mb-2 font-semibold text-rose-900">Error:</p>
            <ul class="space-y-1 text-sm text-rose-800">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-5xl rounded-2xl border border-slate-200 bg-white p-6">
        <form action="{{ isset($user) ? route('admin.user.edit', $user->id) : route('admin.user.tambah') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama</label>
                <input type="text" id="name" name="name" value="{{ $user->name ?? old('name') }}" 
                    class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                <input type="email" id="email" name="email" value="{{ $user->email ?? old('email') }}" 
                    class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <p class="mt-1 text-xs text-slate-500">Khusus role peminjam, email wajib domain @itbss.ac.id.</p>
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">
                    Password {{ isset($user) ? '(Kosongkan jika tidak ingin mengubah)' : '' }}
                </label>
                <input type="password" id="password" name="password" 
                    class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    {{ !isset($user) ? 'required' : '' }}>
            </div>

            @if($isCreate && !$isStaffScope)
                <input type="hidden" name="role" value="peminjam">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Role</label>
                    <div class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">Peminjam</div>
                </div>
            @else
                <div>
                    <label for="role" class="mb-2 block text-sm font-semibold text-slate-700">Role</label>
                    <select id="role" name="role" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih Role --</option>
                        @if(!$isCreate)
                            <option value="peminjam" {{ $selectedRole === 'peminjam' ? 'selected' : '' }}>Peminjam</option>
                        @endif
                        <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            @endif

            <div class="flex gap-3">
                <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white transition hover:bg-blue-700">
                    <i class="fas fa-save mr-1"></i> {{ isset($user) ? 'Perbarui' : 'Simpan' }}
                </button>
                <a href="{{ $backRoute }}" class="rounded-lg border border-slate-300 px-6 py-2 font-semibold text-slate-700 transition hover:bg-slate-100">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
