<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'value_source'];

    /**
     * Decrypt the value if it is encrypted, otherwise return the raw value.
     */
    public function getValueAttribute(mixed $value)
    {
        if (is_null($value) || $value === '') {
            return $value;
        }

        try {
            return decrypt($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Never expose encrypted database contents to API consumers if the
            // application key is wrong or the value was written with a retired key.
            report($e);

            return null;
        }
    }

    /**
     * Always encrypt the value when setting it.
     */
    public function setValueAttribute(mixed $value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['value'] = $value;
        } else {
            $this->attributes['value'] = encrypt($value);
        }
    }

    protected static function booted()
    {
        $clear = function () {
            \Illuminate\Support\Facades\Cache::forget('settings_en');
            \Illuminate\Support\Facades\Cache::forget('settings_ar');
            \Illuminate\Support\Facades\Cache::forget('settings_v2_en');
            \Illuminate\Support\Facades\Cache::forget('settings_v2_ar');
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
