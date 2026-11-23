<!DOCTYPE html>
<html>
<head>
    <title>E-Complain - Laporan</title>
    <style>
        body { font-family: "Segoe UI", Roboto, Arial, sans-serif; color: #14142b; }
        .kop { display: flex; align-items: center; gap: 16px; border-bottom: 3px double #0000c4; padding-bottom: 12px; margin-bottom: 8px; }
        .kop img { width: 64px; height: 64px; object-fit: contain; }
        .kop h2 { margin: 0; color: #0000c4; }
        .kop p { margin: 2px 0 0; font-size: 13px; color: #5a5a72; }
        .meta { font-size: 13px; color: #5a5a72; margin: 0 0 16px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { border: 1px solid #9092ab; padding: 6px 8px; text-align: left; }
        thead tr { background: #eef0ff; }
    </style>
</head>
<body>
<div class="kop">
    <img src="https://upload.wikimedia.org/wikipedia/commons/9/94/Logo_SMKN_1_Cimahi-2014.jpg" alt="Logo">
    <div>
        <h2>Laporan E-Complain</h2>
        <p>Layanan Pengaduan Sekolah</p>
    </div>
</div>
<p class="meta">Dicetak pada: {{ now()->format('d-m-Y H:i') }} &middot; Total: {{ $complains->count() }} keluhan ditanggapi{{ $periode ? ' · Periode: '.$periode : '' }}</p>
<table>
    <thead>
    <tr>
        <th>No</th><th>ID</th><th>Nama Pelapor</th><th>Nama Admin</th>
        <th>Tanggal Masuk</th><th>Tanggal Ditanggapi</th><th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($complains as $i => $c)
        <tr>
            <td>{{ $i + 1 }}</td><td>{{ $c->id }}</td><td>{{ $c->user->name ?? '-' }}</td>
            <td>{{ $c->response->admin->name ?? '-' }}</td>
            <td>{{ $c->tgl_keluhan->format('Y-m-d') }}</td><td>{{ $c->response->tgl_tanggapan->format('Y-m-d') }}</td>
            <td>{{ $c->status }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<script type="text/javascript">window.print();</script>
</body>
</html>
