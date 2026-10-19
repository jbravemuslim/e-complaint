@extends('layouts.app')

@section('title', 'Dashboard Superadmin')
@section('content')
<div class="page-head">
    <div class="eyebrow">Ringkasan</div>
    <h4>Dashboard</h4>
    <p>Kemajuan penanganan seluruh keluhan yang masuk.</p>
</div>
<div class="stat-grid">
    <a class="stat-card stat-red" href="{{ route('superadmin.complains') }}">
        <div class="stat-icon"><i class="material-icons">pending_actions</i></div>
        <div>
            <div class="stat-num">{{ $prosesCount }}</div>
            <div class="stat-label">Keluhan Status Proses</div>
        </div>
    </a>
    <a class="stat-card stat-teal" href="{{ route('superadmin.responses') }}">
        <div class="stat-icon"><i class="material-icons">task_alt</i></div>
        <div>
            <div class="stat-num">{{ $selesaiCount }}</div>
            <div class="stat-label">Keluhan Ditanggapi</div>
        </div>
    </a>
    <a class="stat-card stat-blue" href="{{ route('superadmin.laporan') }}">
        <div class="stat-icon"><i class="material-icons">inbox</i></div>
        <div>
            <div class="stat-num">{{ $totalCount }}</div>
            <div class="stat-label">Total Keluhan Masuk</div>
        </div>
    </a>
</div>
@endsection
