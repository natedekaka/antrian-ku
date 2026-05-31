# Antrian Ku

Sistem antrian berbasis web untuk loket pelayanan. Dibangun dengan PHP 8, MySQL 8, dan dijalankan dengan Podman/Docker.

## Fitur

- **Ambil Nomor** — Pengguna memilih kategori layanan dan mendapatkan nomor antrian
- **Layar Antrian** — Tampilan display yang menunjukkan nomor antrian yang sedang dipanggil
- **Panel Petugas** — Panggil, panggil ulang, skip, dan selesaikan antrian per loket
- **Manajemen Kategori** — Tambah, edit, hapus, aktif/nonaktifkan kategori layanan
- **Manajemen Loket** — Tambah dan edit loket pelayanan
- **Laporan Harian** — Statistik per kategori, riwayat antrian, export CSV, bersihkan data
- **Profil Aplikasi** — Ubah nama aplikasi dan upload logo dari admin
- **TTS / Suara Panggilan** — Browser native Speech Synthesis + Web Audio API (beep)
- **Cetak Tiket** — Cetak nomor antrian setelah mengambil nomor
- **Estimasi Waktu** — Perkiraan waktu tunggu berdasarkan rata-rata pelayanan
- **Responsive** — Tampilan menyesuaikan layar HP, tablet, dan desktop

## Halaman

| Halaman | URL | Akses |
|---------|-----|-------|
| Ambil Nomor | `/` | Publik |
| Layar Antrian | `/layar` | Publik |
| Panel Petugas | `/admin` | Publik (tanpa login) |
| Kategori | `/admin/kategori` | Publik |
| Loket | `/admin/loket` | Publik |
| Laporan | `/admin/laporan` | Publik |
| Profil | `/admin/profil` | Publik |
| phpMyAdmin | `:9000` | root / rootpass |

> **Catatan:** Semua halaman admin saat ini dapat diakses tanpa login. Tambahkan autentikasi sesuai kebutuhan.

## Persyaratan

- Podman (atau Docker)
- podman-compose (atau docker-compose)
- Git (opsional)

## Instalasi

### Via Podman / Docker (Rekomendasi)

```bash
# 1. Clone repositori
git clone <url-repo> antrian-ku
cd antrian-ku

# 2. Jalankan container
podman-compose up -d

# 3. Tunggu sampai database siap (sekitar 10-15 detik)
podman-compose logs -f db
# Tekan Ctrl+C setelah melihat "ready for connections"

# 4. Buka aplikasi
firefox http://localhost:8085
```

Atau jika menggunakan Docker:

```bash
docker-compose up -d
```

### Via XAMPP

#### 1. Persiapan

Pastikan XAMPP sudah terinstall dengan:
- **Apache** (aktif)
- **MySQL / MariaDB** (aktif)
- **PHP 8.x**

#### 2. Letakkan project

```bash
# Copy folder project ke htdocs
cp -r antrian-ku /path/to/xampp/htdocs/antrian-ku
```

Atau clone langsung:

```bash
cd /path/to/xampp/htdocs/
git clone <url-repo> antrian-ku
```

#### 3. Setup database

Buka phpMyAdmin: `http://localhost/phpmyadmin`

1. Buat database baru: `antrian_ku`
2. Pilih database → tab **SQL**
3. Copy paste isi file `antrian-ku/migrations/001_init.sql`
4. Klik **Go** untuk menjalankan

Atau via command line:

```bash
# Windows: sesuaikan path mysql.exe
C:\xampp\mysql\bin\mysql -u root < antrian-ku/migrations/001_init.sql

# Linux / macOS
mysql -u root < antrian-ku/migrations/001_init.sql
```

#### 4. Konfigurasi koneksi database

