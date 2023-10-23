<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $table = 'quotes';

    

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
}

