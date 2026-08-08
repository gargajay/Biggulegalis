<?php

namespace App\Models;

use App\Helper\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $fillable = ['chat_id', 'sender_id', 'receiver_id', 'message', 'media_type', 'media_url', 'media_extension', 'is_seen'];

    protected $appends = [
        'media_file_name',
        'media_display_type',
        'media_icon',
        'media_label',
    ];

    protected $casts = [
        'id' => 'integer',
        'sender_id' => 'integer',
        'receiver_id' => 'integer',
        'message' => 'string',
        'media_type' => 'string',
        'media_url' => 'string',
        'media_extension' => 'string',
        'is_seen' => 'boolean',
    ];

    protected $hidden = [
        'deleted_at',
        'updated_at',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $chat = Chat::find($model->chat_id);
            if (!empty($chat)) {
                $chat->updated_at = Carbon::now('UTC');
                $chat->save();
            }
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

    protected function getMediaFileNameAttribute()
    {
        return $this->getRawOriginal('media_url');
    }

    protected function getMediaDisplayTypeAttribute()
    {
        if (empty($this->getRawOriginal('media_url'))) {
            return null;
        }

        $mediaType = strtolower((string) $this->media_type);
        if ($mediaType === 'image') {
            return 'image';
        }

        if ($mediaType === 'link') {
            return 'link';
        }

        return 'document';
    }

    protected function getMediaIconAttribute()
    {
        if ($this->media_display_type !== 'document') {
            return null;
        }

        return match (strtolower((string) $this->media_extension)) {
            'pdf' => 'pdf',
            'doc', 'docx' => 'word',
            'xls', 'xlsx', 'csv' => 'excel',
            'ppt', 'pptx' => 'powerpoint',
            'txt', 'rtf' => 'text',
            'zip', 'rar', '7z' => 'archive',
            default => 'file',
        };
    }

    protected function getMediaLabelAttribute()
    {
        if (empty($this->getRawOriginal('media_url'))) {
            return null;
        }

        $extension = strtoupper((string) $this->media_extension);

        return $extension ? $extension . ' file' : 'File';
    }
}
