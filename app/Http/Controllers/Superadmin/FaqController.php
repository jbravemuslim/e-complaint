<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('pertanyaan')->get();

        return view('superadmin.faqs', compact('faqs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:500'],
            'jawaban' => ['required', 'string', 'max:5000'],
        ]);

        Faq::create($validated);

        return redirect()->route('superadmin.faqs')->with('success', 'FAQ ditambahkan.');
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:500'],
            'jawaban' => ['required', 'string', 'max:5000'],
        ]);

        $faq->update($validated);

        return redirect()->route('superadmin.faqs')->with('success', 'FAQ diperbarui.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('superadmin.faqs')->with('success', 'FAQ dihapus.');
    }
}
