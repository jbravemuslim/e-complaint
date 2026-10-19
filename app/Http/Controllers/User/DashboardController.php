<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use App\Models\Jenis;
use App\Models\Tujuan;

class DashboardController extends Controller
{
    public function index()
    {
        $tujuans = Tujuan::orderBy('nama_unit')->get();
        $jenisList = Jenis::orderBy('nama_jenis')->get();

        return view('user.dashboard', compact('tujuans', 'jenisList'));
    }

    public function riwayat()
    {
        $complains = Complain::with(['user', 'tujuan', 'jenis', 'response.admin'])
            ->where('user_id', auth()->id())
            ->latest('id')
            ->paginate(10);

        return view('user.riwayat', compact('complains'));
    }
}
