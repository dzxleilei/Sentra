<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomContent extends Model
{
    protected $table = 'room_contents';
    protected $fillable = ['room_id', 'thing_id'];
    
    public function room() {
        return $this->belongsTo(Room::class);
    }
    
    public function thing() {
        return $this->belongsTo(Thing::class);
    }
}
