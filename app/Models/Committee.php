<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $table = 'committees';

    
    protected $appends =['member_list'];

    protected $fillable = [
        'name',
        'association_id',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function getMembersAttribute($value){
        if(!empty($value)){
            $roles = storeJsonArray($value);
           return $roles;
        }
        return $value;
    }

     public function getMemberListAttribute(){
        if(!empty($this->members)){
            $office =   User::with('userAssociation', 'addresses')->whereIn('id', $this->members)->latest()->get();
            return $office;
        }
        return [];
    }

    

    public function association()
    {
        return $this->belongsTo(Association::class,'association_id');
    }
}

