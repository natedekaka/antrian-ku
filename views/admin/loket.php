<?php
$db = Database::getConnection();
$counters = $db->query("SELECT * FROM counters ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Loket</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="page-admin">
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">KELOLA LOKET</h1>
            <p class="admin-subtitle">Atur loket antrian</p>
        </div>
        <nav class="admin-nav">
            <a href="/admin">Dashboard</a>
            <a href="/admin/kategori">Kategori</a>
            <a href="/admin/loket" class="active">Loket</a>
            <a href="/admin/laporan">Laporan</a>
            <a href="/admin/profil">Profil</a>
        </nav>

        <div class="admin-header-actions" style="margin-bottom:24px;">
            <button id="btn-tambah" class="action-btn btn-panggil">+ TAMBAH LOKET</button>
        </div>

        <div id="message-container"></div>

        <div class="admin-table-wrapper">
            <table class="queue-table" id="loket-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Loket</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="loket-body">
                    <tr>
                        <td colspan="4" style="text-align:center;padding:24px;color:#94a3b8;">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="app-footer">by natedekaka</div>
    </div>

    <div id="loket-modal" class="lmodal" style="display:none;">
        <div class="lmodal-bg"></div>
        <div class="lmodal-box">
            <div class="lmodal-head">
                <h3 id="modal-title">Tambah Loket</h3>
                <button class="lmodal-close">&times;</button>
            </div>
            <form id="loket-form">
                <input type="hidden" id="loket-id" name="id">
                <div class="lmodal-field">
                    <label for="loket-name">Nama Loket</label>
                    <input type="text" id="loket-name" name="name" class="lmodal-input" placeholder="Contoh: Loket 1" required>
                </div>
                <div class="lmodal-field">
                    <label for="loket-code">Kode</label>
                    <input type="text" id="loket-code" name="code" class="lmodal-input" placeholder="Contoh: A" required>
                </div>
                <div class="lmodal-aksi">
                    <button type="button" class="lmodal-btn lmodal-batal" id="btn-batal">BATAL</button>
                    <button type="submit" class="lmodal-btn lmodal-simpan" id="btn-simpan">SIMPAN</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .lmodal {
            position: fixed; inset: 0; z-index: 1000;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .lmodal-bg {
            position: absolute; inset: 0; background: rgba(0,0,0,0.5);
        }
        .lmodal-box {
            position: relative; background: #fff; border-radius: 12px;
            width: 100%; max-width: 420px; padding: 28px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            z-index: 1;
        }
        .lmodal-head {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }
        .lmodal-head h3 { margin:0; font-size:18px; color:#1e293b; }
        .lmodal-close {
            background: none; border: none; font-size:24px; cursor:pointer; color:#64748b;
        }
        .lmodal-field {
            margin-bottom: 16px;
        }
        .lmodal-field label {
            display: block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;
        }
        .lmodal-input {
            width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px;
            font-size:14px; box-sizing:border-box;
        }
        .lmodal-input:focus {
            outline:none; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.15);
        }
        .lmodal-aksi {
            display: flex; gap: 8px; justify-content: flex-end; margin-top: 24px;
        }
        .lmodal-btn {
            padding: 8px 24px; border: none; border-radius: 8px;
            font-weight: 600; font-size: 13px; cursor: pointer;
        }
        .lmodal-simpan {
            background: #059669; color: white;
        }
        .lmodal-simpan:hover {
            background: #047857;
        }
        .lmodal-batal {
            background: #e2e8f0; color: #475569;
        }
        .lmodal-batal:hover {
            background: #cbd5e1;
        }
        .action-cell {
            display: flex; gap: 6px;
        }
        .act-btn {
            padding: 4px 12px; border: none; border-radius: 6px;
            font-weight: 600; font-size: 12px; cursor: pointer; transition: .2s;
        }
        .act-edit {
            background: #059669; color: #fff;
        }
        .act-edit:hover {
            background: #047857;
        }
        .act-hapus {
            background: #dc2626; color: #fff;
        }
        .act-hapus:hover {
            background: #b91c1c;
        }
        #message-container {
            margin-bottom: 16px;
        }
        .msg {
            padding: 10px 14px; border-radius: 8px; font-size:13px; margin-bottom:8px;
        }
        .msg-success { background: #d1fae5; color: #065f46; }
        .msg-error { background: #fee2e2; color: #991b1b; }
        .admin-header-actions {
            display: flex; align-items: center;
        }
    </style>

    <script>
        const API = {
            list: '/api/loket',
            tambah: '/api/loket/tambah',
            edit: '/api/loket/edit',
            hapus: '/api/loket/hapus',
        };

        const modal = document.getElementById('loket-modal');
        const modalTitle = document.getElementById('modal-title');
        const form = document.getElementById('loket-form');
        const idInput = document.getElementById('loket-id');
        const nameInput = document.getElementById('loket-name');
        const codeInput = document.getElementById('loket-code');
        const tbody = document.getElementById('loket-body');
        const msgContainer = document.getElementById('message-container');

        let editing = false;

        function showMessage(text, type) {
            const div = document.createElement('div');
            div.className = 'msg msg-' + type;
            div.textContent = text;
            msgContainer.appendChild(div);
            setTimeout(() => div.remove(), 4000);
        }

        async function loadData() {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:24px;color:#94a3b8;">Memuat data...</td></tr>';
            try {
                const res = await fetch(API.list);
                const json = await res.json();
                if (!json.success) { throw new Error(json.message || 'Gagal memuat data'); }
                renderTable(json.data);
            } catch (e) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:24px;color:#dc2626;">Gagal memuat data</td></tr>';
                showMessage(e.message, 'error');
            }
        }

        function renderTable(data) {
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:24px;color:#94a3b8;">Belum ada loket</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(item => {
                const date = item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID', { year:'numeric', month:'long', day:'numeric' }) : '-';
                return '<tr>' +
                    '<td><strong>' + esc(item.code) + '</strong></td>' +
                    '<td>' + esc(item.name) + '</td>' +
                    '<td>' + date + '</td>' +
                    '<td><div class="action-cell">' +
                        '<button class="act-btn act-edit" onclick="editLoket(' + item.id + ')">Edit</button>' +
                        '<button class="act-btn act-hapus" onclick="hapusLoket(' + item.id + ')">Hapus</button>' +
                    '</div></td>' +
                '</tr>';
            }).join('');
        }

        function esc(str) {
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }

        function openModal(title, data) {
            editing = !!data;
            modalTitle.textContent = title;
            if (data) {
                idInput.value = data.id;
                nameInput.value = data.name;
                codeInput.value = data.code;
            } else {
                idInput.value = '';
                nameInput.value = '';
                codeInput.value = '';
            }
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
            form.reset();
            idInput.value = '';
            editing = false;
        }

        document.getElementById('btn-tambah').addEventListener('click', () => {
            openModal('Tambah Loket', null);
        });

        document.querySelector('.lmodal-close').addEventListener('click', closeModal);
        document.getElementById('btn-batal').addEventListener('click', closeModal);
        modal.querySelector('.lmodal-bg').addEventListener('click', closeModal);

        async function editLoket(id) {
            try {
                const res = await fetch(API.list);
                const json = await res.json();
                if (!json.success) throw new Error(json.message || 'Gagal memuat data');
                const item = json.data.find(d => d.id === id);
                if (!item) throw new Error('Data tidak ditemukan');
                openModal('Edit Loket', item);
            } catch (e) {
                showMessage(e.message, 'error');
            }
        }

        function hapusLoket(id) {
            if (!confirm('Yakin ingin menghapus loket ini?')) return;
            fetch(API.hapus, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            })
            .then(r => r.json())
            .then(json => {
                if (json.success) {
                    showMessage('Loket berhasil dihapus', 'success');
                    loadData();
                } else {
                    showMessage(json.message || 'Gagal menghapus loket', 'error');
                }
            })
            .catch(() => showMessage('Terjadi kesalahan', 'error'));
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                name: nameInput.value.trim(),
                code: codeInput.value.trim().toUpperCase(),
            };
            if (editing) payload.id = Number(idInput.value);

            const url = editing ? API.edit : API.tambah;

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (json.success) {
                    showMessage(editing ? 'Loket berhasil diperbarui' : 'Loket berhasil ditambahkan', 'success');
                    closeModal();
                    loadData();
                } else {
                    showMessage(json.message || 'Gagal menyimpan loket', 'error');
                }
            } catch (e) {
                showMessage('Terjadi kesalahan', 'error');
            }
        });

        loadData();
    </script>
</body>
</html>