Buka file `antrian-ku/.env` dan sesuaikan:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=antrian_ku
DB_USER=root
DB_PASS=
```

> **Catatan:** Di XAMPP, user MySQL default adalah `root` tanpa password.
> Jika Anda mengganti password root, sesuaikan `DB_PASS`.

#### 5. Konfigurasi Apache (rewrite module)

Aktifkan `mod_rewrite` di XAMPP:

1. Buka `C:\xampp\apache\conf\httpd.conf`
2. Cari baris: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Hapus tanda `#` di depannya
4. Restart Apache dari XAMPP Control Panel

#### 6. Upload logo (jika diperlukan)

Buat folder uploads dan beri write permission:

```bash
mkdir antrian-ku/public/assets/uploads
# Windows: folder sudah bisa ditulis
# Linux/macOS:
chmod 777 antrian-ku/public/assets/uploads/
```

#### 7. Akses aplikasi

| Halaman | URL |
|---------|-----|
| Ambil Nomor | `http://localhost/antrian-ku/` |
| Layar Antrian | `http://localhost/antrian-ku/layar` |
| Panel Petugas | `http://localhost/antrian-ku/admin` |
| Kategori | `http://localhost/antrian-ku/admin/kategori` |
| Loket | `http://localhost/antrian-ku/admin/loket` |
| Laporan | `http://localhost/antrian-ku/admin/laporan` |
| Profil | `http://localhost/antrian-ku/admin/profil` |

#### 8. Struktur URL di XAMPP

Karena project diletakkan di subfolder (`htdocs/antrian-ku`), aplikasi akan otomatis mendeteksi base path. Pastikan:

- **Nginx config (.conf)** — tidak dipakai di XAMPP, abaikan
- **Routing** — sudah handle subfolder via `index.php` tanpa perlu konfigurasi tambahan

#### Catatan XAMPP

| Komponen | Keterangan |
|----------|------------|
| Apache | Gunakan bawaan XAMPP, port 80 |
| MySQL | Port 3306, user `root`, tanpa password |
| PHP | Minimal PHP 8.0 (disarankan 8.2) |
| Ekstensi | `pdo_mysql` harus aktif (cek php.ini) |
| Upload max | Cek `upload_max_filesize` dan `post_max_size` di php.ini |

## Struktur Direktori

```
antrian-ku/
├── .env                    # Konfigurasi database
├── Dockerfile              # Build image PHP 8.2
├── docker-compose.yml      # Orkestrasi container
├── migrations/
│   └── 001_init.sql        # Skema database + data awal
├── nginx/
│   └── default.conf        # Konfigurasi nginx
├── public/
│   ├── index.php           # Router utama
│   └── assets/
│       ├── css/style.css   # Stylesheet
│       ├── js/app.js       # JavaScript klien
│       └── uploads/        # Upload logo
├── src/
│   ├── Database.php        # Koneksi database (PDO)
│   └── functions.php       # Handler API
└── views/
    ├── ambil-nomor.php     # Halaman ambil nomor
    ├── layar-antrian.php   # Halaman layar display
    └── admin/
        ├── dashboard.php   # Panel petugas
        ├── kategori.php    # Manajemen kategori
        ├── loket.php       # Manajemen loket
        ├── laporan.php     # Laporan harian
        └── profil.php      # Profil aplikasi
```

## API Endpoints

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| GET | `/api/data-antrian` | Data antrian real-time |
| POST | `/api/ambil` | Ambil nomor baru |
| POST | `/api/panggil` | Panggil antrian |
| POST | `/api/panggil-ulang` | Panggil ulang antrian |
| POST | `/api/skip` | Lewati antrian |
| POST | `/api/selesai` | Selesaikan antrian |
| GET | `/api/estimasi?category_id=X` | Estimasi waktu tunggu |
| GET | `/api/kategori` | Daftar kategori |
| POST | `/api/kategori/tambah` | Tambah kategori |
| POST | `/api/kategori/edit` | Edit kategori |
| POST | `/api/kategori/hapus` | Hapus kategori |
| POST | `/api/kategori/toggle` | Aktif/nonaktif kategori |
| GET | `/api/loket` | Daftar loket |
| POST | `/api/loket/tambah` | Tambah loket |
| POST | `/api/loket/edit` | Edit loket |
| POST | `/api/loket/hapus` | Hapus loket |
| GET | `/api/laporan` | Data laporan harian |
| GET | `/api/laporan/csv` | Export CSV laporan |
| POST | `/api/laporan/bersihkan` | Hapus data hari ini |
| GET | `/api/settings` | Ambil pengaturan aplikasi |
| POST | `/api/settings/save` | Simpan pengaturan (multipart) |

