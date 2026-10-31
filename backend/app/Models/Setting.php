<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'value_source'];

    /**
     * Keys that contain sensitive credentials and must be encrypted at rest.
     * Public frontend settings remain plaintext to avoid per-row OpenSSL overhead.
     */
    protected static array $sensitiveKeys = [
        'smtp_password',
        'whatsapp_token',
        'turnstile_secret_key',
    ];

    /**
     * Decrypt the value if it is encrypted, otherwise return the raw value.
     */
    public function getValueAttribute(mixed $value)
    {
        if (is_null($value) || $value === '') {
            return $value;
        }

        // Only attempt OpenSSL decryption if the stored string looks like a Laravel payload (eyJpdiI6...)
        if (is_string($value) && str_starts_with($value, 'eyJpdiI6')) {
            try {
                return decrypt($value);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                report($e);

                return null;
            }
        }

        return $value;
    }

    /**
     * Encrypt only sensitive keys when setting values.
     */
    public function setValueAttribute(mixed $value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['value'] = $value;
        } elseif (in_array($this->attributes['key'] ?? '', self::$sensitiveKeys, true)) {
            $this->attributes['value'] = encrypt($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    protected static function booted()
    {
        $clear = function () {
            \Illuminate\Support\Facades\Cache::forget('settings_en');
            \Illuminate\Support\Facades\Cache::forget('settings_ar');
            \Illuminate\Support\Facades\Cache::forget('settings_v2_en');
            \Illuminate\Support\Facades\Cache::forget('settings_v2_ar');
            \Illuminate\Support\Facades\Cache::forget('footer_data_en');
            \Illuminate\Support\Facades\Cache::forget('footer_data_ar');
            \App\Traits\ClearsApiCache::bumpCacheVersion('footer');
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
