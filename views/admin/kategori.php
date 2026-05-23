<?php
$title = "Kategori Layanan";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .crud-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .crud-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .crud-title {
            font-size: 14px;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .btn-tambah {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            background: #059669;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }
        .btn-tambah:hover {
            background: #047857;
        }
        .btn-tambah svg {
            width: 16px;
            height: 16px;
        }
        .badge-kode {
            display: inline-block;
            padding: 3px 10px;
            background: #dbeafe;
            color: #1d4ed8;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            font-family: monospace;
            letter-spacing: 0.5px;
        }
        .empty-state {
            text-align: center;
            padding: 48px 16px;
            color: #94a3b8;
        }
        .empty-state svg {
            width: 48px;
            height: 48px;
            margin-bottom: 12px;
            opacity: 0.3;
        }
        .empty-state p {
            margin: 0;
            font-size: 14px;
        }
        .action-cell {
            display: flex;
            gap: 6px;
        }
        .act-btn {
            padding: 4px 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            transition: .2s;
        }
        .act-edit {
            background: #059669;
            color: #fff;
        }
        .act-edit:hover {
            background: #047857;
        }
        .act-hapus {
            background: #dc2626;
            color: #fff;
        }
        .act-hapus:hover {
            background: #b91c1c;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-box {
            background: #fff;
            border-radius: 12px;
            padding: 28px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0,0,0,.2);
            animation: modalIn .25s ease-out;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 20px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 10px 14px;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            color: #1e293b;
            font-size: 14px;
            outline: none;
            box-sizing: border-box;
            transition: .2s;
        }
        .form-group input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }
        .form-group input::placeholder {
            color: #9ca3af;
        }
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 24px;
        }
        .btn-simpan {
            padding: 10px 24px;
            background: #059669;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }
        .btn-simpan:hover {
            background: #047857;
        }
        .btn-batal {
            padding: 10px 24px;
            background: #e2e8f0;
            color: #475569;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: .2s;
        }
        .btn-batal:hover {
            background: #cbd5e1;
        }
        .loading-row td {
            text-align: center;
            padding: 32px;
            color: #94a3b8;
        }
        .error-toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            background: #dc2626;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            z-index: 9999;
            animation: toastIn .3s ease-out;
            display: none;
        }
        .error-toast.show {
            display: block;
        }
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(-50%) translateY(20px); }
            to { opacity: 1; transform: translateX(-50%) translateY(0); }
        }

        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 22px;
            cursor: pointer;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            inset: 0;
            background: #cbd5e1;
            border-radius: 22px;
            transition: .3s;
        }
        .toggle-slider::before {
            content: '';
            position: absolute;
            left: 3px;
            bottom: 3px;
            width: 16px;
            height: 16px;
            background: white;
            border-radius: 50%;
            transition: .3s;
        }
        .toggle-switch input:checked + .toggle-slider {
            background: #22c55e;
        }
        .toggle-switch input:checked + .toggle-slider::before {
            transform: translateX(18px);
        }
        .toggle-loading {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>
<body class="page-admin">
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">ADMIN</h1>
            <p class="admin-subtitle">Kategori Layanan</p>
        </div>
        <nav class="admin-nav">
            <a href="/admin">Dashboard</a>
            <a href="/admin/kategori" class="active">Kategori</a>
            <a href="/admin/loket">Loket</a>
            <a href="/admin/laporan">Laporan</a>
            <a href="/admin/profil">Profil</a>
        </nav>

        <div class="crud-container">
            <div class="crud-header">
                <span class="crud-title">Daftar Kategori</span>
                <button class="btn-tambah" id="btnTambah">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Kategori
                </button>
            </div>

            <div class="admin-table-wrapper">
                <table class="queue-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Aktif</th>
                            <th>Dibuat</th>
                            <th style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kategoriBody">
                        <tr class="loading-row">
                            <td colspan="5">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="app-footer">by natedekaka</div>
    </div>

    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-box">
            <h3 class="modal-title" id="modalTitle">Tambah Kategori</h3>
            <form id="formKategori">
                <input type="hidden" id="editId" value="">
                <div class="form-group">
                    <label for="inputNama">Nama Kategori</label>
                    <input type="text" id="inputNama" placeholder="cth: BPJS Umum" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="inputKode">Kode</label>
                    <input type="text" id="inputKode" placeholder="cth: BPJS" maxlength="10" required autocomplete="off" style="text-transform:uppercase">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-batal" id="btnBatal">Batal</button>
                    <button type="submit" class="btn-simpan" id="btnSimpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="error-toast" id="errorToast"></div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const kategoriBody = document.getElementById('kategoriBody');
        const modalOverlay = document.getElementById('modalOverlay');
        const formKategori = document.getElementById('formKategori');
        const modalTitle = document.getElementById('modalTitle');
        const editId = document.getElementById('editId');
        const inputNama = document.getElementById('inputNama');
        const inputKode = document.getElementById('inputKode');
        const btnTambah = document.getElementById('btnTambah');
        const btnBatal = document.getElementById('btnBatal');
        const errorToast = document.getElementById('errorToast');

        let toastTimer = null;

        function showError(msg) {
            errorToast.textContent = msg;
            errorToast.classList.add('show');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => errorToast.classList.remove('show'), 4000);
        }

        function openModal(title, id = '', nama = '', kode = '') {
            modalTitle.textContent = title;
            editId.value = id;
            inputNama.value = nama;
            inputKode.value = kode;
            modalOverlay.classList.add('active');
            inputNama.focus();
        }

        function closeModal() {
            modalOverlay.classList.remove('active');
            formKategori.reset();
            editId.value = '';
        }

        btnTambah.addEventListener('click', () => openModal('Tambah Kategori'));
        btnBatal.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });

        inputKode.addEventListener('input', () => {
            inputKode.value = inputKode.value.toUpperCase();
        });

        window.toggleKategori = async function(id, el) {
            const label = el.closest('.toggle-switch');
            label.classList.add('toggle-loading');
            try {
                const res = await fetch('/api/kategori/toggle', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const json = await res.json();
                if (json.success) {
                    el.checked = json.data.is_active == 1;
                } else {
                    el.checked = !el.checked;
                    showError(json.message || 'Gagal mengubah status');
                }
            } catch (e) {
                el.checked = !el.checked;
                showError('Terjadi kesalahan');
            } finally {
                label.classList.remove('toggle-loading');
            }
        };

        async function loadData() {
            try {
                const res = await fetch('/api/kategori');
                const json = await res.json();
                if (!json.success) throw new Error(json.message || 'Gagal memuat data');
                renderTable(json.data);
            } catch (err) {
                kategoriBody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:32px;color:#ef4444;">${err.message}</td></tr>`;
            }
        }

        function renderTable(data) {
            if (!data || data.length === 0) {
                kategoriBody.innerHTML = `
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <p>Belum ada kategori</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            kategoriBody.innerHTML = data.map(item => {
                const checked = item.is_active == 1 ? 'checked' : '';
                return '<tr>' +
                    '<td><span class="badge-kode">' + escapeHtml(item.code) + '</span></td>' +
                    '<td>' + escapeHtml(item.name) + '</td>' +
                    '<td>' +
                        '<label class="toggle-switch" data-id="' + item.id + '">' +
                            '<input type="checkbox" ' + checked + ' onchange="toggleKategori(' + item.id + ', this)">' +
                            '<span class="toggle-slider"></span>' +
                        '</label>' +
                    '</td>' +
                    '<td>' + formatDate(item.created_at) + '</td>' +
                    '<td>' +
                        '<div class="action-cell">' +
                            '<button class="act-btn act-edit" data-id="' + item.id + '" data-nama="' + escapeAttr(item.name) + '" data-kode="' + escapeAttr(item.code) + '">Edit</button>' +
                            '<button class="act-btn act-hapus" data-id="' + item.id + '" data-nama="' + escapeAttr(item.name) + '">Hapus</button>' +
                        '</div>' +
                    '</td>' +
                '</tr>';
            }).join('');

            document.querySelectorAll('.act-edit').forEach(btn => {
                btn.addEventListener('click', () => {
                    openModal('Edit Kategori', btn.dataset.id, btn.dataset.nama, btn.dataset.kode);
                });
            });

            document.querySelectorAll('.act-hapus').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const nama = btn.dataset.nama;
                    if (!confirm(`Hapus kategori "${nama}"?`)) return;
                    try {
                        const res = await fetch('/api/kategori/hapus', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: btn.dataset.id })
                        });
                        const json = await res.json();
                        if (!json.success) throw new Error(json.message || 'Gagal menghapus');
                        loadData();
                    } catch (err) {
                        showError(err.message);
                    }
                });
            });
        }

        formKategori.addEventListener('submit', async (e) => {
            e.preventDefault();
            const nama = inputNama.value.trim();
            const kode = inputKode.value.trim().toUpperCase();
            if (!nama || !kode) {
                showError('Nama dan kode harus diisi');
                return;
            }
            const id = editId.value;
            const isEdit = !!id;
            const url = isEdit ? '/api/kategori/edit' : '/api/kategori/tambah';
            const body = isEdit ? { id, name: nama, code: kode } : { name: nama, code: kode };

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const json = await res.json();
                if (!json.success) throw new Error(json.message || 'Gagal menyimpan');
                closeModal();
                loadData();
            } catch (err) {
                showError(err.message);
            }
        });

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function escapeAttr(str) {
            return str.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        }

        loadData();
    });
    </script>
</body>
</html>
