<?php

namespace App\Models\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            self::logActivity('created', $model);
        });

        static::updated(function ($model) {
            self::logActivity('updated', $model);
        });

        static::deleted(function ($model) {
            self::logActivity('deleted', $model);
        });
    }

    protected static function logActivity($action, $model)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => "{$action} {$model->getTable()} ID: {$model->id}",
        ]);
    }
}
