<?php

namespace App\Support;

use App\Models\Company;
use App\Models\JobOpening;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SiteData
{
    /**
     * Company logos live in `public/assets/logo/` (filenames may contain spaces).
     */
    public static function companyLogoUrl(?string $basename): ?string
    {
        if ($basename === null || $basename === '') {
            return null;
        }
        if (str_starts_with($basename, 'http://') || str_starts_with($basename, 'https://')) {
            return $basename;
        }

        if (str_starts_with($basename, 'companies/')) {
            return Storage::disk('public')->url($basename);
        }

        return asset('assets/logo/' . rawurlencode($basename));
    }

    /** Header/footer brand mark. */
    public static function brandLogoUrl(): string
    {
        return self::companyLogoUrl('LITUS Group - logos_Artboard 1 - LITUS Group.png') ?? '';
    }

    public static function companies(): array
    {
        if (! Schema::hasTable('companies')) {
            return self::legacyCompanies();
        }

        if (! Company::query()->exists()) {
            return self::legacyCompanies();
        }

        return Company::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Company $company) => $company->toSitePayload())
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected static function legacyCompanies(): array
    {
        static $cache = null;

        if ($cache === null) {
            $cache = require __DIR__ . '/data/companies_default.php';
        }

        return array_map(
            function (array $row, int $index): array {
                return array_merge(['id' => (string) ($index + 1)], $row);
            },
            $cache,
            array_keys($cache)
        );
    }

    /**
     * @return array<string, string> slug => label
     */
    public static function divisionOptions(): array
    {
        return collect(self::divisions())
            ->mapWithKeys(fn (array $div, string $key) => [$key => $div['title']])
            ->all();
    }

    public static function divisions(): array
    {
        return [
            'corporate' => [
                'title' => 'Corporate',
                'description' => 'Group headquarters and corporate functions',
            ],
            'logistics-shipping' => [
                'title' => 'Logistics & Shipping',
                'description' => 'Comprehensive logistics and hospitality solutions',
            ],
            'automotive' => [
                'title' => 'Automotive Division',
                'description' => 'Complete automotive sales, service, and parts',
            ],
            'trading' => [
                'title' => 'Trading Division',
                'description' => 'Diverse trading and supply solutions',
            ],
            'construction' => [
                'title' => 'Construction & Development',
                'description' => 'Building excellence in construction',
            ],
            'technology-retail' => [
                'title' => 'Technology & Retail',
                'description' => 'Advanced technology and office solutions',
            ],
            'hospitality-lifestyle' => [
                'title' => 'Hospitality & Lifestyle',
                'description' => 'Luxury hospitality and travel services',
            ],
        ];
    }

    public static function companiesByDivision(string $divisionKey): array
    {
        return array_values(array_filter(self::companies(), fn ($c) => ($c['division'] ?? '') === $divisionKey));
    }

    public static function companyBySlug(string $slug): ?array
    {
        foreach (self::companies() as $company) {
            if (($company['slug'] ?? '') === $slug) {
                return $company;
            }
        }

        return null;
    }

    /**
     * Order used for the Our Companies grid: row 1 left/right, row 2 left/right, etc.
     */
    public static function companiesForOurCompaniesGrid(): array
    {
        $order = [
            'litus-group',
            'litus-maldives',
            'litus-shipping',
            'litus-automobiles',
            'litus-service-center',
            'litus-parts',
            'litus-general-trading',
            'litus-connect',
            'litus-connect-office-tech',
            'litus-constructions',
            'zaha-residence-hotels',
            'zaha-travels',
            'al-zaha-general-trading',
            'favala-supply',
            'favala-hardware',
            'favala-paint',
        ];

        $bySlug = [];
        foreach (self::companies() as $company) {
            $slug = $company['slug'] ?? '';
            if ($slug !== '') {
                $bySlug[$slug] = $company;
            }
        }

        $result = [];
        foreach ($order as $slug) {
            if (isset($bySlug[$slug])) {
                $result[] = $bySlug[$slug];
            }
        }

        return $result;
    }

    public static function featuredCompanies(): array
    {
        return array_values(array_filter(self::companies(), fn ($c) => (bool)($c['featured'] ?? false)));
    }

    public static function blogPosts(): array
    {
        static $cached = null;
        if ($cached === null) {
            $cached = require __DIR__ . '/data/blog_posts.php';
        }

        return $cached;
    }

    public static function blogCategories(): array
    {
        return ['All', 'Company News', 'Logistics', 'Hospitality', 'Construction', 'Technology', 'Automotive', 'Retail', 'Team', 'Growth', 'Collaboration'];
    }

    public static function galleryEvents(): array
    {
        return [
            [
                'slug' => 'annual-general-meeting-2026',
                'title' => 'Annual General Meeting 2026',
                'date' => 'March 15, 2026',
                'description' => 'LITUS Group brought together leadership and stakeholders for our annual strategic review and planning session.',
                'image' => 'https://images.unsplash.com/photo-1638799869566-b17fa794c4de?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBiYXRocm9vbSUyMGludGVyaW9yJTIwbHV4dXJ5fGVufDF8fHx8MTc3NDMyMzk0NXww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Luxury bathroom interior',
            ],
            [
                'slug' => 'zaha-hotels-grand-opening',
                'title' => 'Zaha Hotels Grand Opening',
                'date' => 'February 28, 2026',
                'description' => 'Celebrating the grand opening of our newest luxury property in the Maldives with world-class amenities.',
                'image' => 'https://images.unsplash.com/photo-1744974256549-8ece7cdb5dd2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtaW5pbWFsaXN0JTIwYmVkcm9vbSUyMHdoaXRlfGVufDF8fHx8MTc3NDM3MTIxNnww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Minimalist bedroom design',
            ],
            [
                'slug' => 'leadership-team-building-retreat',
                'title' => 'Leadership Team Building Retreat',
                'date' => 'February 20, 2026',
                'description' => 'An intensive retreat focused on strategic alignment and team cohesion for our executive leadership.',
                'image' => 'https://images.unsplash.com/photo-1667584523543-d1d9cc828a15?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBsaXZpbmclMjByb29tJTIwaW50ZXJpb3J8ZW58MXx8fHwxNzc0MzQwNDg1fDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Modern living room',
            ],
            [
                'slug' => 'excellence-award-ceremony-2026',
                'title' => 'Excellence Award Ceremony 2026',
                'date' => 'February 10, 2026',
                'description' => 'Honoring outstanding achievements and contributions across all LITUS Group divisions.',
                'image' => 'https://images.unsplash.com/photo-1759223198981-661cadbbff36?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxsdXh1cnklMjBob3RlbCUyMHN1aXRlJTIwcm9vbXxlbnwxfHx8fDE3NzQ0MzQ2MDJ8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Hotel suite interior',
            ],
            [
                'slug' => 'new-headquarters-inauguration',
                'title' => 'New Headquarters Inauguration',
                'date' => 'January 25, 2026',
                'description' => 'Opening our state-of-the-art headquarters facility, marking a new chapter in our company history.',
                'image' => 'https://images.unsplash.com/photo-1622131815452-cc00d8d89f02?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBvZmZpY2UlMjB3b3Jrc3BhY2UlMjBpbnRlcmlvcnxlbnwxfHx8fDE3NzQzODcyNTF8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Modern workspace',
            ],
            [
                'slug' => 'favala-product-launch-event',
                'title' => 'Favala Product Launch Event',
                'date' => 'January 15, 2026',
                'description' => 'Introducing our latest line of premium hardware and construction materials to the market.',
                'image' => 'https://images.unsplash.com/photo-1680210849773-f97a41c6b7ed?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjb250ZW1wb3JhcnklMjBraXRjaGVuJTIwZGVzaWdufGVufDF8fHx8MTc3NDQzODU3N3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Contemporary kitchen',
            ],
            [
                'slug' => 'international-trade-show-2026',
                'title' => 'International Trade Show 2026',
                'date' => 'January 5, 2026',
                'description' => "Showcasing LITUS Group's diverse portfolio at the premier international trade exhibition.",
                'image' => 'https://images.unsplash.com/photo-1771033834141-023d630b3965?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjByZXRhaWwlMjBzdG9yZSUyMGludGVyaW9yfGVufDF8fHx8MTc3NDQyMDgxN3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Retail store interior',
            ],
            [
                'slug' => '25th-anniversary-celebration',
                'title' => '25th Anniversary Celebration',
                'date' => 'December 20, 2025',
                'description' => 'A milestone celebration honoring 25 years of excellence, growth, and innovation.',
                'image' => 'https://images.unsplash.com/photo-1769773297747-bd00e31b33aa?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxlbGVnYW50JTIwcmVzdGF1cmFudCUyMGRpbmluZ3xlbnwxfHx8fDE3NzQ0Mzg1ODN8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Restaurant dining area',
            ],
        ];
    }

    public static function careerOpenings(): array
    {
        if (! Schema::hasTable('job_openings')) {
            return self::legacyCareerOpenings();
        }

        if (! JobOpening::query()->where('is_active', true)->exists()) {
            return [];
        }

        return JobOpening::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (JobOpening $j) => [
                'id' => (string) $j->id,
                'title' => $j->title,
                'company' => $j->company,
                'location' => $j->location,
                'type' => $j->type,
                'department' => $j->department,
                'description' => $j->description,
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected static function legacyCareerOpenings(): array
    {
        return [
            [
                'title' => 'Senior Automotive Technician',
                'company' => 'LITUS Service Center',
                'location' => 'Maldives',
                'type' => 'Full-time',
                'department' => 'Automotive',
                'description' => null,
            ],
            [
                'title' => 'Hotel Manager',
                'company' => 'Zaha Residence & Hotels',
                'location' => 'Maldives',
                'type' => 'Full-time',
                'department' => 'Hospitality',
                'description' => null,
            ],
            [
                'title' => 'Network Engineer',
                'company' => 'LITUS Connect',
                'location' => 'Maldives',
                'type' => 'Full-time',
                'department' => 'Technology',
                'description' => null,
            ],
            [
                'title' => 'Construction Project Manager',
                'company' => 'LITUS Constructions',
                'location' => 'Maldives',
                'type' => 'Full-time',
                'department' => 'Construction',
                'description' => null,
            ],
            [
                'title' => 'Sales Executive',
                'company' => 'LITUS Automobiles',
                'location' => 'Maldives',
                'type' => 'Full-time',
                'department' => 'Sales',
                'description' => null,
            ],
            [
                'title' => 'Logistics Coordinator',
                'company' => 'LITUS Shipping',
                'location' => 'Maldives',
                'type' => 'Full-time',
                'department' => 'Logistics',
                'description' => null,
            ],
        ];
    }

    public static function team(): array
    {
        if (! Schema::hasTable('team_members')) {
            return [];
        }

        if (! TeamMember::query()->where('is_active', true)->exists()) {
            return [];
        }

        return TeamMember::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (TeamMember $m): array {
                $photo = $m->photo;
                if ($photo && str_starts_with($photo, 'team/')) {
                    $photo = Storage::disk('public')->url($photo);
                }

                return [
                    'name' => $m->name,
                    'role' => $m->role,
                    'bio' => $m->bio,
                    'expertise' => $m->expertise,
                    'image' => $photo,
                    'linkedin_url' => $m->linkedin_url,
                    'email' => $m->email,
                ];
            })
            ->all();
    }
}

