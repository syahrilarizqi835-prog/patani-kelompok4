<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotConversation extends Model
{
    protected $table = 'chatbot_conversations';

    protected $fillable = [
        'user_id',
        'message',
        'response',
        'context',
        'tipe',
        'tokens_used',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}