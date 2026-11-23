@extends('layouts.guest')

@section('title', 'Login')
@section('content')
<div class="auth-card">
    <h2>Selamat Datang Kembali</h2>
    <p class="auth-sub">Masuk untuk mengajukan dan memantau keluhanmu.</p>
    @if($errors->any())
        <div class="auth-alert">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <div class="field">
            <i class="material-icons">person</i>
            <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder=" " required autofocus autocomplete="username">
            <label for="username">Username</label>
        </div>
        <div class="field has-toggle">
            <i class="material-icons">lock</i>
            <input id="password" type="password" name="password" placeholder=" " required autocomplete="current-password">
            <label for="password">Password</label>
            <button type="button" class="pw-toggle" data-target="password" aria-label="Tampilkan password"><i class="material-icons">visibility</i></button>
        </div>
        <button type="submit" class="btn-auth">Login</button>
    </form>
    <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
    <a class="auth-back" href="{{ route('home') }}"><i class="material-icons">arrow_back</i> Kembali ke Beranda</a>
</div>
@endsection