## Konfigurasi

### Port

| Layanan | Port Host | Port Container |
|---------|-----------|----------------|
| Web (nginx) | 8085 | 80 |
| Database (MySQL) | 3307 | 3306 |
| phpMyAdmin | 9000 | 80 |

Ubah port di `docker-compose.yml` jika bentrok dengan aplikasi lain.

### Database

- **Host**: `db`
- **Port**: `3306`
- **Database**: `antrian_ku`
- **User**: `antrian`
- **Password**: `antrianpass`
- **Root Password**: `rootpass`

Akses phpMyAdmin: `http://localhost:9000` (root / rootpass)

### Lingkungan

File `.env` berisi konfigurasi database yang digunakan oleh aplikasi:

```
DB_HOST=db
DB_PORT=3306
DB_NAME=antrian_ku
DB_USER=antrian
DB_PASS=antrianpass
```

## Penggunaan

### User — Ambil Nomor

Akses via HP atau laptop: buka `http://localhost:8085/`

**Langkah-langkah:**

1. **Pilih Kategori** — Tap tombol kategori layanan yang diinginkan (contoh: Pendaftaran)
2. **Lihat Nomor** — Modal akan muncul dengan nomor antrian Anda
3. **Cetak Tiket (opsional)** — Klik **Cetak Tiket** untuk mencetak nomor antrian
   - Sebelum mencetak, pilih **ukuran kertas** yang sesuai:
     - **58 mm** — Untuk printer thermal portable / HP (default)
     - **80 mm** — Untuk printer thermal ukuran standar (Epson TM-T82, dll.)
   - Print dialog browser akan terbuka → pilih printer thermal → **Print**
   - Jika logo sudah diupload di halaman Profil, logo akan muncul otomatis di tiket
4. **Ambil Lagi** — Klik **Ambil Antrian Lagi** untuk kembali
5. **Cek Estimasi** — Estimasi waktu tunggu muncul otomatis saat memilih kategori
6. **Tunggu Panggilan** — Nomor akan dipanggil di layar display dan melalui suara (TTS)

> **Tips HP**: Pada print dialog Android, pilih ukuran kertas "58x150mm" atau "80x150mm" sesuai pilihan. Atur margin ke "Minimum" agar tiket tidak terpotong.

### Petugas — Panel Admin

Buka `http://localhost:8085/admin`

**Langkah-langkah:**

1. **Pilih Loket** — Klik loket yang sedang bertugas (Loket 1, Loket 2, dll.)
2. **PANGGIL** — Memanggil antrian berikutnya (suara + beep akan aktif)
3. **PANGGIL ULANG** — Memanggil ulang antrian yang sudah dipanggil
4. **SKIP** — Melewatkan antrian (status berubah menjadi "Dilewati")
5. **SELESAI** — Menandai antrian selesai dilayani

> **Catatan**: Loket yang berbeda bisa memanggil antrian secara bersamaan. Antrian akan terisi otomatis dari yang paling lama menunggu.

### Admin — Manajemen

Buka menu admin yang tersedia:

#### Kategori (`/admin/kategori`)

| Aksi | Cara |
|------|------|
| **Tambah Kategori** | Klik **Tambah Kategori** → isi nama dan kode (contoh: BPJS) → Simpan |
| **Edit Kategori** | Klik **Edit** pada baris kategori → ubah data → Simpan |
| **Hapus Kategori** | Klik **Hapus** → konfirmasi |
| **Aktif/Nonaktifkan** | Geser toggle switch untuk mengaktifkan atau menonaktifkan kategori |

