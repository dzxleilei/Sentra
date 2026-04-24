<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenaltyLog extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
        'borrow_id',
        'action',
        'points',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function borrow()
    {
        return $this->belongsTo(Borrow::class);
    }
}
