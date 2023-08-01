<?php

namespace App\Models;

use App\Helper\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'image',
    ];

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'image' => 'string',
    ];


    protected $appends = [
        'likes_count', 'comments_count', 'is_liked', 'my_post', 'my_comment'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];



    public function user()
    {
        return $this->belongsTo(User::class)->select('id', 'full_name', 'image', 'fitness_level');
    }


    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'model_id')->where('model_name', 'Post')->latest();
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            $model->likes()->delete();
            $model->comments()->delete();
        });
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    public function getMyPostAttribute()
    {
        $userId = auth()->id() ?? 0;
        return $this->user_id === $userId;
    }


    public function getIsLikedAttribute()
    {
        $check = PostLike::where(['user_id' => Auth::id(), 'post_id' => $this->id])->first();
        return !empty($check) ? true : false;
    }

    public function getMyCommentAttribute()
    {
        $check = PostComment::where(['user_id' => Auth::id(), 'post_id' => $this->id])->first();
        return !empty($check) ? true : false;
    }
}
