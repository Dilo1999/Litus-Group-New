<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    private const GRID_SLUG_ORDER = [
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

    public function run(): void
    {
        /** @var array<int, array<string, mixed>> $rows */
        $rows = require __DIR__ . '/../../app/Support/data/companies_default.php';

        $bySlug = [];
        foreach ($rows as $row) {
            $slug = $row['slug'] ?? '';
            if ($slug !== '') {
                $bySlug[$slug] = $row;
            }
        }

        foreach (self::GRID_SLUG_ORDER as $index => $slug) {
            if (! isset($bySlug[$slug])) {
                continue;
            }

            $data = $bySlug[$slug];

            $name = $data['name'];

            Company::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'tagline' => $data['tagline'] ?? null,
                    'description' => $data['description'] ?? null,
                    'description_secondary' => $data['description_secondary'] ?? null,
                    'category' => $data['category'] ?? null,
                    'division' => $data['division'] ?? null,
                    'logo' => $data['logo'] ?? null,
                    'icon' => $data['icon'] ?? 'building2',
                    'hotline' => $data['hotline'] ?? null,
                    'email' => $data['email'] ?? null,
                    'services' => $data['services'] ?? [],
                    'strengths' => $data['strengths'] ?? [],
                    'featured' => (bool) ($data['featured'] ?? false),
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
