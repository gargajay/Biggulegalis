<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nette\SmartObject;

class Payment extends Model
{
    protected $table ='payments';
    use HasFactory;

    protected $fillable = [
        'r_payment_id',
        'method',
        'currency',
        'user_email',
        'user_id',
        'amount',
        'json_response',
        'document_id',
        'type'
    ];



    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}



