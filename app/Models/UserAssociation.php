<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class UserAssociation extends Model
{
    protected $table = 'user_associations';

    protected static function boot()
    {
        parent::boot();

        static::created(function ($userAssociation) {
         $association = Association::find($userAssociation->association_id);
         if($association->permission_type ==2){
            $userAssociation->status = 2;
            $userAssociation->save();
            $invitation = new Invitation();
            $invitation->user_id = Auth::id();
            $invitation->msg = Auth::user()->full_name.' send you request to join  in your association';
            $invitation->association_id = $userAssociation->association_id;
            $invitation->save();
         }

         self::handleRoles($userAssociation);


        });

        static::updated(function ($userAssociation) {
            // Handle logic for updating records, if needed
            self::handleRoles($userAssociation);
        });
    }

    protected static function handleRoles($userAssociation)
    {
        // Check if roles is present and is an array
        if (isset($userAssociation->roles) && is_array($userAssociation->roles)) {
            // Check if roles contain values between 4 and 7
            if (collect($userAssociation->roles)->contains(fn ($role) => in_array($role, [4,5,6,7]))) {
                $userAssociation->permissions = [1,2,3,4,5,6,7,8,9,10,11];
            }
            // Check if roles contain values 2, 3, or 8
            elseif (collect($userAssociation->roles)->contains(fn ($role) => in_array($role, [2, 3]))) {
                $userAssociation->permissions = [1,3,4,8];
            }else{
                $userAssociation->permissions = [3,4,8]; 
            }

            // Save the changes
            $userAssociation->save();
        }
    }


   

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
            $roles = storeJsonArray($value);
           return GroupRole::whereIn('id',$roles)->get();
        }
        return $value;
    }

    public static function checkPresentExitInAssocation($association_id,$roles){
        //prisent id
        $prisent_id = 4 ;
        if(in_array($prisent_id,$roles)){
            $checkIsPresentInAssociation = UserAssociation::where('association_id',$association_id)->whereJsonContains('roles', $prisent_id)->first();
            if($checkIsPresentInAssociation){
                return 'You cannot choose prisent role. it is allready their';

            }
            return false;
        }
        return false;
    }


}
