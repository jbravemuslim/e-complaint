<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComplainController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tujuan_id' => ['required', 'exists:tujuans,id'],
            'jenis_id' => ['required', 'exists:jenis,id'],
            'keluhan' => ['required', 'string', 'max:5000'],
            'lampiran' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ], [
            'lampiran.image' => 'Format File Tidak Di Dukung (gunakan JPG/PNG).',
            'lampiran.mimes' => 'Format File Tidak Di Dukung (gunakan JPG/PNG).',
            'lampiran.max' => 'Ukuran Gambar Tidak Lebih Dari 1MB.',
        ]);

        $filename = null;
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = date('dmYis').'_'.auth()->id().'_'.preg_replace('/[^A-Za-z0-9._-]/', '', $file->getClientOriginalName());
            $file->storeAs('lampiran', $filename, 'public');
        }

        Complain::create([
            'user_id' => auth()->id(),
            'keluhan' => $validated['keluhan'],
            'lampiran' => $filename,
            'tgl_keluhan' => now()->toDateString(),
            'tujuan_id' => $validated['tujuan_id'],
            'jenis_id' => $validated['jenis_id'],
            'status' => 'proses',
        ]);

        return redirect()->route('riwayat')->with('success', 'Keluhan akan segera diproses.');
    }

    public function show(Complain $complain)
    {
        abort_unless($complain->user_id === auth()->id(), 403);

        $complain->load(['tujuan', 'jenis', 'response.admin']);

        return view('user.show', compact('complain'));
    }

    public function destroy(Complain $complain)
    {
        abort_unless($complain->user_id === auth()->id(), 403);

        if ($complain->lampiran) {
            Storage::disk('public')->delete('lampiran/'.$complain->lampiran);
        }

        $complain->delete();

        return redirect()->route('riwayat')->with('success', 'Keluhan dihapus.');
    }
}
