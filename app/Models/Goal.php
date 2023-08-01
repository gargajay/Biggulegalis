<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goal extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'title',
     ];
     protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
     ];
     public function getStatusAttribute()
    {
        if ($this->deleted_at === null) {
            return 'Active';
        } else {
            return 'Inactive';
        }
    }
}
