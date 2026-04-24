<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    public const DOMAIN_BORROW_MAIN = 'borrow_main';
    public const DOMAIN_BORROW_CHECKIN = 'borrow_checkin';
    public const DOMAIN_BORROW_CHECKOUT = 'borrow_checkout';
    public const DOMAIN_THING = 'thing';
    public const DOMAIN_ROOM = 'room';
    public const DOMAIN_DAMAGE_REPORT = 'damage_report';

    public $timestamps = false;

    protected $fillable = [
        'domain',
        'code',
        'label',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function idFor(string $domain, ?string $code): ?int
    {
        $value = trim((string) $code);
        if ($value === '') {
            return null;
        }

        return static::query()
            ->where('domain', $domain)
            ->where('code', $value)
            ->value('id');
    }

    public static function codeForId(?int $id): ?string
    {
        if (! $id) {
            return null;
        }

        return static::query()->whereKey($id)->value('code');
    }
}
