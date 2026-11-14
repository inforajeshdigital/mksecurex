@include('front.partials.page-hero', [
    'title' => $s->get('heading', $page->title ?? ''),
    'subtitle' => $s->get('subheading', $page->excerpt ?? null),
    'badge' => $s->get('badge'),
    'image' => $s->get('image', $page->hero_image ?? null),
    'imageAlt' => $s->get('image_alt', $page->banner_alt ?? null),
    'breadcrumbs' => $breadcrumbs ?? [],
    'buttons' => [['text' => $s->get('button_text'), 'url' => $s->get('button_url')], ['text' => $s->get('button2_text'), 'url' => $s->get('button2_url')]],
])
