@extends('layouts.app')

@section('title', 'Kelola FAQ')
@section('content')
<div class="page-head-row">
    <div class="page-head" style="margin-bottom:0;">
        <div class="eyebrow">Master Data</div>
        <h4>FAQ</h4>
        <p>Daftar pertanyaan dan jawaban yang tampil di halaman user.</p>
    </div>
    <a class="btn blue modal-trigger" href="#modal-add"><i class="material-icons left">add</i>Tambah</a>
</div>
<div class="panel-card flush" style="margin-top:20px;">
<table id="example" class="display responsive-table" style="width:100%">
    <thead><tr><th>No</th><th>Pertanyaan</th><th>Jawaban</th><th>Opsi</th></tr></thead>
    <tbody>
    @foreach($faqs as $i => $f)
        <tr>
            <td>{{ $i + 1 }}</td><td>{{ $f->pertanyaan }}</td><td>{{ $f->jawaban }}</td>
            <td>
                <a class="btn teal modal-trigger" href="#fedit-{{ $f->id }}">Edit</a>
                <form method="POST" action="{{ route('superadmin.faqs.destroy', $f) }}" style="display:inline" onsubmit="return confirm('Anda Yakin Ingin Menghapus?')">
                    @csrf @method('DELETE')
                    <button class="red btn" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
        <div id="fedit-{{ $f->id }}" class="modal">
            <div class="modal-content">
                <h4 class="blue-text">Edit FAQ</h4>
                <form method="POST" action="{{ route('superadmin.faqs.update', $f) }}">
                    @csrf @method('PUT')
                    <div class="input-field"><label>Pertanyaan</label><input type="text" name="pertanyaan" value="{{ $f->pertanyaan }}" required></div>
                    <div class="input-field"><label>Jawaban</label><input type="text" name="jawaban" value="{{ $f->jawaban }}" required></div>
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
        <h4 class="blue-text">Tambah FAQ</h4>
        <form method="POST" action="{{ route('superadmin.faqs.store') }}">
            @csrf
            <div class="input-field"><label>Pertanyaan</label><input type="text" name="pertanyaan" required></div>
            <div class="input-field"><label>Jawaban</label><input type="text" name="jawaban" required></div>
            <input type="submit" value="Simpan" class="btn blue right">
            <div style="clear:both;"></div>
        </form>
    </div>
    <div class="modal-footer"><a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a></div>
</div>
@endsection
