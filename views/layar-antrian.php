<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    $settings = function_exists('getSettings') ? getSettings() : ['app_name' => 'SISTEM ANTRIAN', 'logo_path' => ''];
    echo htmlspecialchars($settings['app_name']);
    ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css?v=2">
</head>
<body class="page-layar">
    <div id="audio-overlay" class="audio-overlay">
        <div class="audio-overlay-box" onclick="aktifkanAudio()">
            <div class="audio-icon">🔊</div>
            <div class="audio-text">Klik untuk mengaktifkan suara</div>
        </div>
    </div>

    <div class="layar-wrapper">
    <div class="layar-header">
        <div style="display:flex;align-items:center;gap:12px;">
            <?php if (!empty($settings['logo_path'])): ?>
                <img src="<?php echo htmlspecialchars($settings['logo_path']); ?>" alt="Logo" style="max-height:36px;width:auto;">
            <?php endif; ?>
            <div class="layar-title"><?php echo htmlspecialchars($settings['app_name']); ?></div>
        </div>
        <div id="layar-clock" class="layar-clock">--:--:--</div>
    </div>

        <div class="layar-main">
            <div>
                <div id="layar-number" class="layar-number">---</div>
                <div id="layar-status" class="layar-status">MENUNGGU ANTRIAN</div>
            </div>
        </div>

        <div class="layar-next">
            <div class="layar-next-label">Antrian Selanjutnya:</div>
            <div id="layar-next-list" class="layar-next-list">
                <div class="layar-next-item status-waiting">---</div>
            </div>
        </div>

        <div class="layar-footer">
            <?php
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT c.code, c.name, COUNT(q.id) as total FROM categories c LEFT JOIN queues q ON q.category_id = c.id AND DATE(q.created_at) = CURDATE() GROUP BY c.id");
            $stmt->execute();
            $stats = $stmt->fetchAll();
            foreach ($stats as $s): ?>
            <div class="layar-stat">
                <span class="layar-stat-code"><?php echo htmlspecialchars($s['code']); ?></span>
                <span class="layar-stat-total"><?php echo (int)$s['total']; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="app-footer">by natedekaka</div>

    <style>
        .audio-overlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(15, 23, 42, 0.95);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: opacity 0.5s ease;
        }
        .audio-overlay.hidden {
            opacity: 0; pointer-events: none;
        }
        .audio-overlay-box {
            text-align: center;
            animation: pulseAudio 2s infinite;
        }
        .audio-icon {
            font-size: 64px; margin-bottom: 16px;
        }
        .audio-text {
            font-family: 'Inter', sans-serif;
            font-size: 1.5rem; font-weight: 600; color: white;
            letter-spacing: 1px;
        }
        @keyframes pulseAudio {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }
    </style>

    <script src="/assets/js/app.js?v=2"></script>
    <script>
        function aktifkanAudio() {
            if (typeof getAudioCtx === 'function') {
                getAudioCtx().resume();
            }
            var el = document.getElementById('audio-overlay');
            if (el) el.classList.add('hidden');
        }
    </script>
</body>
</html>
