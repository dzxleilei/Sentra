<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Room;
use App\Models\Thing;
use App\Models\RoomContent;
use App\Models\Borrow;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@itbss.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $peminjam1 = User::create([
            'name' => 'Peminjam Satu',
            'email' => 'peminjam1@itbss.ac.id',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
        ]);

        $peminjam2 = User::create([
            'name' => 'Peminjam Dua',
            'email' => 'peminjam2@itbss.ac.id',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
        ]);

        // Create rooms
        $room1 = Room::create([
            'kode_room' => 'R001',
            'nama' => 'Ruang Meeting A',
            'status' => 'Tersedia',
        ]);

        $room2 = Room::create([
            'kode_room' => 'R002',
            'nama' => 'Ruang Konferensi',
            'status' => 'Tersedia',
        ]);

        $room3 = Room::create([
            'kode_room' => 'R003',
            'nama' => 'Ruang Seminar',
            'status' => 'Tersedia',
        ]);

        // Create things (barang)
        $proyektor = Thing::create([
            'room_id' => $room1->id,
            'kode_thing' => 'T001',
            'nama' => 'Proyektor Epson',
            'status' => 'Tersedia',
        ]);

        $papanTulis = Thing::create([
            'room_id' => $room1->id,
            'kode_thing' => 'T002',
            'nama' => 'Papan Tulis',
            'status' => 'Tersedia',
        ]);

        $soundSystem = Thing::create([
            'room_id' => $room2->id,
            'kode_thing' => 'T003',
            'nama' => 'Sound System',
            'status' => 'Tersedia',
        ]);

        $kamera = Thing::create([
            'room_id' => null,
            'kode_thing' => 'T004',
            'nama' => 'Kamera DSLR',
            'status' => 'Tersedia',
        ]);
        
        $tripod = Thing::create([
            'room_id' => null,
            'kode_thing' => 'T005',
            'nama' => 'Tripod',
            'status' => 'Tersedia',
        ]);

        $laptop = Thing::create([
            'room_id' => null,
            'kode_thing' => 'T006',
            'nama' => 'Laptop Presentasi',
            'status' => 'Tersedia',
        ]);

        // Create RoomContents (barang dalam ruangan)
        RoomContent::create([
            'room_id' => $room1->id,
            'thing_id' => $proyektor->id,
        ]);

        RoomContent::create([
            'room_id' => $room1->id,
            'thing_id' => $papanTulis->id,
        ]);

        RoomContent::create([
            'room_id' => $room2->id,
            'thing_id' => $soundSystem->id,
        ]);

        RoomContent::create([
            'room_id' => $room3->id,
            'thing_id' => $kamera->id,
        ]);

        // Create test borrowings (peminjaman) yang sedang berlangsung dan menunggu verifikasi
        $now = Carbon::now();

        // Peminjaman 1: Barang (Berlangsung, belum diverifikasi)
        Borrow::create([
            'kode_booking' => 'BK001',
            'user_id' => $peminjam1->id,
            'tipe' => 'barang',
            'thing_id' => $kamera->id,
            'room_id' => null,
            'waktu_mulai_booking' => $now->copy()->subHours(2),
            'waktu_selesai_booking' => $now->copy()->addHours(4),
            'waktu_checkin' => $now->copy()->subHours(2),
            'waktu_checkout' => null,
            'foto_awal' => null,
            'foto_akhir' => null,
            'status' => 'Berlangsung',
            'catatan_pelanggaran' => null,
            'diverifikasi_admin' => false,
        ]);

        // Peminjaman 2: Ruangan (Berlangsung, belum diverifikasi)
        Borrow::create([
            'kode_booking' => 'BK002',
            'user_id' => $peminjam2->id,
            'tipe' => 'ruangan',
            'thing_id' => null,
            'room_id' => $room2->id,
            'waktu_mulai_booking' => $now->copy()->subHours(1),
            'waktu_selesai_booking' => $now->copy()->addHours(3),
            'waktu_checkin' => $now->copy()->subHours(1),
            'waktu_checkout' => null,
            'foto_awal' => null,
            'foto_akhir' => null,
            'status' => 'Berlangsung',
            'catatan_pelanggaran' => null,
            'diverifikasi_admin' => false,
        ]);

        // Peminjaman 3: Barang (Booking, belum diverifikasi)
        Borrow::create([
            'kode_booking' => 'BK003',
            'user_id' => $peminjam1->id,
            'tipe' => 'barang',
            'thing_id' => $tripod->id,
            'room_id' => null,
            'waktu_mulai_booking' => $now->copy()->addHours(2),
            'waktu_selesai_booking' => $now->copy()->addHours(6),
            'waktu_checkin' => null,
            'waktu_checkout' => null,
            'foto_awal' => null,
            'foto_akhir' => null,
            'status' => 'Booking',
            'catatan_pelanggaran' => null,
            'diverifikasi_admin' => false,
        ]);

        // Peminjaman 4: Ruangan (Berlangsung, belum diverifikasi)
        Borrow::create([
            'kode_booking' => 'BK004',
            'user_id' => $peminjam2->id,
            'tipe' => 'ruangan',
            'thing_id' => null,
            'room_id' => $room1->id,
            'waktu_mulai_booking' => $now->copy()->subMinutes(30),
            'waktu_selesai_booking' => $now->copy()->addHours(5),
            'waktu_checkin' => $now->copy()->subMinutes(30),
            'waktu_checkout' => null,
            'foto_awal' => null,
            'foto_akhir' => null,
            'status' => 'Berlangsung',
            'catatan_pelanggaran' => null,
            'diverifikasi_admin' => false,
        ]);

        // Peminjaman 5: Barang (Selesai, sudah diverifikasi - untuk testing riwayat)
        Borrow::create([
            'kode_booking' => 'BK005',
            'user_id' => $peminjam1->id,
            'tipe' => 'barang',
            'thing_id' => $laptop->id,
            'room_id' => null,
            'waktu_mulai_booking' => $now->copy()->subDays(1)->subHours(2),
            'waktu_selesai_booking' => $now->copy()->subDays(1)->addHours(2),
            'waktu_checkin' => $now->copy()->subDays(1)->subHours(2),
            'waktu_checkout' => $now->copy()->subDays(1)->addHours(2),
            'foto_awal' => null,
            'foto_akhir' => null,
            'status' => 'Selesai',
            'catatan_pelanggaran' => null,
            'diverifikasi_admin' => true,
        ]);
    }
}
