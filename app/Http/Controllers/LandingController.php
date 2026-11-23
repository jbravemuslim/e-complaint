<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return match (auth()->user()->role) {
                'superadmin' => redirect()->route('superadmin.dashboard'),
                'admin' => redirect()->route('admin.dashboard'),
                default => redirect()->route('dashboard'),
            };
        }

        return view('landing');
    }
}
