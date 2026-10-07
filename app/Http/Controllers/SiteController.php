<?php

namespace App\Http\Controllers;

use App\Mail\JobApplicationMail;
use App\Models\BlogPost;
use App\Models\Company;
use App\Models\GalleryEvent;
use App\Models\JobOpening;
use App\Services\SeoService;
use App\Support\SiteData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class SiteController extends Controller
{
    public function home(SeoService $seo)
    {
        $seo->applyForPage('site.home', [
            'meta_title' => 'LITUS Group',
            'meta_description' => 'LITUS Group is a diversified business group spanning logistics, automotive, trading, construction, technology, retail, and hospitality—with a strong presence in the Maldives and the wider region.',
        ]);

        return view('site.home', [
            'companies' => SiteData::companies(),
            'heroSpotlightHighlights' => SiteData::heroSpotlightHighlights(),
        ]);
    }

    public function ourCompanies(SeoService $seo)
    {
        $seo->applyForPage('site.our-companies', [
            'meta_title' => 'Our Companies | LITUS Group',
            'meta_description' => 'Explore LITUS Group companies across corporate, logistics, automotive, trading, construction, technology, retail, and hospitality divisions.',
        ]);

        $divisionOrder = [
            'corporate',
            'logistics-shipping',
            'automotive',
            'trading',
            'construction',
            'technology-retail',
            'hospitality-lifestyle',
        ];

        return view('site.our-companies', [
            'divisions' => SiteData::divisions(),
            'companies' => SiteData::companies(),
            'divisionOrder' => $divisionOrder,
        ]);
    }

    public function company(SeoService $seo, string $slug)
    {
        $companyRow = Company::query()->where('slug', $slug)->first();
        $company = SiteData::companyBySlug($slug);
        abort_if(! $company, 404);

        // Companies with a bespoke design get their own view in site/companies/{slug}.blade.php.
        $customView = 'site.companies.'.$slug;
        if (view()->exists($customView)) {
            $seo->applyForPage('site.company.'.$slug, config('company_pages.'.$slug.'.seo', []));

            return view($customView, [
                'company' => $company,
                'companyRow' => $companyRow,
            ]);
        }

        if ($companyRow) {
            $seo->applyForCompany($companyRow);
        } else {
            $seo->applyForPage('site.company', [
                'meta_title' => ($company['name'] ?? 'Company').' | LITUS Group',
                'meta_description' => Str::limit(
                    strip_tags((string) (($company['tagline'] ?? '').' '.($company['description'] ?? ''))),
                    160
                ),
                'og_image' => SiteData::companyLogoUrl($company['logo'] ?? null),
            ]);
        }

        return view('site.company', [
            'company' => $company,
            'companyRow' => $companyRow,
        ]);
    }

    public function about(SeoService $seo)
    {
        $seo->applyForPage('site.about', [
            'meta_title' => 'About LITUS Group | Business Group in the Maldives',
            'meta_description' => 'Explore LITUS Group, a Maldives-based business group serving individuals, businesses and resorts through automotive, logistics, engineering, trading and more.',
        ]);

        return view('site.about');
    }

    public function team(SeoService $seo)
    {
        $seo->applyForPage('site.team', [
            'meta_title' => 'Our Team | LITUS Group',
            'meta_description' => 'Meet the people driving LITUS Group forward—leadership and experts across our operating companies.',
        ]);

        return view('site.team', [
            'team' => SiteData::team(),
        ]);
    }

    public function careers(SeoService $seo)
    {
        $seo->applyForPage('site.careers', [
            'meta_title' => 'Careers | LITUS Group',
            'meta_description' => 'Discover career opportunities at LITUS Group and our family of companies. Explore open roles and grow with us.',
        ]);

        return view('site.careers', [
            'jobOpenings' => SiteData::careerOpenings(),
        ]);
    }

    public function careerJob(SeoService $seo, string $slug)
    {
        $job = JobOpening::query()->where('slug', $slug)->first();
        abort_if(! $job, 404);

        // Closed roles send visitors (and search engines) back to the openings list.
        if (! $job->is_active) {
            return redirect()->route('site.careers', [], 301);
        }

        $seo->applyForJobOpening($job);

        $otherOpenings = array_values(array_filter(
            SiteData::careerOpenings(),
            fn (array $j) => $j['id'] !== (string) $job->id,
        ));

        return view('site.career-job', [
            'job' => collect(SiteData::careerOpenings())->firstWhere('id', (string) $job->id),
            'otherOpenings' => array_slice($otherOpenings, 0, 3),
        ]);
    }

    public function blogs(SeoService $seo)
    {
        $seo->applyForPage('site.blogs', [
            'meta_title' => 'News & Media | LITUS Group',
            'meta_description' => 'Latest news, stories, and media from LITUS Group—company updates, events, and insights.',
        ]);

        return view('site.blogs', [
            'blogPosts' => SiteData::blogPosts(),
            'blogCategories' => SiteData::blogCategories(),
            'galleryEvents' => SiteData::galleryEvents(),
        ]);
    }

    public function blogArticle(SeoService $seo, string $slug)
    {
        $post = BlogPost::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
        abort_if(! $post, 404);

        $seo->applyForBlogPost($post);

        return view('site.blog-article', [
            'post' => SiteData::blogPostToArray($post),
        ]);
    }

    public function eventGallery(SeoService $seo, string $slug)
    {
        $events = SiteData::galleryEvents();
        $event = collect($events)->firstWhere('slug', $slug);
        abort_if(! $event, 404);

        $eventModel = GalleryEvent::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if ($eventModel) {
            $seo->applyForGalleryEvent($eventModel);
        } else {
            $seo->applyForPage('site.event', [
                'meta_title' => ($event['title'] ?? 'Event').' | LITUS Group',
                'meta_description' => Str::limit(strip_tags((string) ($event['description'] ?? '')), 160),
                'og_image' => $event['image'] ?? null,
            ]);
        }

        return view('site.event-gallery', [
            'event' => $event,
        ]);
    }

    public function contact(SeoService $seo)
    {
        $seo->applyForPage('site.contact', [
            'meta_title' => 'Contact Us | LITUS Group',
            'meta_description' => 'Contact LITUS Group for general enquiries, partnerships, or division-specific support. We will respond as soon as we can.',
        ]);

        return view('site.contact');
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
            'company' => ['nullable', 'string', 'max:255'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
        ]);

        $recipient = config('mail.contact_to', config('mail.from.address'));

        // The main contact page's dropdown posts a company slug; show its proper name in the email.
        $companyName = $validated['company'] ?? null;
        if ($companyName && ($siteCompany = SiteData::companyBySlug($companyName))) {
            $companyName = $siteCompany['name'] ?? $companyName;
        }

        if (! empty($validated['company_id'])) {
            $company = Company::query()->select(['id', 'name', 'email'])->find($validated['company_id']);
            $companyName = $company?->name ?? $companyName;
            if ($company?->email) {
                $recipient = $company->email;
            }
        }

        try {
            Mail::mailer(config('mail.default', 'smtp'))->to($recipient)->send(new \App\Mail\CompanyContactMail(
                senderName: $validated['name'],
                senderEmail: $validated['email'],
                senderPhone: $validated['phone'] ?? null,
                messageBody: $validated['message'],
                companyName: $companyName,
            ));
        } catch (Throwable $e) {
            Log::error('Contact form mail failed', ['exception' => $e]);

            return back()
                ->withInput([])
                ->withErrors(['message' => 'We could not send your message. Please try again later.']);
        }

        return back()->with('status', 'Thank you! Your message has been received.')->withInput([]);
    }

    public function jobApplicationSubmit(Request $request)
    {
        $validated = $request->validate([
            'position' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'apply_title_locked' => ['nullable', 'in:0,1'],
            'cv' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx'],
        ]);

        $position = trim((string) ($validated['position'] ?? '')) ?: 'General application';
        $cv = $request->file('cv');

        $recipient = config('mail.careers_to');

        try {
            Mail::mailer(config('mail.default', 'smtp'))->to($recipient)->send(new JobApplicationMail(
                position: $position,
                name: $validated['name'],
                email: $validated['email'],
                phone: $validated['phone'] ?? null,
                cv: $cv,
            ));
        } catch (Throwable $e) {
            Log::error('Job application mail failed', ['exception' => $e]);

            return back()
                ->withInput($request->except('cv'))
                ->withErrors(['cv' => 'We could not send your application. Please try again later or contact HR directly.']);
        }

        return back()
            ->with('job_apply_success', 'Thank you! Your application has been submitted. We will get back to you shortly.');
    }
}
