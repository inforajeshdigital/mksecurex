@include('front.partials.cta', [
    'heading' => $s->get('heading', setting('cta_heading')),
    'text' => $s->get('subheading'),
    'btn1' => ['text' => $s->get('button_text'), 'url' => $s->get('button_url')],
    'btn2' => ['text' => $s->get('button2_text'), 'url' => $s->get('button2_url')],
])
