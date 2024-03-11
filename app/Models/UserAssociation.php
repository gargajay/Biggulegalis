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
            if ($association->permission_type == 2) {
                $userAssociation->status = 2;
                $userAssociation->save();
                $invitation = new Invitation();
                $invitation->user_id = Auth::id();
                $invitation->type = 'from_association';
                $invitation->msg = Auth::user()->full_name . ' send you request to join  in your association';
                $invitation->association_id = $userAssociation->association_id;
                $invitation->save();

                
            }

            //  self::handleRoles($userAssociation);


        });

        // static::updated(function ($userAssociation) {
        //     // Handle logic for updating records, if needed
        //     self::handleRoles($userAssociation);
        // });
    }

    protected static function handleRoles($userAssociation)
    {
        $roles =  $userAssociation->roles->pluck('id')->toArray() ?? [];
        //    dd($roles);
        // Check if roles is present and is an array
        if (isset($roles) && is_array($roles)) {
            // Check if roles contain values between 4 and 7
            if (collect($roles)->contains(fn ($role) => in_array($role, [4]))) {
                $userAssociation->permissions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11,12];
            } elseif (collect($roles)->contains(fn ($role) => in_array($role, [5, 6, 7]))) {
                $userAssociation->permissions = [1, 2, 3, 4, 6, 7, 8,12];
            }
            // Check if roles contain values 2, 3, or 8
            elseif (collect($roles)->contains(fn ($role) => in_array($role, [2, 3]))) {
                $userAssociation->permissions = [1, 3, 4, 8];
            } else {
                $userAssociation->permissions = [8];
            }

            return $userAssociation;
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



    protected $appends = ['association_name','country_id','state_id','dist_id','tehsil_id'];

    public function userRole()
    {
        return $this->belongsTo(GroupRole::class, 'user_role_id');
    }

    public function getAssociationNameAttribute()
    {
        $name = null;
        $name =  Association::where('id', $this->association_id)->pluck('name')->first();
        return $name;
    }

    public function getCountryIdAttribute(){
      return  $this->getParents(1) ?? 0;
    }

    public function getStateIdAttribute(){
        return  $this->getParents(2) ?? 0;
      }
      public function getDistIdAttribute(){
        return  $this->getParents(3) ?? 0;
      }

      public function getTehsilIdAttribute(){
        return  $this->getParents(4) ?? 0;
      }

    public function getRolesAttribute($value)
    {
        if (!empty($value)) {
            $roles = storeJsonArray($value);
            return GroupRole::whereIn('id', $roles)->get();
        }
        return $value;
    }

    public static function checkPresentExitInAssocation($association_id, $roles)
    {
        //prisent id
        $prisent_id = 4;
        // vice seceurty 6
        $secretary_id = 6;
        $joinsecretary_id = 7;
        // vice prisent 5
        $vprisent_id = 5;
        if (in_array($prisent_id, $roles)) {
            $checkIsPresentInAssociation = UserAssociation::where('association_id', $association_id)->whereJsonContains('roles', $prisent_id)->first();
            if ($checkIsPresentInAssociation && $checkIsPresentInAssociation->user_id != Auth::id()) {
                return 'You cannot choose the role of president as it has already been assigned.';
            }
        } elseif (in_array($vprisent_id, $roles)) {
            $checkIsPresentInAssociation = UserAssociation::where('association_id', $association_id)->whereJsonContains('roles', $vprisent_id)->first();
            if ($checkIsPresentInAssociation && $checkIsPresentInAssociation->user_id != Auth::id()) {
                return 'You cannot choose the role of vice president as it has already been assigned.';
            }
        } elseif (in_array($secretary_id, $roles)) {
            $checkIsPresentInAssociation = UserAssociation::where('association_id', $association_id)->whereJsonContains('roles', $secretary_id)->first();
            if ($checkIsPresentInAssociation && $checkIsPresentInAssociation->user_id != Auth::id()) {
                return 'You cannot choose the role of secretary as it has already been assigned.';
            }
        } elseif (in_array($joinsecretary_id, $roles)) {
            $checkIsPresentInAssociation = UserAssociation::where('association_id', $association_id)->whereJsonContains('roles', $joinsecretary_id)->first();
            if ($checkIsPresentInAssociation && $checkIsPresentInAssociation->user_id != Auth::id()) {
                return 'You cannot choose the role of  joint secretary as it has already been assigned..';
            }
        }
        return false;
    }

    public function getParents($type){
        $parentData =[
            'country_id' => 0,
            'state_id' => 0,
            'dist_id' => 0,
            'tehsil_id' =>0
        ];
        if($this->association_id){
            $assocation = Association::where('id',$this->association_id)->first();
            if($assocation->association_type==1){
                $parentData['country_id'] = $assocation->id;

            }elseif($assocation->association_type==2){
                $parentData['country_id'] = $assocation->parent_id;
                $parentData['state_id'] = $assocation->id;
                
            }
            elseif($assocation->association_type==3){
                $parentData['country_id'] = 1;
                $parentData['state_id'] = $assocation->parent_id;
                $parentData['dist_id'] = $assocation->id;
                
            }
            elseif($assocation->association_type==4){
                $parent = Association::where('id',$assocation->parent_id)->first();

                $parentData['country_id'] = 1;
                $parentData['state_id'] = $parent ? $parent->parent_id:0;
                $parentData['dist_id'] = $assocation->parent_id;
                $parentData['tehsil_id'] = $assocation->id;
               
            }

        }

        if($type==1){
           return $parentData['country_id'];
        }elseif($type==2){
            return $parentData['state_id'];
        }
        elseif($type==3){
            return $parentData['dist_id'];
        }
        elseif($type==4){
            return  $parentData['tehsil_id'];
        }
    }
}
