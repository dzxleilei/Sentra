<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamageReport extends Model
{
    protected $fillable = [
        'user_id',
        'borrow_id',
        'thing_id',
        'lokasi_barang',
        'keterangan',
        'foto_bukti',
        'status',
        'status_id',
    ];

    protected $casts = [
        'status_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (DamageReport $report) {
            if ($report->status !== null) {
                $report->status_id = Status::idFor(Status::DOMAIN_DAMAGE_REPORT, $report->status);
            } elseif ($report->status_id) {
                $report->status = Status::codeForId((int) $report->status_id);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borrow()
    {
        return $this->belongsTo(Borrow::class);
    }

    public function thing()
    {
        return $this->belongsTo(Thing::class);
    }

    public function statusRef()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
