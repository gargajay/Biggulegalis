<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateBarCouncil extends Model
{
    protected $table = 'state_bar_councils';

    protected $fillable = [
        'name',
        'description',
        'status',
        'country_id',
    ];

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }
}
