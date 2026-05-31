<?php
$db = Database::getConnection();
$categories = $db->query("SELECT * FROM categories WHERE is_active = 1")->fetchAll();
$settings = function_exists('getSettings') ? getSettings() : ['app_name' => 'SISTEM ANTRIAN', 'logo_path' => ''];
$appName = $settings['app_name'];
$logoPath = $settings['logo_path'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($appName); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="page-ambil">
    <div class="container">
        <div class="ambil-header">
            <div class="ambil-icon">
                <?php if ($logoPath): ?>
                    <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="Logo" style="max-width:64px;max-height:64px;">
                <?php else: ?>
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: white;">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
                <?php endif; ?>
            </div>
            <h1 class="ambil-title"><?php echo htmlspecialchars($appName); ?></h1>
            <p class="ambil-subtitle">Silakan pilih jenis layanan</p>
            <div id="estimasi-waktu" class="hidden"></div>
        </div>

        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
            <button class="category-btn" data-id="<?php echo $category['id']; ?>">
                <div class="category-code"><?php echo htmlspecialchars($category['code']); ?></div>
                <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Modal Antrian -->
        <div id="modal-antrian" class="modal-overlay hidden">
            <div class="modal-content">
                <div class="modal-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#1a56db" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                </div>
                <div class="modal-label">Nomor Antrian Anda</div>
                <div id="queue-number-display" class="queue-number-display">---</div>
                <div id="queue-category-name" class="modal-category"></div>
                <div class="modal-message">Silakan menunggu dipanggil</div>
                <div id="modal-time" class="modal-time"></div>
                <button id="btn-cetak-tiket" class="btn-cetak" data-code="" data-category="">Cetak Tiket</button>
                <button class="btn-ambil-lagi">Ambil Antrian Lagi</button>
            </div>
        </div>

        <div class="app-footer">by natedekaka</div>
    </div>

    <script src="/assets/js/app.js?v=2"></script>
</body>
</html>
