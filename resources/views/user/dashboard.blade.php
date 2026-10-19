@extends('layouts.app')

@section('title', 'Dashboard User')
@section('content')
<div class="page-head">
    <div class="eyebrow">Formulir</div>
    <h4>Ajukan Keluhan</h4>
    <p>Sampaikan keluhan fasilitas sekolahmu di sini.</p>
</div>
<div class="panel-card" style="max-width:640px;">
    @if($tujuans->isEmpty() || $jenisList->isEmpty())
        <div class="card-panel red white-text" style="margin-bottom:16px;">
            Data tujuan atau jenis keluhan belum tersedia. Hubungi superadmin untuk melengkapi master data.
        </div>
    @endif
    <form method="POST" action="{{ route('complains.store') }}" enctype="multipart/form-data">
        @csrf
        <label for="tujuan_id">Tujuan:</label>
        <select id="tujuan_id" name="tujuan_id" required>
            <option value="">- pilih tujuan -</option>
            @foreach($tujuans as $tujuan)
                <option value="{{ $tujuan->id }}" @selected(old('tujuan_id') == $tujuan->id)>{{ $tujuan->nama_unit }}</option>
            @endforeach
        </select>
        @error('tujuan_id')
            <span class="red-text" style="font-size:12.5px;">{{ $message }}</span>
        @enderror
        <label for="jenis_id" style="margin-top:16px; display:block;">Jenis Keluhan:</label>
        <select id="jenis_id" name="jenis_id" required>
            <option value="">- pilih jenis -</option>
            @foreach($jenisList as $jenis)
                <option value="{{ $jenis->id }}" @selected(old('jenis_id') == $jenis->id)>{{ $jenis->nama_jenis }}</option>
            @endforeach
        </select>
        @error('jenis_id')
            <span class="red-text" style="font-size:12.5px;">{{ $message }}</span>
        @enderror
        <textarea class="materialize-textarea" name="keluhan" placeholder="Tulis Keluhan" required>{{ old('keluhan') }}</textarea>
        @error('keluhan')
            <span class="red-text" style="font-size:12.5px;">{{ $message }}</span>
        @enderror
        <label style="margin-top:16px; display:block;">Gambar <span style="color:#78909c; font-size:12.5px;">(JPG/PNG, maks 1MB, opsional)</span></label>
        <input type="file" name="lampiran" accept="image/jpeg,image/png,image/jpg" style="margin:8px 0 4px;">
        @error('lampiran')
            <br><span class="red-text" style="font-size:12.5px;">{{ $message }}</span>
        @enderror
        <br>
        <input type="submit" value="Kirim" class="btn blue" style="margin-top:16px;">
    </form>
</div>
@endsection
