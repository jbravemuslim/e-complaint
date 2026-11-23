<!DOCTYPE html>
<html>
<head>
    <title>E-Complain - @yield('title', 'Dashboard')</title>
    <link rel="shortcut icon" href="https://upload.wikimedia.org/wikipedia/commons/9/94/Logo_SMKN_1_Cimahi-2014.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/materialize.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/e-complain.css') }}">
    <link rel="stylesheet" href="{{ asset('css/hermes.css') }}">
    @stack('styles')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="{{ asset('js/materialize.min.js') }}"></script>
    <script src="{{ asset('js/hermes.js') }}"></script>
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
        <div class="avatar initial" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="profile-name">{{ auth()->user()->name }}</div>
        <span class="role-badge">{{ ucfirst(auth()->user()->role) }}</span>
        <div style="height:16px;"></div>
    </div>
    <ul class="sidebar-menu">
        @php
            $hxUnread = auth()->user()->role === 'user'
                ? \App\Models\Complain::where('user_id', auth()->id())->where('status', 'selesai')->whereNull('user_seen_at')->count()
                : \App\Models\Complain::where('status', 'proses')->count();
            // Dibangun di sini (bukan @if inline) karena Blade tidak mengompilasi @if sebaris HTML.
            // Aman: $hxUnread selalu integer dari count().
            $hxBadge = $hxUnread ? '<span class="hx-badge">'.$hxUnread.'</span>' : '';
        @endphp
        @if(auth()->user()->role === 'user')
            <li class="has-sub {{ request()->routeIs('dashboard', 'riwayat', 'complains.show') ? 'open' : '' }}">
                <a href="#" class="sub-toggle"><i class="material-icons" aria-hidden="true">grid_view</i>Dashboard<i class="material-icons sub-arrow" aria-hidden="true">expand_more</i></a>
                <ul class="sub-menu">
                    <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><a href="{{ route('dashboard') }}"><i class="material-icons" aria-hidden="true">edit_note</i>Ajukan Keluhan</a></li>
                    <li class="{{ request()->routeIs('riwayat', 'complains.show') ? 'active' : '' }}"><a href="{{ route('riwayat') }}"><i class="material-icons" aria-hidden="true">history</i>Riwayat Keluhan{!! $hxBadge !!}</a></li>
                </ul>
            </li>
            <li class="{{ request()->routeIs('faq') ? 'active' : '' }}"><a href="{{ route('faq') }}"><i class="material-icons" aria-hidden="true">quiz</i>FAQ</a></li>
        @elseif(auth()->user()->role === 'admin')
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><a href="{{ route('admin.dashboard') }}"><i class="material-icons" aria-hidden="true">grid_view</i>Dashboard</a></li>
            <li class="{{ request()->routeIs('admin.complains') ? 'active' : '' }}"><a href="{{ route('admin.complains') }}"><i class="material-icons" aria-hidden="true">report</i>Complain{!! $hxBadge !!}</a></li>
            <li class="{{ request()->routeIs('admin.responses') ? 'active' : '' }}"><a href="{{ route('admin.responses') }}"><i class="material-icons" aria-hidden="true">task_alt</i>Selesai</a></li>
        @elseif(auth()->user()->role === 'superadmin')
            <li class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}"><a href="{{ route('superadmin.dashboard') }}"><i class="material-icons" aria-hidden="true">grid_view</i>Dashboard</a></li>
            <li class="{{ request()->routeIs('superadmin.complains') ? 'active' : '' }}"><a href="{{ route('superadmin.complains') }}"><i class="material-icons" aria-hidden="true">report</i>Complain{!! $hxBadge !!}</a></li>
            <li class="{{ request()->routeIs('superadmin.responses') ? 'active' : '' }}"><a href="{{ route('superadmin.responses') }}"><i class="material-icons" aria-hidden="true">task_alt</i>Selesai</a></li>
            <li class="{{ request()->routeIs('superadmin.users') ? 'active' : '' }}"><a href="{{ route('superadmin.users') }}"><i class="material-icons" aria-hidden="true">account_box</i>Tambah User</a></li>
            <li class="{{ request()->routeIs('superadmin.admins') ? 'active' : '' }}"><a href="{{ route('superadmin.admins') }}"><i class="material-icons" aria-hidden="true">security</i>Tambah Admin</a></li>
            <li class="{{ request()->routeIs('superadmin.tujuans') ? 'active' : '' }}"><a href="{{ route('superadmin.tujuans') }}"><i class="material-icons" aria-hidden="true">add_circle</i>Tambah Tujuan</a></li>
            <li class="{{ request()->routeIs('superadmin.jenis') ? 'active' : '' }}"><a href="{{ route('superadmin.jenis') }}"><i class="material-icons" aria-hidden="true">add_box</i>Tambah Jenis</a></li>
            <li class="{{ request()->routeIs('superadmin.faqs') ? 'active' : '' }}"><a href="{{ route('superadmin.faqs') }}"><i class="material-icons" aria-hidden="true">quiz</i>Tambah FAQ</a></li>
            <li class="{{ request()->routeIs('superadmin.laporan*') ? 'active' : '' }}"><a href="{{ route('superadmin.laporan') }}"><i class="material-icons" aria-hidden="true">book</i>Laporan</a></li>
        @endif
    </ul>
    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="material-icons" aria-hidden="true">logout</i>Logout</a>
    </div>
</aside>

@include('layouts._palette')

<form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>

<div class="topbar">
    <a href="#" id="menu-toggle" aria-label="Buka menu"><i class="material-icons" aria-hidden="true">menu</i></a>
    <span>E-Complain</span>
</div>
<div class="corner-ornament"></div>

<main class="main-content">
    @if(session('success'))
        <div class="hx-alert success"><i class="material-icons" aria-hidden="true">check_circle</i><span>{{ session('success') }}</span></div>
    @endif
    @if(session('error'))
        <div class="hx-alert error"><i class="material-icons" aria-hidden="true">error_outline</i><span>{{ session('error') }}</span></div>
    @endif
    @if($errors->any())
        <div class="hx-alert error">
            <i class="material-icons" aria-hidden="true">error_outline</i>
            <div>
                <ul style="margin:0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
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
