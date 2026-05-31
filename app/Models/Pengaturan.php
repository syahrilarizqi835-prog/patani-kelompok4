<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';
    
    protected $fillable = [
        'key',
        'value',
        'deskripsi',
    ];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $deskripsi = null)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'deskripsi' => $deskripsi]
        );
    }
}
