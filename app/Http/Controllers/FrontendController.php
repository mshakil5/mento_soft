<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use App\Mail\QuotationMail;
use App\Models\Contact;
use App\Models\Master;
use App\Models\Service;
use App\Models\Quotation;
use App\Models\ProjectType;
use App\Models\Project;
use App\Models\Product;
use Illuminate\Support\Str;
use SEOMeta;
use OpenGraph;
use Twitter;
use App\Models\CompanyDetails;
use App\Models\FaqQuestion;
use Validator;

class FrontendController extends Controller
{

    public function contact(Request $request)
    {
        $this->seo();

        $product = null;
        if ($request->product_id) {
            $product = Product::find($request->product_id);
        }

        $contact = Master::where('name', 'Contact Page')->where('softcode_id', 7)->first();
        if($contact){
            $this->seo(
                $contact->meta_title,
                $contact->meta_description,
                $contact->meta_keywords,
                $contact->meta_image ? asset('images/meta/' . $contact->meta_image) : null
            );
        }
        
        return view('frontend.contact_us', compact('product'));
    }

    public function getQuotation()
    {
        $quotation = Master::where('name', 'Quotation')->where('softcode_id', 7)->first();

        if($quotation){
            $this->seo(
                $quotation->meta_title,
                $quotation->meta_description,
                $quotation->meta_keywords,
                $quotation->meta_image ? asset('images/meta/' . $quotation->meta_image) : null
            );
        }

        return view('frontend.get_quotation');
    }

