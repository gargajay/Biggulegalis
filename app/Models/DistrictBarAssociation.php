<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictBarAssociation extends Model
{
    protected $table = 'district_bar_associations';

    protected $fillable = [
        'name',
        'description',
        'status',
        'state_bar_council_id',
    ];

    public function stateBarCouncil()
    {
        return $this->belongsTo('App\Models\StateBarCouncil');
    }
}
