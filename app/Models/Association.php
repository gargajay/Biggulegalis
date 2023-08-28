<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Association extends Model
{
    protected $table = 'associations';

    protected $fillable = [
        'name',
        'description',
        'status',
        'parent_id',
        'permission_type'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
        'permission_type',
        'association_type',

    ];

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
}
