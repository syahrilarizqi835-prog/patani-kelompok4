<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'topic_id',
        'user_id',
        'content',
        'likes',
    ];

    public function topic()
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likesRelation()
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }
}

class ForumLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'likeable_type',
        'likeable_id',
    ];

    public function likeable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

class Cuaca extends Model
{
    use HasFactory;

    protected $table = 'cuaca';

    protected $fillable = [
        'lokasi',
        'tanggal',
        'suhu',
        'kelembaban',
        'curah_hujan',
        'kecepatan_angin',
        'kondisi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'suhu' => 'decimal:2',
        'kelembaban' => 'decimal:2',
        'curah_hujan' => 'decimal:2',
        'kecepatan_angin' => 'decimal:2',
    ];
}

class ChatbotConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'response',
        'context',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $type = 'string', $group = 'general')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
    }
}
