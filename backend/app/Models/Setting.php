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
            return $value;
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
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
