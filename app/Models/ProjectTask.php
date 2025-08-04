<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTask extends Model
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

    public function clientProject()
    {
        return $this->belongsTo(ClientProject::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
    
}
