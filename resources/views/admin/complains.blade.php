@extends('layouts.app')

@section('title', 'Complain Proses')
@section('content')
<div class="page-head">
    <div class="eyebrow">Penanganan</div>
    <h4>Dalam Proses</h4>
    <p>Keluhan berstatus proses yang menunggu tanggapanmu.</p>
</div>
<div class="panel-card flush">
<table id="example" class="display responsive-table hx-row" style="width:100%">
    <thead>
    <tr>
        <th>No</th><th>ID</th><th>Nama</th><th>Tanggal Masuk</th><th>Status</th><th>Opsi</th>
    </tr>
    </thead>
    <tbody>
    @foreach($complains as $i => $c)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $c->id }}</td>
            <td>{{ $c->user->name ?? '-' }}</td>
            <td>{{ $c->tgl_keluhan->format('Y-m-d') }}</td>
            <td><span class="status-pill {{ $c->status }}">{{ $c->status }}</span></td>
            <td class="hx-actions">
                <a class="hx-btn-act modal-trigger" href="#more-{{ $c->id }}"><i class="material-icons" aria-hidden="true">visibility</i>Detail</a>
                <form method="POST" action="{{ route('admin.complains.destroy', $c) }}" class="hx-btn-act-form" onsubmit="return confirm('Anda Yakin Ingin Menghapus?')">
                    @csrf @method('DELETE')
                    <button class="hx-btn-act danger" type="submit"><i class="material-icons" aria-hidden="true">delete_outline</i>Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
@foreach($complains as $c)
    <div id="more-{{ $c->id }}" class="modal">
        <div class="modal-content">
            <h5 class="hx-modal-title">Detail Keluhan</h5>
            <dl class="detail-list">
                <dt>ID / Dari</dt><dd>{{ $c->id }} / {{ $c->user->name ?? '-' }}</dd>
                <dt>Tujuan / Jenis</dt><dd>{{ $c->tujuan->nama_unit ?? '-' }} / {{ $c->jenis->nama_jenis ?? '-' }}</dd>
                <dt>Tanggal Masuk</dt><dd>{{ $c->tgl_keluhan->format('Y-m-d') }}</dd>
                <dt>Status</dt><dd><span class="status-pill {{ $c->status }}">{{ $c->status }}</span></dd>
            </dl>
            @if($c->lampiran)
                <img class="detail-img" width="150" src="{{ $c->lampiranUrl() }}" alt="lampiran">
            @else
                <img class="detail-img" src="{{ asset('img/noImage.svg') }}" width="150" alt="no image">
            @endif
            <dl class="detail-list">
                <dt>Pesan</dt><dd>{{ $c->keluhan }}</dd>
            </dl>
            <div class="panel-card" style="margin:16px 0 0; background:#f7f7ff;">
                <form method="POST" action="{{ route('admin.complains.respond', $c) }}">
                    @csrf
                    <div class="input-field" style="margin-top:0;">
                        <label for="tanggapan-{{ $c->id }}">Tulis Tanggapan</label>
                        <textarea id="tanggapan-{{ $c->id }}" name="tanggapan" class="materialize-textarea" required></textarea>
                    </div>
                    <input type="submit" value="Kirim" class="btn blue right">
                    <div style="clear:both;"></div>
                </form>
            </div>
        </div>
        <div class="modal-footer"><a href="#!" class="modal-close waves-effect btn-flat">Tutup</a></div>
    </div>
@endforeach
@endsection
