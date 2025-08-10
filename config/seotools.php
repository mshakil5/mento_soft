<?php
/**
 * @see https://github.com/artesaos/seotools
 */

return [
    'inertia' => env('SEO_TOOLS_INERTIA', false),
    'meta' => [
        'defaults' => [
            'title'       => false, // Remove default title postfix
            'titleBefore' => false,
            'description' => '',    // Empty default description
            'separator'   => ' - ',
            'keywords'    => [],
            'canonical'   => false,
            'robots'      => false,
        ],
        'webmaster_tags' => [
            'google'    => null,
            'bing'      => null,
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => null,
            'norton'    => null,
        ],
        'add_notranslate_class' => false,
    ],
    'opengraph' => [
        'defaults' => [
            'title'       => false, // Remove default OG title
            'description' => '',
            'url'         => false,
            'type'        => false,
            'site_name'   => false,
            'images'      => [],
        ],
    ],
    'twitter' => [
        'defaults' => [
            // Set your defaults or leave empty
        ],
    ],
    'json-ld' => [
        'defaults' => [
            'title'       => false, // Remove default JSON-LD title
            'description' => '',
            'url'         => false,
            'type'        => 'WebPage',
            'images'      => [],
        ],
    ],
];