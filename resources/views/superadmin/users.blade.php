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
<table id="example" class="display responsive-table" style="width:100%">
    <thead><tr><th>No</th><th>Nama</th><th>Username</th><th>Opsi</th></tr></thead>
    <tbody>
    @foreach($users as $i => $u)
        <tr>
            <td>{{ $i + 1 }}</td><td>{{ $u->name }}</td><td>{{ $u->username }}</td>
            <td>
                <a class="btn teal modal-trigger" href="#edit-{{ $u->id }}">Edit</a>
                <form method="POST" action="{{ route('superadmin.users.destroy', $u) }}" style="display:inline" onsubmit="return confirm('Anda Yakin Ingin Menghapus?')">
                    @csrf @method('DELETE')
                    <button class="red btn" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
        <div id="edit-{{ $u->id }}" class="modal">
            <div class="modal-content">
                <h4 class="blue-text">Edit User</h4>
                <form method="POST" action="{{ route('superadmin.users.update', $u) }}">
                    @csrf @method('PUT')
                    <div class="input-field"><label>Nama</label><input type="text" name="name" value="{{ $u->name }}" required></div>
                    <div class="input-field"><label>Username</label><input type="text" name="username" value="{{ $u->username }}" required></div>
                    <div class="input-field"><label>Password baru (kosongkan jika tidak diubah)</label><input type="password" name="password"></div>
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
        <h4 class="blue-text">Tambah User</h4>
        <form method="POST" action="{{ route('superadmin.users.store') }}">
            @csrf
            <div class="input-field"><label>Nama</label><input type="text" name="name" required></div>
            <div class="input-field"><label>Username</label><input type="text" name="username" required></div>
            <div class="input-field"><label>Password</label><input type="password" name="password" required></div>
            <input type="submit" value="Simpan" class="btn blue right">
            <div style="clear:both;"></div>
        </form>
    </div>
    <div class="modal-footer"><a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a></div>
</div>
@endsection
