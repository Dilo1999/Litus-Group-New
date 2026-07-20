<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Company;
use App\Models\GalleryEvent;
use App\Models\PageSeo;
use App\Support\GlobalSeo;
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

    /** @var array<string, mixed> */
    protected array $global;

    protected bool $globalExtrasApplied = false;

    public function __construct()
    {
        $this->global = GlobalSeo::all();
        $this->siteName = GlobalSeo::siteName();

        $configuredDefault = asset('images/content/cta2.jpg');
        $this->defaultOgImage = filled($this->global['og_image'] ?? null)
            ? $this->absoluteImageUrl((string) $this->global['og_image'])
            : $configuredDefault;
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
     * otherwise falls back to provided defaults, then Global SEO settings.
     */
    public function applyForPage(string $routeName, array $defaults = []): void
    {
        $pageSeo = PageSeo::forRoute($routeName);
        $url = url()->current();

        $metaTitle = $pageSeo?->meta_title
            ?? $defaults['meta_title']
            ?? ($this->global['meta_title'] ?? null);
        $metaDesc = $pageSeo?->meta_description
            ?? $defaults['meta_description']
            ?? ($this->global['meta_description'] ?? null);
        $ogTitle = $pageSeo?->og_title ?? $defaults['og_title'] ?? $metaTitle;
        $ogDesc = $pageSeo?->og_description ?? $defaults['og_description'] ?? $metaDesc;
        $ogImage = $this->absoluteImageUrl(
            $pageSeo?->og_image
                ?? $defaults['og_image']
                ?? ($this->global['og_image'] ?? null)
        );
        $twTitle = $pageSeo?->twitter_title ?? $defaults['twitter_title'] ?? $ogTitle;
        $twDesc = $pageSeo?->twitter_description ?? $defaults['twitter_description'] ?? $ogDesc;
        $twImageRaw = $pageSeo?->twitter_image ?? $defaults['twitter_image'] ?? null;
        $twImage = $twImageRaw ? $this->absoluteImageUrl($twImageRaw) : $ogImage;
        $canonical = $pageSeo?->canonical_url ?? $defaults['canonical'] ?? $url;
        $robots = $pageSeo?->robots
            ?? $defaults['robots']
            ?? ($this->global['robots'] ?? null);

        $this->applyCoreMeta($metaTitle, $metaDesc, $canonical, $robots);
        $this->applyOpenGraphWebsite($url, $ogTitle, $ogDesc, $ogImage);
        $this->applyTwitter($twTitle, $twDesc, $twImage);
        $this->applyWebPageJsonLd($metaTitle, $metaDesc, $url, $ogImage);
        $this->applyGlobalExtras();
    }

    public function applyForBlogPost(BlogPost $post): void
    {
        $url = url()->current();

        $metaTitle = $post->meta_title
            ?: ($post->title.' | '.$this->siteName);
        $metaDesc = $post->meta_description
            ?: $this->plainDescription($post->excerpt)
            ?: $this->plainDescription($post->content)
            ?: ($this->global['meta_description'] ?? null);

        $ogTitleSync = $post->og_title ?: $metaTitle;
        $ogDescSync = $post->og_description ?: $metaDesc;

        $ogImagePath = $post->og_image ?: $post->image ?: ($this->global['og_image'] ?? null);
        $ogImage = $this->absoluteImageUrl($ogImagePath);

        $twTitle = $post->twitter_title ?: $ogTitleSync;
        $twDesc = $post->twitter_description ?: $ogDescSync;
        $twImage = $post->twitter_image
            ? $this->absoluteImageUrl($post->twitter_image)
            : $ogImage;

        $canonical = $post->canonical_url ?: $url;
        $robots = $post->robots ?: ($this->global['robots'] ?? null);

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

        $this->applyGlobalExtras();
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
     * Raw head output from SEOTools (meta + opengraph + twitter + json-ld),
     * plus site-wide verification and analytics snippets from Global SEO.
     */
    public function headHtml(): string
    {
        $this->applyGlobalExtras();

        return SEOTools::generate().$this->analyticsScriptHtml();
    }

    protected function applyGlobalExtras(): void
    {
        if ($this->globalExtrasApplied) {
            return;
        }
        $this->globalExtrasApplied = true;

        $keywords = GlobalSeo::keywordList($this->global['keywords'] ?? null);
        if ($keywords !== []) {
            SEOMeta::setKeywords($keywords);
        }

        if (filled($this->global['google_verification'] ?? null)) {
            SEOMeta::addMeta(
                'google-site-verification',
                $this->sanitizeVerificationToken((string) $this->global['google_verification']),
                'name'
            );
        }

        if (filled($this->global['bing_verification'] ?? null)) {
            SEOMeta::addMeta(
                'msvalidate.01',
                $this->sanitizeVerificationToken((string) $this->global['bing_verification']),
                'name'
            );
        }

        if (filled($this->global['twitter_site'] ?? null)) {
            TwitterCard::setSite($this->sanitizeTwitterHandle((string) $this->global['twitter_site']));
        }

        $this->applyOrganizationJsonLd();
    }

    protected function applyOrganizationJsonLd(): void
    {
        $homeUrl = url('/');
        $logo = $this->defaultOgImage;

        JsonLdMulti::newJsonLd();
        JsonLdMulti::setType('Organization');
        JsonLdMulti::setTitle($this->siteName);
        JsonLdMulti::setUrl($homeUrl);
        JsonLdMulti::addImage($logo);
        if (filled($this->global['meta_description'] ?? null)) {
            JsonLdMulti::setDescription((string) $this->global['meta_description']);
        }

        JsonLdMulti::newJsonLd();
        JsonLdMulti::setType('WebSite');
        JsonLdMulti::setTitle($this->siteName);
        JsonLdMulti::setUrl($homeUrl);
        if (filled($this->global['meta_description'] ?? null)) {
            JsonLdMulti::setDescription((string) $this->global['meta_description']);
        }
    }

    protected function analyticsScriptHtml(): string
    {
        $id = $this->sanitizeAnalyticsId($this->global['google_analytics_id'] ?? null);
        if ($id === null) {
            return '';
        }

        if (str_starts_with($id, 'GTM-')) {
            return <<<HTML

<!-- Global SEO: Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$id}');</script>
HTML;
        }

        return <<<HTML

<!-- Global SEO: Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$id}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{$id}');</script>
HTML;
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

    protected function sanitizeVerificationToken(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-.:]/', '', $value) ?? '';
    }

    protected function sanitizeTwitterHandle(string $value): string
    {
        $handle = ltrim(trim($value), '@');
        $handle = preg_replace('/[^a-zA-Z0-9_]/', '', $handle) ?? '';

        return $handle === '' ? '' : '@'.$handle;
    }

    protected function sanitizeAnalyticsId(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $id = strtoupper(trim($value));
        if (preg_match('/^(G-[A-Z0-9]+|GTM-[A-Z0-9]+|UA-\d+-\d+)$/', $id) !== 1) {
            return null;
        }

        return $id;
    }
}
