<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Room extends Model {
    protected $fillable = ["kode_room", "nama", "lantai", "status", "status_id"];

    protected $casts = [
        'status_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Room $room) {
            if ($room->status !== null) {
                $room->status_id = Status::idFor(Status::DOMAIN_ROOM, $room->status);
            } elseif ($room->status_id) {
                $room->status = Status::codeForId((int) $room->status_id);
            }
        });
    }
    
    public function things() { 
        return $this->hasMany(Thing::class); 
    }

    public function statusRef() {
        return $this->belongsTo(Status::class, 'status_id');
    }
    
    public function contents() {
        return $this->hasMany(RoomContent::class);
    }
}
