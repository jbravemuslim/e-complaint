<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\DashboardStats;

class DashboardController extends Controller
{
    public function index()
    {
        $data = DashboardStats::staff((int) auth()->id());
        $data['userCount'] = User::where('role', 'user')->count();
        $data['adminCount'] = User::where('role', 'admin')->count();

        return view('superadmin.dashboard', $data);
    }
}
