@extends('layouts.app')

@section('title', 'Kelola User')
@section('content')
<div class="page-head-row">
    <div class="page-head" style="margin-bottom:0;">
        <div class="eyebrow">Master Data</div>
        <h4>User</h4>
        <p>Kelola akun user yang dapat mengajukan keluhan.</p>
    </div>
    <a class="btn blue modal-trigger" href="#modal-add"><i class="material-icons left">add</i>Tambah</a>
</div>
<div class="panel-card flush" style="margin-top:20px;">
<table id="example" class="display responsive-table hx-row" style="width:100%">
    <thead><tr><th>No</th><th>Nama</th><th>Username</th><th>Opsi</th></tr></thead>
    <tbody>
    @foreach($users as $i => $u)
        <tr>
            <td>{{ $i + 1 }}</td><td>{{ $u->name }}</td><td>{{ $u->username }}</td>
            <td class="hx-actions">
                <a class="hx-btn-act modal-trigger" href="#edit-{{ $u->id }}"><i class="material-icons" aria-hidden="true">edit</i>Edit</a>
                <form method="POST" action="{{ route('superadmin.users.destroy', $u) }}" class="hx-btn-act-form" onsubmit="return confirm('Anda Yakin Ingin Menghapus?')">
                    @csrf @method('DELETE')
                    <button class="hx-btn-act danger" type="submit"><i class="material-icons" aria-hidden="true">delete_outline</i>Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
@foreach($users as $u)
    <div id="edit-{{ $u->id }}" class="modal">
        <div class="modal-content">
            <h5 class="hx-modal-title">Edit User</h5>
            <form method="POST" action="{{ route('superadmin.users.update', $u) }}">
                @csrf @method('PUT')
                <div class="input-field"><label>Nama</label><input type="text" name="name" value="{{ $u->name }}" required></div>
                <div class="input-field"><label>Username</label><input type="text" name="username" value="{{ $u->username }}" required></div>
                <div class="input-field"><label>Password baru (kosongkan jika tidak diubah)</label><input type="password" name="password"></div>
                <input type="submit" value="Simpan" class="btn blue right">
                <div style="clear:both;"></div>
            </form>
        </div>
        <div class="modal-footer"><a href="#!" class="modal-close waves-effect btn-flat">Tutup</a></div>
    </div>
@endforeach
<div id="modal-add" class="modal">
    <div class="modal-content">
        <h5 class="hx-modal-title">Tambah User</h5>
        <form method="POST" action="{{ route('superadmin.users.store') }}">
            @csrf
            <div class="input-field"><label>Nama</label><input type="text" name="name" required></div>
            <div class="input-field"><label>Username</label><input type="text" name="username" required></div>
            <div class="input-field"><label>Password</label><input type="password" name="password" required></div>
            <input type="submit" value="Simpan" class="btn blue right">
            <div style="clear:both;"></div>
        </form>
    </div>
    <div class="modal-footer"><a href="#!" class="modal-close waves-effect btn-flat">Tutup</a></div>
</div>
@endsection
