<?php

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/functions.php';

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// === API ROUTES ===
if (strpos($path, '/api/') === 0) {
    $endpoint = substr($path, 5);

    // Map endpoint ke handler
    $apiRoutes = [
        'ambil' => 'handleAmbilNomor',
        'data-antrian' => 'handleDataAntrian',
        'panggil' => 'handlePanggil',
        'skip' => 'handleSkip',
        'selesai' => 'handleSelesai',
        'panggil-ulang' => 'handlePanggilUlang',
        'kategori' => 'handleKategoriList',
        'kategori/tambah' => 'handleKategoriTambah',
        'kategori/edit' => 'handleKategoriEdit',
        'kategori/hapus' => 'handleKategoriHapus',
        'kategori/toggle' => 'handleKategoriToggle',
        'loket' => 'handleLoketList',
        'loket/tambah' => 'handleLoketTambah',
        'loket/edit' => 'handleLoketEdit',
        'loket/hapus' => 'handleLoketHapus',
        'laporan' => 'handleLaporan',
        'laporan/csv' => 'handleLaporanCsv',
        'laporan/bersihkan' => 'handleLaporanBersihkan',
        'estimasi' => 'handleEstimasi',
        'settings' => 'handleSettingsGet',
        'settings/save' => 'handleSettingsSave',
    ];

    if (isset($apiRoutes[$endpoint])) {
        $handler = $apiRoutes[$endpoint];
        $handler();
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Endpoint tidak ditemukan']);
    }

    return;
}

// === PAGE ROUTES ===
$pageRoutes = [
    '/' => '/../views/ambil-nomor.php',
    '/index.php' => '/../views/ambil-nomor.php',
    '/layar' => '/../views/layar-antrian.php',
    '/admin' => '/../views/admin/dashboard.php',
    '/admin/kategori' => '/../views/admin/kategori.php',
    '/admin/loket' => '/../views/admin/loket.php',
    '/admin/laporan' => '/../views/admin/laporan.php',
    '/admin/profil' => '/../views/admin/profil.php',
];

if (isset($pageRoutes[$path])) {
    require __DIR__ . $pageRoutes[$path];
} else {
    http_response_code(404);
}
