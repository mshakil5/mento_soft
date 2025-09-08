<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ProjectServiceDetail;
use App\Models\CompanyDetails;

class ClientEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;
    public $bodyText;
    public $serviceIds;
    public $invoice;

    public function __construct($subject = null, $body = null, $serviceIds = [], $invoice = null)
    {
        $company = CompanyDetails::first();

        $this->subjectText = $subject ?? 'Invoice from ' . ($company->business_name ?? config('app.name'));
        $this->bodyText    = $body ?? ($company->mail_footer ?? 'Thank you');
        $this->serviceIds  = $serviceIds ?? [];
        $this->invoice     = $invoice;
    }

    public function build()
    {
        $mail = $this->subject($this->subjectText)
                     ->markdown('emails.client-email')
                     ->with([
                         'subject' => $this->subjectText,
                         'body'    => $this->bodyText,
                     ]);

        if(!empty($this->serviceIds)) {
            $services = ProjectServiceDetail::whereIn('id', $this->serviceIds)->get();
            $company = CompanyDetails::first();

            $pdf = Pdf::loadView('emails.project-service-invoice', [
                'services' => $services,
                'company'  => $company
            ]);

            $mail->attachData($pdf->output(), 'service_invoice.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        if ($this->invoice) {
            $company = CompanyDetails::first();

            $pdf = Pdf::loadView('emails.invoice-pdf', [
                'invoice' => $this->invoice,
                'company' => $company
            ]);

            $mail->attachData($pdf->output(), 'invoice.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}