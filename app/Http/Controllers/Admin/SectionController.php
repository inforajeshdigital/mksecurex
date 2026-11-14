<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\Request;

class SectionController extends AdminController
{
    protected function authorizePage(Page $page): void
    {
        $perm = $page->slug === 'home' ? 'homepage' : 'pages';
        abort_unless(auth()->user()->canAccess($perm), 403);
    }

    protected function back(Page $page)
    {
        return $page->slug === 'home' ? redirect()->route('admin.homepage') : redirect()->route('admin.pages.edit', $page);
    }

    public function create(Request $request, Page $page)
    {
        $this->authorizePage($page);
        $type = $request->query('type', 'text_image');
        abort_unless(config("sections.types.$type"), 404);

        return view('admin.sections.form', ['page' => $page, 'section' => new PageSection(['type' => $type, 'is_active' => true, 'data' => []])]);
    }

    public function store(Request $request, Page $page)
    {
        $this->authorizePage($page);
        $section = new PageSection(['page_id' => $page->id, 'sort_order' => (int) $page->sections()->max('sort_order') + 1]);
        $this->fill($request, $section);
        $this->log('create', "Added {$section->label} section to {$page->title}", $section);

        return $this->back($page)->with('success', 'Section added.');
    }

    public function edit(PageSection $section)
    {
        $this->authorizePage($section->page);

        return view('admin.sections.form', ['page' => $section->page, 'section' => $section]);
    }

    public function update(Request $request, PageSection $section)
    {
        $this->authorizePage($section->page);
        $this->fill($request, $section);
        $this->log('update', "Updated {$section->label} section on {$section->page->title}", $section);

        return $request->input('after') === 'stay'
            ? back()->with('success', 'Section saved.')
            : $this->back($section->page)->with('success', 'Section saved.');
    }

    public function toggle(PageSection $section)
    {
        $this->authorizePage($section->page);
        $section->update(['is_active' => ! $section->is_active]);

        return request()->expectsJson()
            ? response()->json(['success' => true, 'active' => $section->is_active])
            : back()->with('success', 'Section '.($section->is_active ? 'enabled' : 'disabled').'.');
    }

    public function duplicate(PageSection $section)
    {
        $this->authorizePage($section->page);
        $copy = $section->replicate();
        $copy->name = trim(($section->name ?: $section->label).' (copy)');
        $copy->sort_order = $section->sort_order + 1;
        $copy->is_active = false;
        $copy->save();

        return back()->with('success', 'Section duplicated (disabled until you enable it).');
    }

    public function destroy(PageSection $section)
    {
        $page = $section->page;
        $this->authorizePage($page);
        $section->delete();
        $this->log('delete', "Deleted {$section->label} section from {$page->title}");

        return $this->back($page)->with('success', 'Section deleted.');
    }

    protected function fill(Request $request, PageSection $section): void
    {
        $type = $request->input('type', $section->type);
        $def = config("sections.types.$type");
        abort_unless($def, 422);

        $request->validate([
            'name' => ['nullable', 'string', 'max:150'],
            'data.heading' => ['nullable', 'string', 'max:300'],
            'data.subheading' => ['nullable', 'string', 'max:1000'],
            'data.badge' => ['nullable', 'string', 'max:100'],
            'data.limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'data.columns' => ['nullable', 'in:2,3,4'],
        ]);

        $old = $section->data ?? [];
        $in = (array) $request->input('data', []);
        $data = [];
        foreach ($def['fields'] as $f) {
            if ($f === 'image') {
                $data['image'] = $this->image($request, 'section_image', $old['image'] ?? null, $in['image_alt'] ?? null);
            } elseif ($f === 'html') {
                // Strip scripts/event handlers from custom HTML.
                $html = (string) ($in['html'] ?? '');
                $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);
                $html = preg_replace('#\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html);
                $data['html'] = $html;
            } else {
                $data[$f] = $in[$f] ?? null;
            }
        }

        if (! empty($def['items'])) {
            $items = [];
            $rows = (array) $request->input('items', []);
            foreach ($rows as $i => $row) {
                $clean = [];
                foreach ($def['items'] as $k) {
                    if ($k === 'image') {
                        $clean['image'] = $this->image($request, "item_image_$i", $row['image_current'] ?? null);
                    } else {
                        $clean[$k] = trim((string) ($row[$k] ?? ''));
                    }
                }
                if (array_filter($clean)) {
                    $items[] = $clean;
                }
            }
            $data['items'] = $items;
        }

        $section->fill([
            'type' => $type,
            'name' => $request->input('name'),
            'data' => $data,
            'is_active' => $request->boolean('is_active'),
        ])->save();
    }
}
