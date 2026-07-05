<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRequest extends Model
{
    protected $table = 'chat_requests';

    protected $fillable = [
        'requester_id',
        'receiver_id',
        'chat_id',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'requester_id' => 'integer',
        'receiver_id' => 'integer',
        'chat_id' => 'integer',
        'status' => 'integer',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->select('id', 'full_name', 'image');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id')->select('id', 'full_name', 'image');
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
