 # Aplikasi Manajemen Pendaftaran & Pengurusan Paspor

Versi: prototype — dokumentasi teknis dan instruksi setup untuk pengembangan lokal.

Deskripsi singkat
--
Aplikasi ini adalah sistem internal untuk mengelola pendaftaran, daftar ulang, dan pengurusan paspor di kantor imigrasi. Fitur utama meliputi:

- Penjadwalan otomatis pendaftar (kuota 5 orang per hari).
- Daftar ulang dengan pengecekan berkas dan generasi nomor antrian otomatis.
- Pengurusan berkas dengan status (Diterima/Ditolak) dan perhitungan pendapatan.
- Dashboard ringkasan, grafik jadwal 7 hari, dan live search (AJAX/jQuery).
- Autentikasi sederhana (login) dan proteksi rute.

Tujuan README
--
Dokumen ini memberikan panduan lengkap untuk: instalasi, konfigurasi, struktur kode penting, rute API & UI, contoh penggunaan, troubleshooting, dan rekomendasi pengembangan.

Persyaratan
--
- PHP 8.x
- Composer
- MySQL / MariaDB (direkomendasikan) atau SQLite (dengan PDO)
- Node/npm (opsional, proyek menggunakan CDN untuk asset)

Instalasi dan setup (singkat)
--
1. Clone repository.
2. Install dependensi PHP:

```bash
composer install
```

3. Copy file environment dan sesuaikan koneksi database:

```bash
cp .env.example .env
# edit .env -> DB_* dan APP_URL
```

4. Generate key aplikasi:

```bash
php artisan key:generate
```

5. Jalankan migrasi dan (opsional) seeder admin:

```bash
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
```

6. Jalankan server dev:

```bash
php artisan serve
```

Akses UI: `http://127.0.0.1:8000` (login diperlukan untuk halaman admin)

Catatan: jika muncul error "could not find driver" saat migrate, aktifkan/instal ekstensi PDO SQLite atau gunakan MySQL dan sesuaikan `.env`.

Struktur ringkas kode
--
- `app/Models/` — Eloquent models (`Pendaftaran`, `DaftarUlang`, `Pengurusan`, `User`)
- `app/Http/Controllers/` — Controller fitur utama (`PendaftaranController`, `DaftarUlangController`, `PengurusanController`, `DashboardController`, `AuthController`)
- `database/migrations/` — Migrations tabel
- `database/seeders/` — Seeder sample, termasuk seeder admin
- `resources/views/` — Blade views (layout, partials, pages)
- `routes/web.php` — Rute UI dan API (lihat bagian rute)

Rute penting
--
Semua rute UI dan API dilindungi oleh `auth` middleware kecuali halaman login.

UI (Blade views):

- `GET /` — Dashboard
- `GET /pendaftaran` — Halaman Pendaftaran
- `GET /daftar-ulang` — Halaman Daftar Ulang
- `GET /pengurusan` — Halaman Pengurusan
- `GET /login`, `POST /login`, `POST /logout`, `GET /user/profile`

API (JSON) — prefix `/api`:

- Pendaftaran: `GET /api/pendaftaran`, `GET /api/pendaftaran/search?q=`, `POST /api/pendaftaran`, `GET/PUT/DELETE /api/pendaftaran/{id}`
- Daftar Ulang: `GET /api/daftar-ulang`, `GET /api/daftar-ulang/search?q=`, `POST /api/daftar-ulang`, `GET/PUT/DELETE /api/daftar-ulang/{id}`
- Pengurusan: `GET /api/pengurusan`, `GET /api/pengurusan/search?q=`, `POST /api/pengurusan`, `GET/DELETE /api/pengurusan/{id}`

Catatan API & perilaku penting
--
- Semua endpoint API mengembalikan JSON yang berguna untuk UI (format tanggal sudah diformat ke locale `id`).
- Behavior pencarian (`search?q=`): multi-field fuzzy search + prioritas exact match jika query berupa angka (no_daftar / no_antrian).
- Pendaftaran melakukan scheduling otomatis saat create (kuota 5/hari). Daftar ulang mengecek kesesuaian jadwal & berkas, lalu otomatis men-generate `no_antrian` bila memenuhi aturan.

Contoh request singkat
--
Contoh curl (asumsi sesi login terjaga via cookie jar):

```bash
curl -X GET "http://127.0.0.1:8000/api/pendaftaran" -b cookiejar.txt -c cookiejar.txt

curl -X GET "http://127.0.0.1:8000/api/pendaftaran/search?q=Senin" -b cookiejar.txt -c cookiejar.txt

curl -X POST "http://127.0.0.1:8000/api/pendaftaran" -b cookiejar.txt -c cookiejar.txt \
	-H "X-CSRF-TOKEN: <token>" \
	-d "nama_pemohon=Rudi&tanggal_daftar=2026-01-07"
```

UI behavior & pengalaman pengguna
--
- Pencarian: input search di setiap halaman (Pendaftaran, Daftar Ulang, Pengurusan) menggunakan AJAX (jQuery) dan hasil langsung dirender ke tabel.
- Pada Pendaftaran, hasil pencarian menimpa isi tabel (tidak lagi dropdown).
- Sidebar memuat quick-stats (jumlah pendaftar, daftar ulang, pengurusan, pendapatan) dan melakukan refresh via endpoint ringkasan.
- Login page dirapikan: menampilkan welcome message dan form login di tampilan yang rapi.

Pengujian & quality gates
--
- Jalankan test suite (jika tersedia):

```bash
php artisan test
# atau
vendor\\bin\\phpunit
```

Debug umum
--
- Jika rute lama `/ui/*` masih muncul setelah perubahan, jalankan:

```bash
php artisan view:clear
php artisan cache:clear
php artisan config:cache
```

- Jika fetch AJAX mengembalikan 403: periksa header `X-CSRF-TOKEN`, atau apakah request dilakukan dengan cookie session yang valid.

File penting untuk pengembangan lebih lanjut
--
- `resources/views/partials/sidebar.blade.php` — quick-stats + navigasi
- `resources/views/partials/navbar.blade.php` — header + tombol toggle sidebar
- `resources/views/auth/login.blade.php` — tampilan login
- `app/Http/Controllers/*Controller.php` — implementasi aturan bisnis
- `routes/web.php` — daftar rute UI & API

Rekomendasi pengembangan (roadmap)
--
1. Tambah paginasi & index DB untuk kolom pencarian (nama, no_daftar, no_antrian, status) jika data besar.
2. Integrasi Meilisearch / Elasticsearch untuk search yang lebih canggih.
3. API token / OAuth untuk akses programatik (mobile/third-party).
4. Role-based access control & audit log untuk aktivitas penting.
5. Test coverage: unit test untuk aturan bisnis kunci (kuota, antrian, perhitungan pendapatan).