    public function storeContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:2|max:50',
            'last_name'  => 'required|string|min:2|max:50',
            'email'      => 'required|email|max:50',
            'product_id' => 'nullable|exists:products,id',
            'phone'             => ['required', 'regex:/^(?:\+44|0)(?:7\d{9}|1\d{9}|2\d{9}|3\d{9})$/'],
            'message'    => 'required|string|max:2000',
        ], [
            'phone.regex' => 'The phone number format is invalid. Use +44XXXXXXXXX or 0XXXXXXXXX.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->withFragment('contact');
        }

        $contact = new Contact();
        $contact->first_name = $request->input('first_name');
        $contact->last_name  = $request->input('last_name');
        $contact->email      = $request->input('email');
        $contact->phone      = $request->input('phone');
        $contact->subject    = $request->input('subject');
        $contact->message    = $request->input('message');
        $contact->product_id = $request->input('product_id');
        $contact->save();

        $contactEmails = ContactEmail::where('status', 1)->pluck('email');

        foreach ($contactEmails as $contactEmail) {
            Mail::mailer('gmail')->to($contactEmail)
              ->send(new ContactMail($contact)
            );
        }

        return back()->with('success', 'Your message has been sent successfully!')->withFragment('contact');
    }

    public function storeQuotation(Request $request)
    {
        $request->validate([
            'first_name'        => 'required|string|min:2|max:50',
            'last_name'         => 'required|string|min:2|max:50',
            'email'             => 'required|email|max:50',
            'phone'             => ['required', 'regex:/^(?:\+44|0)(?:7\d{9}|1\d{9}|2\d{9}|3\d{9})$/'],
            'company'           => 'nullable|string|max:50',
            'website'           => 'nullable|url|max:100',
            'dream_description' => 'nullable|string|max:1000',
            'timeline'          => 'nullable|string|max:255',
            'features'          => 'nullable|array',
            'features.*'        => 'string|max:100',
            'additional_info'   => 'nullable|string|max:2000',
        ], [
            'phone.regex' => 'The phone number format is invalid. Use +44XXXXXXXXX or 0XXXXXXXXX.',
        ]);

        $quotation = new Quotation();
        $quotation->first_name = $request->first_name;
        $quotation->last_name = $request->last_name;
        $quotation->email = $request->email;
        $quotation->phone = $request->phone;
        $quotation->company = $request->company;
        $quotation->website = $request->website;
        $quotation->dream_description = $request->dream_description;
        $quotation->timeline = $request->timeline;
        $quotation->features = $request->features ? json_encode($request->features) : null;
        $quotation->additional_info = $request->additional_info;
        $quotation->save();

        $contactEmails = ContactEmail::where('status', 1)->pluck('email');

        foreach ($contactEmails as $contactEmail) {
            Mail::mailer('gmail')->to($contactEmail)
              ->send(new QuotationMail($quotation)
            );
        }

        return back()->with('success', 'Your quotation request has been submitted successfully!');
    }

    public function index()
    {   
        $masters = Master::whereIn('name', ['landing page', 'Why Choose Us', 'our flexible'])->get();

        $landingPage = $masters->firstWhere('name', 'landing page');
        $whyChooseUs = $masters->firstWhere('name', 'Why Choose Us');
        $ourFlexible = $masters->firstWhere('name', 'our flexible');
        $services = Service::where('status', 1)
          ->orderByRaw('sl = 0, sl ASC')
          ->orderBy('id', 'desc')->limit(6)
          ->get();
        $products = Product::with(['features' => function($q){
            $q->where('status', 1)
              ->inRandomOrder()
              ->take(6);
        }])
          ->where('status', 1)
          ->orderByRaw('sl = 0, sl ASC')
          ->orderBy('id', 'desc')
          ->limit(8)
          ->get();

        $count = $products->count();
        if ($count % 2 != 0 && $count > 1) {
            $products = $products->slice(0, $count - 1);
        }

        $company = CompanyDetails::select('meta_title', 'meta_description', 'meta_keywords', 'meta_image', 'company_name')->first();
        $metaData = Master::where('name', 'Home')->where('softcode_id', 7)->first();
        $companyBusinessName = $company?->business_name ?? 'Mento Software';

        $this->seo(
            $metaData?->meta_title ?? '',
            $metaData?->meta_description ?? '',
            $metaData?->meta_keywords ?? '',
            $metaData?->meta_image ? asset('images/meta/' . $company->meta_image) : null
        );

        return view('frontend.index', compact('landingPage', 'whyChooseUs', 'ourFlexible', 'services', 'products', 'companyBusinessName'));
    }

    public function portfolio()
    {
        $projectTypes = ProjectType::where('status', 1)
            ->whereHas('projects', function ($query) {
                $query->where('status', 1);
            })
            ->with(['projects' => function ($query) {
                $query->where('status', 1)
                      ->orderByRaw('sl = 0, sl ASC')
                      ->orderBy('id', 'desc');
            }])
            ->orderByRaw('sl = 0, sl ASC')
            ->orderBy('id', 'desc')
            ->get();

        $portfolio = Master::where('name', 'Portfolio')->where('softcode_id', 7)->first();
        if($portfolio){
            $this->seo(
                $portfolio->meta_title,
                $portfolio->meta_description,
                $portfolio->meta_keywords,
                $portfolio->meta_image ? asset('images/meta/' . $portfolio->meta_image) : null
            );
        }

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

        $this->seo(
            $project->meta_title,
            $project->meta_description,
            $project->meta_keywords,
            $project->meta_image ? asset('images/projects/meta/' . $project->meta_image) : null
        );

        return view('frontend.project_details', compact('project', 'technologies', 'functionalFeatures'));
    }

    public function productDetails($slug)
    {
        $product = Product::with([
            'features' => function ($q) {
                $q->where('status', 1)
                  ->orderByRaw('sl = 0, sl ASC')
                  ->orderBy('id', 'desc');
            },
            'clients' => function ($q) {
                $q->where('status', 1)
                  ->orderByRaw('sl = 0, sl ASC')
                  ->orderBy('id', 'desc');
            },
            'faqs' => function ($q) {
                $q->where('status', 1)
                  ->orderByRaw('sl = 0, sl ASC')
                  ->orderBy('id', 'desc');
            },
            'videos' => function ($q) {
                $q->where('status', 1)->latest();
            }
        ])
        ->where('slug', $slug)
        ->where('status', 1)
        ->firstOrFail();

        $otherProducts = Product::with(['features' => function($q) {
            $q->where('status', 1)
              ->inRandomOrder()
              ->take(6);
        }])
          ->where('status', 1)
          ->where('id', '!=', $product->id)
          ->orderByRaw('sl = 0, sl ASC')
          ->orderBy('id', 'desc')
          ->limit(8)
          ->get();

        if ($otherProducts->count() % 2 != 0) {
            $otherProducts = $otherProducts->slice(0, $otherProducts->count() - 1);
        }

        $this->seo(
            $product->meta_title,
            $product->meta_description,
            $product->meta_keywords,
            $product->meta_image ? asset('images/products/meta/' . $product->meta_image) : null
        );
        return view('frontend.product_details', compact('product', 'otherProducts'));
    }

    private function seo($title = null, $description = null, $keywords = null, $image = null)
    {
        if ($title) {
            SEOMeta::setTitle($title);
            OpenGraph::setTitle($title);
        }

        if ($description) {
            SEOMeta::setDescription($description);
            OpenGraph::setDescription($description);
        }

        if ($keywords) {
            SEOMeta::setKeywords($keywords);
        }

        if ($image) {
            OpenGraph::addImage($image);
        }
    }

    public function privacyPolicy()
    {
        $companyDetails = CompanyDetails::select('privacy_policy')->first();


        $meta = Master::where('name', 'Privacy')->where('softcode_id', 7)->first();
        if($meta){
            $this->seo(
                $meta->meta_title,
                $meta->meta_description,
                $meta->meta_keywords,
                $meta->meta_image ? asset('images/meta/' . $meta->meta_image) : null
            );
        }


        return view('frontend.privacy', compact('companyDetails'));
    }

    public function termsAndConditions()
    {
        $companyDetails = CompanyDetails::select('terms_and_conditions')->first();
        $meta = Master::where('name', 'Terms')->where('softcode_id', 7)->first();
        if($meta){
            $this->seo(
                $meta->meta_title,
                $meta->meta_description,
                $meta->meta_keywords,
                $meta->meta_image ? asset('images/meta/' . $meta->meta_image) : null
            );
        }

        return view('frontend.terms', compact('companyDetails'));
    }

    public function frequentlyAskedQuestions()
    {
        $faqQuestions = FaqQuestion::orderBy('id', 'asc')->get();

        
        $meta = Master::where('name', 'Faq')->where('softcode_id', 7)->first();
        if($meta){
            $this->seo(
                $meta->meta_title,
                $meta->meta_description,
                $meta->meta_keywords,
                $meta->meta_image ? asset('images/meta/' . $meta->meta_image) : null
            );
        }



        return view('frontend.faq', compact('faqQuestions'));
    }

    public function sitemap()
    {
        $urls = [
            ['loc' => url('/'), 'lastmod' => now()->toDateString(), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => url('/about-us'), 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.8'],
        ];

        $products = Product::where('status', '1')->latest()->get();
        foreach ($products as $product) {
            $urls[] = [
                'loc' => url('/product/' . $product->slug),
                'lastmod' => $product->updated_at->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];
        }

        $services = Service::where('status', '1')->latest()->get();
        foreach ($services as $service) {
            $urls[] = [
                'loc' => url('/service/' . $service->slug),
                'lastmod' => $service->updated_at->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];
        }

        $content = view('frontend.sitemap', compact('urls'))->render();
        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}