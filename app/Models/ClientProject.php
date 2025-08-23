<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClientProject extends Model
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
    
    protected static function boot()
    {
      parent::boot();

      static::deleting(function ($model) {
        if (auth()->check()) {
          $model->deleted_by = auth()->id();
          $model->save();
        }
      });
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function recentUpdates()
    {
        return $this->hasMany(ProjectRecentUpdate::class, 'client_project_id');
    }

    public function getCompletedPercentageAttribute()
    {
        $total = $this->tasks()->count();
        $completed = $this->tasks()->where('status', 3)->count();

        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class);
    }
    
    public function services()
    {
        return $this->hasMany(ProjectService::class);
    }
    
}
