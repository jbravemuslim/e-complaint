<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use Illuminate\Support\Facades\Storage;

class ResponseController extends Controller
{
    public function index()
    {
        // Admin hanya melihat yang ia tanggapi sendiri (sesuai native).
        $complains = Complain::with(['user', 'response.admin'])
            ->whereHas('response', fn ($q) => $q->where('admin_id', auth()->id()))
            ->orderBy('id')
            ->get();

        return view('admin.responses', compact('complains'));
    }

    public function destroy(Complain $complain)
    {
        // Admin hanya boleh menghapus dari daftar miliknya sendiri.
        abort_unless(
            $complain->response && $complain->response->admin_id === auth()->id(),
            403, 'Anda hanya bisa menghapus tanggapan milik sendiri.'
        );

        if ($complain->lampiran) {
            Storage::disk('public')->delete('lampiran/'.$complain->lampiran);
        }

        $complain->delete();

        return redirect()->route('admin.responses')->with('success', 'Data dihapus.');
    }
}
