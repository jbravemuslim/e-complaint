{{-- Komponen ringkasan dashboard staff — dipakai admin & superadmin.
    Kedua role mengirim seluruh variabel dari DashboardStats::staff()
    plus: $listUrl (halaman keluhan) dan $doneUrl (halaman selesai). --}}
<div class="stat-grid" style="margin-bottom: 24px;">
    <a class="stat-card stat-blue" href="{{ $listUrl }}">
        <div class="stat-icon"><i class="material-icons">event_note</i></div>
        <div>
            <div class="stat-num">{{ $mingguIni }}</div>
            <div class="stat-label">Keluhan Masuk Minggu Ini</div>
        </div>
    </a>
    <a class="stat-card stat-amber" href="{{ $listUrl }}">
        <div class="stat-icon"><i class="material-icons">pending_actions</i></div>
        <div>
            <div class="stat-num">{{ $statusCounts['proses'] }}</div>
            <div class="stat-label">Keluhan Diproses</div>
        </div>
    </a>
    <a class="stat-card stat-teal" href="{{ $doneUrl }}">
        <div class="stat-icon"><i class="material-icons">task_alt</i></div>
        <div>
            <div class="stat-num">{{ $statusCounts['selesai'] }}</div>
            <div class="stat-label">Keluhan Selesai</div>
        </div>
    </a>
    <a class="stat-card stat-red" href="{{ $doneUrl }}">
        <div class="stat-icon"><i class="material-icons">history_edu</i></div>
        <div>
            <div class="stat-num">{{ $ditanggapiSaya }}</div>
            <div class="stat-label">Respon dari Akunku</div>
        </div>
    </a>
</div>

<div class="dash-grid">
    <div class="dash-col dash-col-main">
        <div class="chart-card">
            <div class="chart-head">
                <div>
                    <h5>Keluhan per Jenis</h5>
                    <span class="chart-sub">Dipecah berdasarkan status penanganan</span>
                </div>
            </div>
            @if(collect($perJenis['proses'])->sum() + collect($perJenis['selesai'])->sum() > 0)
                <div class="chart-wrap"><canvas id="jenisChart" aria-label="Grafik keluhan per jenis"></canvas></div>
            @else
                <div class="empty-state"><i class="material-icons">donut_small</i><p>Belum ada keluhan tercatat.</p></div>
            @endif
        </div>

        <div class="chart-card">
            <div class="chart-head">
                <div>
                    <h5>Tren Keluhan</h5>
                    <span class="chart-sub">Jumlah keluhan masuk 14 hari terakhir</span>
                </div>
            </div>
            @if(collect($trend['data'])->sum() > 0)
                <div class="chart-wrap"><canvas id="trendChart" aria-label="Grafik tren keluhan"></canvas></div>
            @else
                <div class="empty-state"><i class="material-icons">show_chart</i><p>Belum ada aktivitas dalam 14 hari terakhir.</p></div>
            @endif
        </div>

        <div class="panel-card flush">
            <div class="panel-head">
                <h5>Keluhan Terbaru</h5>
                <a href="{{ $listUrl }}">Lihat Semua</a>
            </div>
            @if(count($recent) > 0)
                <table class="clean-table dash-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pelanggan</th>
                            <th>Tujuan / Jenis</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent as $c)
                            <tr>
                                <td class="id-cell">{{ $c->id }}</td>
                                <td>
                                    <div class="cell-name">
                                        <span class="hl-avatar hl-avatar-sm">{{ self_avatar($c->user->name) }}</span>
                                        <div>
                                            <div class="cell-title">{{ $c->user->name }}</div>
                                            <div class="cell-sub">{{ $c->user->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-title">{{ $c->tujuan->nama_unit ?? '-' }}</div>
                                    <div class="cell-sub">{{ $c->jenis->nama_jenis ?? '-' }}</div>
                                </td>
                                <td class="cell-sub">{{ $c->tgl_keluhan->format('d-m-Y') }}</td>
                                <td><span class="status-pill {{ $c->status }}">{{ ucfirst($c->status) }}</span></td>
                                <td class="cell-action"><a class="ec-btn-sm blue" href="{{ $listUrl }}">Tangani</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state"><i class="material-icons">inbox</i><p>Belum ada keluhan yang masuk.</p></div>
            @endif
        </div>
    </div>

    <div class="dash-col dash-col-side">
        <div class="chart-card">
            <div class="chart-head">
                <div>
                    <h5>Kinerja Penanganan</h5>
                    <span class="chart-sub">Keluhan selesai dibuat pada periodenya</span>
                </div>
            </div>
            <div class="kpi-row"><span>Minggu ini</span><b>{{ $persenMinggu }}%</b></div>
            <div class="progress-ec"><span class="progress-ec-fill" style="width: {{ $persenMinggu }}%;"></span></div>
            <div class="kpi-note">{{ $selesaiMinggu }} dari {{ $mingguIni }} keluhan minggu ini sudah selesai</div>
            <div class="kpi-row"><span>Secara keseluruhan</span><b>{{ $persenTotal }}%</b></div>
            <div class="progress-ec"><span class="progress-ec-fill" style="width: {{ $persenTotal }}%;"></span></div>
            <div class="kpi-note">{{ $statusCounts['selesai'] }} dari {{ $statusCounts['proses'] + $statusCounts['selesai'] }} keluhan sudah selesai</div>
        </div>

        <div class="panel-card">
            <div class="panel-head">
                <h5>Pelanggan Teraktif</h5>
                <span class="panel-sub">Keluhan terbanyak</span>
            </div>
            @if(count($topUsers) > 0)
                <div class="hl-list">
                    @foreach($topUsers as $i => $u)
                        <div class="hl-item">
                            <span class="hl-avatar hl-avatar-{{ $i % 4 }}">{{ self_avatar($u['name']) }}</span>
                            <div class="hl-info">
                                <div class="cell-title">{{ $u['name'] }}</div>
                                <div class="cell-sub">Terakhir: {{ \Carbon\Carbon::parse($u['terakhir'])->format('d-m-Y') }}</div>
                            </div>
                            <span class="hl-badge">{{ $u['total'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state"><i class="material-icons">person_off</i><p>Belum ada keluhan dari pelanggan.</p></div>
            @endif
        </div>
    </div>
</div>

<script src="{{ asset('js/chart.umd.js') }}"></script>
<script>
  (function () {
    if (typeof Chart === 'undefined') { return; }
    Chart.defaults.font.family = "'Segoe UI', system-ui, -apple-system, sans-serif";
    Chart.defaults.color = '#78909c';

    var jenis = @json($perJenis);
    var jenisCanvas = document.getElementById('jenisChart');
    if (jenisCanvas) {
        new Chart(jenisCanvas, {
            type: 'bar',
            data: {
                labels: jenis.labels,
                datasets: [
                    { label: 'Proses', data: jenis.proses, backgroundColor: '#fb8c00', borderRadius: 4 },
                    { label: 'Selesai', data: jenis.selesai, backgroundColor: '#2e9e5b', borderRadius: 4 }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true, beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eef4fa' } },
                    y: { stacked: true, grid: { display: false } }
                },
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, boxHeight: 12, borderRadius: 3, useBorderRadius: true } } }
            }
        });
    }

    var trendCanvas = document.getElementById('trendChart');
    if (trendCanvas) {
        var trend = @json($trend);
        new Chart(trendCanvas, {
            type: 'line',
            data: {
                labels: trend.labels,
                datasets: [{
                    label: 'Keluhan masuk',
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
