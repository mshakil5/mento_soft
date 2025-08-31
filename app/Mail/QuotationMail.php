<?php

namespace App\Mail;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    public function build()
    {
        return $this->subject('New Quotation Message')
                    ->markdown('emails.quotation')
                    ->with([
                        'first_name' => $this->quotation->first_name,
                        'last_name'  => $this->quotation->last_name,
                        'email'      => $this->quotation->email,
                        'phone'      => $this->quotation->phone,
                        'subject'    => $this->quotation->subject ?? 'No Subject',
                        'message'    => $this->quotation->message,
                        'company'    => $this->quotation->company,
                        'website'    => $this->quotation->website,
                        'dream_description'    => $this->quotation->dream_description,
                        'timeline'    => $this->quotation->timeline,
                        'additional_info'    => $this->quotation->additional_info,
                    ]);
    }
}