#### Loket (`/admin/loket`)

| Aksi | Cara |
|------|------|
| **Tambah Loket** | Klik **Tambah Loket** → isi nama dan kode (contoh: Loket 1, kode: 1) → Simpan |
| **Edit Loket** | Klik **Edit** → ubah data → Simpan |
| **Hapus Loket** | Klik **Hapus** → konfirmasi |

#### Laporan (`/admin/laporan`)

- **Statistik Harian** — Total antrian, selesai, dipanggil, menunggu, dilewati
- **Rekap Per Kategori** — Rincian per kategori layanan
- **Antrian Terakhir** — 20 antrian terbaru dengan status
- **Export CSV** — Download laporan dalam format CSV (buka di Excel)
- **Bersihkan Data** — Hapus semua data antrian hari ini

#### Profil (`/admin/profil`)

- **Nama Aplikasi** — Ubah nama yang tampil di halaman utama dan tiket cetakan
- **Logo** — Upload logo (format: PNG, JPG, GIF, SVG, WEBP, maks 2MB)
  - Logo akan muncul di halaman ambil nomor dan tiket cetakan
  - Klik **Hapus Logo** untuk menghapus

### Cetak Tiket ke Printer Thermal

Fitur cetak tiket mendukung printer thermal ukuran **58mm** dan **80mm**:

1. **Pilih ukuran** di modal ambil nomor (58mm default untuk HP/portable)
2. **Klik Cetak Tiket** → browser membuka print dialog
3. Di print dialog:
   - Pilih **printer thermal** yang terhubung (USB/Bluetooth)
   - Ukuran kertas otomatis menyesuaikan pilihan (58mm atau 80mm)
   - Atur margin ke **None** atau **Minimum**
4. **Print** — Tiket akan tercetak dengan:
   - Logo aplikasi (jika diupload)
   - Nama aplikasi
   - Nomor antrian (font besar dan tebal)
   - Nama kategori
   - Tanggal dan jam ambil

## Troubleshooting

### Port bentrok

```bash
# Cek port yang digunakan
ss -tlnp | grep -E '8085|3307|9000'

# Ubah port di docker-compose.yml jika perlu
```

### Database tidak terisi

```bash
# Cek log database
podman-compose logs db

# Jalankan migrasi manual
podman exec -i antrian-ku_db_1 mysql -uroot -prootpass antrian_ku < migrations/001_init.sql
```

### Permission upload logo

```bash
# Jika upload logo gagal, fix permission di dalam container
podman exec antrian-ku_php_1 chown www-data:www-data /var/www/html/public/assets/uploads/
podman exec antrian-ku_php_1 chmod 755 /var/www/html/public/assets/uploads/
```

### Container tidak bisa akses database

```bash
# Restart container
podman-compose restart

# Atau rebuild
podman-compose down
podman-compose up -d
```

### Akses dari HP dalam jaringan yang sama

```bash
# Cek IP laptop
ip addr show | grep -oP 'inet \K[\d.]+' | grep -v 127.0.0.1

# Buka dari HP: http://IP_LAPTOP:8085/
# Contoh: http://192.168.18.126:8085/
```

> **Catatan:** Pastikan firewall tidak memblokir port:
> ```bash
> sudo ufw allow 8085/tcp
> sudo ufw allow 9000/tcp
> ```

## Teknologi

- **Backend**: PHP 8.2 (PDO MySQL)
- **Frontend**: HTML, CSS, JavaScript (native)
- **Web Server**: Nginx
- **Database**: MySQL 8.0
- **Container**: Podman / Docker
- **Suara**: Web Speech API + Web Audio API
- **Font**: Inter (Google Fonts)

## Lisensi

Hak cipta oleh natedekaka.
