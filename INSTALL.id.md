# PresenSI — Panduan Instalasi Lengkap

**Bahasa:** [English](INSTALL.md) | [Bahasa Indonesia](INSTALL.id.md)

> **"Si Pintar Urusan Presensi"**
> Baca seluruh panduan ini dulu sebelum mulai instalasi.
> Lihat juga: `README.md` untuk dokumentasi fitur.

---

_PresenSI oleh burnblazter <hello@fael.my.id>_
_Fork dari [o-present](https://github.com/josephines1/o-present) buatan Josephine_
_Lisensi: GPL-3.0 | [github.com/burnblazter](https://github.com/burnblazter)_

---

## Daftar Isi

- [Bagian 1: Instalasi Lokal (Laragon)](#bagian-1-instalasi-lokal-laragon)
- [Bagian 2: Deployment Shared Hosting (cPanel)](#bagian-2-deployment-shared-hosting-cpanel)
  - [Opsi A — Set Document Root ke `/public`](#opsi-a--set-document-root-ke-public-direkomendasikan) _(Direkomendasikan)_
  - [Opsi B — Index Forwarder](#opsi-b--index-forwarder-alternatif) _(Alternatif)_
- [Catatan Tambahan](#catatan-tambahan)

---

## Bagian 1: Instalasi Lokal (Laragon)

### Prasyarat

| Kebutuhan                                     | Detail                           |
| --------------------------------------------- | -------------------------------- |
| [Laragon Full](https://laragon.org) (terbaru) | Environment dev lokal all-in-one |
| PHP 8.1 atau lebih baru                       | Sudah dibundel di Laragon Full   |
| MySQL                                         | Sudah dibundel di Laragon        |

> **Catatan:** Di beberapa versi Windows, Laragon kadang munculin dialog UAC (User Account Control) pas instalasi atau reload konfigurasi. Selalu klik **[Yes] / [Allow]** di setiap prompt UAC-nya biar prosesnya kelar dengan benar.

---

### Langkah 1 — Extract File Project

Extract `presensi.zip` ke direktori berikut:

```
C:\laragon\www\
```

Struktur folder hasilnya harus kayak gini:

```
C:\laragon\www\presensi\
                        ├── app/
                        ├── public/
                        ├── database/
                        ├── .env.example
                        └── ...
```

---

### Langkah 2 — Jalankan Laragon

1. Buka Laragon. Kalau muncul dialog UAC, klik **[Yes]**.
2. Klik **[Start All]** di jendela utama Laragon. Tunggu sampai indikator Apache dan MySQL-nya berubah hijau.
3. Klik **[Menu]** → **PHP** → **Extensions**, lalu pastikan ekstensi berikut sudah aktif (tercentang):
   - `intl`
   - `mbstring`
   - `json`
   - `mysqlnd`
   - `curl` _(wajib buat Groq API dan Telegram)_
   - `zip` _(wajib buat fitur bulk download)_

---

### Langkah 3 — Konfigurasi Virtual Host

1. Di jendela utama Laragon, klik **[Menu]** → **Apache** → **sites-enabled** → `auto.presensi.test.conf`. File konfigurasinya bakal kebuka di text editor.

2. Ganti seluruh isi file itu dengan ini:

   ```apache
   define ROOT "C:/laragon/www/presensi/public"
   define SITE "presensi.test"

   <VirtualHost *:80>
       DocumentRoot "${ROOT}"
       ServerName ${SITE}
       ServerAlias *.${SITE}
       <Directory "${ROOT}">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>

   <VirtualHost *:443>
       DocumentRoot "${ROOT}"
       ServerName ${SITE}
       ServerAlias *.${SITE}
       <Directory "${ROOT}">
           AllowOverride All
           Require all granted
       </Directory>
       SSLEngine on
       SSLCertificateFile    C:/laragon/etc/ssl/laragon.crt
       SSLCertificateKeyFile C:/laragon/etc/ssl/laragon.key
   </VirtualHost>
   ```

3. Simpan file-nya (`Ctrl+S`) lalu tutup text editor.

4. Balik ke jendela utama Laragon, klik **[Menu]** → **Apache** → **Reload**. Kalau muncul dialog UAC, klik **[Yes]**.

---

### Langkah 4 — Buat Database

> Laragon pakai **HeidiSQL** sebagai database client bawaannya. HeidiSQL konek langsung tanpa perlu autentikasi lewat browser, jadi lebih cepat dibanding phpMyAdmin buat development lokal.

1. Klik **[Menu]** → **[Tools]** → **HeidiSQL**.
2. Di HeidiSQL Session Manager:
   - Klik **[New]** di pojok kiri bawah.
   - Biarkan default lokal (`Host: 127.0.0.1`, `User: root`, `Password: (kosong)`) atau pakai kredensial server MySQL kamu sendiri.
   - Klik **[Open]**.
3. Buat database kosong dengan nama `presensi_db`, collation `utf8mb4_unicode_ci`.
4. Jangan import dump phpMyAdmin lokal. Skemanya dibuat lewat migration CodeIgniter.

---

### Langkah 5 — Konfigurasi `.env`

1. Masuk ke `C:\laragon\www\presensi\`.
2. Copy `.env.example` lalu rename hasil copy-annya jadi `.env`.
   > Pastikan nama filenya persis `.env`, **bukan** `.env.txt`.
3. Buka `.env` di text editor dan update nilai-nilai berikut:

   **Wajib diubah:**

   ```ini
   app.baseURL = 'http://presensi.test/'

   database.default.database = presensi_db
   database.default.username = root
   database.default.password =

   GROQ_API_KEYS = your_groq_api_key
   # Ambil API key-nya di: https://console.groq.com/keys

   telegram.botToken = 'your_telegram_bot_token'
   telegram.chatId   = 'your_telegram_chat_id'
   ```

   **Biarkan default:**

   ```ini
   CI_ENVIRONMENT = production
   app.appTimezone = 'Asia/Makassar'
   GROQ_MODEL = moonshotai/kimi-k2-instruct
   database.default.hostname = localhost
   database.default.DBDriver = MySQLi
   database.default.port     = 3306
   ```

4. Simpan file-nya (`Ctrl+S`).

5. Dari root project, buat skema dan data development fiktif:

   ```bash
   php spark migrate --all
   php spark db:seed DatabaseSeeder
   ```

   Flag `--all` jalanin migration aplikasi sekaligus migration MythAuth yang di-internalize. Seeder-nya cuma buat development dan demo aja. Nggak ada foto, face descriptor, dokumen upload, token, atau data production yang ikut dimasukkan.

---

### Langkah 6 — Akses Sistemnya

1. Buka browser (Chrome, Edge, atau Firefox).
2. Masuk ke: `http://presensi.test/`
3. PresenSI udah siap dipakai.

**Troubleshooting:**

| Gejala                             | Yang Perlu Dicek                                            |
| ---------------------------------- | ----------------------------------------------------------- |
| Muncul "Not Found" atau error page | Pastikan Laragon udah di-reload setelah ngedit file `.conf` |
| Aplikasi nggak kebuka              | Pastikan nama folder project-nya `presensi` (huruf kecil)   |
| Error konfigurasi                  | Pastikan `.env` udah ada dan namanya bukan `.env.example`   |
| Masalah permission                 | Pastikan semua dialog UAC udah dijawab **[Yes]**            |

---

## Bagian 2: Deployment Shared Hosting (cPanel)

Ada dua opsi deployment. Pilih yang sesuai sama paket hosting kamu. **Opsi A lebih direkomendasikan** kalau hosting kamu support pengaturan document root.

---

## Opsi A — Set Document Root ke `/public` _(Direkomendasikan)_

### Langkah 1 — Konfigurasi Document Root Domain

1. Login ke cPanel.
2. Buka **[Domains]** atau **[Addon Domains]**.
3. Cari domain kamu lalu klik **[Manage]**.
4. Cari kolom **Document Root** dan ubah nilainya dari:
   ```
   public_html
   ```
   jadi:
   ```
   public_html/presensi/public
   ```
5. Klik **[Save]** atau **[Submit]**.

---

### Langkah 2 — Upload dan Extract File

1. Di cPanel, buka **[File Manager]**.
2. Masuk ke `public_html/`.
3. Klik **[Upload]** di toolbar, pilih `presensi.zip` dari komputer kamu, dan tunggu progress bar-nya sampai 100%.
4. Balik ke File Manager (refresh kalau perlu), klik kanan `presensi.zip` → **[Extract]**.
5. Pastikan path **Extract To**-nya `/public_html/` lalu klik **[Extract Files]**.
6. Cek struktur hasilnya:
   ```
   public_html/presensi/
                        ├── app/
                        ├── public/
                        ├── database/
                        ├── .env.example
                        └── ...
   ```

---

### Langkah 3 — Buat Database dan User

1. Di cPanel, buka **[MySQL Databases]**.
2. Di bagian **Create New Database**, masukkan nama database (contoh: `presensi_db`) lalu klik **[Create Database]**.
3. Scroll ke **MySQL Users** → **Add New User**:
   - **Username:** contoh, `presensi_user`
   - **Password:** pakai password yang kuat
   - Klik **[Create User]**
     > **Penting:** Catat username, password, dan nama database-nya.
4. Scroll ke **Add User To Database**, pilih user dan database yang baru dibuat, lalu klik **[Add]**.
5. Di halaman privileges, centang **[ALL PRIVILEGES]** lalu klik **[Make Changes]**.

---

### Langkah 4 — Buat Skema Database

1. Buka direktori project lewat cPanel Terminal, atau konek via SSH.
2. Set nilai-nilai database di `.env`.
3. Jalankan migration dari root project:

   ```bash
   php spark migrate --all
   ```

4. Khusus environment development, tambahin data fiktif:

   ```bash
   php spark db:seed DatabaseSeeder
   ```

   Jangan import dump phpMyAdmin lokal atau jalanin demo seeder di production.

---

### Langkah 5 — Konfigurasi `.env`

1. Di File Manager, masuk ke `public_html/presensi/`.
2. Klik kanan `.env.example` → **[Copy]**, set tujuannya ke `/public_html/presensi/`, rename jadi `.env`, lalu klik **[Copy File(s)]**.
3. Klik kanan `.env` → **[Edit]** → konfirmasi dengan klik **[Edit]**.
4. Update nilai-nilai berikut:

   **Wajib diubah:**

   ```ini
   app.baseURL = 'https://domainkamu.com/'

   database.default.database = nama_database_kamu
   database.default.username = user_database_kamu
   database.default.password = password_database_kamu
   ```

   **Opsional (isi kalau fiturnya dipakai):**

   ```ini
   GROQ_API_KEYS = your_groq_api_key
   telegram.botToken = 'your_telegram_bot_token'
   telegram.chatId   = 'your_telegram_chat_id'
   ```

   **Biarkan default:**

   ```ini
   CI_ENVIRONMENT = production
   app.appTimezone = 'Asia/Makassar'
   GROQ_MODEL = moonshotai/kimi-k2-instruct
   database.default.hostname = localhost
   database.default.DBDriver = MySQLi
   database.default.port     = 3306
   ```

5. Klik **[Save Changes]**.

---

### Langkah 6 — Set Permission `writable/`

1. Di File Manager, masuk ke `public_html/presensi/`.
2. Klik kanan folder `writable` → **[Change Permissions]**.
3. Set nilai permission-nya ke `755`.
4. Centang **[Recurse into subdirectories]** dan pilih **[Apply to all]**.
5. Klik **[Change Permissions]**.

---

### Langkah 7 — Akses Sistemnya

1. Buka browser kamu.
2. Masuk ke: `https://domainkamu.com/`
3. PresenSI udah siap dipakai.

**Troubleshooting:**

| Gejala                | Yang Perlu Dicek                                                         |
| --------------------- | ------------------------------------------------------------------------ |
| Error HTTP 500        | Cek versi PHP di cPanel → **[Select PHP Version]** → minimal 8.1         |
| Ada fitur yang hilang | Aktifkan ekstensi yang dibutuhkan: `intl`, `mbstring`, `json`, `mysqlnd` |
| Error penulisan file  | Pastikan permission folder `writable/` udah diset ke `755`               |
| Error database        | Cek ulang semua nilai di `.env`, siapa tau ada typo                      |

---

## Opsi B — Index Forwarder _(Alternatif)_

Pakai opsi ini kalau paket hosting kamu nggak ngizinin ubah setting Document Root domain.

### Langkah 1–4 — Upload, Database, dan Import

Ikutin **Langkah 2, 3, dan 4** dari [Opsi A](#opsi-a--set-document-root-ke-public-direkomendasikan).

---

### Langkah 5 — Buat Index Forwarder

1. Di File Manager, masuk ke `public_html/`.
2. Kalau udah ada file `index.php`, klik kanan → **[Rename]** → ubah jadi `index.php.bak`.
3. Klik **[+ File]** di toolbar, namain `index.php`, lalu klik **[Create New File]**.
4. Klik kanan `index.php` yang baru → **[Edit]** → konfirmasi dengan klik **[Edit]**.
5. Paste kode berikut:

   ```php
   <?php
   define('FCPATH', __DIR__ . '/');
   chdir(__DIR__ . '/presensi/public');
   require __DIR__ . '/presensi/public/index.php';
   ```

6. Klik **[Save Changes]**.

---

### Langkah 6 — Edit `.htaccess`

1. Masih di `public_html/`.
2. Kalau belum ada file `.htaccess`, klik **[+ File]** → namain `.htaccess` → **[Create New File]**.
3. Klik kanan `.htaccess` → **[Edit]** → konfirmasi dengan klik **[Edit]**.
4. Tambahin ini di **bagian bawah** file (setelah konten yang udah ada):

   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ presensi/public/$1 [L]
   ```

5. Klik **[Save Changes]**.

---

### Langkah 7–8 — Konfigurasi `.env`, Permission, dan Akses

Ikutin **Langkah 5, 6, dan 7** dari [Opsi A](#opsi-a--set-document-root-ke-public-direkomendasikan).

---

## Catatan Tambahan

### Ekstensi PHP yang Dibutuhkan

Ekstensi PHP berikut harus aktif di server atau environment lokal kamu:

| Ekstensi   | Kegunaan                        |
| ---------- | ------------------------------- |
| `intl`     | Dukungan internasionalisasi     |
| `mbstring` | Penanganan string multibyte     |
| `json`     | Encoding/decoding JSON          |
| `mysqlnd`  | Driver native MySQL             |
| `curl`     | Integrasi Groq API dan Telegram |
| `zip`      | Fitur bulk download             |

Di shared hosting, aktifkan lewat cPanel → **[Select PHP Version]** → tab **[Extensions]**.
