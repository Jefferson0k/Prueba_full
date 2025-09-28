<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields;

    protected $fillable = [
        'key', 'value', 'type', 'group', 'description', 'is_public', 'is_active'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_active' => 'boolean',
    ];

    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_JSON = 'json';

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    // Métodos estáticos para configuración
    public static function get($key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->where('is_active', true)->first();
            
            if (!$setting) {
                return $default;
            }

            return static::castValue($setting->value, $setting->type);
        });
    }

    public static function set($key, $value, $type = self::TYPE_STRING, $group = 'general')
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'is_active' => true,
            ]
        );

        Cache::forget("setting.{$key}");
        return $setting;
    }

    public static function getByGroup($group)
    {
        return Cache::remember("settings.group.{$group}", 3600, function () use ($group) {
            return static::byGroup($group)->active()->get()->mapWithKeys(function ($setting) {
                return [$setting->key => static::castValue($setting->value, $setting->type)];
            });
        });
    }

    protected static function castValue($value, $type)
    {
        return match($type) {
            self::TYPE_INTEGER => (int) $value,
            self::TYPE_DECIMAL => (float) $value,
            self::TYPE_BOOLEAN => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            self::TYPE_JSON => json_decode($value, true),
            default => $value
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("setting.{$setting->key}");
            Cache::forget("settings.group.{$setting->group}");
        });

        static::deleted(function ($setting) {
            Cache::forget("setting.{$setting->key}");
            Cache::forget("settings.group.{$setting->group}");
        });
    }
}
