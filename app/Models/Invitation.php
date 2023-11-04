<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $table = 'invitations';

    

   
    protected $hidden = [
        'deleted_at',
        'updated_at',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class,'association_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
   
}

