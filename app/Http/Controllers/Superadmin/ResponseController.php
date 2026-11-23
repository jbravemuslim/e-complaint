<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use Illuminate\Support\Facades\Storage;

class ResponseController extends Controller
{
    public function index()
    {
        $complains = Complain::with(['user', 'response.admin'])
            ->whereHas('response')
            ->orderBy('id')
            ->get();

        return view('superadmin.responses', compact('complains'));
    }

    public function destroy(Complain $complain)
    {
        if ($complain->lampiran) {
            Storage::disk('public')->delete('lampiran/'.$complain->lampiran);
        }

        $complain->delete();

        return redirect()->route('superadmin.responses')->with('success', 'Data dihapus.');
    }
}
