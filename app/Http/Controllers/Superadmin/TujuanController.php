<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Tujuan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TujuanController extends Controller
{
    public function index()
    {
        $tujuans = Tujuan::orderBy('nama_unit')->get();

        return view('superadmin.tujuans', compact('tujuans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_unit' => ['required', 'string', 'max:255', 'unique:tujuans,nama_unit'],
        ]);

        Tujuan::create($validated);

        return redirect()->route('superadmin.tujuans')->with('success', 'Tujuan ditambahkan.');
    }

    public function update(Request $request, Tujuan $tujuan)
    {
        $validated = $request->validate([
            'nama_unit' => ['required', 'string', 'max:255', Rule::unique('tujuans', 'nama_unit')->ignore($tujuan->id)],
        ]);

        $tujuan->update($validated);

        return redirect()->route('superadmin.tujuans')->with('success', 'Tujuan diperbarui.');
    }

    public function destroy(Tujuan $tujuan)
    {
        $tujuan->delete();

        return redirect()->route('superadmin.tujuans')->with('success', 'Tujuan dihapus.');
    }
}
