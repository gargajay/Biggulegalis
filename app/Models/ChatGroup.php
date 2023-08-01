<?php

namespace App\Models;

use App\Helper\Helper;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    protected $table = 'chat_groups';
    protected $fillable = ['user_id', 'name', 'description', 'image'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'image' => 'string',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'full_name', 'email', 'image');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_chat_groups', 'chat_group_id', 'user_id')
            ->withPivot('is_admin');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'model_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, UserChatGroup::class, 'chat_group_id')->select('users.id', 'full_name', 'email', 'image');
    }

    protected function getImageAttribute($value)
    {
        return Helper::FilePublicLink($value, GROUP_IMAGE_INFO);
    }
}
