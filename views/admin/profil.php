<?php
$settings = getSettings();
$appName = $settings['app_name'] ?? 'SISTEM ANTRIAN';
$logoPath = $settings['logo_path'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Aplikasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .profil-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .profil-card h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 24px 0;
        }
        .profil-field {
            margin-bottom: 20px;
        }
        .profil-field label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .profil-field input[type="text"] {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            color: #1e293b;
            outline: none;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }
        .profil-field input[type="text"]:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .profil-logo-preview {
            width: 120px;
            height: 120px;
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 12px;
            background: #f8fafc;
        }
        .profil-logo-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .profil-logo-preview .no-logo {
            color: #94a3b8;
            font-size: 0.85rem;
            text-align: center;
            padding: 8px;
        }
        .profil-logo-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .btn-upload {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: #1a56db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-upload:hover {
            background: #1648c0;
        }
        .btn-hapus-logo {
            padding: 8px 16px;
            background: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-hapus-logo:hover {
            background: #fecaca;
        }
        .btn-simpan-profil {
            width: 100%;
            padding: 12px;
            background: #059669;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 8px;
        }
        .btn-simpan-profil:hover {
            background: #047857;
        }
        .btn-simpan-profil:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        #upload-progress {
            display: none;
            margin-top: 8px;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            text-align: center;
        }
        #upload-progress.show {
            display: block;
        }
        #upload-progress.success {
            background: #d1fae5;
            color: #065f46;
        }
        #upload-progress.error {
            background: #fee2e2;
            color: #991b1b;
        }
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
        }
        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        @media (max-width: 480px) {
            .profil-card { padding: 20px 16px; }
            .profil-logo-preview { width: 100px; height: 100px; }
        }
    </style>
</head>
<body class="page-admin">
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">PROFIL APLIKASI</h1>
            <p class="admin-subtitle">Ubah nama aplikasi dan logo</p>
        </div>
        <nav class="admin-nav">
            <a href="/admin">Dashboard</a>
            <a href="/admin/kategori">Kategori</a>
            <a href="/admin/loket">Loket</a>
            <a href="/admin/laporan">Laporan</a>
            <a href="/admin/profil" class="active">Profil</a>
        </nav>

        <div class="profil-card">
            <h2>Pengaturan Tampilan</h2>
            <form id="formProfil">
                <div class="profil-field">
                    <label for="app_name">Nama Aplikasi</label>
                    <input type="text" id="app_name" name="app_name" value="<?php echo htmlspecialchars($appName); ?>" placeholder="SISTEM ANTRIAN" maxlength="100" required>
                </div>

                <div class="profil-field">
                    <label>Logo Aplikasi</label>
                    <div class="profil-logo-preview" id="logoPreview">
                        <?php if ($logoPath): ?>
                            <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="Logo" id="logoImg">
                        <?php else: ?>
                            <div class="no-logo" id="noLogoText">Belum ada logo</div>
                        <?php endif; ?>
                    </div>
                    <div class="profil-logo-actions">
                        <div class="file-input-wrapper btn-upload">
                            <span>📁 Pilih File</span>
                            <input type="file" id="logoInput" name="logo" accept="image/png,image/jpeg,image/gif,image/svg+xml,image/webp">
                        </div>
                        <?php if ($logoPath): ?>
                            <button type="button" class="btn-hapus-logo" id="btnHapusLogo">🗑 Hapus Logo</button>
                        <?php endif; ?>
                    </div>
                    <div style="font-size:0.8rem;color:#94a3b8;margin-top:6px;">
                        Format: PNG, JPG, GIF, SVG, WEBP. Maks 2MB.
                    </div>
                </div>

                <button type="submit" class="btn-simpan-profil" id="btnSimpan">SIMPAN</button>
            </form>
            <div id="upload-progress"></div>
        </div>
        <div class="app-footer">by natedekaka</div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('formProfil');
        const btnSimpan = document.getElementById('btnSimpan');
        const progress = document.getElementById('upload-progress');
        const logoInput = document.getElementById('logoInput');
        const logoPreview = document.getElementById('logoPreview');
        const btnHapusLogo = document.getElementById('btnHapusLogo');
        const noLogoText = document.getElementById('noLogoText');

        // Preview logo before upload
        logoInput.addEventListener('change', () => {
            const file = logoInput.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                showMsg('File terlalu besar. Maks 2MB.', 'error');
                logoInput.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.id = 'logoImg';
                img.alt = 'Logo';
                logoPreview.innerHTML = '';
                logoPreview.appendChild(img);
                if (btnHapusLogo) btnHapusLogo.remove();
            };
            reader.readAsDataURL(file);
        });

        // Remove logo
        if (btnHapusLogo) {
            btnHapusLogo.addEventListener('click', () => {
                // Mark logo for removal on server side
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_logo';
                input.value = '1';
                form.appendChild(input);
                logoPreview.innerHTML = '<div class="no-logo" id="noLogoText">Belum ada logo</div>';
                btnHapusLogo.remove();
                showMsg('Logo akan dihapus saat disimpan', 'success');
            });
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            btnSimpan.disabled = true;
            btnSimpan.textContent = 'MENYIMPAN...';

            const formData = new FormData(form);
            formData.set('app_name', document.getElementById('app_name').value.trim() || 'SISTEM ANTRIAN');

            try {
                const res = await fetch('/api/settings/save', {
                    method: 'POST',
                    body: formData
                });
                const json = await res.json();
                if (json.success) {
                    showMsg('Pengaturan berhasil disimpan', 'success');
                } else {
                    showMsg(json.error || 'Gagal menyimpan', 'error');
                }
            } catch (e) {
                showMsg('Terjadi kesalahan. Silakan coba lagi.', 'error');
            } finally {
                btnSimpan.disabled = false;
                btnSimpan.textContent = 'SIMPAN';
                // Remove the remove_logo hidden field if it exists
                const rl = form.querySelector('input[name="remove_logo"]');
                if (rl) rl.remove();
            }
        });

        function showMsg(msg, type) {
            progress.textContent = msg;
            progress.className = 'show ' + type;
            setTimeout(() => { progress.className = ''; }, 4000);
        }
    });
    </script>
</body>
</html>
