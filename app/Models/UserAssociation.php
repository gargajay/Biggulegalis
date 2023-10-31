<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class UserAssociation extends Model
{
    protected $table = 'user_associations';

   

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $cast = [
        'permissions' => 'json'
    ];

    protected $appends = ['association_name'];

    public function userRole()
    {
        return $this->belongsTo(GroupRole::class,'user_role_id');
    }

    public function getAssociationNameAttribute()
    {
        $name = null;
        $name =  Association::where('id',$this->association_id)->pluck('name')->first();
        return $name;

    }
}
