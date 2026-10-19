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
<table id="example" class="display responsive-table" style="width:100%">
    <thead><tr><th>No</th><th>Nama Jenis</th><th>Opsi</th></tr></thead>
    <tbody>
    @foreach($jenisList as $i => $j)
        <tr>
            <td>{{ $i + 1 }}</td><td>{{ $j->nama_jenis }}</td>
            <td>
                <a class="btn teal modal-trigger" href="#jedit-{{ $j->id }}">Edit</a>
                <form method="POST" action="{{ route('superadmin.jenis.destroy', $j) }}" style="display:inline" onsubmit="return confirm('Anda Yakin Ingin Menghapus?')">
                    @csrf @method('DELETE')
                    <button class="red btn" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
        <div id="jedit-{{ $j->id }}" class="modal">
            <div class="modal-content">
                <h4 class="blue-text">Edit Jenis</h4>
                <form method="POST" action="{{ route('superadmin.jenis.update', $j) }}">
                    @csrf @method('PUT')
                    <div class="input-field"><label>Nama Jenis</label><input type="text" name="nama_jenis" value="{{ $j->nama_jenis }}" required></div>
                    <input type="submit" value="Simpan" class="btn blue right">
                    <div style="clear:both;"></div>
                </form>
            </div>
            <div class="modal-footer"><a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a></div>
        </div>
    @endforeach
    </tbody>
</table>
</div>
<div id="modal-add" class="modal">
    <div class="modal-content">
        <h4 class="blue-text">Tambah Jenis</h4>
        <form method="POST" action="{{ route('superadmin.jenis.store') }}">
            @csrf
            <div class="input-field"><label>Nama Jenis</label><input type="text" name="nama_jenis" required></div>
            <input type="submit" value="Simpan" class="btn blue right">
            <div style="clear:both;"></div>
        </form>
    </div>
    <div class="modal-footer"><a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a></div>
</div>
@endsection
