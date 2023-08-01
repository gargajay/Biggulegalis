<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chats';
    protected $fillable = [
        'sender_id', 'receiver_id', 'model', 'model_id', 'type',
        'sender_chat_hide', 'receiver_chat_hide', 'sender_delete_chat_at', 'receiver_delete_chat_at'
    ];

    protected $casts = [
        'id' => 'integer',
        'sender_id' => 'integer',
        'receiver_id' => 'integer',
        'model' => 'string',
        'model_id' => 'integer',
        'type' => 'integer',
        'sender_chat_hide' => 'integer',
        'receiver_chat_hide' => 'integer',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function sender_detail()
    {
        return $this->belongsTo(User::class, 'sender_id')->select('id', 'full_name as name', 'email', 'image');
    }

    public function receiver_detail()
    {
        if ($this->type == 1) {
            return User::where('id', $this->receiver_id)->select('id', 'full_name as name', 'email', 'image')->first();
        } else {
            return ChatGroup::where('id', $this->receiver_id)->select('id', 'name', 'image')->first();
        }
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function last_message()
    {
        if ($this->type != 2) {
            return Message::where('chat_id', $this->id)->latest()->first();
        } else {
            $chatIds = Chat::where('receiver_id', $this->receiver_id)->pluck('id');
            return Message::whereIn('chat_id', $chatIds)->latest()->first();
        }
    }

    public function chatGroup()
    {
        return $this->belongsTo(ChatGroup::class, 'model_id');
    }

    public function getUnseenMessageCountAttribute()
    {
        return $this->messages()->where('seen', false)->count();
    }
}
