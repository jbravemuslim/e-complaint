<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Complain — Sampaikan Keluhanmu</title>
    <link rel="shortcut icon" href="https://upload.wikimedia.org/wikipedia/commons/9/94/Logo_SMKN_1_Cimahi-2014.jpg">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>

<nav class="nav">
    <a class="brand" href="{{ route('home') }}">
        <img src="https://upload.wikimedia.org/wikipedia/commons/9/94/Logo_SMKN_1_Cimahi-2014.jpg" alt="Logo SMKN 1 Cimahi">
        <span>E-Complain</span>
    </a>
    <div class="nav-actions">
        <a class="btn btn-primary btn-sm" href="{{ route('login') }}">Login</a>
        <a class="nav-register" href="{{ route('register') }}">Daftar</a>
    </div>
</nav>

<header class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <span class="badge">Layanan Pengaduan Sekolah</span>
            <h1>Punya Keluhan soal Fasilitas Sekolah? Sampaikan di Sini.</h1>
            <p>E-Complain adalah kanal resmi pengaduan lingkungan sekolah. Tulis keluhanmu, lampirkan foto, pantau statusnya, dan terima tanggapan langsung dari admin unit terkait — semua dalam satu tempat.</p>
            <div class="hero-cta">
                <a class="btn btn-primary" href="{{ route('register') }}">Ajukan Keluhan</a>
                <a class="btn btn-outline" href="#cara-kerja">Lihat Cara Kerja</a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-ring"></div>
            <div class="hero-logo">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/94/Logo_SMKN_1_Cimahi-2014.jpg" alt="Logo SMKN 1 Cimahi">
            </div>
            <span class="hero-chip chip-1"><i class="material-icons">support_agent</i> Respon Admin</span>
            <span class="hero-chip chip-2"><i class="material-icons">verified</i> Tercatat Sistem</span>
        </div>
    </div>
</header>

<section class="section" id="cara-kerja">
    <p class="eyebrow">Cara Kerja</p>
    <h2>Dari Keluhan sampai Ditanggapi</h2>
    <p class="lead">Empat langkah mudah sebelum dan sesudah kamu mengajukan complain.</p>
    <div class="steps">
        <div class="step reveal">
            <span class="step-num">1</span>
            <div class="step-icon"><i class="material-icons">login</i></div>
            <h3>Daftar / Login Akun</h3>
            <p>Buat akun dengan nama, username, dan password — atau masuk jika sudah punya akun.</p>
        </div>
        <div class="step reveal">
            <span class="step-num">2</span>
            <div class="step-icon"><i class="material-icons">edit_note</i></div>
            <h3>Tulis Keluhan</h3>
            <p>Pilih unit tujuan dan jenis keluhan, tulis kronologinya, dan lampirkan foto (jpg/png, maks 1MB) bila ada.</p>
        </div>
        <div class="step reveal">
            <span class="step-num">3</span>
            <div class="step-icon"><i class="material-icons">timeline</i></div>
            <h3>Pantau Status</h3>
            <p>Lihat status keluhanmu di Riwayat Keluhan: <em>proses</em> berarti sedang ditangani, <em>selesai</em> berarti sudah ditanggapi.</p>
        </div>
        <div class="step reveal">
            <span class="step-num">4</span>
            <div class="step-icon"><i class="material-icons">task_alt</i></div>
            <h3>Terima Tanggapan</h3>
            <p>Admin unit terkait memberi tanggapan langsung pada keluhanmu. Klik More untuk membaca detailnya.</p>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="wrap">
        <p class="eyebrow">Kenapa E-Complain</p>
        <h2>Keluhanmu Tercatat &amp; Terpantau</h2>
        <p class="lead">Tidak ada lagi keluhan yang hilang begitu saja.</p>
        <div class="features">
            <div class="feature reveal">
                <i class="material-icons">photo_camera</i>
                <div>
                    <h3>Bukti Foto</h3>
                    <p>Lampirkan foto kondisi di lapangan agar admin cepat memahami masalahnya.</p>
                </div>
            </div>
            <div class="feature reveal">
                <i class="material-icons">manage_search</i>
                <div>
                    <h3>Riwayat Transparan</h3>
                    <p>Semua keluhanmu tersimpan rapi lengkap dengan tanggal dan statusnya.</p>
                </div>
            </div>
            <div class="feature reveal">
                <i class="material-icons">support_agent</i>
                <div>
                    <h3>Respon Admin</h3>
                    <p>Setiap keluhan yang masuk wajib ditanggapi oleh admin unit tujuan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-band">
    <h2>Siap Menyampaikan Keluhan?</h2>
    <p>Daftar atau login sekarang dan sampaikan aspirasimu untuk lingkungan sekolah yang lebih baik.</p>
    <a class="btn btn-primary" href="{{ route('register') }}">Daftar untuk Mengajukan Keluhan</a>
</section>

<footer>
    E-Complain &middot; Layanan Pengaduan Sekolah
</footer>

<script>
(function () {
    var els = document.querySelectorAll('.reveal');
    if (!('IntersectionObserver' in window)) {
        els.forEach(function (el) { el.classList.add('visible'); });
        return;
    }
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    els.forEach(function (el) { io.observe(el); });
})();
</script>

</body>
</html>
