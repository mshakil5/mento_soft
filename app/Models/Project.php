<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{

    use SoftDeletes;

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
    }

    public function service ()
    {
        return $this->belongsTo(Service::class);
    }

    public function projectType ()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function projectSliders ()
    {
        return $this->hasMany(ProjectSlider::class);
    }
}
