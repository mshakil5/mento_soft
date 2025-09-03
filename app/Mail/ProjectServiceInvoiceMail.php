<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\ProjectServiceDetail;
use App\Models\CompanyDetails;

class ProjectServiceInvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $service;

    public function __construct(ProjectServiceDetail $service)
    {
        $this->service = $service;
    }

    public function build()
    {
        $company = CompanyDetails::first()->business_name ?? config('app.name');

        return $this->subject('Your Service Invoice from ' . $company)
                    ->view('emails.project-service-invoice');
    }
}