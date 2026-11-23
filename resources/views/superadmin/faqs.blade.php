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
<table id="example" class="display responsive-table hx-row" style="width:100%">
    <thead><tr><th>No</th><th>Pertanyaan</th><th>Jawaban</th><th>Opsi</th></tr></thead>
    <tbody>
    @foreach($faqs as $i => $f)
        <tr>
            <td>{{ $i + 1 }}</td><td>{{ $f->pertanyaan }}</td><td>{{ $f->jawaban }}</td>
            <td class="hx-actions">
                <a class="hx-btn-act modal-trigger" href="#fedit-{{ $f->id }}"><i class="material-icons" aria-hidden="true">edit</i>Edit</a>
                <form method="POST" action="{{ route('superadmin.faqs.destroy', $f) }}" class="hx-btn-act-form" onsubmit="return confirm('Anda Yakin Ingin Menghapus?')">
                    @csrf @method('DELETE')
                    <button class="hx-btn-act danger" type="submit"><i class="material-icons" aria-hidden="true">delete_outline</i>Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
@foreach($faqs as $f)
    <div id="fedit-{{ $f->id }}" class="modal">
        <div class="modal-content">
            <h5 class="hx-modal-title">Edit FAQ</h5>
            <form method="POST" action="{{ route('superadmin.faqs.update', $f) }}">
                @csrf @method('PUT')
                <div class="input-field"><label>Pertanyaan</label><input type="text" name="pertanyaan" value="{{ $f->pertanyaan }}" required></div>
                <div class="input-field"><label>Jawaban</label><input type="text" name="jawaban" value="{{ $f->jawaban }}" required></div>
                <input type="submit" value="Simpan" class="btn blue right">
                <div style="clear:both;"></div>
            </form>
        </div>
        <div class="modal-footer"><a href="#!" class="modal-close waves-effect btn-flat">Tutup</a></div>
    </div>
@endforeach
<div id="modal-add" class="modal">
    <div class="modal-content">
        <h5 class="hx-modal-title">Tambah FAQ</h5>
        <form method="POST" action="{{ route('superadmin.faqs.store') }}">
            @csrf
            <div class="input-field"><label>Pertanyaan</label><input type="text" name="pertanyaan" required></div>
            <div class="input-field"><label>Jawaban</label><input type="text" name="jawaban" required></div>
            <input type="submit" value="Simpan" class="btn blue right">
            <div style="clear:both;"></div>
        </form>
    </div>
    <div class="modal-footer"><a href="#!" class="modal-close waves-effect btn-flat">Tutup</a></div>
</div>
@endsection
