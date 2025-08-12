<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectServiceDetail extends Model
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

    public function projectService()
    {
        return $this->belongsTo(ProjectService::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function isPending()
    {
        $due = $this->transactions()
            ->where('transaction_type', 'Due')
            ->sum('amount');

        $received = $this->transactions()
            ->where('transaction_type', 'Received')
            ->sum('amount');

        return ($due - $received) > 0;
    }
}
