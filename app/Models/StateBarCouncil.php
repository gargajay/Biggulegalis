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

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
