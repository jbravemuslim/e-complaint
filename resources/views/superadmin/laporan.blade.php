@extends('layouts.app')

@section('title', 'Laporan')
@section('content')
<div class="page-head-row">
    <div class="page-head" style="margin-bottom:0;">
        <div class="eyebrow">Rekap</div>
        <h4>Laporan</h4>
        <p>Seluruh keluhan yang sudah ditanggapi, siap cetak.</p>
    </div>
    <a class="btn blue" href="{{ route('superadmin.laporan.cetak', request()->query()) }}" target="_blank"><i class="material-icons left">print</i>Cetak</a>
</div>
<div class="panel-card" style="margin-top:20px;">
<form method="GET" action="{{ route('superadmin.laporan') }}" class="row" style="margin-bottom:8px;">
    <div class="input-field col s12 m4">
        <input type="date" id="dari" name="dari" value="{{ $filters['dari'] ?? '' }}">
        <label for="dari" class="active">Dari tanggal</label>
    </div>
    <div class="input-field col s12 m4">
        <input type="date" id="sampai" name="sampai" value="{{ $filters['sampai'] ?? '' }}">
        <label for="sampai" class="active">Sampai tanggal</label>
    </div>
    <div class="col s12 m4" style="padding-top:20px;">
        <button type="submit" class="btn blue">Filter</button>
        <a href="{{ route('superadmin.laporan') }}" class="btn grey">Reset</a>
    </div>
</form>
@if($complains->isEmpty())
    <div class="empty-state">
        <i class="material-icons">assignment</i>
        <p>Tidak ada laporan pada periode ini.</p>
    </div>
@else
<table id="example" class="display responsive-table hx-row" style="width:100%">
    <thead><tr><th>No</th><th>ID</th><th>Nama Pelapor</th><th>Nama Admin</th><th>Tanggal Masuk</th><th>Tanggal Ditanggapi</th><th>Status</th><th>Opsi</th></tr></thead>
    <tbody>
    @foreach($complains as $i => $c)
        <tr>
            <td>{{ $i + 1 }}</td><td>{{ $c->id }}</td><td>{{ $c->user->name ?? '-' }}</td>
            <td>{{ $c->response->admin->name ?? '-' }}</td>
            <td>{{ $c->tgl_keluhan->format('Y-m-d') }}</td><td>{{ $c->response->tgl_tanggapan->format('Y-m-d') }}</td>
            <td><span class="status-pill {{ $c->status }}">{{ $c->status }}</span></td>
            <td class="hx-actions"><a class="hx-btn-act modal-trigger" href="#lap-{{ $c->id }}"><i class="material-icons" aria-hidden="true">visibility</i>Detail</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
@foreach($complains as $c)
    <div id="lap-{{ $c->id }}" class="modal">
        <div class="modal-content">
            <h5 class="hx-modal-title">Detail Keluhan</h5>
            <dl class="detail-list">
                <dt>ID / Dari / Admin</dt><dd>{{ $c->id }} / {{ $c->user->name ?? '-' }} / {{ $c->response->admin->name ?? '-' }}</dd>
            </dl>
            @if($c->lampiran)
                <img class="detail-img" width="150" src="{{ $c->lampiranUrl() }}" alt="lampiran">
            @endif
            <dl class="detail-list">
                <dt>Pesan</dt><dd>{{ $c->keluhan }}</dd>
                <dt>Respon</dt><dd>{{ $c->response->tanggapan }}</dd>
            </dl>
        </div>
        <div class="modal-footer"><a href="#!" class="modal-close waves-effect btn-flat">Tutup</a></div>
    </div>
@endforeach
    @endif
</div>
@endsection
