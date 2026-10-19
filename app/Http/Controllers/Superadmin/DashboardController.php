<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use App\Models\Response;

class DashboardController extends Controller
{
    public function index()
    {
        $prosesCount = Complain::where('status', 'proses')->count();
        $selesaiCount = Complain::where('status', 'selesai')->count();
        $totalCount = Complain::count();

        return view('superadmin.dashboard', compact('prosesCount', 'selesaiCount', 'totalCount'));
    }
}
