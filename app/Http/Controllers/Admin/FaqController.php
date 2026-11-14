<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends AdminController
{
    public const CATEGORIES = ['general' => 'General / Homepage', 'services' => 'Services Page', 'about' => 'About Page', 'contact' => 'Contact Page'];

    public function index(Request $request)
    {
        $faqs = Faq::whereNull('faqable_type')
            ->when($request->category, fn ($q, $c) => $q->where('category', $c))
            ->orderBy('category')->orderBy('sort_order')->get();

        return view('admin.faqs.index', ['faqs' => $faqs, 'categories' => self::CATEGORIES]);
    }

    public function create()
    {
        return view('admin.faqs.form', ['faq' => new Faq(['is_active' => true, 'category' => 'general']), 'categories' => self::CATEGORIES]);
    }

    public function store(Request $request)
    {
        $faq = Faq::create($this->validated($request) + ['sort_order' => (int) Faq::max('sort_order') + 1]);
        $this->log('create', 'Created FAQ: '.$faq->question, $faq);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ created.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.form', ['faq' => $faq, 'categories' => self::CATEGORIES]);
    }

    public function update(Request $request, Faq $faq)
    {
        $faq->update($this->validated($request));
        $this->log('update', 'Updated FAQ: '.$faq->question, $faq);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ saved.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        $this->log('delete', 'Deleted FAQ: '.$faq->question);

        return back()->with('success', 'FAQ deleted.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'category' => ['required', 'in:'.implode(',', array_keys(self::CATEGORIES))],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
