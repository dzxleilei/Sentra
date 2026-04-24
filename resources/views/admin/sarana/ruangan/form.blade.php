@extends('layouts.admin')

@section('page_title', $ruangan ? 'Edit Ruangan' : 'Tambah Ruangan')
@section('page_subtitle', 'Atur data ruangan beserta status operasionalnya')

@section('content')
<div class="max-w-7xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <span>{{ $ruangan ? 'Edit Ruangan' : 'Tambah Ruangan' }}</span>
    </div>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            {{ $ruangan ? 'Edit Ruangan' : 'Tambah Ruangan Baru' }}
        </h1>
        <p class="text-gray-600 mt-2">
            {{ $ruangan ? 'Perbarui informasi ruangan' : 'Tambahkan ruangan baru ke sistem' }}
        </p>
    </div>

    <!-- Form Card -->
    <div class="max-w-5xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST">
            @csrf

            <!-- Kode Ruangan -->
            <div class="mb-6">
                <label for="kode_room" class="block text-sm font-semibold text-gray-700 mb-2">
                    Kode Ruangan <span class="text-red-600">*</span>
                </label>
                <input type="text" id="kode_room" name="kode_room" 
                    value="{{ old('kode_room', $ruangan?->kode_room) }}"
                    placeholder="Contoh: R001"
                    {{ $ruangan ? 'readonly' : '' }}
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('kode_room')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Ruangan -->
            <div class="mb-6">
                <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Ruangan <span class="text-red-600">*</span>
                </label>
                <input type="text" id="nama" name="nama" 
                    value="{{ old('nama', $ruangan?->nama) }}"
                    placeholder="Contoh: Ruang Rapat Besar"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('nama')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="lantai" class="block text-sm font-semibold text-gray-700 mb-2">
                    Lantai Ruangan <span class="text-red-600">*</span>
                </label>
                <input type="text" id="lantai" name="lantai"
                    value="{{ old('lantai', $ruangan?->lantai) }}"
                    placeholder="Contoh: 3.02"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('lantai')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                    Status <span class="text-red-600">*</span>
                </label>
                <select id="status" name="status" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Tersedia" {{ old('status', $ruangan?->status) === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Dipakai" {{ old('status', $ruangan?->status) === 'Dipakai' ? 'selected' : '' }}>Dipakai</option>
                    <option value="Maintenance" {{ old('status', $ruangan?->status) === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="Tidak Tersedia" {{ old('status', $ruangan?->status) === 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                </select>
                @error('status')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="mt-8 flex gap-4">
                <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white transition hover:bg-blue-700">
                    {{ $ruangan ? '💾 Perbarui' : '✚ Tambah' }} Ruangan
                </button>
                <a href="{{ route('admin.ruangan') }}" class="rounded-lg border border-slate-300 bg-white px-6 py-2 font-semibold text-slate-700 transition hover:bg-slate-100">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
