@extends('layouts.admin')

@section('page_title', $barang ? 'Edit Barang' : 'Tambah Barang')
@section('page_subtitle', 'Atur data barang, lokasi, dan status ketersediaan')

@section('content')
<div class="max-w-7xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <a href="{{ route('admin.barang') }}" class="hover:text-blue-600">Manajemen Barang</a>
        <span>/</span>
        <span>{{ $barang ? 'Edit Barang' : 'Tambah Barang' }}</span>
    </div>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900">
            <i class="fas fa-boxes-stacked text-blue-600 mr-2"></i>{{ $barang ? 'Edit Barang' : 'Tambah Barang Baru' }}
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            {{ $barang ? 'Perbarui informasi barang' : 'Tambahkan barang baru ke sistem' }}
        </p>
    </div>

    <div class="max-w-5xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST">
            @csrf

            <!-- Kode Barang -->
            <div class="mb-6">
                <label for="kode_thing" class="mb-2 block text-sm font-semibold text-slate-700">
                    Kode Barang <span class="text-red-600">*</span>
                </label>
                <input type="text" id="kode_thing" name="kode_thing" 
                    value="{{ old('kode_thing', $barang?->kode_thing) }}"
                    placeholder="Contoh: B001"
                    {{ $barang ? 'readonly' : '' }}
                    class="w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('kode_thing') ? 'border-red-500' : 'border-slate-300' }}"
                    required>
                @error('kode_thing')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Barang -->
            <div class="mb-6">
                <label for="nama" class="mb-2 block text-sm font-semibold text-slate-700">
                    Nama Barang <span class="text-red-600">*</span>
                </label>
                <input type="text" id="nama" name="nama" 
                    value="{{ old('nama', $barang?->nama) }}"
                    placeholder="Contoh: Proyektor Epson EB-2140W"
                    class="w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('nama') ? 'border-red-500' : 'border-slate-300' }}"
                    required>
                @error('nama')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lokasi Ruangan -->
            <div class="mb-6">
                <label for="room_id" class="mb-2 block text-sm font-semibold text-slate-700">
                    Lokasi Ruangan <span class="text-red-600">*</span>
                </label>
                <select id="room_id" name="room_id" 
                    class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach($ruangan as $room)
                        <option value="{{ $room->id }}" 
                            {{ old('room_id', $barang?->room_id) == $room->id ? 'selected' : '' }}>
                            {{ $room->nama }}
                        </option>
                    @endforeach
                </select>
                @error('room_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">
                    Status <span class="text-red-600">*</span>
                </label>
                <select id="status" name="status" 
                    class="w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('status') ? 'border-red-500' : 'border-slate-300' }}"
                    required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Tersedia" {{ old('status', $barang?->status) === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Dipinjam" {{ old('status', $barang?->status) === 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="Tidak Tersedia" {{ old('status', $barang?->status) === 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                </select>
                @error('status')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 mt-8">
                        <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white transition hover:bg-blue-700">
                    <i class="fas fa-save mr-1"></i> {{ $barang ? 'Perbarui' : 'Simpan' }}
                </button>
                        <a href="{{ route('admin.barang') }}" class="rounded-lg border border-slate-300 bg-white px-6 py-2 font-semibold text-slate-700 transition hover:bg-slate-100">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
