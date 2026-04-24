@extends('layouts.verifikator')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-800 mb-8">
        <i class="fas fa-question-circle mr-2" style="color: #317EFB;"></i>Frequently Asked Questions
    </h2>

    <div class="space-y-4">
        <!-- FAQ Item 1 -->
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Apa tugas utama seorang verifikator?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Sebagai verifikator, tugas Anda adalah memvalidasi setiap peminjaman yang dilakukan oleh peminjam. Ini mencakup verifikasi identitas peminjam, pengecekan barang/ruangan yang akan dipinjam, mencatat waktu check-in dan check-out, serta melaporkan pelanggaran jika terjadi.</p>
            </div>
        </div>

        <!-- FAQ Item 2 -->
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Bagaimana cara memvalidasi booking peminjaman?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Buka menu "Daftar Validasi" untuk melihat antrian peminjaman yang perlu divalidasi. Pilih peminjaman dari daftar, periksa detail barang/ruangan dan identitas peminjam, ambil foto awal barang/ruangan, kemudian klik "Validasi" untuk menyelesaikan proses check-in.</p>
            </div>
        </div>

        <!-- FAQ Item 3 -->
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Kapan saya harus menggunakan fitur check-out?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Fitur check-out digunakan ketika peminjam mengembalikan barang/ruangan yang telah dipinjam. Ambil foto akhir untuk dokumentasi kondisi barang, catat waktu pengembalian, dan periksa apakah ada kerusakan atau masalah. Jika ada masalah, Anda dapat langsung menambahkan laporan pelanggaran.</p>
            </div>
        </div>

        <!-- FAQ Item 4 -->
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Bagaimana cara melaporkan pelanggaran?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Jika Anda menemukan pelanggaran selama proses verifikasi (keterlambatan, kerusakan barang, dll), buka menu "Laporan Pelanggaran" untuk mencatat secara detail. Sertakan deskripsi pelanggaran, foto bukti jika ada, dan tanggal/waktu kejadian. Laporan akan diteruskan ke administrator untuk ditindaklanjuti.</p>
            </div>
        </div>

        <!-- FAQ Item 5 -->
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Apa yang harus saya lakukan jika menemukan barang rusak?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Jika menemukan barang rusak saat check-out, ambil dokumentasi foto kerusakan, catat deskripsi detail kerusakan di sistem, dan lampirkan foto sebagai bukti. Ini akan dicatat sebagai pelanggaran dan administrator akan mengevaluasi perlu tidaknya penalti bagi peminjam.</p>
            </div>
        </div>

        <!-- FAQ Item 6 -->
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Bagaimana cara menggunakan barcode scanner?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Barcode scanner dapat digunakan untuk verifikasi cepat barang/ruangan yang dipinjam. Arahkan scanner ke barcode barang, data akan terisi otomatis di sistem. Jika scanner tidak tersedia, Anda dapat memasukan kode secara manual melalui field yang disediakan.</p>
            </div>
        </div>
    </div>

    <div class="mt-8 p-6 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="font-semibold text-blue-900 mb-2"><i class="fas fa-info-circle mr-2"></i>Bantuan Lebih Lanjut</h3>
        <p class="text-blue-800">Jika Anda memiliki pertanyaan yang tidak terjawab di atas, silakan hubungi administrator sistem atau tim support melalui email support@sentra.local.</p>
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
