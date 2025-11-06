<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    protected static bool $cacheAvailable = true;

    /**
     * Retrieve a setting value with optional default.
     */
    public static function getValue(string $key, $default = null)
    {
        $settings = static::allCached();
        return $settings[$key] ?? $default;
    }

    /**
     * Persist a setting value.
     */
    public static function setValue(string $key, $value): void
    {
        if (!Schema::hasTable((new static())->getTable())) {
            return;
        }

        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        if (static::$cacheAvailable) {
            try {
                Cache::forget(static::cacheKey());
            } catch (\Throwable $e) {
                report($e);
                static::$cacheAvailable = false;
            }
        }
    }

    /**
     * Retrieve multiple settings at once.
     *
     * @return array<string, mixed>
     */
    public static function getValues(array $keys, array $defaults = []): array
    {
        $settings = static::allCached();
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $settings[$key] ?? ($defaults[$key] ?? null);
        }

        return $result;
    }

    /**
     * Return all settings as key => value array with caching.
     *
     * @return array<string, mixed>
     */
    protected static function allCached(): array
    {
        if (!Schema::hasTable((new static())->getTable())) {
            return [];
        }

        if (!static::$cacheAvailable) {
            return static::query()->pluck('value', 'key')->toArray();
        }

        try {
            return Cache::rememberForever(static::cacheKey(), function () {
                return static::query()->pluck('value', 'key')->toArray();
            });
        } catch (\Throwable $e) {
            report($e);
            static::$cacheAvailable = false;
            return static::query()->pluck('value', 'key')->toArray();
        }
    }

    protected static function cacheKey(): string
    {
        return 'app_settings_cache';
    }
}
