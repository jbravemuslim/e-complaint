@extends('layouts.app')

@section('title', 'Kelola Jenis')
@section('content')
<div class="page-head-row">
    <div class="page-head" style="margin-bottom:0;">
        <div class="eyebrow">Master Data</div>
        <h4>Jenis</h4>
        <p>Daftar jenis keluhan.</p>
    </div>
    <a class="btn blue modal-trigger" href="#modal-add"><i class="material-icons left">add</i>Tambah</a>
</div>
<div class="panel-card flush" style="margin-top:20px;">
<table id="example" class="display responsive-table hx-row" style="width:100%">
    <thead><tr><th>No</th><th>Nama Jenis</th><th>Opsi</th></tr></thead>
    <tbody>
    @foreach($jenisList as $i => $j)
        <tr>
            <td>{{ $i + 1 }}</td><td>{{ $j->nama_jenis }}</td>
            <td class="hx-actions">
                <a class="hx-btn-act modal-trigger" href="#jedit-{{ $j->id }}"><i class="material-icons" aria-hidden="true">edit</i>Edit</a>
                <form method="POST" action="{{ route('superadmin.jenis.destroy', $j) }}" class="hx-btn-act-form" onsubmit="return confirm('Anda Yakin Ingin Menghapus?')">
                    @csrf @method('DELETE')
                    <button class="hx-btn-act danger" type="submit"><i class="material-icons" aria-hidden="true">delete_outline</i>Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
@foreach($jenisList as $j)
    <div id="jedit-{{ $j->id }}" class="modal">
        <div class="modal-content">
            <h5 class="hx-modal-title">Edit Jenis</h5>
            <form method="POST" action="{{ route('superadmin.jenis.update', $j) }}">
                @csrf @method('PUT')
                <div class="input-field"><label>Nama Jenis</label><input type="text" name="nama_jenis" value="{{ $j->nama_jenis }}" required></div>
                <input type="submit" value="Simpan" class="btn blue right">
                <div style="clear:both;"></div>
            </form>
        </div>
        <div class="modal-footer"><a href="#!" class="modal-close waves-effect btn-flat">Tutup</a></div>
    </div>
@endforeach
<div id="modal-add" class="modal">
    <div class="modal-content">
        <h5 class="hx-modal-title">Tambah Jenis</h5>
        <form method="POST" action="{{ route('superadmin.jenis.store') }}">
            @csrf
            <div class="input-field"><label>Nama Jenis</label><input type="text" name="nama_jenis" required></div>
            <input type="submit" value="Simpan" class="btn blue right">
            <div style="clear:both;"></div>
        </form>
    </div>
    <div class="modal-footer"><a href="#!" class="modal-close waves-effect btn-flat">Tutup</a></div>
</div>
@endsection
