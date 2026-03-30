<?php

namespace App\Http\Controllers;

use App\Support\SiteData;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home()
    {
        return view('site.home', [
            'companies' => SiteData::companies(),
            'featuredCompanies' => SiteData::featuredCompanies(),
        ]);
    }

    public function ourCompanies()
    {
        $divisionOrder = [
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

    public function company(string $slug)
    {
        $company = SiteData::companyBySlug($slug);
        abort_if(!$company, 404);

        return view('site.company', [
            'company' => $company,
        ]);
    }

    public function about()
    {
        return view('site.about');
    }

    public function team()
    {
        return view('site.team', [
            'team' => SiteData::team(),
        ]);
    }

    public function careers()
    {
        return view('site.careers', [
            'jobOpenings' => SiteData::careerOpenings(),
        ]);
    }

    public function blogs()
    {
        return view('site.blogs', [
            'title' => 'News & Media | LITUS Group',
            'blogPosts' => SiteData::blogPosts(),
            'blogCategories' => SiteData::blogCategories(),
            'galleryEvents' => SiteData::galleryEvents(),
        ]);
    }

    public function eventGallery(string $slug)
    {
        $events = SiteData::galleryEvents();
        $event = collect($events)->firstWhere('slug', $slug);
        abort_if(!$event, 404);

        return view('site.event-gallery', [
            'event' => $event,
        ]);
    }

    public function contact()
    {
        return view('site.contact', [
            'title' => 'Contact Us | LITUS Group',
        ]);
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
            'company' => ['nullable', 'string', 'max:255'],
        ]);

        return back()->with('status', 'Thank you! Your message has been received.')->withInput([]);
    }
}

