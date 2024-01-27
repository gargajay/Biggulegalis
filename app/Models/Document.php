<?php

namespace App\Models;

use App\Helper\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    public $appends = ['is_buy_me','payment_url'];

    protected $fillable = [
        'title',
        'description',
        'file',
        'price'
    ];

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'description' => 'string',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected function getFileAttribute($value)
    {
        return Helper::FilePublicLink($value, DOCUMENT_IMAGE_INFO);
    }


    public function getStatusAttribute()
    {
        if ($this->deleted_at === null) {
            return 'Active';
        } else {
            return 'Inactive';
        }
    }


    public function getIsBuyMeAttribute()
    {
        $d =  Payment::where(['user_id' => Auth::id(), 'id' => $this->id])->first();

        if (!empty($d)) {
            return true;
        }

        return false;
    }

    public function getPaymentUrlAttribute()
    {
        if($this->price>0){
           return $url = url('payment?document_id='). $this->id."&u_id=". base64_encode(Auth::id()??0);

        }

        return "";
    }
}
