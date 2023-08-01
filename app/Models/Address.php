<?php

namespace App\Models;

use App\Exceptions\PublicException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'zip',
        'latitude',
        'longitude',
        'geolocation',
    ];

    protected $casts = [
        'id' => 'integer',
        'type' => 'string',
        'address_line_1' => 'string',
        'address_line_2' => 'string',
        'city' => 'string',
        'state' => 'string',
        'country' => 'string',
        'zip' => 'string',
        'latitude' => 'string',
        'longitude' => 'string',
        'geolocation' => 'string',
    ];

    protected $hidden = [
        'type',
        'geolocation',
        'deleted_at',
        'created_at',
        'updated_at',
    ];


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $rules = [
                'type' => 'nullable|string|max:255',
                'address_line_1' => 'nullable|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'zip' => 'nullable|string|max:255',
                'latitude' => 'nullable|latitude|max:50',
                'longitude' => 'nullable|longitude|max:50',
            ];

            // validate input data using the Validator method of the PublicException class
            PublicException::Validator($model->getAttributes(), $rules);
        });

        static::updating(function ($model) {
            $rules = [
                'id' => 'required|numeric|positive_integer|iexists:addresses,id',
                'type' => 'nullable|string|max:255',
                'address_line_1' => 'nullable|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'zip' => 'nullable|string|max:255',
                'latitude' => 'nullable|latitude|max:50',
                'longitude' => 'nullable|longitude|max:50',
            ];

            // validate input data using the Validator method of the PublicException class
            PublicException::Validator($model->getAttributes(), $rules);
        });
    }


    public function setCityAttribute($value)
    {
        $this->attributes['city'] = ucwords(strtolower($value ?? ''));
    }

    public function setStateAttribute($value)
    {
        $this->attributes['state'] = ucwords(strtolower($value ?? ''));
    }

    public function setCountryAttribute($value)
    {
        $this->attributes['country'] = ucwords(strtolower($value ?? ''));
    }
}
