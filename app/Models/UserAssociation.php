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
        'permissions' => 'json',
        'roles' => 'json'
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

    public function getRolesAttribute($value){
        if(!empty($value)){
            $roles = json_decode($value);
           return GroupRole::whereIn('id',$roles)->get();
        }
        return $value;
    }

    public static function checkPresentExitInAssocation($association_id,$roles){
        //prisent id
        $prisent_id = 4 ;
        if(in_array($prisent_id,$roles)){
            $checkIsPresentInAssociation = UserAssociation::where('association_id',$association_id)->whereJsonContains('roles', $prisent_id)->first();
            return 'You cannot choose prisent role. it is allready their';
        }
        return false;
    }

    
}
