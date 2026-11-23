@extends('layouts.app')

@section('title', 'Kelola Admin')
@section('content')
<div class="page-head-row">
    <div class="page-head" style="margin-bottom:0;">
        <div class="eyebrow">Master Data</div>
        <h4>Admin</h4>
        <p>Kelola akun admin dan superadmin penanggap keluhan.</p>
    </div>
    <a class="btn blue modal-trigger" href="#modal-add"><i class="material-icons left">add</i>Tambah</a>
</div>
<div class="panel-card flush" style="margin-top:20px;">
<table id="example" class="display responsive-table hx-row" style="width:100%">
    <thead><tr><th>No</th><th>Nama</th><th>Username</th><th>Level</th><th>Opsi</th></tr></thead>
    <tbody>
    @foreach($admins as $i => $a)
        <tr>
            <td>{{ $i + 1 }}</td><td>{{ $a->name }}</td><td>{{ $a->username }}</td>
            <td><span class="status-pill {{ $a->role === 'superadmin' ? 'selesai' : 'proses' }}">{{ $a->role }}</span></td>
            <td class="hx-actions">
                <a class="hx-btn-act modal-trigger" href="#aedit-{{ $a->id }}"><i class="material-icons" aria-hidden="true">edit</i>Edit</a>
                <form method="POST" action="{{ route('superadmin.admins.destroy', $a) }}" class="hx-btn-act-form" onsubmit="return confirm('Anda Yakin Ingin Menghapus?')">
                    @csrf @method('DELETE')
                    <button class="hx-btn-act danger" type="submit"><i class="material-icons" aria-hidden="true">delete_outline</i>Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
@foreach($admins as $a)
    <div id="aedit-{{ $a->id }}" class="modal">
        <div class="modal-content">
            <h5 class="hx-modal-title">Edit Admin</h5>
            <form method="POST" action="{{ route('superadmin.admins.update', $a) }}">
                @csrf @method('PUT')
                <div class="input-field"><label>Nama</label><input type="text" name="name" value="{{ $a->name }}" required></div>
                <div class="input-field"><label>Username</label><input type="text" name="username" value="{{ $a->username }}" required></div>
                <div class="input-field"><label>Password baru (kosongkan jika tidak diubah)</label><input type="password" name="password"></div>
                <p>
                    <label><input value="superadmin" class="with-gap" name="role" type="radio" @checked($a->role === 'superadmin') /><span>Superadmin</span></label>
                    <label style="margin-left:16px;"><input value="admin" class="with-gap" name="role" type="radio" @checked($a->role === 'admin') /><span>Admin</span></label>
                </p>
                <input type="submit" value="Simpan" class="btn blue right">
                <div style="clear:both;"></div>
            </form>
        </div>
        <div class="modal-footer"><a href="#!" class="modal-close waves-effect btn-flat">Tutup</a></div>
    </div>
@endforeach
<div id="modal-add" class="modal">
    <div class="modal-content">
        <h5 class="hx-modal-title">Tambah Admin</h5>
        <form method="POST" action="{{ route('superadmin.admins.store') }}">
            @csrf
            <div class="input-field"><label>Nama</label><input type="text" name="name" required></div>
            <div class="input-field"><label>Username</label><input type="text" name="username" required></div>
            <div class="input-field"><label>Password</label><input type="password" name="password" required></div>
            <div class="input-field">
                <select name="role" required>
                    <option value="" disabled selected>Pilih Level</option>
                    <option value="superadmin">Superadmin</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <input type="submit" value="Simpan" class="btn blue right">
            <div style="clear:both;"></div>
        </form>
    </div>
    <div class="modal-footer"><a href="#!" class="modal-close waves-effect btn-flat">Tutup</a></div>
</div>
@endsection
