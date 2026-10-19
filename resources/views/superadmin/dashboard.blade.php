@extends('layouts.app')

@section('title', 'Dashboard Superadmin')
@section('content')
<div class="page-head">
    <div class="eyebrow">Ringkasan</div>
    <h4>Dashboard <span class="accent">Superadmin</span></h4>
    <p>Kilas balik keseluruhan aplikasi: keluhan, akun, dan master data.</p>
</div>

<div class="stat-grid" style="margin-bottom: 24px;">
    <a class="stat-card stat-blue" href="{{ route('superadmin.users') }}">
        <div class="stat-icon"><i class="material-icons">group</i></div>
        <div>
            <div class="stat-num">{{ $userCount }}</div>
            <div class="stat-label">Total Pelanggan</div>
        </div>
    </a>
    <a class="stat-card stat-amber" href="{{ route('superadmin.admins') }}">
        <div class="stat-icon"><i class="material-icons">admin_panel_settings</i></div>
        <div>
            <div class="stat-num">{{ $adminCount }}</div>
            <div class="stat-label">Admin Aktif</div>
        </div>
    </a>
    <a class="stat-card stat-teal" href="{{ route('superadmin.laporan') }}">
        <div class="stat-icon"><i class="material-icons">book</i></div>
        <div>
            <div class="stat-num">Laporan</div>
            <div class="stat-label">Rekap &amp; Cetak Keluhan</div>
        </div>
    </a>
</div>

@include('shared.overview', [
    'listUrl' => route('superadmin.complains'),
    'doneUrl' => route('superadmin.responses'),
])
@endsection
