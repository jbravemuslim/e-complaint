@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('content')
<div class="page-head">
    <div class="eyebrow">Ringkasan</div>
    <h4>Dashboard <span class="accent">Admin</span></h4>
    <p>Pantau kemajuan penanganan keluhan yang masuk.</p>
</div>

@include('shared.overview', [
    'listUrl' => route('admin.complains'),
    'doneUrl' => route('admin.responses'),
])
@endsection
