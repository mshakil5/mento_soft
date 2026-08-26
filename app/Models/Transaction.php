<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use LogsActivity, SoftDeletes;


    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at']);
    }

    protected static function boot()
    {
        parent::boot();

        // 1. Your existing deleting logic
        static::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by = auth()->id();
                $model->save();
            }
        });

        // 2. NEW: Auto-calculate vat_amount and at_amount before saving
        static::saving(function ($model) {
            $amount = $model->amount ?? 0;
            $vatPercent = $model->vat_rate ?? 0;
            
            // Calculate vat_amount
            $model->vat_amount = ($amount * $vatPercent) / 100;
            
            // Calculate at_amount (Total Amount = Amount + VAT Amount)
            $model->at_amount = $amount + $model->vat_amount;
        });
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function equityHolder()
    {
        return $this->belongsTo(EquityHolder::class, 'share_holder_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function projectServiceDetail()
    {
        return $this->belongsTo(ProjectServiceDetail::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(ProjectServiceDetail::class, 'project_service_detail_id');
    }

    public function project()
    {
        return $this->belongsTo(ClientProject::class, 'client_project_id');
    }
}
