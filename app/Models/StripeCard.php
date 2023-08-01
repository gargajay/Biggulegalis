<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripeCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'stripe_card_id',
        'name_on_card',
        'card_last_four',
        'digit_length',
        'card_expiry_month',
        'card_expiry_year',
        'is_active',
    ];

    protected $hidden = [
        'user_id',
        'stripe_card_id',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'card_last_four' => 'integer',
        'digit_length' => 'integer',
        'card_expiry_month' => 'integer',
        'card_expiry_year' => 'integer',
        'is_active' => 'boolean',
    ];


    protected function getCardDigitAttribute($value)
    {
        return str_repeat("*", $this->digit_length - strlen($this->card_last_four)) . $this->card_last_four; // Add 12 stars before the last four digits
    }
}
