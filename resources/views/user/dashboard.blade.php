@extends('layouts.app')

@section('title', 'Dashboard')
@section('content')
<div class="page-head">
    <div class="eyebrow">Beranda</div>
    <h4>Halo, <span class="accent">{{ Illuminate\Support\Str::before(auth()->user()->name, ' ') }}</span></h4>
    <p>Pantau perkembangan keluhanmu, lalu sampaikan yang baru di sini.</p>
</div>

<div class="stat-grid" style="margin-bottom: 24px;">
    <a class="stat-card stat-blue" href="{{ route('riwayat') }}">
        <div class="stat-icon"><i class="material-icons">receipt_long</i></div>
        <div>
            <div class="stat-num">{{ $stat['total'] }}</div>
            <div class="stat-label">Total Keluhanku</div>
        </div>
    </a>
    <a class="stat-card stat-amber" href="{{ route('riwayat') }}">
        <div class="stat-icon"><i class="material-icons">pending_actions</i></div>
        <div>
            <div class="stat-num">{{ $stat['proses'] }}</div>
            <div class="stat-label">Masih Diproses</div>
        </div>
    </a>
    <a class="stat-card stat-teal" href="{{ route('riwayat') }}">
        <div class="stat-icon"><i class="material-icons">task_alt</i></div>
        <div>
            <div class="stat-num">{{ $stat['selesai'] }}</div>
            <div class="stat-label">Selesai Ditanggapi</div>
        </div>
    </a>
</div>

<div class="dash-grid">
    <div class="dash-col dash-col-main">
        <div class="chart-card">
            <div class="chart-head">
                <div>
                    <h5>Status Keluhanku</h5>
                    <span class="chart-sub">Perbandingan keluhan yang masih diproses dan selesai</span>
                </div>
            </div>
            @if($stat['total'] > 0)
                <div class="chart-wrap chart-wrap-donut"><canvas id="statusDonut" aria-label="Grafik status keluhanku"></canvas></div>
            @else
                <div class="empty-state"><i class="material-icons">donut_small</i><p>Belum ada keluhan tercatat. Ajukan keluhan pertamamu di bawah!</p></div>
            @endif
        </div>

        <div class="chart-card">
            <div class="chart-head">
                <div>
                    <h5>Aktivitas Keluhan</h5>
                    <span class="chart-sub">Keluhan yang ku-ajukan 14 hari terakhir</span>
                </div>
            </div>
            @if(collect($stat['trend']['data'])->sum() > 0)
                <div class="chart-wrap"><canvas id="trendChartUser" aria-label="Grafik aktivitas keluhan"></canvas></div>
            @else
                <div class="empty-state"><i class="material-icons">show_chart</i><p>Belum ada aktivitas dalam 14 hari terakhir.</p></div>
            @endif
        </div>
    </div>

    <div class="dash-col dash-col-side">
        <div class="panel-card">
            <div class="panel-head">
                <h5>Riwayat Terbaru</h5>
                <a href="{{ route('riwayat') }}">Lihat Semua</a>
            </div>
            @if(count($stat['recent']) > 0)
                <div class="hl-list">
                    @foreach($stat['recent'] as $c)
                        <div class="hl-item">
                            <span class="hl-avatar hl-avatar-{{ $loop->index % 4 }}"><i class="material-icons">description</i></span>
                            <div class="hl-info">
                                <div class="cell-title">{{ $c->jenis->nama_jenis ?? '-' }} &middot; {{ $c->tgl_keluhan->format('d-m-Y') }}</div>
                                <div class="cell-sub">{{ Illuminate\Support\Str::limit($c->keluhan, 46) }}</div>
                                <span class="status-pill {{ $c->status }}">{{ ucfirst($c->status) }}</span>
                            </div>
                            <a class="ec-btn-sm blue" href="{{ route('complains.show', $c) }}">Detail</a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state"><i class="material-icons">history</i><p>Kamu belum pernah mengajukan keluhan.</p></div>
            @endif
        </div>
    </div>
</div>

<div class="page-head" style="margin-top: 34px;">
    <div class="eyebrow">Formulir</div>
    <h4>Ajukan <span class="accent">Keluhan</span></h4>
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

<script src="{{ asset('js/chart.umd.js') }}"></script>
<script>
  (function () {
    if (typeof Chart === 'undefined') { return; }
    Chart.defaults.font.family = "'Segoe UI', system-ui, -apple-system, sans-serif";
    Chart.defaults.color = '#78909c';

    var donutCanvas = document.getElementById('statusDonut');
    if (donutCanvas) {
        new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Proses', 'Selesai'],
                datasets: [{
                    data: [{{ (int) $stat['proses'] }}, {{ (int) $stat['selesai'] }}],
                    backgroundColor: ['#fb8c00', '#2e9e5b'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '64%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, boxHeight: 12, useBorderRadius: true, borderRadius: 3 } } }
            }
        });
    }

    var trendCanvas = document.getElementById('trendChartUser');
    if (trendCanvas) {
        var trend = @json($stat['trend']);
        new Chart(trendCanvas, {
            type: 'line',
            data: {
                labels: trend.labels,
                datasets: [{
                    label: 'Keluhanku',
                    data: trend.data,
                    borderColor: '#2196f3',
                    backgroundColor: 'rgba(33,150,243,.14)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 2.5,
                    pointBackgroundColor: '#2196f3'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eef4fa' } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });
    }
  })();
</script>
@endsection
