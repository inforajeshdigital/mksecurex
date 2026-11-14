<?php

namespace App\Http\Controllers\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends AdminController
{
    public function index()
    {
        return redirect()->route('admin.homepage');
    }

    public function create()
    {
        return view('admin.sliders.form', ['slider' => new Slider(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['sort_order'] = (int) Slider::max('sort_order') + 1;
        $slider = Slider::create($data);
        $this->log('create', "Created slide {$slider->heading}", $slider);

        return redirect()->route('admin.homepage')->with('success', 'Slide created.');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.form', ['slider' => $slider]);
    }

    public function update(Request $request, Slider $slider)
    {
        $slider->update($this->validated($request, $slider));
        $this->log('update', "Updated slide {$slider->heading}", $slider);

        return redirect()->route('admin.homepage')->with('success', 'Slide saved.');
    }

    public function destroy(Slider $slider)
    {
        $slider->delete();
        $this->log('delete', "Deleted slide {$slider->heading}");

        return back()->with('success', 'Slide deleted.');
    }

    protected function validated(Request $request, ?Slider $slider = null): array
    {
        $data = $request->validate([
            'badge' => ['nullable', 'string', 'max:100'],
            'heading' => ['required', 'string', 'max:200'],
            'subheading' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'btn1_text' => ['nullable', 'string', 'max:60'],
            'btn1_url' => ['nullable', 'string', 'max:255'],
            'btn2_text' => ['nullable', 'string', 'max:60'],
            'btn2_url' => ['nullable', 'string', 'max:255'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['image'] = $this->image($request, 'image', $slider?->image, $data['image_alt'] ?? null);

        return $data;
    }
}
