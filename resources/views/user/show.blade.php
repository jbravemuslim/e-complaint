@extends('layouts.app')

@section('title', 'Detail Keluhan')
@section('content')
<div class="page-head">
    <div class="eyebrow">Riwayat</div>
    <h4>Detail Keluhan</h4>
    <p>Rincian keluhan beserta tanggapannya.</p>
</div>
<div class="panel-card">
    <dl class="detail-list">
        <dt>No Antrian</dt><dd>{{ $complain->id }}</dd>
        <dt>Tujuan</dt><dd>{{ $complain->tujuan->nama_unit ?? '-' }}</dd>
        <dt>Jenis</dt><dd>{{ $complain->jenis->nama_jenis ?? '-' }}</dd>
        <dt>Dilaporkan Pada</dt><dd>{{ $complain->tgl_keluhan->format('Y-m-d') }}</dd>
        @if($complain->response)
            <dt>Admin</dt><dd>{{ $complain->response->admin->name ?? '-' }}</dd>
            <dt>Tanggal Ditanggapi</dt><dd>{{ $complain->response->tgl_tanggapan->format('Y-m-d') }}</dd>
        @endif
        <dt>Status</dt><dd><span class="status-pill {{ $complain->status }}">{{ $complain->status }}</span></dd>
    </dl>
    @if($complain->lampiran)
        <img class="detail-img" src="{{ $complain->lampiranUrl() }}" width="200" alt="lampiran">
    @else
        <img class="detail-img" src="{{ asset('img/noImage.svg') }}" width="200" alt="no image">
    @endif
    <dl class="detail-list">
        <dt>Pesan</dt><dd>{{ $complain->keluhan }}</dd>
        @if($complain->response)
            <dt>Respon Admin {{ $complain->response->admin->name ?? '' }}</dt><dd>{{ $complain->response->tanggapan }}</dd>
        @endif
    </dl>
    <div style="margin-top:20px;">
        <a href="{{ route('riwayat') }}" class="btn blue">Kembali ke Riwayat</a>
    </div>
</div>
@endsection
