<?php

namespace App\Models;

use App\Helper\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'description', 'value'];

    protected function getValueAttribute($value)
    {
        $value = json_decode($value, true);
        if ($this->key == 'APP') {
            $value['app_icon'] = isset($value['app_icon']) ? $value['app_icon'] : null;
            $value['app_icon_old'] = $value['app_icon'];
            $value['app_icon'] = Helper::FilePublicLink($value['app_icon'], APP_IMAGE_INFO);
        }
        return $value;
    }



    public static function boot()
    {
        parent::boot();

        // Delete file when model is updated
        self::updated(function ($model) {

            if ($model->getRawOriginal('key') == 'APP' && $model->isDirty('value')) {
                $oldValue = json_decode($model->getRawOriginal('value'), true)['app_icon'] ?? null;
                $newValue = json_decode($model->getChanges()['value'], true)['app_icon'] ?? null;
                if ($oldValue && $oldValue != $newValue) {
                    Helper::FileDelete($oldValue, APP_IMAGE_INFO);
                }
            }
        });
    }
}
