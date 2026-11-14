<?php

namespace App\Http\Controllers\Admin;

use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use Illuminate\Http\Request;

class MenuController extends AdminController
{
    public function index(Request $request)
    {
        foreach (NavigationMenu::LOCATIONS as $loc => $name) {
            NavigationMenu::firstOrCreate(['location' => $loc], ['name' => $name]);
        }
        $menus = NavigationMenu::with(['items' => fn ($q) => $q->whereNull('parent_id')->with('children')])->get()->keyBy('location');
        $active = $request->query('menu', 'header');

        return view('admin.menus.index', ['menus' => $menus, 'active' => $menus->has($active) ? $active : 'header']);
    }

    public function store(Request $request, NavigationMenu $menu)
    {
        $data = $this->validated($request);
        $data['navigation_menu_id'] = $menu->id;
        $data['sort_order'] = (int) $menu->items()->max('sort_order') + 1;
        NavigationItem::create($data);
        $this->log('create', "Added menu item {$data['label']} to {$menu->name}");

        return redirect()->route('admin.menus.index', ['menu' => $menu->location])->with('success', 'Menu item added.');
    }

    public function update(Request $request, NavigationItem $item)
    {
        $item->update($this->validated($request, $item));

        return redirect()->route('admin.menus.index', ['menu' => $item->menu->location])->with('success', 'Menu item saved.');
    }

    public function destroy(NavigationItem $item)
    {
        $loc = $item->menu->location;
        $item->delete();

        return redirect()->route('admin.menus.index', ['menu' => $loc])->with('success', 'Menu item deleted.');
    }

    protected function validated(Request $request, ?NavigationItem $item = null): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'url' => ['nullable', 'string', 'max:255', 'not_regex:/^\s*javascript:/i'],
            'type' => ['required', 'in:'.implode(',', array_keys(NavigationItem::TYPES))],
            'parent_id' => ['nullable', 'integer', 'exists:navigation_items,id'],
        ]);
        if ($item && (int) ($data['parent_id'] ?? 0) === $item->id) {
            $data['parent_id'] = null;
        }
        $data['new_tab'] = $request->boolean('new_tab');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
