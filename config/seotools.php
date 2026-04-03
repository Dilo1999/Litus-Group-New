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
            'title' => 'LITUS Group',
            'titleBefore' => false,
            'description' => 'LITUS Group is a diversified business group with operations across logistics, automotive, trading, construction, technology, retail, and hospitality.',
            'separator' => ' | ',
            'keywords' => ['LITUS Group', 'Maldives', 'logistics', 'automotive', 'hospitality', 'trading'],
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
            'title' => 'LITUS Group',
            'description' => 'LITUS Group is a diversified business group operating across multiple industries in the Maldives and the wider region.',
            'url' => null,
            'type' => 'website',
            'site_name' => 'LITUS Group',
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
            'title' => 'LITUS Group',
            'description' => 'LITUS Group is a diversified business group with operations across logistics, automotive, trading, construction, technology, retail, and hospitality.',
            'url' => 'current',
            'type' => 'WebPage',
            'images' => [],
        ],
    ],
];
