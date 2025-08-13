<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        return $this->subject('New Contact Message')
                    ->markdown('emails.contact')
                    ->with([
                        'first_name' => $this->contact->first_name,
                        'last_name'  => $this->contact->last_name,
                        'email'      => $this->contact->email,
                        'phone'      => $this->contact->phone,
                        'subject'    => $this->contact->subject ?? 'No Subject',
                        'message'    => $this->contact->message,
                        'product'    => $this->contact->product_id 
                                      ? \App\Models\Product::find($this->contact->product_id) 
                                      : null,
                    ]);
    }
}