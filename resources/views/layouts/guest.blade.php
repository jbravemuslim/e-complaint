<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Complain - @yield('title', 'Login')</title>
    <link rel="shortcut icon" href="https://upload.wikimedia.org/wikipedia/commons/9/94/Logo_SMKN_1_Cimahi-2014.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/hermes.css') }}">
</head>
<body>
<div class="auth-split">
    <aside class="auth-brand">
        <div class="auth-brand-inner">
            <a class="auth-logo" href="{{ route('home') }}">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/94/Logo_SMKN_1_Cimahi-2014.jpg" alt="Logo SMKN 1 Cimahi">
                <span>E-Complain</span>
            </a>
            <p class="hx-mono auth-kicker">[ Portal Pengaduan ]</p>
            <h1 class="hx-display hx-gradient-text">Satu Kanal untuk Semua Keluhan Sekolah.</h1>
            <p class="auth-tagline">Sampaikan keluhan fasilitas, pantau statusnya, dan terima tanggapan admin — cepat dan transparan.</p>
            <ul class="auth-points">
                <li><i class="material-icons" aria-hidden="true">photo_camera</i><span>Lampirkan foto sebagai bukti</span></li>
                <li><i class="material-icons" aria-hidden="true">timeline</i><span>Pantau status proses sampai selesai</span></li>
                <li><i class="material-icons" aria-hidden="true">support_agent</i><span>Ditanggapi admin unit terkait</span></li>
            </ul>
        </div>
        <div class="hx-aurora" aria-hidden="true">
            <div class="hx-blob b-cobalt" style="width:380px;height:380px;left:-10%;top:-15%;"></div>
            <div class="hx-blob b-cyan" style="width:300px;height:300px;right:-8%;top:30%;opacity:.32;"></div>
            <div class="hx-blob b-magenta" style="width:280px;height:280px;left:35%;bottom:-25%;opacity:.28;"></div>
            <div class="hx-blob b-violet" style="width:320px;height:320px;right:10%;bottom:-30%;opacity:.3;"></div>
        </div>
        <canvas class="hx-particles" aria-hidden="true"></canvas>
        <div class="auth-orb auth-orb-1"></div>
        <div class="auth-orb auth-orb-2"></div>
    </aside>
    <main class="auth-main">
        <div class="hx-reveal">
            @yield('content')
        </div>
    </main>
</div>
<script src="{{ asset('js/hermes.js') }}"></script>
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
