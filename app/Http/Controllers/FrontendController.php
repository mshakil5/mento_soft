<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use App\Models\Contact;

class FrontendController extends Controller
{
    public function storeContact(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone'      => 'required',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = new Contact();
        $contact->first_name  = $request->input('first_name');
        $contact->last_name  = $request->input('last_name');
        $contact->email = $request->input('email');
        $contact->phone = $request->input('phone');
        $contact->subject = $request->input('subject');
        $contact->message = $request->input('message');
        $contact->save();

        $contactEmails = ContactEmail::where('status', 1)->pluck('email');

        foreach ($contactEmails as $contactEmail) {
          Mail::to($contactEmail)->send(new ContactMail($contact));
      }

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function index()
    { 
        return view('frontend.index');
    }
}
