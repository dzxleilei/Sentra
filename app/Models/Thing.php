<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Thing extends Model {
    protected $fillable = ["kode_thing", "nama", "status", "status_id", "room_id"];

    protected $casts = [
        'status_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Thing $thing) {
            if ($thing->status !== null) {
                $thing->status_id = Status::idFor(Status::DOMAIN_THING, $thing->status);
            } elseif ($thing->status_id) {
                $thing->status = Status::codeForId((int) $thing->status_id);
            }
        });
    }
    
    public function room() { 
        return $this->belongsTo(Room::class); 
    }

    public function statusRef() {
        return $this->belongsTo(Status::class, 'status_id');
    }
    
    public function roomContents() {
        return $this->hasMany(RoomContent::class);
    }
}
