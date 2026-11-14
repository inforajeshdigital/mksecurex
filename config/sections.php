<?php

/*
|--------------------------------------------------------------------------
| Page builder section types
|--------------------------------------------------------------------------
| `fields` = which common inputs the admin editor shows for the type.
| `items`  = repeater columns shown for the type (empty = no repeater).
*/

return [
    'types' => [
        'hero_slider' => [
            'label' => 'Hero Banner Slider', 'icon' => 'fa-images',
            'fields' => [], 'items' => [],
            'help' => 'Slides are managed in Homepage → Hero Slides.',
        ],
        'page_hero' => [
            'label' => 'Page Hero Banner', 'icon' => 'fa-panorama',
            'fields' => ['badge', 'heading', 'subheading', 'image', 'image_alt', 'button_text', 'button_url', 'button2_text', 'button2_url'], 'items' => [],
        ],
        'text_image' => [
            'label' => 'Text + Image', 'icon' => 'fa-table-columns',
            'fields' => ['badge', 'heading', 'content', 'image', 'image_alt', 'layout', 'background', 'button_text', 'button_url'],
            'items' => ['icon', 'title', 'text'],
        ],
        'features' => [
            'label' => 'Feature Cards (Why Choose Us / Values)', 'icon' => 'fa-star',
            'fields' => ['badge', 'heading', 'subheading', 'columns', 'background'],
            'items' => ['icon', 'title', 'text'],
        ],
        'process' => [
            'label' => 'Process Steps', 'icon' => 'fa-list-ol',
            'fields' => ['badge', 'heading', 'subheading', 'background'],
            'items' => ['title', 'text'],
        ],
        'stats' => [
            'label' => 'Statistics Counters', 'icon' => 'fa-chart-simple',
            'fields' => ['badge', 'heading', 'background'],
            'items' => ['icon', 'value', 'suffix', 'title'],
            'help' => 'Publish only verified figures.',
        ],
        'service_cards' => [
            'label' => 'Service Cards', 'icon' => 'fa-briefcase',
            'fields' => ['badge', 'heading', 'subheading', 'limit', 'background', 'button_text', 'button_url'], 'items' => [],
        ],
        'industry_cards' => [
            'label' => 'Industry Cards', 'icon' => 'fa-industry',
            'fields' => ['badge', 'heading', 'subheading', 'limit', 'background', 'button_text', 'button_url'], 'items' => [],
        ],
        'testimonials' => [
            'label' => 'Testimonials', 'icon' => 'fa-quote-left',
            'fields' => ['badge', 'heading', 'background'], 'items' => [],
        ],
        'faqs' => [
            'label' => 'FAQs', 'icon' => 'fa-circle-question',
            'fields' => ['badge', 'heading', 'subheading', 'category', 'background'], 'items' => [],
        ],
        'gallery' => [
            'label' => 'Image Gallery', 'icon' => 'fa-photo-film',
            'fields' => ['badge', 'heading', 'columns', 'background'],
            'items' => ['image', 'title'],
        ],
        'cta' => [
            'label' => 'Call To Action', 'icon' => 'fa-bullhorn',
            'fields' => ['heading', 'subheading', 'button_text', 'button_url', 'button2_text', 'button2_url'], 'items' => [],
        ],
        'enquiry_form' => [
            'label' => 'Enquiry Form', 'icon' => 'fa-envelope-open-text',
            'fields' => ['badge', 'heading', 'subheading', 'background'],
            'items' => ['icon', 'title', 'text'],
        ],
        'contact_info' => [
            'label' => 'Contact Details + Form + Map', 'icon' => 'fa-location-dot',
            'fields' => ['badge', 'heading', 'subheading'], 'items' => [],
            'help' => 'Address, phone, email, hours and map come from Contact Information settings.',
        ],
        'rich_text' => [
            'label' => 'Rich Text Block', 'icon' => 'fa-align-left',
            'fields' => ['heading', 'content', 'background'], 'items' => [],
        ],
        'custom_html' => [
            'label' => 'Custom HTML', 'icon' => 'fa-code',
            'fields' => ['html'], 'items' => [],
            'help' => 'Scripts are stripped. Only trusted HTML.',
        ],
    ],

    'backgrounds' => ['white' => 'White', 'light' => 'Light Blue Tint', 'dark' => 'Navy (Dark)', 'primary' => 'Royal Blue'],
];
