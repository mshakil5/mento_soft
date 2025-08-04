<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDetail extends Model
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

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function clientProject()
    {
        return $this->belongsTo(ClientProject::class);
    }

}
