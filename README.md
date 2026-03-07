<div align="center">

<img src="public/assets/img/logo.png" alt="PresenSI Logo" width="120"/>

# PresenSI

### _Si Pintar Urusan Presensi_

**An intelligent, enterprise-grade attendance management system featuring
3-Factor Multi-Factor Authentication, client-side AI biometrics,
GPS geofencing, and a natural language AI assistant.**

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-EF4223?style=flat-square&logo=codeigniter)](https://codeigniter.com)
[![TensorFlow.js](https://img.shields.io/badge/TensorFlow.js-Human.js-FF6F00?style=flat-square&logo=tensorflow)](https://github.com/vladmandic/human)
[![License](https://img.shields.io/badge/License-GPL--3.0-blue?style=flat-square)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production-success?style=flat-square)]()

[Features](#-key-features) •
[Architecture](#-system-architecture) •
[Installation](#-installation) •
[Configuration](#-configuration) •
[Usage](#-usage) •
[Testing](#-testing--results) •
[API](#-api-reference)

---

> Originally forked from [`o-present`](https://github.com/josephines1/o-present) by Josephine,
> PresenSI has been extensively re-engineered with a new architecture, security model,
> AI integration, and feature set — making it a fundamentally different system.
>
> **Deployed at SMA Negeri 1 Balikpapan serving 1,000+ active students.**

</div>

---

## 📖 Background

Traditional attendance systems — paper roll calls and fingerprint scanners — are
fundamentally vulnerable to proxy attendance ("titip absen"), provide no location
verification, and create significant administrative overhead.

PresenSI solves this by requiring **three independent factors to be satisfied
simultaneously** before any attendance record is accepted:

| Factor    | What it verifies                           | Technology                      |
| --------- | ------------------------------------------ | ------------------------------- |
| **WHO**   | Biometric identity via face recognition    | Human.js + TensorFlow.js (WASM) |
| **WHERE** | Physical presence within school grounds    | GPS + Haversine geofencing      |
| **WHEN**  | Tamper-proof server-synchronized timestamp | Server `timeDiff` correction    |

Because all three factors use **AND logic**, compromising one factor is never
sufficient. A photo cannot pass liveness detection. Being off-campus blocks the GPS
factor. Changing your device clock is corrected by `timeDiff`.

---

## ✨ Key Features

### 🔐 Multi-Factor Authentication Engine

- **3-Factor AND logic** — all factors must pass simultaneously; no bypasses
- **Active Liveness Detection** — randomized head movement challenges (tilt up/down,
  turn left/right) validated via `face.rotation.pitch` and `face.rotation.yaw`
- **Face Recognition** — 1024-dimensional face embeddings via Human.js
  (MobileNetV2-based), cosine similarity threshold ≥ 0.62
- **GPS Geofencing** — Haversine formula with configurable radius per location,
  continuous `watchPosition()` monitoring
- **Server Time Synchronization** — `timeDiff = ServerTime − ClientTime` injected
  at page load; all timestamps use corrected time, immune to device clock manipulation

### 🤖 AI Assistant ("Si Pintar")

- Natural language interface for attendance data queries in **Bahasa Indonesia**
- **Two-Pass LLM Pipeline**: Pass 1 converts natural language → SQL;
  Pass 2 narrates SQL results → human-readable response
- Powered by **Groq API** (low-latency LPU inference) with **API key pooling**
  for rate limit resilience
- **Regex sanitizer** blocks DML queries (`INSERT`, `UPDATE`, `DELETE`, `DROP`)
- **Per-user data isolation** — students only see their own records

### 🏢 Kiosk Mode

- Fullscreen Single Page Application for shared attendance terminals
- **3-state workflow**: Barcode scan → Face verification → Success/Failure
- **30-second idempotency window** prevents duplicate entries from repeated scans
- **Dynamic CSRF tokens** refreshed per AJAX response — no page reload needed
- QR code fallback for users who fail face recognition

### 🛡️ Privacy-First Biometrics

- **Zero image upload** — facial images are never transmitted to the server
- Only the mathematical face embedding vector (1024 floats) is stored
- Embeddings cannot be used to reconstruct the original face
- All AI inference runs entirely **client-side** via WebAssembly

### 📊 Management & Reporting

- **Real-time dashboard** with attendance statistics (present, absent, sick/leave, late)
- **Excel import/export** via PhpSpreadsheet with per-unit multi-sheet XLSX
- **Absence management** with approval workflow, PDF certificate upload, date
  overlap validation
- **Holiday management** with external API sync (`libur.deno.dev`) + manual override
- **Built-in File Manager** with bulk ZIP download and auto-cleanup
- **Barcode 128 printing** for Kiosk Mode identification

### 📢 Integrations

- **Telegram Bot API** — real-time push notifications to parent groups on
  successful attendance
- **Groq API** — ultra-low-latency LLM inference for Si Pintar
- **OpenStreetMap + Leaflet.js** — interactive maps for location management
- **Nominatim** — coordinate search for location setup

### 🔒 Security

- **Double password hashing**: SHA-384 pre-hash → Argon2id KDF
- **Role-Based Access Control (RBAC)**: Admin, Head, Pegawai/Siswa, Kiosk, Helper
- **CSRF protection** on all POST forms
- **HTMLPurifier + Laminas Escaper** input sanitization
- **Time-limited tokens** for password reset (24h) and email change (5 min)
- **Zero CDN dependency** — all JS libraries self-hosted, no external dependency risk

---

## 🏛️ System Architecture

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

### MVC Layer Details

| Layer           | Responsibility                                                                    |
| --------------- | --------------------------------------------------------------------------------- |
| **Controllers** | Request routing, input validation, RBAC filter enforcement, service orchestration |
| **Models**      | Database interaction via CI4 Query Builder, validation rules, soft delete         |
| **Views**       | Tabler UI (Bootstrap 5) templates, zero business logic                            |
| **Filters**     | `AuthFilter` (session check) + `RoleFilter` (RBAC middleware)                     |

---

## 🗄️ Database Schema (Domain Overview)

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

Key design decisions:

- `users.password_hash` — SHA-384 pre-hash + Argon2id (never plain Bcrypt)
- `face_descriptors.descriptor` — `MEDIUMTEXT` storing JSON array of 1024 floats
- `presensi` — stores both check-in and check-out with photo path references
- `lokasi_presensi` — configurable `latitude`, `longitude`, `radius`, `timezone`,
  `jam_masuk`, `jam_keluar` per location
- All tables use soft deletes (`deleted_at`) for audit trail preservation

---

## ⚙️ Tech Stack

### Backend

| Component      | Technology                              |
| -------------- | --------------------------------------- |
| Language       | PHP 8.1+                                |
| Framework      | CodeIgniter 4 (MVC, PSR-4)              |
| Database       | MySQL 5.7+ / MariaDB                    |
| Authentication | MythAuth (internalized, bug-fixed fork) |
| Spreadsheet    | PhpSpreadsheet (XLSX export/import)     |

### Frontend

| Component    | Technology                                   |
| ------------ | -------------------------------------------- |
| UI Framework | Tabler UI (Bootstrap 5.3)                    |
| Face AI      | Human.js v3.3.6 (TensorFlow.js + WASM/WebGL) |
| Maps         | Leaflet.js + OpenStreetMap/CartoCDN          |
| Barcode      | QuaggaJS (Code 128, QR)                      |
| Image Crop   | Cropper.js (1:1 ratio for face enrollment)   |
| Rich Text    | TinyMCE                                      |
| UX           | SweetAlert2, Select2, Flatpickr, DarkReader  |

### External APIs

| Service          | Purpose                                     |
| ---------------- | ------------------------------------------- |
| Groq API         | LLM inference for Si Pintar AI assistant    |
| Telegram Bot API | Real-time attendance push notifications     |
| libur.deno.dev   | Indonesian national holiday synchronization |
| Nominatim        | Geocoding for location coordinate search    |

---

## 📋 Prerequisites

- **PHP** 8.1 or higher (8.2+ recommended)
- **Composer** 2.x
- **MySQL** 5.7+ or **MariaDB** 10.6+
- **Web Server**: Apache 2.4+ (with `mod_rewrite`) or Nginx
- **PHP Extensions**: `intl`, `mbstring`, `json`, `mysqlnd`, `curl`, `zip`
- **Browser**: Any modern Chromium/WebKit/Gecko browser with WebAssembly support
  (Chrome 57+, Firefox, Safari, Edge — Internet Explorer is **not** supported)

---

## 🚀 Installation

See the [INSTALL.md](INSTALL.md) file for full details.

## ⚙️ Configuration

All configuration is managed through the `.env` file.

### Core Application

| Variable          | Description                   | Example                         |
| ----------------- | ----------------------------- | ------------------------------- |
| `CI_ENVIRONMENT`  | `development` or `production` | `production`                    |
| `app.baseURL`     | Full URL with trailing slash  | `https://presensi.example.com/` |
| `app.appTimezone` | PHP timezone for server time  | `Asia/Makassar`                 |

### Database

| Variable                    | Description       |
| --------------------------- | ----------------- |
| `database.default.hostname` | Database host     |
| `database.default.database` | Database name     |
| `database.default.username` | Database user     |
| `database.default.password` | Database password |

### AI Assistant (Si Pintar)

| Variable        | Description                                 | Example                            |
| --------------- | ------------------------------------------- | ---------------------------------- |
| `GROQ_API_KEY`  | Primary Groq API key                        | `gsk_abc123...`                    |
| `GROQ_API_KEYS` | Multiple keys for pooling (comma-separated) | `key1,key2,key3`                   |
| `GROQ_MODEL`    | LLM model to use                            | `moonshotai/kimi-k2-instruct-0905` |

> **Tip:** Multiple API keys in `GROQ_API_KEYS` enable automatic random rotation
> on each request, distributing load across keys and increasing effective rate limits.

### Telegram Notifications

| Variable            | Description                                     |
| ------------------- | ----------------------------------------------- |
| `telegram.botToken` | Token from [@BotFather](https://t.me/BotFather) |
| `telegram.chatId`   | Target group/channel ID (negative for groups)   |

---

## 📁 Project Structure

```
PresenSI/
├── app/
│   ├── Controllers/
│   │   ├── Admin.php                  # Admin dashboard & config
│   │   ├── Presensi.php               # MFA check-in/out processing
│   │   ├── Pegawai.php                # Employee/student master data
│   │   ├── Kiosk.php                  # Shared attendance terminal
│   │   ├── FaceEnrollmentAdmin.php    # Admin face descriptor management
│   │   ├── FaceEnrollmentRequest.php  # Student self-enrollment requests
│   │   ├── Ketidakhadiran.php         # Absence/leave management
│   │   └── Auth/                      # Login, register, password reset
│   ├── Models/
│   │   ├── UsersModel.php
│   │   ├── PresensiModel.php
│   │   ├── PegawaiModel.php
│   │   ├── FaceDescriptorModel.php
│   │   └── KetidakhadiranModel.php
│   ├── Views/
│   │   ├── admin/                     # Admin dashboard templates
│   │   ├── kiosk/                     # Fullscreen kiosk interface
│   │   ├── auth/                      # Login/register forms
│   │   └── components/                # Reusable UI components
│   ├── Filters/
│   │   ├── AuthFilter.php             # Session authentication check
│   │   └── RoleFilter.php             # RBAC middleware
│   ├── Helpers/
│   │   └── telegram_helper.php        # Telegram notification helper
│   └── Libraries/
│       └── MythAuth/                  # Internalized auth library
│
├── public/
│   ├── assets/
│   │   ├── js/
│   │   │   ├── human.js               # Face recognition AI (~1.5MB, self-hosted)
│   │   │   ├── quagga.min.js          # Barcode scanner
│   │   │   └── leaflet.min.js         # Interactive maps
│   │   └── models/                    # TensorFlow.js model binaries (.bin/.json)
│   └── uploads/
│       ├── presensi/                  # Check-in/out photo evidence
│       ├── faces/                     # Face enrollment training images
│       └── surat/                     # Absence certificate PDFs
│
├── database/
│   ├── Migrations/                    # Schema version control
│   └── Seeds/                         # Initial data seeders
│
├── writable/                          # Cache, logs, sessions (must be writable)
├── tests/                             # PHPUnit test files
├── composer.json
├── .env.example                       # Environment template
└── spark                              # CodeIgniter CLI
```

---

## 🎯 Usage

### User Roles

| Role              | Access Level           | Primary Use Case                                   |
| ----------------- | ---------------------- | -------------------------------------------------- |
| **Admin**         | Full system access     | System configuration, user management, all reports |
| **Head**          | Read + approval access | Monitoring, analytics, leave approval              |
| **Pegawai/Siswa** | Personal access        | Daily attendance, leave requests, personal reports |
| **Kiosk**         | Terminal-only access   | Operating shared attendance terminals              |
| **Helper**        | Limited admin          | User data management only, no sensitive modules    |

---

### Attendance Flow (Pegawai/Siswa)

```
Open /presensi/masuk
        │
        ▼
┌─ Factor 1: GPS ──────────────────────────────────────────┐
│  watchPosition() → Haversine distance calculation        │
│  ✅ Within radius → green UI, proceed                    │
│  ❌ Outside radius → red UI, button locked               │
└──────────────────────────────────────────────────────────┘
        │ (pass)
        ▼
┌─ Factor 2: Liveness Detection ───────────────────────────┐
│  Human.js loads → random challenge displayed             │
│  e.g. "Tilt Up" → user tilts head up                    │
│  Validated via face.rotation.pitch / yaw                 │
│  Progress bar advances → all challenges complete         │
└──────────────────────────────────────────────────────────┘
        │ (pass)
        ▼
┌─ Factor 3: Face Recognition ─────────────────────────────┐
│  Extract 1024-dim embedding from camera frame            │
│  Cosine similarity vs all stored descriptors             │
│  similarity ≥ 0.62 → identity confirmed                  │
│  3-second countdown → auto-capture snapshot              │
└──────────────────────────────────────────────────────────┘
        │ (all factors pass)
        ▼
POST to server:
  - face_embedding (NOT the image)
  - GPS coordinates
  - timestamp = ClientTime + timeDiff  ← server-corrected
        │
        ▼
Server re-validates all factors → saves record
        │
        ▼
Dashboard updates + AI "Fun Fact" (age/emotion estimate)
```

---

### Face Enrollment

Students can self-enroll their face for attendance:

1. Go to **Profile → Daftar Wajah**
2. Capture face photo via webcam or upload image
3. Crop to 1:1 using Cropper.js
4. Submit enrollment request (max 3 requests/day)
5. Admin approves → descriptor activated for attendance use

Admins can also directly manage descriptors at **Pengguna → Face Descriptor**.

---

### AI Assistant (Si Pintar)

Click the floating AI widget on any dashboard page. Example queries:

```
"Siapa saja yang alpha hari ini?"
→ Lists all unexcused absentees for today

"Rekap kehadiran kelas XII-10 bulan ini"
→ Per-student summary: present, absent, sick, late

"Berapa kali saya terlambat bulan Februari?"
→ Personal late count (students only see own data)

"Tampilkan tren kehadiran minggu ini"
→ Attendance trend narrative with data
```

> **Security note:** Queries like `DROP TABLE`, `DELETE`, or accessing other
> users' data are blocked by the regex sanitizer and per-user isolation layer.

---

### Kiosk Mode

Designed for school gate terminals operated by a staff member:

1. Login with a **Kiosk** role account → automatically enters fullscreen SPA
2. **State 1 (Scanner)**: Student scans their Barcode 128 badge
3. **State 2 (Verification)**: Face recognition runs automatically (no button press)
4. **State 3 Success**: Record saved → Telegram notification sent → returns to State 1
5. **State 3 Failure**: QR code fallback displayed for manual verification

The 30-second idempotency window prevents duplicate records if the same badge
is accidentally scanned multiple times.

---

## 🧪 Testing & Results

### Functional Test Summary

All 50 test scenarios passed across 5 modules:

| Module          | Scenarios | Result      |
| --------------- | --------- | ----------- |
| Authentication  | 11        | ✅ All Pass |
| MFA Attendance  | 12        | ✅ All Pass |
| Kiosk Mode      | 8         | ✅ All Pass |
| Data Management | 12        | ✅ All Pass |
| AI Si Pintar    | 7         | ✅ All Pass |

### Key Security Validations

| Attack Vector                 | Test                          | Result                                  |
| ----------------------------- | ----------------------------- | --------------------------------------- |
| Printed photo spoofing        | Present A4 photo to camera    | ✅ Blocked by liveness (no 3D movement) |
| Video replay attack           | Present phone video to camera | ✅ Blocked by liveness detection        |
| Device clock manipulation     | Change device time by 1 hour  | ✅ `timeDiff` corrects to server time   |
| SQL injection via AI          | `DROP TABLE presensi`         | ✅ Rejected by regex sanitizer          |
| Cross-user data access via AI | Query other student's data    | ✅ Per-user isolation enforced          |
| Duplicate kiosk scan          | Scan same badge twice in 10s  | ✅ Idempotency window blocks duplicate  |

### Face Recognition Performance

| Condition                    | Qualitative Accuracy | Notes                                      |
| ---------------------------- | -------------------- | ------------------------------------------ |
| Normal indoor lighting       | Very High            | Similarity consistently > 0.75             |
| Low light                    | Good                 | Minimum ambient lighting required          |
| Strong backlight             | Good                 | Minor degradation, still reliable          |
| Glasses (same as enrollment) | Good                 | Stable recognition                         |
| Significant hairstyle change | Good                 | Embedding focuses on core facial features  |
| Printed photo attack         | Blocked              | Liveness detection prevents this entirely  |
| Face at >45° angle           | Low                  | Human.js requires frontal face orientation |

### Browser Compatibility

| Browser              | WebAssembly | Face Recognition | Status          |
| -------------------- | ----------- | ---------------- | --------------- |
| Chrome (57+)         | ✅          | ✅               | Fully Supported |
| Firefox (Latest)     | ✅          | ✅               | Fully Supported |
| Safari (Latest)      | ✅          | ✅               | Fully Supported |
| Edge (Latest)        | ✅          | ✅               | Fully Supported |
| Chrome Mobile        | ✅          | ✅               | Fully Supported |
| Safari Mobile (iOS)  | ✅          | ✅               | Fully Supported |
| Internet Explorer 11 | ❌          | ❌               | Not Supported   |
| Chrome < 57          | ❌          | ❌               | Not Supported   |

> Legacy browsers are intentionally unsupported — WebAssembly is a hard requirement
> for running Human.js inference.

### Client-Side Inference Performance

| Device Class           | Inference Time per Frame    |
| ---------------------- | --------------------------- |
| High-end (2022+)       | < 200 ms                    |
| Mid-range (2020–2022)  | 200–400 ms                  |
| Entry-level (pre-2020) | > 500 ms (still functional) |

Model loading: ~5–10s on first load (network dependent), < 1s from `localStorage` cache.

---

## 📡 API Reference

PresenSI operates primarily as an MVC web application, but exposes internal AJAX
endpoints. The primary one used externally is the AI chat endpoint.

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

### Other Internal Endpoints

| Endpoint                | Method   | Description                     |
| ----------------------- | -------- | ------------------------------- |
| `/presensi/masuk`       | GET/POST | Check-in with MFA               |
| `/presensi/keluar`      | GET/POST | Check-out with MFA              |
| `/kiosk/cariPegawai`    | POST     | Barcode lookup (Kiosk)          |
| `/kiosk/prosesPresensi` | POST     | Kiosk attendance submission     |
| `/wajah/request`        | POST     | Face enrollment request         |
| `/api/server-time`      | GET      | Server timestamp for `timeDiff` |

---

## 🔧 Two-Pass LLM Pipeline (Technical Deep Dive)

Si Pintar processes queries in two sequential LLM calls:

````
User query (Bahasa Indonesia)
         │
         ▼
┌─ Pass 1: Text-to-SQL ──────────────────────────┐
│  System context: DB schema + table info        │
│  Model generates: QUERY: ```sql SELECT ...```  │
│                                                │
│  Regex Sanitizer checks:                       │
│  ✅ Allow: SELECT only                         │
│  ❌ Block: INSERT, UPDATE, DELETE, DROP, etc.  │
│  ✅ Limit: 30 rows max                         │
└────────────────────────────────────────────────┘
         │ (SQL extracted + executed against DB)
         ▼
    Raw JSON results from database
         │
         ▼
┌─ Pass 2: SQL-to-Natural Language ──────────────┐
│  System context: "Answer in Bahasa Indonesia"  │
│  Input: raw query results as JSON              │
│  Output: narrative response + formatted data   │
│  Markdown → DOMPurify → rendered HTML          │
└────────────────────────────────────────────────┘
         │
         ▼
Floating chat bubble renders response
````

---

## 🛡️ Security Architecture

### Password Security

```
User password → SHA-384(password) → base64_encode → Argon2id(result)
```

SHA-384 pre-hashing addresses Bcrypt/Argon2's 72-character input limit —
long passwords are safely reduced to a fixed-length digest before KDF processing.

### Time Integrity

```javascript
// Calculated once at page load
const timeDiff = serverTime - clientTime

// All timestamps use corrected time
const presenceTimestamp = Date.now() + timeDiff
// → Immune to device clock manipulation
```

### Biometric Privacy

- Face images → processed entirely in browser (Human.js + WASM)
- Only the resulting 1024-float embedding vector is sent to the server
- Embeddings are mathematically one-way: the original face cannot be reconstructed
- Face data access is restricted to the owner and admin roles

---

## 🤝 Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -m 'Add some feature'`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Open a Pull Request

Please ensure your code follows the existing CodeIgniter 4 conventions and
includes appropriate test cases for new functionality.

---

## 📜 License

This project is licensed under the **GNU General Public License v3.0 (GPL-3.0)**.

Permissions of this strong copyleft license are conditioned on making available
complete source code of licensed works and modifications, which include larger
works using a licensed work, under the same license.

See the [LICENSE](LICENSE) file for full details.

---

## 🙏 Acknowledgements

- **[o-present](https://github.com/josephines1/o-present)** by Josephine — the original
  open-source project that PresenSI was built upon and extensively re-engineered from
- **[vladmandic/human](https://github.com/vladmandic/human)** — the incredible
  browser-native AI library that makes client-side biometrics possible
- **[Groq](https://groq.com)** — for ultra-low-latency LLM inference via LPU hardware
- **[Tabler UI](https://tabler.io)** — the clean, Bootstrap 5-based dashboard template
- **SMA Negeri 1 Balikpapan** — for the opportunity to implement and validate this
  system at scale with 1,000+ real users

---

<div align="center">

Made with ❤️ in Indonesia

_"Si Pintar Urusan Presensi"_

</div>
