<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Company;
use App\Models\GalleryEvent;
use App\Models\JobOpening;
use App\Support\SiteData;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

/**
 * sitemap.xml built from live admin data, so new or switched-off companies, jobs,
 * news posts and events are reflected automatically without editing a file.
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        foreach ([
            ['site.home', 'weekly', '1.0'],
            ['site.our-companies', 'weekly', '0.9'],
            ['site.about', 'monthly', '0.8'],
            ['site.team', 'monthly', '0.8'],
            ['site.careers', 'daily', '0.8'],
            ['site.blogs', 'weekly', '0.8'],
            ['site.contact', 'monthly', '0.8'],
        ] as [$route, $changefreq, $priority]) {
            $urls[] = $this->url(route($route), null, $changefreq, $priority);
        }

        // Companies (SiteData also covers the legacy list when the table is empty).
        $companyUpdatedAt = Schema::hasTable('companies')
            ? Company::query()->where('is_active', true)->pluck('updated_at', 'slug')
            : collect();
        foreach (SiteData::companies() as $company) {
            if (filled($company['slug'] ?? null)) {
                $urls[] = $this->url(route('site.company', $company['slug']), $companyUpdatedAt[$company['slug']] ?? null, 'monthly', '0.7');
            }
        }

        if (Schema::hasTable('job_openings')) {
            foreach (JobOpening::query()->where('is_active', true)->whereNotNull('slug')->orderBy('id')->get(['slug', 'updated_at']) as $job) {
                $urls[] = $this->url(route('site.careers.show', $job->slug), $job->updated_at, 'weekly', '0.7');
            }
        }

        if (Schema::hasTable('blog_posts')) {
            foreach (BlogPost::query()->where('is_active', true)->whereNotNull('slug')->orderByDesc('created_at')->get(['slug', 'updated_at']) as $post) {
                $urls[] = $this->url(route('site.blog-article', $post->slug), $post->updated_at, 'monthly', '0.6');
            }
        }

        if (Schema::hasTable('gallery_events')) {
            foreach (GalleryEvent::query()->where('is_active', true)->whereNotNull('slug')->orderBy('sort_order')->get(['slug', 'updated_at']) as $event) {
                $urls[] = $this->url(route('site.event', $event->slug), $event->updated_at, 'monthly', '0.5');
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
            .implode('', array_unique($urls))
            .'</urlset>'."\n";

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    protected function url(string $loc, ?Carbon $lastmod, string $changefreq, string $priority): string
    {
        return "    <url>\n"
            .'        <loc>'.htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8')."</loc>\n"
            .($lastmod ? '        <lastmod>'.$lastmod->toAtomString()."</lastmod>\n" : '')
            ."        <changefreq>{$changefreq}</changefreq>\n"
            ."        <priority>{$priority}</priority>\n"
            ."    </url>\n";
    }
}
