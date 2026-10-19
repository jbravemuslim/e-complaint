<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComplainController extends Controller
{
    public function index()
    {
        $complains = Complain::with(['user', 'tujuan', 'jenis'])
            ->where('status', 'proses')
            ->orderBy('id')
            ->get();

        return view('superadmin.complains', compact('complains'));
    }

    public function respond(Request $request, Complain $complain)
    {
        abort_unless($complain->status === 'proses', 422, 'Keluhan sudah ditanggapi.');

        $validated = $request->validate([
            'tanggapan' => ['required', 'string', 'max:5000'],
        ]);

        try {
            DB::transaction(function () use ($complain, $validated): void {
                // Kunci baris agar dua staff yang menanggapi bersamaan tidak ganda.
                $target = Complain::whereKey($complain->id)->lockForUpdate()->firstOrFail();
                abort_unless($target->status === 'proses' && $target->response()->doesntExist(), 422, 'Keluhan sudah ditanggapi.');

                $target->response()->create([
                    'admin_id' => auth()->id(),
                    'tanggapan' => $validated['tanggapan'],
                    'tgl_tanggapan' => now()->toDateString(),
                ]);

                $target->update(['status' => 'selesai']);
            });
        } catch (QueryException) {
            abort(422, 'Keluhan sudah ditanggapi.');
        }

        return redirect()->route('superadmin.complains')->with('success', 'Tanggapan terkirim.');
    }

    public function destroy(Complain $complain)
    {
        if ($complain->lampiran) {
            Storage::disk('public')->delete('lampiran/'.$complain->lampiran);
        }

        $complain->delete();

        return redirect()->route('superadmin.complains')->with('success', 'Keluhan dihapus.');
    }
}
