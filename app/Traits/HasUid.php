<?php 

namespace App\Traits;

trait HasUid {
    
    /**
     * Bootstrap any application services.
     */
    public static function bootHasUid()
    {
        // Create uid when creating list.
        static::creating(function ($model) {
            // Create new uid
            $uid = uniqid();

            while (get_class($model)::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $model->uid = $uid;
        });
    }

    public function getRouteKeyName() {
        return 'uid';
    }
}