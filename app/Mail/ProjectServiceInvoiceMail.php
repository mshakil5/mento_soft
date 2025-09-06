<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\ProjectServiceDetail;
use App\Models\CompanyDetails;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $pdf = Pdf::loadView('emails.project-service-invoice', [
            'service' => $this->service,
            'company' => CompanyDetails::first()
        ]);

        return $this->subject('Your Service Invoice from ' . $company)
                    ->view('emails.invoice-message')
                    ->attachData($pdf->output(), 'invoice.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
