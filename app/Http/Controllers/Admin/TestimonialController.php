<?php

namespace App\Http\Controllers\Admin;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends AdminController
{
    public function index()
    {
        return view('admin.testimonials.index', ['testimonials' => Testimonial::orderBy('sort_order')->get()]);
    }

    public function create()
    {
        return view('admin.testimonials.form', ['testimonial' => new Testimonial(['rating' => 5, 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        $t = Testimonial::create($this->validated($request) + ['sort_order' => (int) Testimonial::max('sort_order') + 1]);
        $this->log('create', "Created testimonial from {$t->client_name}", $t);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial created.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $testimonial->update($this->validated($request, $testimonial));
        $this->log('update', "Updated testimonial from {$testimonial->client_name}", $testimonial);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial saved.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        $this->log('delete', "Deleted testimonial from {$testimonial->client_name}");

        return back()->with('success', 'Testimonial deleted.');
    }

    protected function validated(Request $request, ?Testimonial $t = null): array
    {
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:120'],
            'designation' => ['nullable', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:150'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'content' => ['required', 'string', 'max:2000'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['image'] = $this->image($request, 'image', $t?->image, $data['client_name']);

        return $data;
    }
}
