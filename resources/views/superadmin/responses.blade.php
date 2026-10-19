@extends('layouts.app')

@section('title', 'Ditanggapi')
@section('content')
<div class="page-head">
    <div class="eyebrow">Selesai</div>
    <h4>Ditanggapi</h4>
    <p>Seluruh keluhan yang sudah ditanggapi admin.</p>
</div>
<div class="panel-card flush">
<table id="example" class="display responsive-table" style="width:100%">
    <thead><tr><th>No</th><th>ID</th><th>Nama</th><th>Admin</th><th>Tanggal Masuk</th><th>Tanggal Ditanggapi</th><th>Status</th><th>Opsi</th></tr></thead>
    <tbody>
    @foreach($complains as $i => $c)
        <tr>
            <td>{{ $i + 1 }}</td><td>{{ $c->id }}</td><td>{{ $c->user->name ?? '-' }}</td>
            <td>{{ $c->response->admin->name ?? '-' }}</td>
            <td>{{ $c->tgl_keluhan->format('Y-m-d') }}</td><td>{{ $c->response->tgl_tanggapan->format('Y-m-d') }}</td>
            <td><span class="status-pill {{ $c->status }}">{{ $c->status }}</span></td>
            <td>
                <a class="btn blue modal-trigger" href="#sresp-{{ $c->id }}">More</a>
                <form method="POST" action="{{ route('superadmin.responses.destroy', $c) }}" style="display:inline" onsubmit="return confirm('Anda Yakin Ingin Menghapus?')">
                    @csrf @method('DELETE')
                    <button class="btn red" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
        <div id="sresp-{{ $c->id }}" class="modal">
            <div class="modal-content">
                <h4 class="blue-text">Detail</h4>
                <dl class="detail-list">
                    <dt>ID / Dari / Admin</dt><dd>{{ $c->id }} / {{ $c->user->name ?? '-' }} / {{ $c->response->admin->name ?? '-' }}</dd>
                </dl>
                @if($c->lampiran)
                    <img class="detail-img" width="150" src="{{ $c->lampiranUrl() }}" alt="lampiran">
                @else
                    <img class="detail-img" src="{{ asset('img/noImage.svg') }}" width="150" alt="no image">
                @endif
                <dl class="detail-list">
                    <dt>Pesan</dt><dd>{{ $c->keluhan }}</dd>
                    <dt>Respon</dt><dd>{{ $c->response->tanggapan }}</dd>
                </dl>
            </div>
            <div class="modal-footer"><a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a></div>
        </div>
    @endforeach
    </tbody>
</table>
</div>
@endsection
