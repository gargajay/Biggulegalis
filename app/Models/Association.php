<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Association extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'associations';

    protected $fillable = [
        'name',
        'description',
        'status',
        'parent_id',
        'permission_type',
        'location'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
        'permission_type',

    ];

    protected $appends = ['is_child'];

    public function country()
    {
        return $this->belongsTo(self::class,'parent_id')->where('association_type',1);
    }

    public function stateBarCouncil()
    {
        return $this->belongsTo(self::class,'parent_id')->where('association_type',2)->with('country');
    }

    public function districtBarAssociation()
    {
      return $this->belongsTo(self::class,'parent_id')->where('association_type',3)->with('stateBarCouncil');
    }

    // public function getStatusAttribute()
    // {
    //     if ($this->deleted_at === null) {
    //         return 'Active';
    //     } else {
    //         return 'Inactive';
    //     }
    // }

    public function getParentAttribute()
    {
        $value = $this->parent_id;
        if($value==0){
            return 'NA';
        }

       $r = Association::where('id',$value)->first();
       return $r->name ?? 'NA';
    }

    public function getTypeAttribute($value)
    {
        $value = $this->association_type;
        if ($value===1) {
            
            return 'Country Association';
        } else if($value=='2') {
            return 'State Assocation';
        }
        else if($value=='3') {
            return 'Dist Assocation';
        }else if($value=='4') {
            return 'Teshil/Other';
        }
    }

    public static function assocationTypes(){
        return [
            'Country Association',
            'State Assocation',
            'Dist Assocation',
            'Teshil/Other'
        ];
    }

    public static function getAllAssociations(){
       return Association::select('id','name')->get();
    }

    public static function permissonTypes(){
        return [
            'Open',
            'Invite only',
            'Close',
        ];
    }

    public function getIsChildAttribute(){
       $exit =  Association::where('parent_id',$this->id)->first();
       if(!empty($exit)){
        return true;
       }
       return false;
    }
}
