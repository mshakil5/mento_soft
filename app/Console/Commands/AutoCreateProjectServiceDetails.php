<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\ProjectServiceDetail;
use App\Models\Transaction;

class AutoCreateProjectServiceDetails extends Command
{
    protected $signature = 'project-service:auto-create';
    protected $description = 'Auto create next service details for due project services';

    public function handle()
    {
        $end = Carbon::today()->addDays(7)->format('Y-m-d');

        $details = ProjectServiceDetail::where('is_auto', 1)
            ->where('status', 1)
            ->where('bill_paid', 1)
            ->where('next_created', 0)
            ->where('next_start_date', '<=', $end)
            ->get();

        foreach ($details as $detail) {
            $newDetail = $detail->replicate();

            $startDate = Carbon::parse($detail->next_start_date)->format('Y-m-d');
            $endDate = Carbon::parse($detail->next_end_date)->format('Y-m-d');

            $newDetail->start_date = $startDate;
            $newDetail->end_date = $endDate;
            $newDetail->next_created = 0;
            $newDetail->bill_paid = 0;
            $newDetail->last_auto_run = now();
            $newDetail->created_at = now();
            $newDetail->updated_at = now();

            $nextStart = Carbon::parse($endDate)->addDay();
            if ($detail->cycle_type === 1) {
                $nextEnd = $nextStart->copy()->addMonthNoOverflow()->subDay();
            } else {
                $nextEnd = $nextStart->copy()->addYear()->subDay();
            }

            $newDetail->next_start_date = $nextStart->format('Y-m-d');
            $newDetail->next_end_date = $nextEnd->format('Y-m-d');

            $newDetail->save();

            $detail->next_created = 1;
            $detail->save();

            $transaction = new Transaction();
            $transaction->date = $startDate;
            $transaction->project_service_detail_id = $newDetail->id;
            $transaction->client_id = $newDetail->projectService?->clientProject?->client_id;
            $transaction->table_type = 'Income';
            $transaction->transaction_type = 'Due';
            $transaction->payment_type = 'Bank';
            $transaction->description = $newDetail->note ?? "Due for {$newDetail->projectService->name} for service period {$startDate} to {$endDate}";
            $transaction->amount = $newDetail->amount;
            $transaction->at_amount = $newDetail->amount;
            $transaction->created_by = $newDetail->created_by;
            $transaction->save();

            $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();
        }

        $this->info('Auto-create process completed.');
    }
}