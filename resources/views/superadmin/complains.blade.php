@extends('layouts.app')

@section('title', 'Complain Proses')
@section('content')
<div class="page-head">
    <div class="eyebrow">Penanganan</div>
    <h4>Dalam Proses</h4>
    <p>Seluruh keluhan berstatus proses yang menunggu tanggapan.</p>
</div>
<div class="panel-card flush">
<table id="example" class="display responsive-table" style="width:100%">
    <thead><tr><th>No</th><th>ID</th><th>Nama</th><th>Tanggal Masuk</th><th>Status</th><th>Opsi</th></tr></thead>
    <tbody>
    @foreach($complains as $i => $c)
        <tr>
            <td>{{ $i + 1 }}</td><td>{{ $c->id }}</td><td>{{ $c->user->name ?? '-' }}</td>
            <td>{{ $c->tgl_keluhan->format('Y-m-d') }}</td>
            <td><span class="status-pill {{ $c->status }}">{{ $c->status }}</span></td>
            <td>
                <a class="btn modal-trigger blue" href="#smore-{{ $c->id }}">More</a>
                <form method="POST" action="{{ route('superadmin.complains.destroy', $c) }}" style="display:inline" onsubmit="return confirm('Anda Yakin Ingin Menghapus?')">
                    @csrf @method('DELETE')
                    <button class="btn red" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
        <div id="smore-{{ $c->id }}" class="modal">
            <div class="modal-content">
                <h4 class="blue-text">Detail</h4>
                <dl class="detail-list">
                    <dt>ID / Dari</dt><dd>{{ $c->id }} / {{ $c->user->name ?? '-' }}</dd>
                    <dt>Tujuan / Jenis</dt><dd>{{ $c->tujuan->nama_unit ?? '-' }} / {{ $c->jenis->nama_jenis ?? '-' }}</dd>
                    <dt>Tanggal Masuk</dt><dd>{{ $c->tgl_keluhan->format('Y-m-d') }}</dd>
                </dl>
                @if($c->lampiran)
                    <img class="detail-img" width="150" src="{{ $c->lampiranUrl() }}" alt="lampiran">
                @else
                    <img class="detail-img" src="{{ asset('img/noImage.svg') }}" width="150" alt="no image">
                @endif
                <dl class="detail-list">
                    <dt>Pesan</dt><dd>{{ $c->keluhan }}</dd>
                </dl>
                <div class="panel-card" style="margin:16px 0 0; background:#f7fbff;">
                    <form method="POST" action="{{ route('superadmin.complains.respond', $c) }}">
                        @csrf
                        <div class="input-field" style="margin-top:0;">
                            <label for="st-{{ $c->id }}">Tulis Tanggapan</label>
                            <textarea id="st-{{ $c->id }}" name="tanggapan" class="materialize-textarea" required></textarea>
                        </div>
                        <input type="submit" value="Kirim" class="btn blue right">
                        <div style="clear:both;"></div>
                    </form>
                </div>
            </div>
            <div class="modal-footer"><a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a></div>
        </div>
    @endforeach
    </tbody>
</table>
</div>
@endsection
