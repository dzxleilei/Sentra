<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AppSetting extends Model
{
    protected $table = 'app_settings';

    protected $fillable = ['key', 'value'];

    private static function tableReady(): bool
    {
        try {
            return Schema::hasTable('app_settings');
        } catch (Throwable) {
            return false;
        }
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        if (! static::tableReady()) {
            return $default;
        }

        try {
            $setting = static::query()->where('key', $key)->first();
        } catch (Throwable) {
            return $default;
        }

        return $setting?->value ?? $default;
    }

    public static function integer(string $key, int $default = 0): int
    {
        return (int) static::getValue($key, $default);
    }

    public static function setValue(string $key, mixed $value): void
    {
        if (! static::tableReady()) {
            return;
        }

        try {
            static::query()->updateOrCreate([
                'key' => $key,
            ], [
                'value' => (string) $value,
            ]);
        } catch (Throwable) {
            return;
        }
    }
}