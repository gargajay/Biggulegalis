<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tehsil extends Model
{
    protected $table = 'tehsils';

    protected $fillable = [
        'name',
        'description',
        'status',
        'district_bar_association_id',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function districtBarAssociation()
    {
        return $this->belongsTo(DistrictBarAssociation::class)->with('stateBarCouncil');
    }
   
}

