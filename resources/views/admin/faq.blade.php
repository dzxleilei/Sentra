@extends('layouts.admin')

@section('page_title', 'FAQ')
@section('page_subtitle', 'Pertanyaan umum seputar pengelolaan sistem')

@section('content')
<div class="max-w-7xl p-2">
    <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i>
            Dashboard
        </a>
        <span>/</span>
        <span>FAQ</span>
    </div>

    <h2 class="mb-6 text-2xl font-bold text-slate-900">
        <i class="fas fa-question-circle mr-2 text-blue-600"></i>Frequently Asked Questions
    </h2>

    <div class="space-y-4">
        <!-- FAQ Item 1 -->
        <div class="rounded-2xl border border-slate-200 bg-white transition hover:shadow-sm">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Bagaimana cara membuat user baru?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Anda dapat membuat user baru melalui menu "Manajemen User". Klik tombol "Tambah User", isi form dengan informasi lengkap (nama, email, password, dan role), kemudian klik "Simpan". Anda juga dapat mengimpor multiple user sekaligus melalui file CSV.</p>
            </div>
        </div>

        <!-- FAQ Item 2 -->
        <div class="rounded-2xl border border-slate-200 bg-white transition hover:shadow-sm">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Apa itu RoomContent dan bagaimana cara mengaturnya?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">RoomContent adalah fitur untuk mengelola barang (barang) yang ada di dalam ruangan tertentu beserta jumlahnya. Ketika membuat atau mengedit ruangan, Anda dapat menambahkan barang dan menentukan kuantitasnya. Ini membantu melacak inventaris barang di setiap ruangan.</p>
            </div>
        </div>

        <!-- FAQ Item 3 -->
        <div class="rounded-2xl border border-slate-200 bg-white transition hover:shadow-sm">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Bagaimana cara memverifikasi peminjaman?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Anda dapat memverifikasi peminjaman melalui menu "Verifikasi Booking" di bagian "FITUR VERIFIKATOR". Pilih booking dari antrian verifikasi, periksa detail peminjaman, dan lakukan validasi dengan memindai barcode atau foto. Administrator dapat melihat semua peminjaman yang sedang berlangsung dan menandai pelanggaran jika diperlukan.</p>
            </div>
        </div>

        <!-- FAQ Item 4 -->
        <div class="rounded-2xl border border-slate-200 bg-white transition hover:shadow-sm">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Bagaimana cara mengimpor user dari CSV?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Buka menu "Manajemen User", klik tombol "Import User", lalu klik "Download Template CSV" untuk mendapatkan template. Isi data sesuai format (Nama, Email, Password, Role), kemudian upload file tersebut. Sistem akan memvalidasi dan mengimpor data secara otomatis.</p>
            </div>
        </div>

        <!-- FAQ Item 5 -->
        <div class="rounded-2xl border border-slate-200 bg-white transition hover:shadow-sm">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Bagaimana cara melihat laporan peminjaman?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Buka menu "Laporan" di bagian "MONITORING". Anda dapat melihat statistik peminjaman, daftar barang yang sering dipinjam, dan periode peminjaman. Sistem juga menyediakan fitur export untuk mengunduh laporan dalam berbagai format.</p>
            </div>
        </div>

        <!-- FAQ Item 6 -->
        <div class="rounded-2xl border border-slate-200 bg-white transition hover:shadow-sm">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Apa itu pelanggaran dan bagaimana cara menanganinya?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Pelanggaran adalah ketika peminjam melanggar aturan peminjaman (misalnya keterlambatan pengembalian). Menu "Kelola Penalti" menampilkan daftar pelanggaran yang tercatat. Anda dapat menambahkan catatan dan menentukan tindakan lanjut untuk setiap pelanggaran.</p>
            </div>
        </div>
    </div>

    <div class="mt-8 rounded-2xl border border-blue-200 bg-blue-50 p-6">
        <h3 class="font-semibold text-blue-900 mb-2"><i class="fas fa-info-circle mr-2"></i>Bantuan Lebih Lanjut</h3>
        <p class="text-blue-800">Jika Anda memiliki pertanyaan yang tidak terjawab di atas, silakan hubungi tim support melalui email support@sentra.local atau hubungi administrator sistem.</p>
    </div>
</div>

<script>
function toggleFAQ(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('i.fa-chevron-down');
    
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}
</script>
@endsection
