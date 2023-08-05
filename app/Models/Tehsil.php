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

    // Define the relationship with DistrictBarAssociation model
    public function districtBarAssociation()
    {
        return $this->belongsTo(DistrictBarAssociation::class);
    }
}

