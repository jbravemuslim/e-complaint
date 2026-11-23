@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('content')
<div class="page-head">
    <div class="eyebrow">Ringkasan</div>
    <h4>Dashboard</h4>
    <p>Kemajuan penanganan keluhan yang masuk hari ini.</p>
</div>
<div class="stat-grid">
    <a class="stat-card stat-red hx-reveal" style="--hx-d:0s" href="{{ route('admin.complains') }}">
        <div class="stat-icon"><i class="material-icons">pending_actions</i></div>
        <div>
            <div class="stat-num" data-hx-count="{{ $prosesCount }}">{{ $prosesCount }}</div>
            <div class="stat-label">Keluhan Status Proses</div>
        </div>
    </a>
    <a class="stat-card stat-teal hx-reveal" style="--hx-d:.12s" href="{{ route('admin.responses') }}">
        <div class="stat-icon"><i class="material-icons">task_alt</i></div>
        <div>
            <div class="stat-num" data-hx-count="{{ $ditanggapiCount }}">{{ $ditanggapiCount }}</div>
            <div class="stat-label">Keluhan Ditanggapi</div>
        </div>
    </a>
</div>
@endsection
