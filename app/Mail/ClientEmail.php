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

    public function __construct($subject, $body, $serviceIds = [])
    {
        $this->subjectText = $subject;
        $this->bodyText = $body;
        $this->serviceIds = $serviceIds ?? [];
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
                'company' => $company
            ]);

            $mail->attachData($pdf->output(), 'invoice.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}