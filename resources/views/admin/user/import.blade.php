@extends('layouts.admin')

@php
    $scope = request('scope', 'peminjam');
    $backRoute = $scope === 'staff' ? route('admin.user.staff') : route('admin.user.peminjam');
@endphp

@section('page_title', 'Import User CSV')
@section('page_subtitle', 'Import massal akun pengguna via file CSV')

@section('content')
<div class="max-w-7xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <span>Import User</span>
    </div>

    <h2 class="mb-6 text-2xl font-bold text-slate-900">
        <i class="fas fa-upload text-blue-600 mr-2"></i>Import User dari CSV
    </h2>

    @if($message = Session::get('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
            <i class="fas fa-check-circle mr-2"></i> {{ $message }}
        </div>
    @endif

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

    <div class="max-w-5xl rounded-2xl border border-slate-200 bg-white p-8 space-y-6">
        <!-- Format Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="font-semibold text-blue-900 mb-3"><i class="fas fa-info-circle mr-2"></i>Format CSV</h3>
            <p class="text-blue-800 text-sm mb-3">File CSV harus memiliki format seperti berikut (tanpa header):</p>
            <div class="bg-white p-3 rounded border border-blue-200 font-mono text-xs overflow-x-auto">
                <p>Budi Santoso,budi@example.com,password123,peminjam</p>
                <p>Admin User,admin@example.com,adminpass,admin</p>
            </div>
            <p class="text-blue-800 text-sm mt-3">Kolom: Nama, Email, Password, Role (admin/peminjam)</p>
        </div>

        <!-- Upload Form -->
        <form action="{{ route('admin.user.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="csv_file" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-file-csv mr-1 text-red-600"></i>Pilih File CSV
                </label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" 
                    class="w-full cursor-pointer rounded-lg border-2 border-dashed border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none"
                    required>
                <p class="text-gray-500 text-xs mt-2">Format yang didukung: CSV, TXT</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="fas fa-file-import mr-1"></i> Upload & Import
                </button>
                <a href="{{ $backRoute }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-semibold">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
            </div>
        </form>

        <!-- Download Template -->
        <div class="border-t pt-6">
            <h3 class="font-semibold text-gray-900 mb-3"><i class="fas fa-download mr-2 text-red-600"></i>Template CSV</h3>
            <p class="text-gray-600 text-sm mb-4">Download template untuk mempermudah input data:</p>
            <a href="javascript:downloadTemplate()" class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold text-sm">
                <i class="fas fa-download mr-1"></i> Download Template CSV
            </a>
        </div>
    </div>
</div>

<script>
function downloadTemplate() {
    const csv = 'Nama,Email,Password,Role\nContoh User,user@example.com,password123,peminjam';
    const element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(csv));
    element.setAttribute('download', 'template-user.csv');
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}
</script>
@endsection
