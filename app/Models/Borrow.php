<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model {
    protected $fillable = ["kode_booking", "user_id", "tipe", "room_id", "thing_id", "waktu_mulai_booking", "waktu_selesai_booking", "waktu_checkin", "waktu_checkout", "alasan_peminjaman", "alasan_lainnya", "lokasi_penggunaan", "lokasi_lainnya", "foto_awal", "foto_akhir", "status", "status_id", "status_checkin", "status_checkin_id", "status_checkout", "status_checkout_id", "penalty_points_applied", "catatan_pelanggaran", "diverifikasi_admin"];
    
    protected $casts = [
        'waktu_mulai_booking' => 'datetime',
        'waktu_selesai_booking' => 'datetime',
        'waktu_checkin' => 'datetime',
        'waktu_checkout' => 'datetime',
        'status_id' => 'integer',
        'status_checkin_id' => 'integer',
        'status_checkout_id' => 'integer',
        'penalty_points_applied' => 'integer',
        'diverifikasi_admin' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Borrow $borrow) {
            if ($borrow->status !== null) {
                $borrow->status_id = Status::idFor(Status::DOMAIN_BORROW_MAIN, $borrow->status);
            } elseif ($borrow->status_id) {
                $borrow->status = Status::codeForId((int) $borrow->status_id);
            }

            if ($borrow->status_checkin !== null) {
                $borrow->status_checkin_id = Status::idFor(Status::DOMAIN_BORROW_CHECKIN, $borrow->status_checkin);
            } elseif ($borrow->status_checkin_id) {
                $borrow->status_checkin = Status::codeForId((int) $borrow->status_checkin_id);
            }

            if ($borrow->status_checkout !== null) {
                $borrow->status_checkout_id = Status::idFor(Status::DOMAIN_BORROW_CHECKOUT, $borrow->status_checkout);
            } elseif ($borrow->status_checkout_id) {
                $borrow->status_checkout = Status::codeForId((int) $borrow->status_checkout_id);
            }
        });
    }
    
    public function user() { return $this->belongsTo(User::class); }
    public function room() { return $this->belongsTo(Room::class); }
    public function thing() { return $this->belongsTo(Thing::class); }
    public function statusRef() { return $this->belongsTo(Status::class, 'status_id'); }
    public function statusCheckinRef() { return $this->belongsTo(Status::class, 'status_checkin_id'); }
    public function statusCheckoutRef() { return $this->belongsTo(Status::class, 'status_checkout_id'); }
}
