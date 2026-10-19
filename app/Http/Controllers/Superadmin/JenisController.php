<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JenisController extends Controller
{
    public function index()
    {
        $jenisList = Jenis::orderBy('nama_jenis')->get();

        return view('superadmin.jenis', compact('jenisList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis' => ['required', 'string', 'max:255', 'unique:jenis,nama_jenis'],
        ]);

        Jenis::create($validated);

        return redirect()->route('superadmin.jenis')->with('success', 'Jenis ditambahkan.');
    }

    public function update(Request $request, Jenis $jeni)
    {
        $validated = $request->validate([
            'nama_jenis' => ['required', 'string', 'max:255', Rule::unique('jenis', 'nama_jenis')->ignore($jeni->id)],
        ]);

        $jeni->update($validated);

        return redirect()->route('superadmin.jenis')->with('success', 'Jenis diperbarui.');
    }

    public function destroy(Jenis $jeni)
    {
        $jeni->delete();

        return redirect()->route('superadmin.jenis')->with('success', 'Jenis dihapus.');
    }
}
