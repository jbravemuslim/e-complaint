<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ]);

        $complains = $this->filtered($filters)->get();

        return view('superadmin.laporan', compact('complains', 'filters'));
    }

    public function cetak(Request $request)
    {
        $filters = $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ]);

        $complains = $this->filtered($filters)->get();

        $periode = null;
        if (! empty($filters['dari']) || ! empty($filters['sampai'])) {
            $periode = ($filters['dari'] ?? '...').' s/d '.($filters['sampai'] ?? '...');
        }

        return view('superadmin.cetak', compact('complains', 'filters', 'periode'));
    }

    private function filtered(array $filters)
    {
        $query = Complain::with(['user', 'response.admin'])
            ->whereHas('response')
            ->orderByDesc('tgl_keluhan');

        if (! empty($filters['dari'])) {
            $query->whereDate('tgl_keluhan', '>=', $filters['dari']);
        }

        if (! empty($filters['sampai'])) {
            $query->whereDate('tgl_keluhan', '<=', $filters['sampai']);
        }

        return $query;
    }
}
