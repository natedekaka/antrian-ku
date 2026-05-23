<?php
$today = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .laporan-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }
        .stat-card {
            border-radius: 12px;
            padding: 16px 20px;
            color: #fff;
        }
        .stat-card .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.85;
            margin-bottom: 4px;
        }
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.1;
        }
        .laporan-section {
            margin-bottom: 28px;
        }
        .laporan-section h3 {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 10px 0;
        }
        .admin-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            background: #059669;
            color: #fff;
            transition: background .15s;
        }
        .btn-export:hover {
            background: #047857;
        }
        .btn-bersihkan {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            background: #dc2626;
            color: #fff;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-bersihkan:hover {
            background: #b91c1c;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-waiting {
            background: #dbeafe;
            color: #1d4ed8;
        }
        .badge-called {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-skipped {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-completed {
            background: #e2e8f0;
            color: #475569;
        }
        .laporan-date {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .laporan-stats {
                grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
                gap: 10px;
            }
            .stat-card { padding: 14px; }
            .stat-card .stat-value { font-size: 1.5rem; }
        }
        @media (max-width: 480px) {
            .laporan-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .stat-card { padding: 12px 10px; }
            .stat-card .stat-value { font-size: 1.3rem; }
        }
    </style>
</head>
<body class="page-admin">
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-actions">
                <div>
                    <h1 class="admin-title">LAPORAN HARIAN</h1>
                    <p class="laporan-date"><?php echo $today; ?></p>
                </div>
                <div class="laporan-actions">
                    <button id="btn-bersihkan" class="btn-bersihkan" onclick="bersihkanLaporan()">🗑 Bersihkan Hari Ini</button>
                    <a href="/api/laporan/csv" class="btn-export">⬇ Export CSV</a>
                </div>
            </div>
        </div>
        <nav class="admin-nav">
            <a href="/admin">Dashboard</a>
            <a href="/admin/kategori">Kategori</a>
            <a href="/admin/loket">Loket</a>
            <a href="/admin/laporan" class="active">Laporan</a>
            <a href="/admin/profil">Profil</a>
        </nav>

        <div class="laporan-stats" id="stat-cards"></div>

        <div class="laporan-section">
            <h3>Rekap Per Kategori</h3>
            <div class="admin-table-wrapper" style="padding:0;">
                <table class="queue-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Total</th>
                            <th>Selesai</th>
                            <th>Lewat</th>
                            <th>Rata Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="rekap-body"></tbody>
                </table>
            </div>
        </div>

        <div class="laporan-section">
            <h3>Antrian Terakhir</h3>
            <div class="admin-table-wrapper" style="padding:0;">
                <table class="queue-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="terakhir-body"></tbody>
                </table>
            </div>
        </div>
        <div class="app-footer">by natedekaka</div>
    </div>

    <script>
    function fmtDetik(secs) {
        if (secs == null) return '-';
        const m = Math.floor(secs / 60);
        const d = Math.floor(secs % 60);
        return m + ':' + String(d).padStart(2, '0');
    }

    function badgeHtml(status) {
        const map = { waiting: 'badge-waiting', called: 'badge-called', skipped: 'badge-skipped', completed: 'badge-completed' };
        const cls = map[status] || 'badge-waiting';
        const label = { waiting: 'Menunggu', called: 'Dipanggil', skipped: 'Dilewati', completed: 'Selesai' };
        return '<span class="badge ' + cls + '">' + (label[status] || status) + '</span>';
    }

    async function loadLaporan() {
        try {
            const r = await fetch('/api/laporan');
            const res = await r.json();
            if (!res.success) return;
            const d = res.data;

            // stat cards
            const cards = [
                { label: 'Total', value: d.total.total, color: '#1a56db' },
                { label: 'Selesai', value: d.total.selesai, color: '#059669' },
                { label: 'Dipanggil', value: d.total.dipanggil, color: '#3b82f6' },
                { label: 'Menunggu', value: d.total.menunggu, color: '#d97706' },
                { label: 'Dilewati', value: d.total.lewat, color: '#64748b' },
            ];
            document.getElementById('stat-cards').innerHTML = cards.map(c =>
                '<div class="stat-card" style="background:' + c.color + '">' +
                    '<div class="stat-label">' + c.label + '</div>' +
                    '<div class="stat-value">' + c.value + '</div>' +
                '</div>'
            ).join('');

            // rekap per kategori
            document.getElementById('rekap-body').innerHTML = d.per_kategori.map(k =>
                '<tr>' +
                    '<td>' + k.code + ' — ' + k.name + '</td>' +
                    '<td>' + k.total + '</td>' +
                    '<td>' + k.selesai + '</td>' +
                    '<td>' + k.lewat + '</td>' +
                    '<td>' + fmtDetik(k.rata_waktu_detik) + '</td>' +
                '</tr>'
            ).join('');

            // antrian terakhir
            document.getElementById('terakhir-body').innerHTML = d.terakhir.map(t =>
                '<tr>' +
                    '<td>' + t.queue_code + '</td>' +
                    '<td>' + t.category_name + '</td>' +
                    '<td>' + badgeHtml(t.status) + '</td>' +
                    '<td>' + t.created_at + '</td>' +
                '</tr>'
            ).join('');

        } catch (e) {
            console.error('Gagal memuat laporan', e);
        }
    }

    function bersihkanLaporan() {
        if (!confirm('Yakin ingin menghapus SEMUA data antrian hari ini? Data yang sudah dihapus tidak bisa dikembalikan.')) return;
        const btn = document.getElementById('btn-bersihkan');
        btn.disabled = true;
        btn.textContent = '⏳ Menghapus...';
        fetch('/api/laporan/bersihkan', { method: 'POST' })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    alert('Berhasil menghapus ' + res.data.terhapus + ' antrian hari ini.');
                    loadLaporan();
                } else {
                    alert('Gagal: ' + (res.message || res.error));
                }
            })
            .catch(() => alert('Terjadi kesalahan'))
            .finally(() => {
                btn.disabled = false;
                btn.textContent = '🗑 Bersihkan Hari Ini';
            });
    }

    document.addEventListener('DOMContentLoaded', loadLaporan);
    </script>
</body>
</html>
