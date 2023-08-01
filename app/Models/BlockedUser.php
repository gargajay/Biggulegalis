<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedUser extends Model
{
    use HasFactory;

    protected $fillable = ['blocked_user_id', 'blockedBy_user_id'];

    protected $casts = [
        'id' => 'integer',
        'blocked_user_id' => 'integer',
        'blockedBy_user_id' => 'integer',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'blocked_user_id')->select('id', 'full_name', 'image');
    }
}
