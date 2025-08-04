<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Master extends Model
{

    use SoftDeletes;

    public function softcode()
    {
        return $this->belongsTo(Softcode::class, 'softcode_id');
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
}
