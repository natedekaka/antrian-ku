<?php
$db = Database::getConnection();
$counters = $db->query("SELECT * FROM counters")->fetchAll();
$settings = function_exists('getSettings') ? getSettings() : ['app_name' => 'SISTEM ANTRIAN'];
$appName = $settings['app_name'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($appName); ?> — Panel Petugas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="page-admin">
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title"><?php echo htmlspecialchars($appName); ?></h1>
            <p class="admin-subtitle">Panel Petugas — Sistem Antrian</p>
        </div>
        <nav class="admin-nav">
            <a href="/admin">Dashboard</a>
            <a href="/admin/kategori">Kategori</a>
            <a href="/admin/loket">Loket</a>
            <a href="/admin/laporan">Laporan</a>
            <a href="/admin/profil">Profil</a>
        </nav>

        <div class="counter-selector">
            <?php foreach ($counters as $counter): ?>
            <button class="counter-btn" data-id="<?php echo $counter['id']; ?>">
                <?php echo htmlspecialchars($counter['name']); ?>
            </button>
            <?php endforeach; ?>
        </div>

        <div class="active-queue-card">
            <div class="active-queue-label">Antrian Aktif</div>
            <div id="admin-active-number" class="active-queue-number">---</div>
            <div id="admin-active-status" class="active-queue-status">Tidak ada antrian aktif</div>
        </div>

        <div class="action-buttons">
            <button id="btn-panggil" class="action-btn btn-panggil">PANGGIL</button>
            <button id="btn-ulang" class="action-btn btn-ulang">PANGGIL ULANG</button>
            <button id="btn-skip" class="action-btn btn-skip">SKIP</button>
            <button id="btn-selesai" class="action-btn btn-selesai">SELESAI</button>
        </div>

        <div class="admin-table-wrapper">
            <h3 class="admin-table-title">Antrian Hari Ini</h3>
            <table class="queue-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Antrian</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody id="admin-queue-body">
                    <tr>
                        <td colspan="5" style="text-align:center;padding:24px;color:#94a3b8;">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="app-footer">by natedekaka</div>
    </div>

    <script src="/assets/js/app.js?v=2"></script>
</body>
</html>
