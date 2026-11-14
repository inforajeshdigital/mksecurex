@php
    echo match ($s->get('background', 'white')) {
        'light' => 'bg-light-mk',
        'dark' => 'bg-dark-section',
        'primary' => 'bg-primary-section',
        default => '',
    };
@endphp
