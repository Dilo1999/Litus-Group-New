<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Company;
use App\Models\GalleryEvent;
use App\Models\PageSeo;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeoService
{
    protected string $defaultOgImage;

    protected string $siteName;

    public function __construct()
    {
        $this->defaultOgImage = asset('images/content/cta2.jpg');
        $this->siteName = config('seotools.opengraph.defaults.site_name', 'LITUS Group');
    }

    /**
     * Resolve a stored public-disk path or absolute URL to a full URL for meta tags.
     */
    public function absoluteImageUrl(?string $pathOrUrl): string
    {
        if ($pathOrUrl === null || $pathOrUrl === '') {
            return $this->defaultOgImage;
        }
        if (Str::startsWith($pathOrUrl, ['http://', 'https://'])) {
            return $pathOrUrl;
        }

        return Storage::disk('public')->url($pathOrUrl);
    }

    /**
     * Apply SEO for a static page. Uses admin-configured PageSeo when available,
     * otherwise falls back to provided defaults.
     */
    public function applyForPage(string $routeName, array $defaults = []): void
    {
        $pageSeo = PageSeo::forRoute($routeName);
        $url = url()->current();

        $metaTitle = $pageSeo?->meta_title ?? $defaults['meta_title'] ?? null;
        $metaDesc = $pageSeo?->meta_description ?? $defaults['meta_description'] ?? null;
        $ogTitle = $pageSeo?->og_title ?? $defaults['og_title'] ?? $metaTitle;
        $ogDesc = $pageSeo?->og_description ?? $defaults['og_description'] ?? $metaDesc;
        $ogImage = $this->absoluteImageUrl($pageSeo?->og_image ?? $defaults['og_image'] ?? null);
        $twTitle = $pageSeo?->twitter_title ?? $defaults['twitter_title'] ?? $ogTitle;
        $twDesc = $pageSeo?->twitter_description ?? $defaults['twitter_description'] ?? $ogDesc;
        $twImageRaw = $pageSeo?->twitter_image ?? $defaults['twitter_image'] ?? null;
        $twImage = $twImageRaw ? $this->absoluteImageUrl($twImageRaw) : $ogImage;
        $canonical = $pageSeo?->canonical_url ?? $defaults['canonical'] ?? $url;
        $robots = $pageSeo?->robots ?? $defaults['robots'] ?? null;

        $this->applyCoreMeta($metaTitle, $metaDesc, $canonical, $robots);
        $this->applyOpenGraphWebsite($url, $ogTitle, $ogDesc, $ogImage);
        $this->applyTwitter($twTitle, $twDesc, $twImage);
        $this->applyWebPageJsonLd($metaTitle, $metaDesc, $url, $ogImage);
    }

    public function applyForBlogPost(BlogPost $post): void
    {
        $url = url()->current();

        $metaTitle = $post->meta_title
            ?: ($post->title.' | '.$this->siteName);
        $metaDesc = $post->meta_description
            ?: $this->plainDescription($post->excerpt)
            ?: $this->plainDescription($post->content);

        $ogTitleSync = $post->og_title ?: $metaTitle;
        $ogDescSync = $post->og_description ?: $metaDesc;

        $ogImagePath = $post->og_image ?: $post->image;
        $ogImage = $this->absoluteImageUrl($ogImagePath);

        $twTitle = $post->twitter_title ?: $ogTitleSync;
        $twDesc = $post->twitter_description ?: $ogDescSync;
        $twImage = $post->twitter_image
            ? $this->absoluteImageUrl($post->twitter_image)
            : $ogImage;

        $canonical = $post->canonical_url ?: $url;
        $robots = $post->robots;

        $this->applyCoreMeta($metaTitle, $metaDesc, $canonical, $robots);

        OpenGraph::setSiteName($this->siteName);
        OpenGraph::setUrl($url);
        OpenGraph::setTitle($ogTitleSync);
        OpenGraph::setDescription($ogDescSync);
        OpenGraph::addImage($ogImage);
        OpenGraph::setType('article');

        $articleAttrs = array_filter([
            'published_time' => $post->published_at?->toIso8601String(),
            'modified_time' => $post->updated_at?->toIso8601String(),
            'author' => $post->author,
            'section' => $post->category,
        ]);
        if ($articleAttrs !== []) {
            OpenGraph::setArticle($articleAttrs);
        }

        $cardType = ($post->twitter_image || $post->og_image || $post->image)
            ? 'summary_large_image'
            : 'summary';
        TwitterCard::setType($cardType);
        TwitterCard::setTitle($twTitle);
        TwitterCard::setDescription($twDesc);
        if ($twImage) {
            TwitterCard::setImages([$twImage]);
        }

        JsonLdMulti::setType('BlogPosting');
        JsonLdMulti::setTitle($metaTitle);
        JsonLdMulti::setUrl($url);
        if ($metaDesc) {
            JsonLdMulti::setDescription($metaDesc);
        }
        JsonLdMulti::addImage($ogImage);
        JsonLdMulti::addValue('headline', $post->title);
        if ($post->published_at) {
            JsonLdMulti::addValue('datePublished', $post->published_at->toIso8601String());
        }
        if ($post->updated_at) {
            JsonLdMulti::addValue('dateModified', $post->updated_at->toIso8601String());
        }
        if ($post->author) {
            JsonLdMulti::addValue('author', [
                '@type' => 'Person',
                'name' => $post->author,
            ]);
        }
    }

    public function applyForCompany(Company $company): void
    {
        // Only Blog Posts get per-record SEO. Company pages use the Page SEO
        // record for route `site.company`, with these values as safe defaults.
        $this->applyForPage('site.company', [
            'meta_title' => $company->name.' | '.$this->siteName,
            'meta_description' => $this->plainDescription($company->description)
                ?: $this->plainDescription($company->tagline),
            'og_image' => $this->absoluteImageUrl($company->about_image ?: $company->logo),
        ]);
    }

    public function applyForGalleryEvent(GalleryEvent $event): void
    {
        // Gallery event pages also use the static Page SEO record for
        // `site.event`. Event-specific text is only used as a fallback.
        $this->applyForPage('site.event', [
            'meta_title' => $event->title.' | '.$this->siteName,
            'meta_description' => $this->plainDescription($event->description),
            'og_image' => $this->absoluteImageUrl($event->cover_image),
        ]);
    }

    /**
     * Raw head output from SEOTools (meta + opengraph + twitter + json-ld).
     */
    public function headHtml(): string
    {
        return SEOTools::generate();
    }

    protected function applyCoreMeta(?string $metaTitle, ?string $metaDesc, string $canonical, ?string $robots): void
    {
        if ($metaTitle) {
            SEOMeta::setTitle($metaTitle);
        }
        if ($metaDesc) {
            SEOMeta::setDescription($metaDesc);
        }
        SEOMeta::setCanonical($canonical);
        if ($robots) {
            SEOMeta::setRobots($robots);
        }
    }

    protected function applyOpenGraphWebsite(string $url, ?string $ogTitle, ?string $ogDesc, string $ogImage): void
    {
        OpenGraph::setSiteName($this->siteName);
        OpenGraph::setUrl($url);
        if ($ogTitle) {
            OpenGraph::setTitle($ogTitle);
        }
        if ($ogDesc) {
            OpenGraph::setDescription($ogDesc);
        }
        OpenGraph::addImage($ogImage);
        OpenGraph::setType('website');
    }

    protected function applyTwitter(?string $twTitle, ?string $twDesc, string $twImage): void
    {
        TwitterCard::setType('summary_large_image');
        if ($twTitle) {
            TwitterCard::setTitle($twTitle);
        }
        if ($twDesc) {
            TwitterCard::setDescription($twDesc);
        }
        TwitterCard::setImages([$twImage]);
    }

    protected function applyWebPageJsonLd(?string $title, ?string $description, string $url, string $image): void
    {
        JsonLdMulti::setType('WebPage');
        if ($title) {
            JsonLdMulti::setTitle($title);
        }
        JsonLdMulti::setUrl($url);
        if ($description) {
            JsonLdMulti::setDescription($description);
        }
        JsonLdMulti::addImage($image);
    }

    protected function plainDescription(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return null;
        }
        $text = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return Str::limit($text, 300, '') ?: null;
    }
}
