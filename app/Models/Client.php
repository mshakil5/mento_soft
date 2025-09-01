<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clientType()
    {
        return $this->belongsTo(ClientType::class);
    }

    public function projects()
    {
        return $this->hasMany(ClientProject::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(ClientEmailLog::class, 'client_id');
    }

    public function invoiceEmails()
    {
        return $this->emailLogs()->whereNotNull('invoice_id');
    }

    public function services()
    {
        return $this->hasMany(ProjectServiceDetail::class);
    }
    
}
