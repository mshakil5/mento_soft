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

    public function serviceType()
    {
        return $this->belongsTo(ProjectService::class, 'project_service_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function project()
    {
        return $this->belongsTo(ClientProject::class, 'client_project_id');
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

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'project_service_detail_id')
                    ->latestOfMany();
    }

    public function renewal()
    {
        return $this->hasOne(ServiceRenewal::class, 'project_service_detail_id')->latest();
    }
}
