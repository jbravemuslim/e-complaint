@extends('layouts.guest')

@section('title', 'Daftar')
@section('content')
<div class="auth-card">
    <h2>Daftar Akun</h2>
    <p class="auth-sub">Buat akun untuk mulai menyampaikan keluhanmu.</p>
    <form method="POST" action="{{ route('register.attempt') }}">
        @csrf
        <div class="field @error('name') invalid @enderror">
            <i class="material-icons">badge</i>
            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder=" " required autofocus autocomplete="name">
            <label for="name">Nama</label>
            @error('name')<small class="field-error">{{ $message }}</small>@enderror
        </div>
        <div class="field @error('username') invalid @enderror">
            <i class="material-icons">person</i>
            <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder=" " required autocomplete="username">
            <label for="username">Username</label>
            @error('username')<small class="field-error">{{ $message }}</small>@enderror
        </div>
        <div class="field has-toggle @error('password') invalid @enderror">
            <i class="material-icons">lock</i>
            <input id="password" type="password" name="password" placeholder=" " required autocomplete="new-password">
            <label for="password">Password</label>
            <button type="button" class="pw-toggle" data-target="password" aria-label="Tampilkan password"><i class="material-icons">visibility</i></button>
            @error('password')<small class="field-error">{{ $message }}</small>@enderror
        </div>
        <div class="field has-toggle">
            <i class="material-icons">lock_outline</i>
            <input id="password_confirmation" type="password" name="password_confirmation" placeholder=" " required autocomplete="new-password">
            <label for="password_confirmation">Konfirmasi Password</label>
            <button type="button" class="pw-toggle" data-target="password_confirmation" aria-label="Tampilkan password"><i class="material-icons">visibility</i></button>
        </div>
        <button type="submit" class="btn-auth">Daftar</button>
    </form>
    <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
    <a class="auth-back" href="{{ route('home') }}"><i class="material-icons">arrow_back</i> Kembali ke Beranda</a>
</div>
@endsection
