<?php

namespace App\Http\Controllers\Admin;

use App\Models;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SortController extends AdminController
{
    /** model key => [class, permission] */
    protected const MODELS = [
        'sections' => [Models\PageSection::class, 'pages'],
        'sliders' => [Models\Slider::class, 'homepage'],
        'services' => [Models\Service::class, 'services'],
        'industries' => [Models\Industry::class, 'industries'],
        'testimonials' => [Models\Testimonial::class, 'testimonials'],
        'faqs' => [Models\Faq::class, 'faqs'],
        'menu_items' => [Models\NavigationItem::class, 'navigation'],
        'pages' => [Models\Page::class, 'pages'],
    ];

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'model' => ['required', 'in:'.implode(',', array_keys(self::MODELS))],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);
        [$class, $perm] = self::MODELS[$data['model']];
        if ($data['model'] === 'sections') {
            abort_unless(auth()->user()->canAccess('pages') || auth()->user()->canAccess('homepage'), 403);
        } else {
            abort_unless(auth()->user()->canAccess($perm), 403);
        }

        foreach ($data['ids'] as $i => $id) {
            $class::whereKey($id)->update(['sort_order' => $i + 1]);
        }
        Cache::forget('nav.services');
        Cache::forget('nav.industries');
        Models\NavigationMenu::flushCache();

        return response()->json(['success' => true, 'message' => 'Order saved']);
    }
}
