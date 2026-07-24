<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'group', 'type', 'value'];

    protected $casts = [];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            // Settings are read from the public layout on every request, including
            // before the `settings` table exists (fresh install, mid-migration) —
            // fall back to $default instead of a hard 500 in that window.
            try {
                $setting = static::where('key', $key)->first();
            } catch (\Exception $e) {
                return $default;
            }

            if (! $setting) {
                return $default;
            }

            return match ($setting->type) {
                'integer' => (int) $setting->value,
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'json' => json_decode($setting->value, true),
                default => $setting->value,
            };
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value, string $group = 'general', string $type = 'string'): void
    {
        $castValue = match ($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $castValue, 'group' => $group, 'type' => $type]
        );

        Cache::forget("setting.{$key}");
    }
}
