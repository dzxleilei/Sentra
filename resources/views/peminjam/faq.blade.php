@extends('layouts.peminjam')

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
                        Bagaimana cara meminjam barang?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Untuk meminjam barang, buka menu "Daftar Barang", pilih barang yang ingin dipinjam, tentukan jumlah dan waktu peminjaman, kemudian klik "Pesan". Pengajuan akan tercatat sebagai booking dan dapat langsung diproses sesuai alur check-in/check-out.</p>
            </div>
        </div>

        <!-- FAQ Item 2 -->
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Apa perbedaan antara peminjaman barang dan ruangan?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Peminjaman barang adalah ketika Anda meminjam item spesifik (misalnya proyektor, kamera, dll). Peminjaman ruangan adalah ketika Anda meminjam seluruh ruangan beserta barang-barang yang ada di dalamnya. Pilih yang sesuai dengan kebutuhan Anda melalui menu yang tersedia.</p>
            </div>
        </div>

        <!-- FAQ Item 3 -->
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Bagaimana cara melakukan check-in dan check-out?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Check-in dilakukan ketika Anda mulai menggunakan barang/ruangan. Check-out dilakukan saat Anda selesai mengembalikannya. Kedua proses ini membutuhkan dokumentasi sesuai alur sistem.</p>
            </div>
        </div>

        <!-- FAQ Item 4 -->
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Berapa lama saya dapat meminjam barang/ruangan?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Durasi peminjaman tergantung pada kebijakan institusi. Anda dapat menentukan waktu mulai dan selesai peminjaman saat melakukan booking. Jika memerlukan perpanjangan, hubungi admin sebelum waktu selesai yang telah ditentukan.</p>
            </div>
        </div>

        <!-- FAQ Item 5 -->
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Apa yang harus saya lakukan jika barang/ruangan mengalami kerusakan?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Segera laporkan kepada admin jika terdapat kerusakan pada barang/ruangan yang Anda pinjam. Laporan akan didokumentasikan pada sistem dan Anda mungkin dikenakan penalti tergantung jenis serta tingkat keparahan kerusakan.</p>
            </div>
        </div>

        <!-- FAQ Item 6 -->
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Bagaimana cara melihat riwayat peminjaman saya?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Buka menu "Riwayat Peminjaman" untuk melihat semua peminjaman yang telah Anda lakukan, termasuk status peminjaman, tanggal, dan barang/ruangan yang dipinjam. Data ini membantu Anda melacak perilaku peminjaman dan penalti yang mungkin Anda terima.</p>
            </div>
        </div>

        <!-- FAQ Item 7 -->
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <button class="w-full px-6 py-4 text-left focus:outline-none group" onclick="toggleFAQ(this)">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                        Apa itu penalti dan bagaimana cara melihatnya?
                    </h3>
                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-600 transition"></i>
                </div>
            </button>
            <div class="hidden px-6 pb-4 border-t border-gray-200">
                <p class="text-gray-700">Penalti diberikan jika Anda melanggar aturan peminjaman (keterlambatan, kerusakan barang, dll). Buka menu "Penalti" untuk melihat semua penalti yang Anda terima beserta penjelasan, tanggal, dan status penyelesaiannya.</p>
            </div>
        </div>
    </div>

    <div class="mt-8 p-6 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="font-semibold text-blue-900 mb-2"><i class="fas fa-info-circle mr-2"></i>Bantuan Lebih Lanjut</h3>
        <p class="text-blue-800">Jika Anda memiliki pertanyaan yang tidak terjawab di atas, silakan hubungi administrator sistem untuk mendapatkan bantuan lebih lanjut.</p>
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
