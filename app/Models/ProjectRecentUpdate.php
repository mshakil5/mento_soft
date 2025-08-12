<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectRecentUpdate extends Model
{
    use SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at']);
    }

    protected $guarded = [];

    protected static function boot()
    {
      parent::boot();

      static::deleting(function ($model) {
        if (auth()->check()) {
          $model->deleted_by = auth()->id();
          $model->save();
        }
      });

      static::deleting(function ($model) {
          if ($model->attachment) {
              $path = public_path('images/recent-updates/' . $model->attachment);
              if (file_exists($path)) {
                  unlink($path);
              }
          }
      });
    }

    public function project()
    {
        return $this->belongsTo(ClientProject::class, 'client_project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
