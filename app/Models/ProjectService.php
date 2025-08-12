<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectService extends Model
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

    protected $casts = [
        'status' => 'boolean'
    ];

    public function clientProject()
    {
        return $this->belongsTo(ClientProject::class);
    }

    public function details()
    {
        return $this->hasMany(ProjectServiceDetail::class);
    }
    
}
