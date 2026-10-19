<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use App\Models\Jenis;
use App\Models\Tujuan;
use App\Support\DashboardStats;

class DashboardController extends Controller
{
    public function index()
    {
        $tujuans = Tujuan::orderBy('nama_unit')->get();
        $jenisList = Jenis::orderBy('nama_jenis')->get();
        $stat = DashboardStats::personal((int) auth()->id());

        return view('user.dashboard', compact('tujuans', 'jenisList', 'stat'));
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
