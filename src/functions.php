<?php

function handleAmbilNomor(): void {
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $categoryId = (int)($input['category_id'] ?? 0);

        if ($categoryId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Category ID tidak valid']);
            return;
        }

        $db = Database::getConnection();
        $db->beginTransaction();

        $stmt = $db->prepare("SELECT COALESCE(MAX(queue_number), 0) + 1 FROM queues WHERE category_id = ? AND DATE(created_at) = CURDATE()");
        $stmt->execute([$categoryId]);
        $queueNumber = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT id, name, code, is_active FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        $category = $stmt->fetch();

        if (!$category) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Kategori tidak ditemukan']);
            return;
        }

        if (!$category['is_active']) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Kategori sedang tidak aktif']);
            return;
        }

        $queueCode = $category['code'] . str_pad($queueNumber, 3, '0', STR_PAD_LEFT);

        $stmt = $db->prepare("INSERT INTO queues (category_id, queue_number, queue_code, status) VALUES (?, ?, ?, 'waiting')");
        $stmt->execute([$categoryId, $queueNumber, $queueCode]);

        $db->commit();

        echo json_encode([
            'success' => true,
            'data' => [
                'queue_code' => $queueCode,
                'queue_number' => $queueNumber,
                'category_name' => $category['name'],
                'category_code' => $category['code'],
            ]
        ]);
    } catch (Exception $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleDataAntrian(): void {
    header('Content-Type: application/json');
    try {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT q.*, c.name as category_name FROM queues q JOIN categories c ON q.category_id = c.id WHERE q.status = 'called' AND DATE(q.created_at) = CURDATE() ORDER BY q.called_at DESC");
        $stmt->execute();
        $currentCalls = $stmt->fetchAll();

        $stmt = $db->prepare("SELECT q.*, c.name as category_name FROM queues q JOIN categories c ON q.category_id = c.id WHERE q.status = 'waiting' AND DATE(q.created_at) = CURDATE() ORDER BY q.created_at ASC LIMIT 10");
        $stmt->execute();
        $waitingList = $stmt->fetchAll();

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM queues WHERE DATE(created_at) = CURDATE()");
        $stmt->execute();
        $todayTotal = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM queues WHERE status = 'waiting' AND DATE(created_at) = CURDATE()");
        $stmt->execute();
        $waitingTotal = (int)$stmt->fetchColumn();

        $stmt = $db->query("SELECT * FROM categories WHERE is_active = 1");
        $categories = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'data' => [
                'current_calls' => $currentCalls,
                'waiting_list' => $waitingList,
                'today_total' => $todayTotal,
                'waiting_total' => $waitingTotal,
                'categories' => $categories,
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handlePanggil(): void {
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $counterId = isset($input['counter_id']) ? (int)$input['counter_id'] : 1;

        $db = Database::getConnection();
        $db->beginTransaction();

        $stmt = $db->prepare("SELECT q.*, c.name as category_name FROM queues q JOIN categories c ON q.category_id = c.id WHERE q.status = 'waiting' AND DATE(q.created_at) = CURDATE() ORDER BY q.created_at ASC LIMIT 1 FOR UPDATE");
        $stmt->execute();
        $queue = $stmt->fetch();

        if (!$queue) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Tidak ada antrian yang menunggu']);
            return;
        }

        $stmt = $db->prepare("UPDATE queues SET status = 'called', counter_id = ?, called_at = NOW() WHERE id = ?");
        $stmt->execute([$counterId, $queue['id']]);

        $db->commit();

        echo json_encode([
            'success' => true,
            'data' => [
                'queue_id' => $queue['id'],
                'queue_code' => $queue['queue_code'],
                'category_name' => $queue['category_name'],
                'counter_id' => $counterId,
            ]
        ]);
    } catch (Exception $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleSkip(): void {
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $queueId = (int)($input['queue_id'] ?? 0);

        if ($queueId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Queue ID tidak valid']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE queues SET status = 'skipped' WHERE id = ?");
        $stmt->execute([$queueId]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleSelesai(): void {
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $queueId = (int)($input['queue_id'] ?? 0);

        if ($queueId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Queue ID tidak valid']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE queues SET status = 'completed', completed_at = NOW() WHERE id = ?");
        $stmt->execute([$queueId]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handlePanggilUlang(): void {
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $queueId = (int)($input['queue_id'] ?? 0);

        if ($queueId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Queue ID tidak valid']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE queues SET called_at = NOW() WHERE id = ?");
        $stmt->execute([$queueId]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ===================== CRUD KATEGORI =====================

function handleKategoriList(): void {
    header('Content-Type: application/json');
    try {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM categories ORDER BY id ASC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleKategoriTambah(): void {
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $name = trim($input['name'] ?? '');
        $code = strtoupper(trim($input['code'] ?? ''));

        if ($name === '' || $code === '') {
            echo json_encode(['success' => false, 'error' => 'Nama dan kode wajib diisi']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO categories (name, code) VALUES (?, ?)");
        $stmt->execute([$name, $code]);

        echo json_encode(['success' => true, 'data' => ['id' => (int)$db->lastInsertId()]]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleKategoriEdit(): void {
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);
        $name = trim($input['name'] ?? '');
        $code = strtoupper(trim($input['code'] ?? ''));

        if ($id <= 0 || $name === '' || $code === '') {
            echo json_encode(['success' => false, 'error' => 'Data tidak valid']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE categories SET name = ?, code = ? WHERE id = ?");
        $stmt->execute([$name, $code, $id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleKategoriHapus(): void {
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID tidak valid']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleKategoriToggle(): void {
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID tidak valid']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE categories SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$id]);

        $stmt = $db->prepare("SELECT is_active FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $isActive = (int)$stmt->fetchColumn();

        echo json_encode(['success' => true, 'data' => ['is_active' => $isActive]]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ===================== CRUD LOKET =====================

function handleLoketList(): void {
    header('Content-Type: application/json');
    try {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM counters ORDER BY id ASC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleLoketTambah(): void {
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $name = trim($input['name'] ?? '');
        $code = strtoupper(trim($input['code'] ?? ''));

        if ($name === '' || $code === '') {
            echo json_encode(['success' => false, 'error' => 'Nama dan kode wajib diisi']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO counters (name, code) VALUES (?, ?)");
        $stmt->execute([$name, $code]);

        echo json_encode(['success' => true, 'data' => ['id' => (int)$db->lastInsertId()]]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleLoketEdit(): void {
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);
        $name = trim($input['name'] ?? '');
        $code = strtoupper(trim($input['code'] ?? ''));

        if ($id <= 0 || $name === '' || $code === '') {
            echo json_encode(['success' => false, 'error' => 'Data tidak valid']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE counters SET name = ?, code = ? WHERE id = ?");
        $stmt->execute([$name, $code, $id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleLoketHapus(): void {
    header('Content-Type: application/json');
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID tidak valid']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM counters WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ===================== LAPORAN =====================

function handleLaporan(): void {
    header('Content-Type: application/json');
    try {
        $db = Database::getConnection();

        // Rekap per kategori
        $stmt = $db->query("
            SELECT c.id, c.name, c.code,
                   COUNT(q.id) as total,
                   SUM(CASE WHEN q.status = 'completed' THEN 1 ELSE 0 END) as selesai,
                   SUM(CASE WHEN q.status = 'skipped' THEN 1 ELSE 0 END) as lewat,
                   SUM(CASE WHEN q.status = 'called' THEN 1 ELSE 0 END) as dipanggil,
                   SUM(CASE WHEN q.status = 'waiting' THEN 1 ELSE 0 END) as menunggu,
                   COALESCE(ROUND(AVG(TIMESTAMPDIFF(SECOND, q.called_at, q.completed_at))), 0) as rata_waktu_detik
            FROM categories c
            LEFT JOIN queues q ON q.category_id = c.id AND DATE(q.created_at) = CURDATE()
            GROUP BY c.id
            ORDER BY c.id
        ");
        $perKategori = $stmt->fetchAll();

        // Total keseluruhan
        $stmt = $db->query("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status = 'skipped' THEN 1 ELSE 0 END) as lewat,
                SUM(CASE WHEN status = 'called' THEN 1 ELSE 0 END) as dipanggil,
                SUM(CASE WHEN status = 'waiting' THEN 1 ELSE 0 END) as menunggu,
                COALESCE(ROUND(AVG(TIMESTAMPDIFF(SECOND, called_at, completed_at))), 0) as rata_waktu_detik
            FROM queues
            WHERE DATE(created_at) = CURDATE()
        ");
        $total = $stmt->fetch();

        // Antrian terakhir
        $stmt = $db->query("
            SELECT q.queue_code, c.name as category_name, q.status, q.created_at
            FROM queues q
            JOIN categories c ON c.id = q.category_id
            WHERE DATE(q.created_at) = CURDATE()
            ORDER BY q.created_at DESC
            LIMIT 20
        ");
        $terakhir = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'data' => [
                'per_kategori' => $perKategori,
                'total' => $total,
                'terakhir' => $terakhir,
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleLaporanCsv(): void {
    try {
        $db = Database::getConnection();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan-antrian-' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

        fputcsv($output, ['No', 'Kode Antrian', 'Kategori', 'Status', 'Waktu Ambil', 'Waktu Panggil', 'Waktu Selesai']);

        $stmt = $db->query("
            SELECT q.queue_code, c.name as category_name, q.status,
                   q.created_at, q.called_at, q.completed_at
            FROM queues q
            JOIN categories c ON c.id = q.category_id
            WHERE DATE(q.created_at) = CURDATE()
            ORDER BY q.created_at ASC
        ");
        $rows = $stmt->fetchAll();

        $no = 1;
        foreach ($rows as $row) {
            fputcsv($output, [
                $no++,
                $row['queue_code'],
                $row['category_name'],
                $row['status'],
                $row['created_at'] ?? '-',
                $row['called_at'] ?? '-',
                $row['completed_at'] ?? '-',
            ]);
        }

        fclose($output);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ===================== BERSIHKAN LAPORAN =====================

function handleLaporanBersihkan(): void {
    header('Content-Type: application/json');
    try {
        $db = Database::getConnection();

        $stmt = $db->prepare("DELETE FROM queues WHERE DATE(created_at) = CURDATE()");
        $stmt->execute();
        $deleted = $stmt->rowCount();

        echo json_encode([
            'success' => true,
            'data' => ['terhapus' => $deleted],
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ===================== ESTIMASI WAKTU =====================

function handleEstimasi(): void {
    header('Content-Type: application/json');
    try {
        $categoryId = (int)($_GET['category_id'] ?? 0);
        $db = Database::getConnection();

        // Rata-rata waktu layanan per kategori (dalam detik)
        $stmt = $db->prepare("
            SELECT COALESCE(ROUND(AVG(TIMESTAMPDIFF(SECOND, called_at, completed_at))), 0) as rata_detik
            FROM queues
            WHERE category_id = ? AND status = 'completed' AND completed_at IS NOT NULL
        ");
        $stmt->execute([$categoryId]);
        $rataDetik = (int)$stmt->fetchColumn();

        // Jumlah antrian di depan
        $stmt = $db->prepare("
            SELECT COUNT(*) as total
            FROM queues
            WHERE category_id = ? AND status = 'waiting' AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute([$categoryId]);
        $antrianDepan = (int)$stmt->fetchColumn();

        $estimasiDetik = $rataDetik * ($antrianDepan + 1);

        echo json_encode([
            'success' => true,
            'data' => [
                'rata_waktu' => $rataDetik,
                'antrian_depan' => $antrianDepan,
                'estimasi_detik' => $estimasiDetik,
                'estimasi_menit' => $estimasiDetik > 0 ? max(1, ceil($estimasiDetik / 60)) : 0,
                'estimasi_label' => $estimasiDetik > 0
                    ? 'Estimasi: ~' . max(1, ceil($estimasiDetik / 60)) . ' menit'
                    : 'Sedang dihitung...',
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ===================== SETTINGS / PROFIL =====================

function getSettings(): array {
    $defaults = [
        'app_name' => 'SISTEM ANTRIAN',
        'logo_path' => '',
    ];
    try {
        $db = Database::getConnection();
        $db->exec("CREATE TABLE IF NOT EXISTS settings (`key` VARCHAR(100) PRIMARY KEY, `value` TEXT)");
        $stmt = $db->query("SELECT `key`, `value` FROM settings");
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $defaults[$row['key']] = $row['value'];
        }
        // Fix uploads dir permission for www-data
        $uploadDir = __DIR__ . '/../public/assets/uploads/';
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);
        @chmod($uploadDir, 0777);
    } catch (Exception $e) {}
    return $defaults;
}

function handleSettingsGet(): void {
    header('Content-Type: application/json');
    try {
        $settings = getSettings();
        echo json_encode(['success' => true, 'data' => $settings]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleSettingsSave(): void {
    header('Content-Type: application/json');
    try {
        $db = Database::getConnection();
        $db->exec("CREATE TABLE IF NOT EXISTS settings (`key` VARCHAR(100) PRIMARY KEY, `value` TEXT)");

        $appName = trim($_POST['app_name'] ?? 'SISTEM ANTRIAN');
        if ($appName === '') $appName = 'SISTEM ANTRIAN';

        $stmt = $db->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?");
        $stmt->execute(['app_name', $appName, $appName]);

        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                echo json_encode(['success' => false, 'error' => 'Format file tidak didukung. Gunakan: ' . implode(', ', $allowed)]);
                return;
            }
            $uploadDir = __DIR__ . '/../public/assets/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            // Fix permissions so www-data can write (rootless podman workaround)
            @chmod($uploadDir, 0777);

            $filename = 'logo.' . $ext;
            $dest = $uploadDir . $filename;

            // Hapus logo lama
            $old = $db->query("SELECT `value` FROM settings WHERE `key` = 'logo_path'")->fetchColumn();
            if ($old) {
                $oldFile = __DIR__ . '/../public' . $old;
                if (file_exists($oldFile)) unlink($oldFile);
            }

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest)) {
                $logoPath = '/assets/uploads/' . $filename;
                $stmt->execute(['logo_path', $logoPath, $logoPath]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Gagal mengupload file']);
                return;
            }
        }

        // Remove logo
        if (isset($_POST['remove_logo']) && $_POST['remove_logo'] === '1') {
            $old = $db->query("SELECT `value` FROM settings WHERE `key` = 'logo_path'")->fetchColumn();
            if ($old) {
                $oldFile = __DIR__ . '/../public' . $old;
                if (file_exists($oldFile)) unlink($oldFile);
            }
            $stmt->execute(['logo_path', '', '']);
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
