<?php

namespace App\Models;

use App\Helper\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_name',
        'model_id',
        'file_name',
        'file_type',
        'file_extension',
     ];

     protected $casts = [
        'id' => 'integer',
        'model_name' => 'string',
        'model_id' => 'integer',
        'file_name' => 'string',
        'file_type' => 'string',
        'file_extension' => 'string',
    ];

     protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
     ];

     protected function getFileNameAttribute($value)
     {
         return Helper::FilePublicLink($value, POST_IMAGE_INFO);
     }
}
