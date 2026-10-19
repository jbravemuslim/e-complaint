<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Complain - @yield('title', 'Login')</title>
    <link rel="shortcut icon" href="https://upload.wikimedia.org/wikipedia/commons/9/94/Logo_SMKN_1_Cimahi-2014.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
<div class="auth-split">
    <aside class="auth-brand">
        <div class="auth-brand-inner">
            <a class="auth-logo" href="{{ route('home') }}">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/94/Logo_SMKN_1_Cimahi-2014.jpg" alt="Logo SMKN 1 Cimahi">
                <span>E-Complain</span>
            </a>
            <h1>Satu Kanal untuk Semua Keluhan Sekolah.</h1>
            <p class="auth-tagline">Sampaikan keluhan fasilitas, pantau statusnya, dan terima tanggapan admin — cepat dan transparan.</p>
            <ul class="auth-points">
                <li><i class="material-icons">photo_camera</i><span>Lampirkan foto sebagai bukti</span></li>
                <li><i class="material-icons">timeline</i><span>Pantau status proses sampai selesai</span></li>
                <li><i class="material-icons">support_agent</i><span>Ditanggapi admin unit terkait</span></li>
            </ul>
        </div>
        <div class="auth-orb auth-orb-1"></div>
        <div class="auth-orb auth-orb-2"></div>
    </aside>
    <main class="auth-main">
        @yield('content')
    </main>
</div>
<script>
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.pw-toggle');
    if (!btn) return;
    var input = document.getElementById(btn.dataset.target);
    if (!input) return;
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.querySelector('.material-icons').textContent = show ? 'visibility_off' : 'visibility';
});
</script>
</body>
</html>
