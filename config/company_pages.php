<?php

/*
|--------------------------------------------------------------------------
| Bespoke company pages
|--------------------------------------------------------------------------
| SEO defaults for companies that have their own view in
| resources/views/site/companies/{slug}.blade.php. Admin "Page SEO" records
| for route `site.company.{slug}` override these values.
*/

return [
    'zaha-travels' => [
        'seo' => [
            'meta_title' => 'Zaha Travels | Maldives & Sri Lanka Specialists',
            'meta_description' => 'Meet Zaha Travels, a LITUS Group company specialising in Maldives and Sri Lanka travel, with expert planning, 100+ resort and hotel partners, and local support.',
            'og_title' => 'Zaha Travels · Travel guided by Experts',
            'og_description' => 'Maldives & Sri Lanka destination specialists. A LITUS Group company serving travellers, travel agencies and tour operators.',
            'og_image' => 'https://www.zahatravels.com/storage/destinations/hero/maldives.jpeg',
        ],
    ],
];
