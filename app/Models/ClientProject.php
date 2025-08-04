<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientProject extends Model
{

    use SoftDeletes; 
    
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

    public function getCompletedPercentageAttribute()
    {
        $total = $this->tasks()->count();
        $completed = $this->tasks()->where('status', 1)->count();

        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class);
    }
    
}
