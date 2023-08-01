<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class images extends Model
{

    use HasFactory;

    public function getImageAttribute($value = '')
    {
        if (!empty($value)) {
            return asset('/uploads/images/' . $value);
        }
        return asset('/images/default-profile.jpg');
    } 
}
