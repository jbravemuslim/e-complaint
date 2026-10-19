<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DashboardStats;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', DashboardStats::staff((int) auth()->id()));
    }
}
