<?php

namespace App\Models;

use App\Helper\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'announcements';

    

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'association_id',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class,'association_id');
    }

    protected function getFileAttribute($value)
    {
        return Helper::FilePublicLink($value, EVENT_IMAGE_INFO);
    }
}

