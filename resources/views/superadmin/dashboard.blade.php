@extends('layouts.app')

@section('title', 'Dashboard Superadmin')
@section('content')
<div class="page-head">
    <div class="eyebrow">Ringkasan</div>
    <h4>Dashboard</h4>
    <p>Kemajuan penanganan seluruh keluhan yang masuk.</p>
</div>
<div class="stat-grid">
    <a class="stat-card stat-red hx-reveal" style="--hx-d:0s" href="{{ route('superadmin.complains') }}">
        <div class="stat-icon"><i class="material-icons">pending_actions</i></div>
        <div>
            <div class="stat-num" data-hx-count="{{ $prosesCount }}">{{ $prosesCount }}</div>
            <div class="stat-label">Keluhan Status Proses</div>
        </div>
    </a>
    <a class="stat-card stat-teal hx-reveal" style="--hx-d:.12s" href="{{ route('superadmin.responses') }}">
        <div class="stat-icon"><i class="material-icons">task_alt</i></div>
        <div>
            <div class="stat-num" data-hx-count="{{ $selesaiCount }}">{{ $selesaiCount }}</div>
            <div class="stat-label">Keluhan Ditanggapi</div>
        </div>
    </a>
    <a class="stat-card stat-blue hx-reveal" style="--hx-d:.24s" href="{{ route('superadmin.laporan') }}">
        <div class="stat-icon"><i class="material-icons">inbox</i></div>
        <div>
            <div class="stat-num" data-hx-count="{{ $totalCount }}">{{ $totalCount }}</div>
            <div class="stat-label">Total Keluhan Masuk</div>
        </div>
    </a>
</div>
<div class="panel-card hx-reveal" style="--hx-d:.3s">
    <div class="page-head" style="margin-bottom:12px;">
        <div class="eyebrow">Tren 6 Bulan</div>
        <h4 style="font-size:20px;">Masuk vs Ditanggapi</h4>
    </div>
    <canvas id="hx-chart" data-hx-chart='@json($chart)' height="220" style="width:100%;"></canvas>
    <p class="hx-mono" style="margin:12px 0 0; color:var(--ec-muted);">
        <span style="color:#0000f2;">&#9632;</span> Masuk
        &nbsp;&nbsp;<span style="color:#009688;">&#9632;</span> Ditanggapi
    </p>
</div>
@endsection
