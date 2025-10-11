<?php

namespace Azuriom\Plugin\ApiExtender\Models;

use Illuminate\Database\Eloquent\Model;

class ApiExtenderSetting extends Model
{
    protected $table = 'apiextender_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return void
     */
    public static function setValue(string $key, $value, string $type = 'string'): void
    {
        $stringValue = static::valueToString($value, $type);

        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'type' => $type,
            ]
        );
    }

    /**
     * Cast value based on type
     *
     * @param string $value
     * @param string $type
     * @return mixed
     */
    protected static function castValue(?string $value, string $type)
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => $value === 'true',
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value, true),
            'datetime' => $value ? \Carbon\Carbon::parse($value) : null,
            default => $value,
        };
    }

    /**
     * Convert value to string for storage
     *
     * @param mixed $value
     * @param string $type
     * @return string|null
     */
    protected static function valueToString($value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => $value ? 'true' : 'false',
            'json' => json_encode($value),
            'datetime' => $value instanceof \Carbon\Carbon ? $value->toDateTimeString() : (string) $value,
            default => (string) $value,
        };
    }

    /**
     * Get all settings as key-value array
     *
     * @return array
     */
    public static function getAllSettings(): array
    {
        $settings = static::all();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = static::castValue($setting->value, $setting->type);
        }

        return $result;
    }
}