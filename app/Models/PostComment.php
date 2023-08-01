<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PostComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'comment',
    ];
    protected $appends = [
        'my_comment'
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'post_id' => 'integer',
        'comment' => 'string',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->select('id', 'full_name', 'email', 'image');
    }

    public function getMyCommentAttribute()
    {
        $check = PostComment::where(['user_id' => Auth::id(), 'id' => $this->id])->first();
        return !empty($check) ? true : false;
    }
}
