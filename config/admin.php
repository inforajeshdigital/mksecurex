<?php

/*
| Admin sidebar & permission modules.
| `perm` is the permission key checked against the user's role.
*/

return [
    'permissions' => [
        'dashboard' => 'Dashboard',
        'settings' => 'Website Settings & CTA',
        'header' => 'Header Management',
        'footer' => 'Footer Management',
        'homepage' => 'Homepage Management',
        'pages' => 'Page Management',
        'services' => 'Services',
        'industries' => 'Industries',
        'testimonials' => 'Testimonials',
        'faqs' => 'FAQs',
        'enquiries' => 'Enquiries',
        'media' => 'Media Library',
        'seo' => 'SEO, Robots & Sitemap',
        'redirects' => 'Redirects',
        'navigation' => 'Navigation',
        'contact' => 'Contact Information',
        'floating' => 'WhatsApp & Call',
        'users' => 'Users & Roles',
        'maintenance' => 'Backup / Maintenance',
    ],

    'menu' => [
        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'route' => 'admin.dashboard', 'perm' => 'dashboard'],
        ['heading' => 'Content'],
        ['label' => 'Homepage', 'icon' => 'fa-house', 'route' => 'admin.homepage', 'perm' => 'homepage', 'active' => ['admin.homepage*', 'admin.sliders*']],
        ['label' => 'Pages', 'icon' => 'fa-file-lines', 'route' => 'admin.pages.index', 'perm' => 'pages', 'active' => ['admin.pages*', 'admin.sections*']],
        ['label' => 'About Us', 'icon' => 'fa-building', 'route' => 'admin.about', 'perm' => 'pages'],
        ['label' => 'Services', 'icon' => 'fa-briefcase', 'route' => 'admin.services.index', 'perm' => 'services'],
        ['label' => 'Industries', 'icon' => 'fa-industry', 'route' => 'admin.industries.index', 'perm' => 'industries'],
        ['label' => 'Testimonials', 'icon' => 'fa-quote-left', 'route' => 'admin.testimonials.index', 'perm' => 'testimonials'],
        ['label' => 'FAQs', 'icon' => 'fa-circle-question', 'route' => 'admin.faqs.index', 'perm' => 'faqs'],
        ['label' => 'Media Library', 'icon' => 'fa-photo-film', 'route' => 'admin.media.index', 'perm' => 'media'],
        ['heading' => 'Leads'],
        ['label' => 'Enquiries', 'icon' => 'fa-envelope-open-text', 'route' => 'admin.enquiries.index', 'perm' => 'enquiries', 'badge' => 'enquiries'],
        ['heading' => 'Appearance'],
        ['label' => 'Website Settings', 'icon' => 'fa-gear', 'route' => 'admin.settings', 'params' => ['group' => 'general'], 'perm' => 'settings', 'match' => 'general'],
        ['label' => 'Header', 'icon' => 'fa-window-maximize', 'route' => 'admin.settings', 'params' => ['group' => 'header'], 'perm' => 'header', 'match' => 'header'],
        ['label' => 'Footer', 'icon' => 'fa-table-cells-large', 'route' => 'admin.settings', 'params' => ['group' => 'footer'], 'perm' => 'footer', 'match' => 'footer'],
        ['label' => 'Navigation', 'icon' => 'fa-bars', 'route' => 'admin.menus.index', 'perm' => 'navigation', 'active' => ['admin.menus*']],
        ['label' => 'Contact Info', 'icon' => 'fa-address-book', 'route' => 'admin.settings', 'params' => ['group' => 'contact'], 'perm' => 'contact', 'match' => 'contact'],
        ['label' => 'WhatsApp & Call', 'icon' => 'fa-phone-volume', 'route' => 'admin.settings', 'params' => ['group' => 'floating'], 'perm' => 'floating', 'match' => 'floating'],
        ['label' => 'Global CTA', 'icon' => 'fa-bullhorn', 'route' => 'admin.settings', 'params' => ['group' => 'cta'], 'perm' => 'settings', 'match' => 'cta'],
        ['heading' => 'SEO'],
        ['label' => 'SEO Management', 'icon' => 'fa-magnifying-glass-chart', 'route' => 'admin.seo.index', 'perm' => 'seo', 'active' => ['admin.seo.index', 'admin.seo.edit', 'admin.seo.update']],
        ['label' => 'SEO Defaults', 'icon' => 'fa-sliders', 'route' => 'admin.settings', 'params' => ['group' => 'seo'], 'perm' => 'seo', 'match' => 'seo'],
        ['label' => 'Redirects', 'icon' => 'fa-diamond-turn-right', 'route' => 'admin.redirects.index', 'perm' => 'redirects'],
        ['label' => 'Robots.txt', 'icon' => 'fa-robot', 'route' => 'admin.seo.robots', 'perm' => 'seo'],
        ['label' => 'Sitemap', 'icon' => 'fa-sitemap', 'route' => 'admin.seo.sitemap', 'perm' => 'seo'],
        ['heading' => 'System'],
        ['label' => 'Users & Roles', 'icon' => 'fa-users-gear', 'route' => 'admin.users.index', 'perm' => 'users', 'active' => ['admin.users*', 'admin.roles*']],
        ['label' => 'Activity Log', 'icon' => 'fa-clock-rotate-left', 'route' => 'admin.activity', 'perm' => 'users'],
        ['label' => 'Backup & Maintenance', 'icon' => 'fa-screwdriver-wrench', 'route' => 'admin.maintenance', 'perm' => 'maintenance'],
    ],
];
