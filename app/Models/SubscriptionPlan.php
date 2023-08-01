<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category',
        'name',
        'min_users',
        'max_users',
        'duration',
        'interval',
        'price',
        'currency',
        'sort_order'
    ];

    protected $casts = [
        'category' => 'string',
        'name' => 'string',
        'min_users' => 'integer',
        'max_users' => 'integer',
        'duration' => 'integer',
        'interval' => 'integer',
        'price' => 'float',
        'currency' => 'string',
        'sort_order' => 'integer',
    ];

    protected $hidden = [
        'min_users',
        'max_users',
        'duration',
        'interval',
        'price',
        'currency',
        'sort_order',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function getDisplayAmountAttribute()
    {
        $symbol = SUBSCRIPTION_CURRENCY_SYMBOL[$this->currency];
        $price = number_format($this->price, 2);
        $duration = ($this->duration > 1) ? "{$this->duration} " : "";
        $interval = array_flip(SUBSCRIPTION_PLAN_INTERVAL)[$this->interval];
        $intervalText = words($interval);

        return "{$symbol}{$price} / {$duration}{$intervalText}";
    }


    public function getActivePlanAttribute()
    {
        return Auth::user()->default_subscription_plan_id == $this->id ? true : false;
    }



    public function getStatusAttribute()
    {
        if ($this->deleted_at === null) {
            return 'Active';
        } else {
            return 'Inactive';
        }
    }
}
