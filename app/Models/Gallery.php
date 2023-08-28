<?php

namespace App\Models;

use App\Helper\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'gallery';

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'association_id',
        'image',
        'date'
    ];

    protected function getImageAttribute($value)
    {
        return Helper::FilePublicLink($value, EVENT_IMAGE_INFO);
    }

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class,'association_id');
    }
    
   
}

