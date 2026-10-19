<!DOCTYPE html>
<html>
<head>
    <title>E-Complain - @yield('title', 'Dashboard')</title>
    <link rel="shortcut icon" href="https://upload.wikimedia.org/wikipedia/commons/9/94/Logo_SMKN_1_Cimahi-2014.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/materialize.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/e-complain.css') }}">
    @stack('styles')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="{{ asset('js/materialize.min.js') }}"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script type="text/javascript" charset="utf8" src="{{ asset('js/datatables.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#example').DataTable();
            $('select').formSelect();
        });
    </script>
</head>
<body>
<aside class="sidebar-fixed" id="sidebar">
    <div class="sidebar-profile">
        <div class="avatar"><i class="material-icons">person</i></div>
        <div class="profile-name">{{ auth()->user()->name }}</div>
        <span class="role-badge">{{ ucfirst(auth()->user()->role) }}</span>
        <div style="height:16px;"></div>
    </div>
    <ul class="sidebar-menu">
        @if(auth()->user()->role === 'user')
            <li class="has-sub {{ request()->routeIs('dashboard', 'riwayat', 'complains.show') ? 'open' : '' }}">
                <a href="#" class="sub-toggle"><i class="material-icons">grid_view</i>Dashboard<i class="material-icons sub-arrow">expand_more</i></a>
                <ul class="sub-menu">
                    <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><a href="{{ route('dashboard') }}"><i class="material-icons">edit_note</i>Ajukan Keluhan</a></li>
                    <li class="{{ request()->routeIs('riwayat', 'complains.show') ? 'active' : '' }}"><a href="{{ route('riwayat') }}"><i class="material-icons">history</i>Riwayat Keluhan</a></li>
                </ul>
            </li>
            <li class="{{ request()->routeIs('faq') ? 'active' : '' }}"><a href="{{ route('faq') }}"><i class="material-icons">quiz</i>FAQ</a></li>
        @elseif(auth()->user()->role === 'admin')
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><a href="{{ route('admin.dashboard') }}"><i class="material-icons">grid_view</i>Dashboard</a></li>
            <li class="{{ request()->routeIs('admin.complains') ? 'active' : '' }}"><a href="{{ route('admin.complains') }}"><i class="material-icons">report</i>Complain</a></li>
            <li class="{{ request()->routeIs('admin.responses') ? 'active' : '' }}"><a href="{{ route('admin.responses') }}"><i class="material-icons">task_alt</i>Selesai</a></li>
        @elseif(auth()->user()->role === 'superadmin')
            <li class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}"><a href="{{ route('superadmin.dashboard') }}"><i class="material-icons">grid_view</i>Dashboard</a></li>
            <li class="{{ request()->routeIs('superadmin.complains') ? 'active' : '' }}"><a href="{{ route('superadmin.complains') }}"><i class="material-icons">report</i>Complain</a></li>
            <li class="{{ request()->routeIs('superadmin.responses') ? 'active' : '' }}"><a href="{{ route('superadmin.responses') }}"><i class="material-icons">task_alt</i>Selesai</a></li>
            <li class="{{ request()->routeIs('superadmin.users') ? 'active' : '' }}"><a href="{{ route('superadmin.users') }}"><i class="material-icons">account_box</i>Tambah User</a></li>
            <li class="{{ request()->routeIs('superadmin.admins') ? 'active' : '' }}"><a href="{{ route('superadmin.admins') }}"><i class="material-icons">security</i>Tambah Admin</a></li>
            <li class="{{ request()->routeIs('superadmin.tujuans') ? 'active' : '' }}"><a href="{{ route('superadmin.tujuans') }}"><i class="material-icons">add_circle</i>Tambah Tujuan</a></li>
            <li class="{{ request()->routeIs('superadmin.jenis') ? 'active' : '' }}"><a href="{{ route('superadmin.jenis') }}"><i class="material-icons">add_box</i>Tambah Jenis</a></li>
            <li class="{{ request()->routeIs('superadmin.faqs') ? 'active' : '' }}"><a href="{{ route('superadmin.faqs') }}"><i class="material-icons">quiz</i>Tambah FAQ</a></li>
            <li class="{{ request()->routeIs('superadmin.laporan*') ? 'active' : '' }}"><a href="{{ route('superadmin.laporan') }}"><i class="material-icons">book</i>Laporan</a></li>
        @endif
    </ul>
    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="material-icons">logout</i>Logout</a>
    </div>
</aside>

<form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>

<div class="topbar">
    <a href="#" id="menu-toggle"><i class="material-icons">menu</i></a>
    <span>E-Complain</span>
</div>
<div class="corner-ornament"></div>

<main class="main-content">
    @if(session('success'))
        <div class="card-panel teal white-text">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="card-panel red white-text">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="card-panel red white-text">
            <ul style="margin:0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        M.Modal.init(document.querySelectorAll('.modal'));
        document.getElementById('menu-toggle').addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('sidebar').classList.toggle('open');
        });
        document.querySelectorAll('.sub-toggle').forEach(function (t) {
            t.addEventListener('click', function (e) {
                e.preventDefault();
                this.closest('.has-sub').classList.toggle('open');
            });
        });
    });
</script>
</body>
</html>
