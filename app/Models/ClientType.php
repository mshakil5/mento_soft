<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientType extends Model
{
    use SoftDeletes;

    public function clients()
    {
        return $this->hasMany(Client::class);
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
