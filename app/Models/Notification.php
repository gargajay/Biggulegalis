<?php

namespace App\Models;

use App\Exceptions\PublicException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'send_by',
        'received_by',
        'title',
        'body',
        'data',
        'read',
        'type',
        'model_id',
        'model_name',
    ];

    protected $casts = [
        'send_by' => 'integer',
        'received_by' => 'integer',
        'title' => 'array',
        'body' => 'array',
        'data' => 'array',
        'read' => 'boolean',
        'type' => 'string',
        'model_id' => 'integer',
        'model_name'=> 'string',
    ];

    protected $hidden = [
        'deleted_at',
        'updated_at',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'send_by')->select(['id', 'full_name', 'image']);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by')->select(['id', 'full_name', 'image']);
    }


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $rules = [
                'send_by' => 'required|iexists:users,id',
                'received_by' => 'required|iexists:users,id',
                'title' => 'required|valid_json',
                'body' => 'required|valid_json',
                'data' => 'required|valid_json',
                'read' => 'nullable|boolean',
                'type' => 'required|string|max:255',
            ];
            // validate input data using the Validator method of the PublicException class
            PublicException::Validator($model->getAttributes(), $rules);
        });

        static::updating(function ($model) {
            $rules = [
                'send_by' => 'required|iexists:users,id',
                'received_by' => 'required|iexists:users,id',
                'title' => 'required|valid_json',
                'body' => 'required|valid_json',
                'data' => 'required|valid_json',
                'read' => 'nullable|boolean',
                'type' => 'required|string|max:255',
            ];

            // validate input data using the Validator method of the PublicException class
            PublicException::Validator($model->getAttributes(), $rules);
        });
    }


    protected function getTitleAttribute($value)
    {
        return isJson($value) ? customTrans(json_decode($value, true)) : '';
    }

    protected function getBodyAttribute($value)
    {
        return isJson($value) ? customTrans(json_decode($value, true)) : '';
    }

    protected function getDataAttribute($value)
    {
        return isJson($value) ? json_decode($value, true) : [];
    }
}
