<?php

/**
 * @see https://github.com/artesaos/seotools
 */

return [
    'inertia' => env('SEO_TOOLS_INERTIA', false),
    'meta' => [
        /*
         * The default configurations to be used by the meta generator.
         */
        'defaults' => [
            'title' => 'Al Zaha General Trading',
            'titleBefore' => false,
            'description' => 'Premium sourcing and supply chain solutions from Dubai. End-to-end logistics, consulting, and procurement for resorts, construction, retail, and industrial sectors.',
            'separator' => ' – ',
            'keywords' => ['sourcing', 'supply chain', 'logistics', 'Dubai', 'procurement'],
            'canonical' => 'current',
            'robots' => false, // Set to 'all', 'none' or any combination of index/noindex and follow/nofollow
        ],
        /*
         * Webmaster tags are always added.
         */
        'webmaster_tags' => [
            'google' => null,
            'bing' => null,
            'alexa' => null,
            'pinterest' => null,
            'yandex' => null,
            'norton' => null,
        ],

        'add_notranslate_class' => false,
    ],
    'opengraph' => [
        /*
         * The default configurations to be used by the opengraph generator.
         */
        'defaults' => [
            'title' => 'Al Zaha General Trading',
            'description' => 'Premium sourcing and supply chain solutions from Dubai.',
            'url' => null,
            'type' => 'website',
            'site_name' => 'Al Zaha General Trading',
            'images' => [],
        ],
    ],
    'twitter' => [
        /*
         * The default values to be used by the twitter cards generator.
         */
        'defaults' => [
            // 'card'        => 'summary',
            // 'site'        => '@LuizVinicius73',
        ],
    ],
    'json-ld' => [
        /*
         * The default configurations to be used by the json-ld generator.
         */
        'defaults' => [
            'title' => 'Al Zaha General Trading',
            'description' => 'Premium sourcing and supply chain solutions from Dubai.',
            'url' => 'current',
            'type' => 'WebPage',
            'images' => [],
        ],
    ],
];
