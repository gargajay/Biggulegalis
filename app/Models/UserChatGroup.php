<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserChatGroup extends Model
{
    protected $table = 'user_chat_groups';
    protected $fillable = ['chat_group_id', 'user_id', 'is_admin'];
    protected $casts = [
        'id' => 'integer',
        'chat_group_id' => 'integer',
        'user_id' => 'integer',
        'is_admin' => 'integer',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chatGroup()
    {
        return $this->belongsTo(ChatGroup::class);
    }
}
