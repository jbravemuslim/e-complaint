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

        // Digrup di PHP (bukan SQL) agar identik di SQLite dan MySQL.
        $start = now()->subMonths(5)->startOfMonth()->toDateString();
        $masuk = Complain::where('tgl_keluhan', '>=', $start)->get(['tgl_keluhan'])
            ->groupBy(fn ($c) => $c->tgl_keluhan->format('Y-m'))->map->count();
        $selesai = Response::where('tgl_tanggapan', '>=', $start)->get(['tgl_tanggapan'])
            ->groupBy(fn ($r) => $r->tgl_tanggapan->format('Y-m'))->map->count();

        $chart = ['labels' => [], 'masuk' => [], 'selesai' => []];
        foreach (range(5, 0) as $i) {
            $m = now()->subMonths($i);
            $chart['labels'][] = $m->translatedFormat('M Y');
            $chart['masuk'][] = $masuk[$m->format('Y-m')] ?? 0;
            $chart['selesai'][] = $selesai[$m->format('Y-m')] ?? 0;
        }

        return view('superadmin.dashboard', compact('prosesCount', 'selesaiCount', 'totalCount', 'chart'));
    }
}
