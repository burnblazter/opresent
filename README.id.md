<div align="center">

<img src="public/assets/img/logo.png" alt="PresenSI Logo" width="120"/>

# PresenSI

### _Si Pintar Urusan Presensi_

**Sistem manajemen presensi dengan autentikasi 3 faktor, pengenalan wajah client-side, geofencing GPS, dan asisten AI yang bisa diajak tanya jawab pakai bahasa natural.**

**Bahasa:** [English](README.md) | [Bahasa Indonesia](README.id.md)

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-EF4223?style=flat-square&logo=codeigniter)](https://codeigniter.com)
[![TensorFlow.js](https://img.shields.io/badge/TensorFlow.js-Human.js-FF6F00?style=flat-square&logo=tensorflow)](https://github.com/vladmandic/human)
[![License](https://img.shields.io/badge/License-GPL--3.0-blue?style=flat-square)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production-success?style=flat-square)]()

[Fitur](#-fitur-utama) •
[Arsitektur](#-arsitektur-sistem) •
[Instalasi](#-instalasi) •
[Konfigurasi](#-konfigurasi) •
[Penggunaan](#-penggunaan) •
[Testing](#-testing--hasil) •
[API](#-api-reference)

---

> Fork dari [`o-present`](https://github.com/josephines1/o-present) buatan Josephine,
> lalu dibangun ulang dengan arsitektur, model keamanan, integrasi AI, dan fitur yang beda sama sekali.
>
> **Sudah jalan production di SMA Negeri 1 Balikpapan, dipakai 1.000+ siswa aktif.**

</div>

---

## 📖 Latar Belakang

Absen kertas sama fingerprint scanner sebenarnya punya masalah yang sama: gampang
diakalin. Titip absen itu hal yang sangat mudah dilakukan, nggak ada pengecekan lokasi,
dan rekapnya harus direkonsiliasi manual setelahnya.

Jawaban PresenSI untuk masalah ini adalah mewajibkan **tiga faktor independen untuk
lolos secara bersamaan** sebelum sebuah record presensi diterima:

| Faktor    | Yang Diverifikasi                               | Teknologi                       |
| --------- | ----------------------------------------------- | ------------------------------- |
| **WHO**   | Identitas biometrik lewat face recognition      | Human.js + TensorFlow.js (WASM) |
| **WHERE** | Keberadaan fisik di area sekolah                | GPS + Haversine geofencing      |
| **WHEN**  | Timestamp yang disinkronkan server, anti-tamper | Koreksi `timeDiff` server       |

Ketiganya pakai logika AND, jadi ngakalin satu faktor aja nggak akan berguna. Foto
cetak gagal di liveness detection. Di luar area sekolah bikin faktor GPS gagal.
Ubah jam di device bakal dikoreksi sama `timeDiff` sebelum sempat masuk ke database.

---

## ✨ Fitur Utama

### 🔐 Multi-Factor Authentication Engine

- **Logika AND 3 faktor**, semua pengecekan harus lolos, nggak ada jalan pintas
- **Active liveness detection** dengan tantangan gerakan kepala acak (tunduk/dongak,
  menoleh kiri/kanan), divalidasi lewat `face.rotation.pitch` dan `face.rotation.yaw`
- **Face recognition** pakai embedding 1024 dimensi dari Human.js
  (berbasis MobileNetV2), threshold cosine similarity di angka 0.62
- **GPS geofencing** lewat formula Haversine dengan radius yang bisa diatur per
  lokasi, dipantau terus-menerus lewat `watchPosition()`
- **Sinkronisasi waktu server**, `timeDiff = ServerTime − ClientTime` dihitung saat
  halaman dimuat, semua timestamp setelahnya pakai nilai yang sudah dikoreksi jadi
  jam di device nggak bisa dipalsukan

### 🤖 Asisten AI ("Si Pintar")

- Query data presensi pakai bahasa natural, dalam Bahasa Indonesia
- **Pipeline LLM dua tahap**: tahap pertama ubah pertanyaan jadi SQL, tahap kedua
  ubah hasil SQL-nya jadi jawaban yang enak dibaca
- Jalan di atas **Groq** (inference LPU, latensi rendah) dengan API key pooling
  biar nggak mentok rate limit
- Ada regex sanitizer yang blokir apa pun selain `SELECT` (`INSERT`, `UPDATE`,
  `DELETE`, `DROP`, dll langsung ditolak sebelum dieksekusi)
- Isolasi data per user, siswa cuma bisa query data miliknya sendiri

### 🏢 Kiosk Mode

- SPA fullscreen untuk terminal presensi bersama
- **Alur 3 state**: scan barcode → verifikasi wajah → berhasil/gagal
- **Jendela idempotency 30 detik** supaya nggak ada entry duplikat dari scan berulang
- Token CSRF di-refresh tiap respons AJAX, nggak perlu reload halaman
- Ada fallback QR code kalau face recognition gagal

### 🛡️ Privacy-First Biometrics

- **Nggak ada gambar yang dikirim ke server**, cuma embedding hasilnya aja
- Setiap wajah disimpan sebagai vektor 1024 float, nggak lebih dari itu
- Embeddingnya satu arah, nggak bisa direkonstruksi balik jadi wajah
- Semua inferensi jalan di sisi client lewat WebAssembly

### 📊 Manajemen & Pelaporan

- Dashboard real-time yang mencakup hadir, absen, sakit/izin, dan terlambat
- Import/export Excel lewat PhpSpreadsheet, XLSX multi-sheet per unit
- Manajemen ketidakhadiran dengan alur approval, upload sertifikat PDF, dan
  validasi overlap tanggal
- Kalender libur yang disinkron dari API eksternal (`libur.deno.dev`) dengan
  override manual
- File manager bawaan dengan download ZIP massal dan auto-cleanup
- Cetak barcode Code 128 untuk identifikasi kiosk

### 📢 Integrasi

- **Telegram Bot API** untuk notifikasi push real-time ke grup orang tua
- **Groq API** untuk inferensi LLM Si Pintar
- **OpenStreetMap + Leaflet.js** untuk peta manajemen lokasi
- **Nominatim** untuk pencarian koordinat saat setup lokasi

### 🔒 Keamanan

- Password diproses lewat **SHA-384 pre-hash → Argon2id KDF**, double hashing
- **RBAC** dengan lima role: Admin, Head, Pegawai/Siswa, Kiosk, Helper
- Proteksi CSRF di setiap form POST
- Sanitasi input pakai HTMLPurifier dan Laminas Escaper
- Token dengan batas waktu untuk reset password (24 jam) dan ganti email (5 menit)
- **Zero CDN dependency**, semua library JS di-host sendiri jadi nggak ada risiko
  dari script pihak ketiga

---

## 🏛️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────┐
│                  CLIENT LAYER (Browser)                 │
│                                                         │
│  Human.js     Leaflet.js   QuaggaJS    Vanilla JS       │
│  (WASM/WebGL) (OSM Maps)  (Barcode)   (ES6+, PWA)      │
│                                                         │
│          HTTPS / AJAX (JSON + FormData)                 │
│   [face embeddings, coordinates, tokens — NO images]    │
└─────────────────┬───────────────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────────────┐
│         APPLICATION LAYER (Shared Hosting / cPanel)     │
│                                                         │
│   Controllers          Models           Views           │
│   (Routing, Filters)   (Query Builder,  (Tabler UI      │
│                         Business Logic)  Templates)     │
│                                                         │
│   Services & Libraries: MythAuth | CI4 | PhpSpreadsheet │
│                         HTMLPurifier | Laminas Escaper  │
└──────────┬──────────────────┬──────────────────┬────────┘
           │                  │                  │
    ┌──────▼──────┐  ┌────────▼──────┐  ┌───────▼────────┐
    │  MySQL /    │  │  SMTP Server  │  │  External APIs │
    │  MariaDB    │  │  (Email)      │  │  Groq, Telegram│
    │             │  │               │  │  libur.deno.dev│
    └─────────────┘  └───────────────┘  └────────────────┘
```

### Detail Layer MVC

| Layer           | Tanggung Jawab                                                               |
| --------------- | ---------------------------------------------------------------------------- |
| **Controllers** | Routing request, validasi input, enforcement filter RBAC, orkestrasi service |
| **Models**      | Interaksi database lewat CI4 Query Builder, aturan validasi, soft delete     |
| **Views**       | Template Tabler UI (Bootstrap 5), nol business logic                         |
| **Filters**     | `AuthFilter` (cek sesi) + `RoleFilter` (middleware RBAC)                     |

---

## 🗄️ Skema Database (Gambaran Domain)

```
DOMAIN 1: Authentication          DOMAIN 3: Biometrics
  users                             face_descriptors
  auth_groups                       face_descriptors_request
  auth_groups_users
  auth_logins                     DOMAIN 4: Transactional
                                    presensi
DOMAIN 2: Master Data               ketidakhadiran
  pegawai                           hari_libur
  jabatan (Units/Classes)
  lokasi_presensi
```

Beberapa keputusan desain yang perlu dicatat:

- `users.password_hash`, SHA-384 pre-hash + Argon2id, nggak pernah plain Bcrypt
- `face_descriptors.descriptor` tipenya `MEDIUMTEXT` yang nyimpen array JSON 1024 float
- `presensi` nyimpen check-in dan check-out sekaligus, lengkap dengan referensi path foto
- `lokasi_presensi` nyimpen `latitude`, `longitude`, `radius`, `timezone`,
  `jam_masuk`, `jam_keluar` per lokasi
- Semua tabel pakai soft delete (`deleted_at`) biar jejak audit tetap terjaga

---

## ⚙️ Tech Stack

### Backend

| Komponen    | Teknologi                                                 |
| ----------- | --------------------------------------------------------- |
| Bahasa      | PHP 8.1+                                                  |
| Framework   | CodeIgniter 4 (MVC, PSR-4)                                |
| Database    | MySQL 5.7+ / MariaDB                                      |
| Autentikasi | MythAuth (fork yang di-internalize dan dibenerin bug-nya) |
| Spreadsheet | PhpSpreadsheet (export/import XLSX)                       |

### Frontend

| Komponen     | Teknologi                                     |
| ------------ | --------------------------------------------- |
| UI Framework | Tabler UI (Bootstrap 5.3)                     |
| Face AI      | Human.js v3.3.6 (TensorFlow.js + WASM/WebGL)  |
| Maps         | Leaflet.js + OpenStreetMap/CartoCDN           |
| Barcode      | QuaggaJS (Code 128, QR)                       |
| Image Crop   | Cropper.js (rasio 1:1 untuk enrollment wajah) |
| Rich Text    | TinyMCE                                       |
| UX           | SweetAlert2, Select2, Flatpickr, DarkReader   |

### External API

| Layanan          | Kegunaan                                   |
| ---------------- | ------------------------------------------ |
| Groq API         | Inferensi LLM untuk asisten AI Si Pintar   |
| Telegram Bot API | Notifikasi push presensi secara real-time  |
| libur.deno.dev   | Sinkronisasi hari libur nasional Indonesia |
| Nominatim        | Geocoding untuk pencarian koordinat lokasi |

---

## 📋 Prasyarat

- **PHP** 8.1 ke atas (disarankan 8.2+)
- **Composer** 2.x
- **MySQL** 5.7+ atau **MariaDB** 10.6+
- **Web Server**: Apache 2.4+ (dengan `mod_rewrite`) atau Nginx
- **Ekstensi PHP**: `intl`, `mbstring`, `json`, `mysqlnd`, `curl`, `zip`
- **Browser**: browser modern berbasis Chromium/WebKit/Gecko apa pun yang support
  WebAssembly (Chrome 57+, Firefox, Safari, Edge; Internet Explorer **nggak** didukung)

---

## 🚀 Instalasi

Lihat [INSTALL.md](INSTALL.md) untuk panduan lengkapnya.
Versi terjemahan Bahasa Indonesia ada di [README.id.md](README.id.md) dan [INSTALL.id.md](INSTALL.id.md).

### Ringkasan setup database

Migration CodeIgniter 4 jadi sumber kebenaran untuk skema. Buat database MySQL
atau MariaDB kosong, set koneksinya di `.env`, lalu jalankan:

```bash
php spark migrate --all
php spark db:seed DatabaseSeeder
```

`--all` sudah termasuk migration MythAuth yang di-internalize. `DatabaseSeeder`
cuma bikin data development fiktif, nggak ada face descriptor, foto, dokumen
upload, token, atau record production yang dibuat dari situ. Jangan import dump
phpMyAdmin lokal ke repository ini.

## ⚙️ Konfigurasi

Semuanya diatur lewat file `.env`.

### Core Application

| Variabel          | Deskripsi                         | Contoh                          |
| ----------------- | --------------------------------- | ------------------------------- |
| `CI_ENVIRONMENT`  | `development` atau `production`   | `production`                    |
| `app.baseURL`     | URL lengkap dengan trailing slash | `https://presensi.example.com/` |
| `app.appTimezone` | Timezone PHP untuk waktu server   | `Asia/Makassar`                 |

### Database

| Variabel                    | Deskripsi         |
| --------------------------- | ----------------- |
| `database.default.hostname` | Host database     |
| `database.default.database` | Nama database     |
| `database.default.username` | User database     |
| `database.default.password` | Password database |

### Asisten AI (Si Pintar)

| Variabel        | Deskripsi                                 | Contoh                        |
| --------------- | ----------------------------------------- | ----------------------------- |
| `GROQ_API_KEY`  | API key Groq utama                        | `gsk_abc123...`               |
| `GROQ_API_KEYS` | Beberapa key untuk pooling (dipisah koma) | `key1,key2,key3`              |
| `GROQ_MODEL`    | Model LLM yang dipakai                    | `moonshotai/kimi-k2-instruct` |

> **Tips:** isi beberapa key di `GROQ_API_KEYS` bikin rotasi acak jalan otomatis
> per request, jadi beban kesebar dan rate limit efektifnya naik.

### Notifikasi Telegram

| Variabel            | Deskripsi                                       |
| ------------------- | ----------------------------------------------- |
| `telegram.botToken` | Token dari [@BotFather](https://t.me/BotFather) |
| `telegram.chatId`   | ID grup/channel tujuan (negatif untuk grup)     |

---

## 📁 Struktur Project

```
PresenSI/
├── app/
│   ├── Controllers/
│   │   ├── Admin.php                  # Dashboard admin & config
│   │   ├── Presensi.php               # Pemrosesan check-in/out MFA
│   │   ├── Pegawai.php                # Master data pegawai/siswa
│   │   ├── Kiosk.php                  # Terminal presensi bersama
│   │   ├── FaceEnrollmentAdmin.php    # Manajemen face descriptor oleh admin
│   │   ├── FaceEnrollmentRequest.php  # Permintaan self-enrollment siswa
│   │   ├── Ketidakhadiran.php         # Manajemen absen/izin
│   │   └── Auth/                      # Login, register, reset password
│   ├── Models/
│   │   ├── UsersModel.php
│   │   ├── PresensiModel.php
│   │   ├── PegawaiModel.php
│   │   ├── FaceDescriptorModel.php
│   │   └── KetidakhadiranModel.php
│   ├── Views/
│   │   ├── admin/                     # Template dashboard admin
│   │   ├── kiosk/                     # Interface kiosk fullscreen
│   │   ├── auth/                      # Form login/register
│   │   └── components/                # Komponen UI yang dipakai ulang
│   ├── Filters/
│   │   ├── AuthFilter.php             # Cek autentikasi sesi
│   │   └── RoleFilter.php             # Middleware RBAC
│   ├── Helpers/
│   │   └── telegram_helper.php        # Helper notifikasi Telegram
│   └── Libraries/
│       └── MythAuth/                  # Library auth yang di-internalize
│
├── public/
│   ├── assets/
│   │   ├── js/
│   │   │   ├── human.js               # Face recognition AI (~1.5MB, self-hosted)
│   │   │   ├── quagga.min.js          # Scanner barcode
│   │   │   └── leaflet.min.js         # Peta interaktif
│   │   └── models/                    # Binary model TensorFlow.js (.bin/.json)
│   └── uploads/
│       ├── presensi/                  # Bukti foto check-in/out
│       ├── faces/                     # Gambar training enrollment wajah
│       └── surat/                     # PDF sertifikat ketidakhadiran
│
├── database/
│   ├── Migrations/                    # Version control skema
│   └── Seeds/                         # Seeder data awal
│
├── writable/                          # Cache, log, sesi (harus writable)
├── tests/                             # File test PHPUnit
├── composer.json
├── .env.example                       # Template environment
└── spark                              # CLI CodeIgniter
```

---

## 🎯 Penggunaan

### Role User

| Role              | Level Akses           | Use Case Utama                                           |
| ----------------- | --------------------- | -------------------------------------------------------- |
| **Admin**         | Akses penuh sistem    | Konfigurasi sistem, manajemen user, semua laporan        |
| **Head**          | Akses baca + approval | Monitoring, analitik, approval izin                      |
| **Pegawai/Siswa** | Akses personal        | Presensi harian, pengajuan izin, laporan pribadi         |
| **Kiosk**         | Akses hanya terminal  | Operasional terminal presensi bersama                    |
| **Helper**        | Admin terbatas        | Manajemen data user saja, nggak ada akses modul sensitif |

---

### Alur Presensi (Pegawai/Siswa)

```
Buka /presensi/masuk
        │
        ▼
┌─ Faktor 1: GPS ──────────────────────────────────────────┐
│  watchPosition() → hitung jarak pakai Haversine           │
│  ✅ Masih dalam radius → UI hijau, lanjut                 │
│  ❌ Di luar radius → UI merah, tombol terkunci             │
└──────────────────────────────────────────────────────────┘
        │ (lolos)
        ▼
┌─ Faktor 2: Liveness Detection ───────────────────────────┐
│  Human.js dimuat → tantangan acak ditampilkan              │
│  contoh: "Dongakkan Kepala" → user dongak                  │
│  Divalidasi lewat face.rotation.pitch / yaw                │
│  Progress bar jalan → semua tantangan selesai               │
└──────────────────────────────────────────────────────────┘
        │ (lolos)
        ▼
┌─ Faktor 3: Face Recognition ─────────────────────────────┐
│  Ekstrak embedding 1024 dimensi dari frame kamera           │
│  Cosine similarity dibandingkan ke semua descriptor         │
│  similarity ≥ 0.62 → identitas terkonfirmasi                │
│  Countdown 3 detik → snapshot otomatis diambil              │
└──────────────────────────────────────────────────────────┘
        │ (semua faktor lolos)
        ▼
POST ke server:
  - face_embedding (BUKAN gambarnya)
  - koordinat GPS
  - timestamp = ClientTime + timeDiff  ← dikoreksi server
        │
        ▼
Server validasi ulang semua faktor → record disimpan
        │
        ▼
Dashboard update + "Fun Fact" dari AI (estimasi usia/emosi)
```

---

### Enrollment Wajah

Siswa daftar wajahnya sendiri:

1. Buka **Profile → Daftar Wajah**
2. Ambil foto lewat webcam, atau upload
3. Crop ke rasio 1:1 pakai Cropper.js
4. Submit permintaan enrollment (dibatasi maksimal 3 permintaan/hari)
5. Admin approve → descriptor langsung aktif buat presensi

Admin juga bisa kelola descriptor langsung, di **Pengguna → Face Descriptor**.

---

### Asisten AI (Si Pintar)

Klik widget AI yang mengambang di halaman dashboard mana pun. Contoh query:

```
"Siapa saja yang alpha hari ini?"
→ Menampilkan semua siswa yang absen tanpa keterangan hari ini

"Rekap kehadiran kelas XII-10 bulan ini"
→ Ringkasan per siswa: hadir, absen, sakit, terlambat

"Berapa kali saya terlambat bulan Februari?"
→ Hitungan keterlambatan pribadi (siswa cuma bisa lihat data sendiri)

"Tampilkan tren kehadiran minggu ini"
→ Narasi tren kehadiran lengkap dengan datanya
```

> **Catatan keamanan:** hal-hal kayak `DROP TABLE`, `DELETE`, atau coba ambil data
> user lain bakal diblokir sama regex sanitizer dan layer isolasi per-user.

---

### Kiosk Mode

Dibuat buat terminal gerbang sekolah yang dioperasikan satu orang staf:

1. Login pakai akun role **Kiosk**, langsung masuk ke SPA fullscreen
2. **State 1 (Scanner)**: siswa scan badge Code 128 miliknya
3. **State 2 (Verifikasi)**: face recognition jalan otomatis, nggak perlu tekan tombol
4. **State 3 Berhasil**: record tersimpan, notifikasi Telegram terkirim, balik ke State 1
5. **State 3 Gagal**: fallback QR code ditampilkan untuk verifikasi manual

Jendela idempotency 30 detik mencegah record ganda kalau badge yang sama ke-scan
dua kali nggak sengaja.

---

## 🧪 Testing & Hasil

### Ringkasan Functional Test

Semua 50 skenario test lolos di 5 modul:

| Modul          | Skenario | Hasil          |
| -------------- | -------- | -------------- |
| Autentikasi    | 11       | ✅ Semua Lolos |
| Presensi MFA   | 12       | ✅ Semua Lolos |
| Kiosk Mode     | 8        | ✅ Semua Lolos |
| Manajemen Data | 12       | ✅ Semua Lolos |
| AI Si Pintar   | 7        | ✅ Semua Lolos |

### Validasi Keamanan Penting

| Vektor Serangan                | Test                               | Hasil                                       |
| ------------------------------ | ---------------------------------- | ------------------------------------------- |
| Spoofing foto cetak            | Pasang foto A4 ke kamera           | ✅ Diblokir liveness (nggak ada gerakan 3D) |
| Serangan video replay          | Pasang video dari HP ke kamera     | ✅ Diblokir liveness detection              |
| Manipulasi jam device          | Ubah jam device 1 jam              | ✅ `timeDiff` koreksi ke waktu server       |
| SQL injection lewat AI         | `DROP TABLE presensi`              | ✅ Ditolak regex sanitizer                  |
| Akses data cross-user lewat AI | Query data siswa lain              | ✅ Isolasi per-user ditegakkan              |
| Scan kiosk duplikat            | Scan badge sama dua kali dalam 10s | ✅ Jendela idempotency blokir duplikat      |

### Performa Face Recognition

| Kondisi                               | Akurasi Kualitatif | Catatan                                    |
| ------------------------------------- | ------------------ | ------------------------------------------ |
| Pencahayaan indoor normal             | Sangat Tinggi      | Similarity konsisten > 0.75                |
| Cahaya minim                          | Bagus              | Butuh pencahayaan ambient minimum          |
| Backlight kuat                        | Bagus              | Sedikit degradasi, tetap reliable          |
| Pakai kacamata (sama saat enrollment) | Bagus              | Recognition tetap stabil                   |
| Perubahan gaya rambut signifikan      | Bagus              | Embedding fokus ke fitur wajah inti        |
| Serangan foto cetak                   | Diblokir           | Liveness detection mencegah ini sepenuhnya |
| Sudut wajah >45°                      | Rendah             | Human.js butuh orientasi wajah frontal     |

### Kompatibilitas Browser

| Browser              | WebAssembly | Face Recognition | Status         |
| -------------------- | ----------- | ---------------- | -------------- |
| Chrome (57+)         | ✅          | ✅               | Didukung Penuh |
| Firefox (Terbaru)    | ✅          | ✅               | Didukung Penuh |
| Safari (Terbaru)     | ✅          | ✅               | Didukung Penuh |
| Edge (Terbaru)       | ✅          | ✅               | Didukung Penuh |
| Chrome Mobile        | ✅          | ✅               | Didukung Penuh |
| Safari Mobile (iOS)  | ✅          | ✅               | Didukung Penuh |
| Internet Explorer 11 | ❌          | ❌               | Tidak Didukung |
| Chrome < 57          | ❌          | ❌               | Tidak Didukung |

> Browser lawas memang sengaja nggak didukung, WebAssembly itu syarat wajib buat
> menjalankan inferensi Human.js.

### Performa Inferensi Client-Side

| Kelas Device               | Waktu Inferensi per Frame   |
| -------------------------- | --------------------------- |
| High-end (2022+)           | < 200 ms                    |
| Mid-range (2020–2022)      | 200–400 ms                  |
| Entry-level (sebelum 2020) | > 500 ms (tetap fungsional) |

Loading model biasanya 5 sampai 10 detik di load pertama tergantung jaringan,
dan di bawah 1 detik kalau udah ke-cache di `localStorage`.

---

## 📡 API Reference

PresenSI pada dasarnya web app MVC, tapi ada beberapa endpoint AJAX internal
yang diekspos. Yang paling relevan dari luar adalah endpoint AI chat.

### AI Chat

```
POST /chat
Content-Type: application/json
```

**Request:**

```json
{
  "message": "Siapa saja yang terlambat hari ini?",
  "history": "[{\"role\":\"user\",\"content\":\"Halo\"}]"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Berikut daftar pegawai yang terlambat hari ini:\n- Budi (XII-10)\n- Siti (XII-7)",
  "history_user": "Siapa saja yang terlambat hari ini?",
  "history_assistant": "Berikut daftar pegawai..."
}
```

### Endpoint Internal Lainnya

| Endpoint                | Method   | Deskripsi                         |
| ----------------------- | -------- | --------------------------------- |
| `/presensi/masuk`       | GET/POST | Check-in dengan MFA               |
| `/presensi/keluar`      | GET/POST | Check-out dengan MFA              |
| `/kiosk/cariPegawai`    | POST     | Pencarian barcode (Kiosk)         |
| `/kiosk/prosesPresensi` | POST     | Submission presensi kiosk         |
| `/wajah/request`        | POST     | Permintaan enrollment wajah       |
| `/api/server-time`      | GET      | Timestamp server untuk `timeDiff` |

---

## 🔧 Pipeline LLM Dua Tahap (Technical Deep Dive)

Si Pintar memproses tiap query lewat dua panggilan LLM berurutan:

````
Query user (Bahasa Indonesia)
         │
         ▼
┌─ Tahap 1: Text-to-SQL ──────────────────────────┐
│  Konteks sistem: skema DB + info tabel           │
│  Model generate: QUERY: ```sql SELECT ...```     │
│                                                  │
│  Pengecekan Regex Sanitizer:                     │
│  ✅ Diizinkan: SELECT saja                       │
│  ❌ Diblokir: INSERT, UPDATE, DELETE, DROP, dll  │
│  ✅ Limit: maksimal 30 baris                     │
└───────────────────────────────────────────────────┘
         │ (SQL diekstrak + dieksekusi ke DB)
         ▼
    Hasil mentah JSON dari database
         │
         ▼
┌─ Tahap 2: SQL-to-Natural Language ──────────────┐
│  Konteks sistem: "Jawab dalam Bahasa Indonesia"  │
│  Input: hasil query mentah berupa JSON           │
│  Output: respons naratif + data terformat        │
│  Markdown → DOMPurify → HTML yang dirender       │
└───────────────────────────────────────────────────┘
         │
         ▼
Bubble chat mengambang menampilkan respons
````

---

## 🛡️ Arsitektur Keamanan

### Keamanan Password

```
Password user → SHA-384(password) → base64_encode → Argon2id(hasil)
```

Pre-hashing SHA-384 ini jadi solusi untuk batas input 72 karakter di Bcrypt/Argon2,
jadi password panjang direduksi dulu jadi digest dengan panjang tetap sebelum
diproses KDF.

### Integritas Waktu

```javascript
// Dihitung sekali saat halaman dimuat
const timeDiff = serverTime - clientTime

// Semua timestamp pakai waktu yang sudah dikoreksi
const presenceTimestamp = Date.now() + timeDiff
// → Kebal terhadap manipulasi jam device
```

### Privasi Biometrik

- Gambar wajah diproses sepenuhnya di browser (Human.js + WASM)
- Cuma vektor embedding 1024 float hasilnya yang dikirim ke server
- Embeddingnya satu arah secara matematis, nggak bisa balik jadi wajah lagi
- Akses data wajah dibatasi hanya untuk pemiliknya dan role admin

---

## 🤝 Berkontribusi

Kontribusi sangat welcome. Alurnya kurang lebih begini:

1. Fork repository ini
2. Buat feature branch (`git checkout -b feature/fitur-kamu`)
3. Commit perubahannya (`git commit -m 'Tambah fitur X'`)
4. Push ke branch itu (`git push origin feature/fitur-kamu`)
5. Buka Pull Request

Usahakan kode tetap konsisten sama konvensi CodeIgniter 4 yang sudah ada, dan
sertakan test case untuk fungsionalitas baru.

---

## 📜 Lisensi

Dilisensikan di bawah **GNU General Public License v3.0 (GPL-3.0)**.

Ini lisensi copyleft yang kuat, artinya memakai lisensi ini berarti modifikasi
atau karya turunan yang lebih besar yang memakainya juga wajib menyediakan source
code lengkapnya di bawah lisensi yang sama.

Lihat file [LICENSE](LICENSE) untuk detail lengkapnya.

---

## 🙏 Ucapan Terima Kasih

- **[o-present](https://github.com/josephines1/o-present)** buatan Josephine, project
  open source awal yang jadi fondasi sebelum dibangun ulang secara ekstensif
- **[vladmandic/human](https://github.com/vladmandic/human)**, library yang bikin
  biometrik browser-native kayak gini bisa kejadian
- **[Groq](https://groq.com)** untuk inferensi LLM latensi rendah di balik Si Pintar
- **[Tabler UI](https://tabler.io)** untuk template dashboard berbasis Bootstrap 5
- **SMA Negeri 1 Balikpapan**, atas kesempatannya buat jalanin ini di skala nyata
  dengan 1.000+ pengguna asli

---

<div align="center">

Dibuat dengan ❤️ di Indonesia

_"Si Pintar Urusan Presensi"_

</div>
