<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatNotification extends Model
{
    protected $table = 'chat_notifications';
    protected $fillable = ['chat_id', 'user_id', 'is_enabled'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
