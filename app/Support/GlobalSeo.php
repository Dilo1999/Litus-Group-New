<?php

namespace App\Support;

use App\Models\SiteSetting;

class GlobalSeo
{
    public const KEY_SITE_NAME = 'seo.global.site_name';

    public const KEY_META_TITLE = 'seo.global.meta_title';

    public const KEY_META_DESCRIPTION = 'seo.global.meta_description';

    public const KEY_KEYWORDS = 'seo.global.keywords';

    public const KEY_OG_IMAGE = 'seo.global.og_image';

    public const KEY_ROBOTS = 'seo.global.robots';

    public const KEY_TWITTER_SITE = 'seo.global.twitter_site';

    public const KEY_GOOGLE_VERIFICATION = 'seo.global.google_verification';

    public const KEY_BING_VERIFICATION = 'seo.global.bing_verification';

    public const KEY_GOOGLE_ANALYTICS_ID = 'seo.global.google_analytics_id';

    /**
     * @return array{
     *     site_name: ?string,
     *     meta_title: ?string,
     *     meta_description: ?string,
     *     keywords: ?string,
     *     og_image: ?string,
     *     robots: ?string,
     *     twitter_site: ?string,
     *     google_verification: ?string,
     *     bing_verification: ?string,
     *     google_analytics_id: ?string
     * }
     */
    public static function all(): array
    {
        return [
            'site_name' => self::stringOrNull(SiteSetting::getValue(self::KEY_SITE_NAME)),
            'meta_title' => self::stringOrNull(SiteSetting::getValue(self::KEY_META_TITLE)),
            'meta_description' => self::stringOrNull(SiteSetting::getValue(self::KEY_META_DESCRIPTION)),
            'keywords' => self::stringOrNull(SiteSetting::getValue(self::KEY_KEYWORDS)),
            'og_image' => self::stringOrNull(SiteSetting::getValue(self::KEY_OG_IMAGE)),
            'robots' => self::stringOrNull(SiteSetting::getValue(self::KEY_ROBOTS)),
            'twitter_site' => self::stringOrNull(SiteSetting::getValue(self::KEY_TWITTER_SITE)),
            'google_verification' => self::stringOrNull(SiteSetting::getValue(self::KEY_GOOGLE_VERIFICATION)),
            'bing_verification' => self::stringOrNull(SiteSetting::getValue(self::KEY_BING_VERIFICATION)),
            'google_analytics_id' => self::stringOrNull(SiteSetting::getValue(self::KEY_GOOGLE_ANALYTICS_ID)),
        ];
    }

    public static function siteName(): string
    {
        return self::all()['site_name']
            ?: (string) config('seotools.opengraph.defaults.site_name', 'LITUS Group');
    }

    /**
     * @return list<string>
     */
    public static function keywordList(?string $keywords = null): array
    {
        $raw = $keywords ?? self::all()['keywords'] ?? '';
        if ($raw === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (string $part): string => trim($part),
            explode(',', $raw)
        )));
    }

    protected static function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
