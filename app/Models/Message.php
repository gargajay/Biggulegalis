<?php

namespace App\Models;

use App\Helper\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $fillable = ['chat_id', 'sender_id', 'receiver_id', 'message', 'media_type', 'media_url', 'media_extension', 'is_seen'];

    protected $casts = [
        'id' => 'integer',
        'sender_id' => 'integer',
        'receiver_id' => 'integer',
        'message' => 'string',
        'media_type' => 'string',
        'media_url' => 'string',
        'media_extension' => 'string',
        'is_seen' => 'string',
    ];

    protected $hidden = [
        'deleted_at',
        'updated_at',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $chat = Chat::find($model->chat_id);
            $chat->updated_at = Carbon::now('UTC');
            $chat->save();
        });
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender_detail()
    {
        return $this->belongsTo(User::class, 'sender_id')->select('id', 'full_name', 'image');
    }

    public function receiver_detail()
    {
        return $this->belongsTo(User::class, 'receiver_id')->select('id', 'full_name', 'image');
    }

    protected function getMediaUrlAttribute($value)
    {
        if (!empty($value)) {
            return Helper::FilePublicLink($value, CHAT_IMAGE_INFO);
        }
        return $value;
    }
}
