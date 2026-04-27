@extends('layouts.admin')

@section('page_title', isset($ruangan) ? 'Edit Ruangan & Isi' : 'Tambah Ruangan & Isi')
@section('page_subtitle', 'Kelola ruangan sekaligus daftar barang di dalamnya')

@section('content')
<div class="max-w-7xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <a href="{{ route('admin.ruangan') }}" class="hover:text-blue-600">Manajemen Ruangan</a>
        <span>/</span>
        <span>{{ isset($ruangan) ? 'Edit Ruangan & Isi' : 'Tambah Ruangan & Isi' }}</span>
    </div>

    <h2 class="mb-8 text-2xl font-bold text-slate-900">
        <i class="fas fa-door-open text-blue-600 mr-2"></i>
        {{ isset($ruangan) ? 'Edit Ruangan & Isi Barang' : 'Tambah Ruangan & Isi Barang' }}
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

    <form action="{{ isset($ruangan) ? route('admin.ruangan.edit', $ruangan->id) : route('admin.ruangan.tambah') }}" method="POST" class="max-w-5xl space-y-6">
        @csrf

        <!-- Info Ruangan -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-6">
            <h3 class="text-lg font-bold text-slate-900"><i class="fas fa-building mr-2 text-blue-600"></i>Info Ruangan</h3>

            <div>
                <label for="kode_room" class="mb-2 block text-sm font-semibold text-slate-700">Kode Ruangan</label>
                <input type="text" id="kode_room" name="kode_room" value="{{ $ruangan->kode_room ?? old('kode_room') }}" 
                    class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Misal: RUANG-001" required>
            </div>

            <div>
                <label for="nama" class="mb-2 block text-sm font-semibold text-slate-700">Nama Ruangan</label>
                <input type="text" id="nama" name="nama" value="{{ $ruangan->nama ?? old('nama') }}" 
                    class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Misal: Ruang Kelas 1A" required>
            </div>

            <div>
                <label for="lantai" class="mb-2 block text-sm font-semibold text-slate-700">Lantai Ruangan</label>
                <input type="text" id="lantai" name="lantai" value="{{ $ruangan->lantai ?? old('lantai') }}"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Misal: 3.02" required>
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
                <select id="status" name="status" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Tersedia" {{ ($ruangan->status ?? old('status')) === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Dipakai" {{ ($ruangan->status ?? old('status')) === 'Dipakai' ? 'selected' : '' }}>Dipakai</option>
                    <option value="Maintenance" {{ ($ruangan->status ?? old('status')) === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="Tidak Tersedia" {{ ($ruangan->status ?? old('status')) === 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                </select>
            </div>
        </div>

        <!-- Isi Ruangan (Barang) -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900"><i class="fas fa-box mr-2 text-blue-600"></i>Isi Ruangan</h3>
                <button type="button" onclick="tambahBaris()" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    <i class="fas fa-plus mr-1"></i> Tambah Barang
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800">Nama Barang</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800">Kode Barang</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="barang-table" class="divide-y divide-slate-200">
                        @php
                            $selectedThingIds = collect($selectedThingIds ?? []);
                        @endphp

                        @if($selectedThingIds->count() > 0)
                            @foreach($selectedThingIds as $selectedThingId)
                                @php
                                    $selectedThing = $semua_barang->firstWhere('id', (int) $selectedThingId);
                                @endphp
                                @if($selectedThing)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3">
                                        <select name="barang[]" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" onchange="updateKode(this)">
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach($semua_barang as $barang)
                                                <option value="{{ $barang->id }}" data-kode="{{ $barang->kode_thing }}" {{ (int) $selectedThingId === (int) $barang->id ? 'selected' : '' }}>{{ $barang->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm" readonly value="{{ $selectedThing->kode_thing }}">
                                    </td>
                                    <td class="px-4 py-3">
                                        <button type="button" onclick="hapusBaris(this)" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                            <i class="fas fa-trash-can mr-1"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div id="empty-message" class="py-8 text-center text-slate-600">
                <i class="fas fa-inbox mb-3 text-4xl text-slate-300"></i>
                <p>Belum ada barang ditambahkan. Klik "Tambah Barang" untuk memulai.</p>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                <i class="fas fa-save mr-1"></i> {{ isset($ruangan) ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('admin.ruangan') }}" class="rounded-lg border border-slate-300 px-6 py-2 font-semibold text-slate-700 transition hover:bg-slate-100">
                <i class="fas fa-times mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>

<script>
function tambahBaris() {
    const table = document.getElementById('barang-table');
    const emptyMsg = document.getElementById('empty-message');
    
    const row = document.createElement('tr');
    row.className = 'hover:bg-slate-50';
    row.innerHTML = `
        <td class="px-4 py-3">
            <select name="barang[]" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" onchange="updateKode(this)">
                <option value="">-- Pilih Barang --</option>
                @foreach($semua_barang as $barang)
                    <option value="{{ $barang->id }}" data-kode="{{ $barang->kode_thing }}">{{ $barang->nama }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-4 py-3">
            <input type="text" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm" readonly placeholder="Auto fill">
        </td>
        <td class="px-4 py-3">
            <button type="button" onclick="hapusBaris(this)" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                <i class="fas fa-trash mr-1"></i> Hapus
            </button>
        </td>
    `;
    
    table.appendChild(row);
    if (emptyMsg) emptyMsg.style.display = 'none';
    refreshBarangOptions();
}

function hapusBaris(btn) {
    btn.closest('tr').remove();
    const table = document.getElementById('barang-table');
    if (table.children.length === 0) {
        const emptyMsg = document.getElementById('empty-message');
        if (emptyMsg) emptyMsg.style.display = 'block';
    }
    refreshBarangOptions();
}

function updateKode(select) {
    const selectedOption = select.options[select.selectedIndex];
    const row = select.closest('tr');
    if (!row) return;

    const kodeInput = row.cells[1]?.querySelector('input');
    if (!kodeInput) return;

    kodeInput.value = selectedOption?.dataset?.kode || '';
    refreshBarangOptions();
}

function refreshBarangOptions() {
    const table = document.getElementById('barang-table');
    if (!table) return;

    const selects = Array.from(table.querySelectorAll('select[name="barang[]"]'));

    selects.forEach((select) => {
        const currentValue = select.value;
        const selectedInOtherRows = new Set(
            selects
                .filter((other) => other !== select)
                .map((other) => other.value)
                .filter((value) => value !== '')
        );

        Array.from(select.options).forEach((option, index) => {
            if (index === 0 || option.value === '') {
                option.hidden = false;
                option.disabled = false;
                return;
            }

            const shouldHide = selectedInOtherRows.has(option.value) && option.value !== currentValue;
            option.hidden = shouldHide;
            option.disabled = shouldHide;
        });
    });
}

// Hide empty message if there are rows
window.addEventListener('load', function() {
    const table = document.getElementById('barang-table');
    if (table.children.length > 0) {
        const emptyMsg = document.getElementById('empty-message');
        if (emptyMsg) emptyMsg.style.display = 'none';
    }

    table.querySelectorAll('select[name="barang[]"]').forEach((select) => updateKode(select));
    refreshBarangOptions();
});
</script>
@endsection
