<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use App\Models\Contact;
use App\Models\Master;
use App\Models\Service;
use App\Models\ClientReview;
use App\Models\Quotation;
use App\Models\ProjectType;
use App\Models\Project;
use App\Models\Product;
use Illuminate\Support\Str;
use SEOMeta;
use OpenGraph;
use Twitter;
use App\Models\CompanyDetails;

class FrontendController extends Controller
{

    public function contact()
    {
        $this->setDefaultSEO();
        
        return view('frontend.contact_us');
    }

    public function getQuotation()
    {
        $this->setDefaultSEO();

        return view('frontend.get_quotation');
    }

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

    public function storeQuotation(Request $request)
    {
        $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email',
            'phone'             => 'required',
            'company'           => 'required',
            'dream_description' => 'required',
        ]);

        Quotation::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'company'           => $request->company,
            'website'           => $request->website,
            'dream_description' => $request->dream_description,
            'timeline'          => $request->timeline,
            'features'          => $request->features ? json_encode($request->features) : null,
            'additional_info'   => $request->additional_info,
        ]);

        return back()->with('success', 'Your quotation request has been submitted successfully!');
    }

    public function index()
    { 
        $landingPage = Master::where('name', 'landing page')->first();
        $whyChooseUs = Master::where('name', 'Why Choose Us')->first();
        $ourFlexible = Master::where('name', 'our flexible')->first();
        $services = Service::where('status', 1)->latest()->limit(6)->get();
        $testimonials = ClientReview::where('status', 1)->latest()->get();
        $products = Product::with(['features' => function($q){
            $q->where('status', 1)->latest();
        }])->where('status', 1)->latest()->limit(8)->get();

        $this->setDefaultSEO();

        return view('frontend.index', compact('landingPage', 'whyChooseUs', 'ourFlexible', 'services', 'testimonials', 'products'));
    }

    public function portfolio()
    {
        $projectTypes = ProjectType::where('status', 1)
            ->whereHas('projects', function ($query) {
                $query->where('status', 1);
            })
            ->with(['projects' => function ($query) {
                $query->where('status', 1);
            }])
            ->get();

        $this->setDefaultSEO();

        return view('frontend.portfolio', compact('projectTypes'));
    }

    public function portfolioDetails($slug)
    {
        $project = Project::with('projectSliders')->where('slug', $slug)->where('status', 1)->firstOrFail();

        $technologies = $project->technologies_used
            ? array_filter(array_map('trim', explode(',', $project->technologies_used)))
            : [];

        $functionalFeatures = $project->functional_features
            ? array_filter(array_map('trim', explode(',', $project->functional_features)))
            : [];

        $company = CompanyDetails::first();

        $this->setDefaultSEO(
            $project->meta_title,
            $project->meta_description,
            $project->meta_keywords,
            $project->meta_image ? 'images/projects/meta/' . $project->meta_image : null
        );

        return view('frontend.project_details', compact('project', 'technologies', 'functionalFeatures'));
    }

    public function productDetails($slug)
    {
        $product = Product::with([
            'features' => function ($q) {
                $q->where('status', 1)->latest();
            },
            'clients' => function ($q) {
                $q->where('status', 1)->latest();
            },
            'faqs' => function ($q) {
                $q->where('status', 1)->latest();
            },
            'videos' => function ($q) {
                $q->where('status', 1)->latest();
            }
        ])
        ->where('slug', $slug)
        ->where('status', 1)
        ->firstOrFail();

        $this->setDefaultSEO(
            $product->meta_title,
            $product->meta_description,
            $product->meta_keywords,
            $product->meta_image ? 'images/products/meta/' . $product->meta_image : null
        );
        return view('frontend.product_details', compact('product'));
    }

    private function setDefaultSEO($title = null, $description = null, $keywords = null, $image = null)
    {
        $company = CompanyDetails::first();

        SEOMeta::setTitle($title ?? $company->meta_title ?? $company->company_name);
        SEOMeta::setDescription($description ?? $company->meta_description);
        SEOMeta::setKeywords($keywords ?? $company->meta_keywords);

        OpenGraph::setTitle($title ?? $company->company_name);
        OpenGraph::setDescription($description ?? $company->meta_description);
        if ($image ?? $company->meta_image) {
            OpenGraph::addImage(asset('images/company/meta/' . ($image ?? $company->meta_image)));
        }
    }

}
