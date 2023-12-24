<?php

namespace App\Models;

use App\Helper\Helper;
use Illuminate\Database\Eloquent\Model;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class OldMember extends Model
{
    protected $table = 'old_members';

    protected $cast = [
        'roles' => 'json'
    ];

   

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];



    public function getRolesAttribute($value){
        if(!empty($value)){
            $roles = storeJsonArray($value);
           return GroupRole::whereIn('id',$roles)->get();
        }
        return $value;
    }


    public function getAssociationNameAttribute()
    {
        $name = null;
        $name =  Association::where('id',$this->association_id)->pluck('name')->first();
        return $name;

    }

    protected function getImageAttribute($value)
    {
        return Helper::FilePublicLink($value, USER_IMAGE_INFO);
    }



}
