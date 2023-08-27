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

    protected $appends = ['association_name'];

    public function userRole()
    {
        return $this->belongsTo(GroupRole::class,'user_role_id');
    }

    public function getAssociationNameAttribute()
    {
        $name = null;
        if($this->association_type==ASSOCIATION_TYPE['bar_council_of_india']){
            $name =  Country::where('id',$this->association_id)->pluck('name')->first();
        }else if($this->association_type==ASSOCIATION_TYPE['state_bar_councils']){
            $name =  StateBarCouncil::where('id',$this->association_id)->pluck('name')->first();
        }
        else if($this->association_type==ASSOCIATION_TYPE['district_bar_councils']){
            $name =  DistrictBarAssociation::where('id',$this->association_id)->pluck('name')->first();
        }
        else if($this->association_type==ASSOCIATION_TYPE['tehsil']){
            $name =  Tehsil::where('id',$this->association_id)->pluck('name')->first();
        }

        return $name;

    }
}
