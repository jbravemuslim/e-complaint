<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use App\Models\Response;

class DashboardController extends Controller
{
    public function index()
    {
        $prosesCount = Complain::where('status', 'proses')->count();
        $ditanggapiCount = Response::where('admin_id', auth()->id())->count();

        return view('admin.dashboard', compact('prosesCount', 'ditanggapiCount'));
    }
}
