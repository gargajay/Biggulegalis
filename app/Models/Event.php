<?php

namespace App\Models;

use App\Helper\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'start',
        'end',
        'image',
    ];

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'start' => 'string',
        'end' => 'string',
        'image' => 'string',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected function setStartAttribute($value)
    {
        $this->attributes['start'] = Carbon::parse($value)->format('Y-m-d H:i:00');
    }

    protected function setEndAttribute($value)
    {
        $this->attributes['end'] = Carbon::parse($value)->format('Y-m-d H:i:00');
    }

    protected function getImageAttribute($value)
    {
        return Helper::FilePublicLink($value, EVENT_IMAGE_INFO);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class,EventMember::class)->select('users.id', 'full_name','email','image')->where('event_members.deleted_at', null);
    }
}
