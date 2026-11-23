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
        <div class="hx-alert error" style="margin-bottom:16px;">
            <i class="material-icons" aria-hidden="true">error_outline</i>
            <span>Data tujuan atau jenis keluhan belum tersedia. Hubungi superadmin untuk melengkapi master data.</span>
        </div>
    @endif
    <form class="hx-form" method="POST" action="{{ route('complains.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="hx-field">
            <label for="tujuan_id">Tujuan</label>
            <select id="tujuan_id" name="tujuan_id" required>
                <option value="" disabled @selected(!old('tujuan_id'))>- pilih tujuan -</option>
                @foreach($tujuans as $tujuan)
                    <option value="{{ $tujuan->id }}" @selected(old('tujuan_id') == $tujuan->id)>{{ $tujuan->nama_unit }}</option>
                @endforeach
            </select>
            @error('tujuan_id')
                <span class="hx-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="hx-field">
            <label for="jenis_id">Jenis Keluhan</label>
            <select id="jenis_id" name="jenis_id" required>
                <option value="" disabled @selected(!old('jenis_id'))>- pilih jenis -</option>
                @foreach($jenisList as $jenis)
                    <option value="{{ $jenis->id }}" @selected(old('jenis_id') == $jenis->id)>{{ $jenis->nama_jenis }}</option>
                @endforeach
            </select>
            @error('jenis_id')
                <span class="hx-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="hx-field">
            <label for="keluhan">Tulis Keluhan</label>
            <textarea id="keluhan" name="keluhan" placeholder="Jelaskan kronologi keluhanmu..." required>{{ old('keluhan') }}</textarea>
            @error('keluhan')
                <span class="hx-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="hx-field">
            <label>Gambar <span class="opt">(JPG/PNG, maks 1MB, opsional)</span></label>
            <input type="file" name="lampiran" accept="image/jpeg,image/png,image/jpg">
            @error('lampiran')
                <span class="hx-error">{{ $message }}</span>
            @enderror
        </div>
        <input type="submit" value="Kirim Keluhan" class="btn blue" style="margin-top:8px;">
    </form>
</div>
@endsection
