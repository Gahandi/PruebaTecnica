<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        // Cache settings for performance 
        // In a real production environment, you might want to cache this for longer
        // For development, we'll cache for a short time or request duration
        return Cache::remember('setting_' . $key, 60, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            // Cast value based on type
            if ($setting->type === 'boolean') {
                return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
            }

            if ($setting->type === 'integer') {
                return (int) $setting->value;
            }

            if ($setting->type === 'json') {
                return json_decode($setting->value, true);
            }

            return $setting->value;
        });
    }

    /**
     * Get service charge percentage.
     * Returns a float like 16.0
     */
    public static function getServiceChargePercentage()
    {
        return self::get('service_charge_percentage', 16);
    }

    /**
     * Get service charge name.
     */
    public static function getServiceChargeName()
    {
        return self::get('service_charge_name', 'Cargo por servicio');
    }
}
