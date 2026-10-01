<div align="center">

<img src="public/assets/img/logo.png" alt="PresenSI Logo" width="120"/>

# PresenSI

### _Si Pintar Urusan Presensi_

**Attendance management system with 3-factor authentication, client-side face recognition, GPS geofencing, and a natural language query assistant.**

**Language:** [English](README.md) | [Bahasa Indonesia](README.id.md)

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

> Forked from [`o-present`](https://github.com/josephines1/o-present) by Josephine,
> then rebuilt with a different architecture, security model, AI integration, and feature set.
>
> **Running in production at SMA Negeri 1 Balikpapan, 1,000+ active students.**

</div>

---

## 📖 Background

Paper roll calls and fingerprint scanners have the same problem: they're easy to game.
Proxy attendance ("titip absen") is trivial, there's no location check, and someone
has to manually reconcile the records afterward.

PresenSI's answer to this is to require **three independent factors to pass at the
same time** before an attendance record gets accepted:

| Factor    | What it verifies                           | Technology                      |
| --------- | ------------------------------------------ | ------------------------------- |
| **WHO**   | Biometric identity via face recognition    | Human.js + TensorFlow.js (WASM) |
| **WHERE** | Physical presence within school grounds    | GPS + Haversine geofencing      |
| **WHEN**  | Tamper-proof server-synchronized timestamp | Server `timeDiff` correction    |

All three run on AND logic, so beating one factor doesn't get you anywhere. A photo
fails liveness detection. Being off-campus fails the GPS check. Changing your device
clock gets corrected by `timeDiff` before it ever reaches the database.

---

## ✨ Key Features

### 🔐 Multi-Factor Authentication Engine

- **3-factor AND logic**, all checks have to pass, no bypasses
- **Active liveness detection** with randomized head movement challenges (tilt up/down,
  turn left/right), validated against `face.rotation.pitch` and `face.rotation.yaw`
- **Face recognition** using 1024-dimensional embeddings from Human.js
  (MobileNetV2-based), cosine similarity threshold set at 0.62
- **GPS geofencing** via Haversine formula with a configurable radius per location,
  continuous monitoring through `watchPosition()`
- **Server time sync**, `timeDiff = ServerTime − ClientTime` computed at page load,
  every timestamp afterward uses the corrected value so device clocks can't be spoofed

### 🤖 AI Assistant ("Si Pintar")

- Natural language queries over attendance data, in Bahasa Indonesia
- **Two-pass LLM pipeline**: first pass turns the question into SQL, second pass
  turns the SQL result back into a readable answer
- Runs on **Groq** (LPU inference, low latency) with API key pooling for rate
  limit headroom
- A regex sanitizer blocks anything that isn't `SELECT` (`INSERT`, `UPDATE`,
  `DELETE`, `DROP`, etc. are rejected before execution)
- Per-user data isolation, students can only query their own records

### 🏢 Kiosk Mode

- Fullscreen SPA for shared attendance terminals
- **3-state flow**: barcode scan → face verification → success/failure
- **30-second idempotency window** to stop duplicate entries from repeated scans
- CSRF tokens refresh per AJAX response, no page reload required
- Falls back to QR code if face recognition fails

### 🛡️ Privacy-First Biometrics

- **No images ever hit the server**, only the resulting embedding vector does
- Each face is stored as a 1024-float vector, nothing else
- The embedding is one-way, you can't reconstruct a face from it
- All inference happens client-side via WebAssembly

### 📊 Management & Reporting

- Real-time dashboard covering present, absent, sick/leave, and late counts
- Excel import/export through PhpSpreadsheet, multi-sheet XLSX per unit
- Absence management with an approval workflow, PDF certificate upload, and
  date overlap validation
- Holiday calendar synced from an external API (`libur.deno.dev`) with manual override
- Built-in file manager with bulk ZIP download and auto-cleanup
- Code 128 barcode printing for kiosk identification

### 📢 Integrations

- **Telegram Bot API** for real-time push notifications to parent groups
- **Groq API** for Si Pintar's LLM inference
- **OpenStreetMap + Leaflet.js** for location management maps
- **Nominatim** for coordinate lookup during location setup

### 🔒 Security

- Passwords go through **SHA-384 pre-hash → Argon2id KDF**, double hashing
- **RBAC** across five roles: Admin, Head, Pegawai/Siswa, Kiosk, Helper
- CSRF protection on every POST form
- Input sanitized with HTMLPurifier and Laminas Escaper
- Time-limited tokens for password reset (24h) and email change (5 min)
- **Zero CDN dependency**, all JS libraries are self-hosted so there's no
  third-party script risk

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

A few design decisions worth noting:

- `users.password_hash`, SHA-384 pre-hash + Argon2id, never plain Bcrypt
- `face_descriptors.descriptor` is `MEDIUMTEXT` holding a JSON array of 1024 floats
- `presensi` stores both check-in and check-out, with photo path references
- `lokasi_presensi` holds `latitude`, `longitude`, `radius`, `timezone`,
  `jam_masuk`, `jam_keluar` per location
- Every table uses soft deletes (`deleted_at`) to keep an audit trail

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
- **Browser**: any modern Chromium/WebKit/Gecko browser with WebAssembly support
  (Chrome 57+, Firefox, Safari, Edge; Internet Explorer is **not** supported)

---

## 🚀 Installation

See [INSTALL.md](INSTALL.md) for the full walkthrough.
Indonesian translations are at [README.id.md](README.id.md) and [INSTALL.id.md](INSTALL.id.md).

### Database setup summary

CodeIgniter 4 migrations are the source of truth for the schema. Create an empty
MySQL or MariaDB database, set the connection in `.env`, then run:

```bash
php spark migrate --all
php spark db:seed DatabaseSeeder
```

`--all` includes the internalized MythAuth migration. `DatabaseSeeder` only creates
fictional development data, no face descriptors, photos, uploaded documents,
tokens, or production records come from it. Don't import a local phpMyAdmin dump
into this repository.

## ⚙️ Configuration

Everything is set through the `.env` file.

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

| Variable        | Description                                 | Example                       |
| --------------- | ------------------------------------------- | ----------------------------- |
| `GROQ_API_KEY`  | Primary Groq API key                        | `gsk_abc123...`               |
| `GROQ_API_KEYS` | Multiple keys for pooling (comma-separated) | `key1,key2,key3`              |
| `GROQ_MODEL`    | LLM model to use                            | `qwen/qwen3.8-27b` |

> **Tip:** passing multiple keys in `GROQ_API_KEYS` enables random rotation per
> request, which spreads the load and raises your effective rate limit.

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
│  ❌ Outside radius → red UI, button locked                │
└──────────────────────────────────────────────────────────┘
        │ (pass)
        ▼
┌─ Factor 2: Liveness Detection ───────────────────────────┐
│  Human.js loads → random challenge displayed              │
│  e.g. "Tilt Up" → user tilts head up                      │
│  Validated via face.rotation.pitch / yaw                  │
│  Progress bar advances → all challenges complete           │
└──────────────────────────────────────────────────────────┘
        │ (pass)
        ▼
┌─ Factor 3: Face Recognition ─────────────────────────────┐
│  Extract 1024-dim embedding from camera frame              │
│  Cosine similarity vs all stored descriptors                │
│  similarity ≥ 0.62 → identity confirmed                     │
│  3-second countdown → auto-capture snapshot                 │
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

Students self-enroll their face:

1. Go to **Profile → Daftar Wajah**
2. Capture a photo via webcam, or upload one
3. Crop to 1:1 using Cropper.js
4. Submit the enrollment request (capped at 3 requests/day)
5. Admin approves it, descriptor goes live for attendance

Admins can manage descriptors directly too, at **Pengguna → Face Descriptor**.

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

> **Security note:** things like `DROP TABLE`, `DELETE`, or trying to pull another
> user's data get blocked by the regex sanitizer and the per-user isolation layer.

---

### Kiosk Mode

Built for school gate terminals run by a staff member:

1. Log in with a **Kiosk** role account, it drops straight into fullscreen SPA
2. **State 1 (Scanner)**: student scans their Code 128 badge
3. **State 2 (Verification)**: face recognition runs automatically, no button needed
4. **State 3 Success**: record saved, Telegram notification sent, back to State 1
5. **State 3 Failure**: QR code fallback shown for manual verification

The 30-second idempotency window stops duplicate records if the same badge
gets scanned twice by accident.

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

> Legacy browsers are unsupported on purpose, WebAssembly is a hard requirement
> for running Human.js inference.

### Client-Side Inference Performance

| Device Class           | Inference Time per Frame    |
| ---------------------- | --------------------------- |
| High-end (2022+)       | < 200 ms                    |
| Mid-range (2020–2022)  | 200–400 ms                  |
| Entry-level (pre-2020) | > 500 ms (still functional) |

Model loading takes roughly 5 to 10 seconds on first load depending on network,
and under 1 second once cached in `localStorage`.

---

## 📡 API Reference

PresenSI is mostly an MVC web app, but it does expose internal AJAX endpoints.
The one most relevant externally is the AI chat endpoint.

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

Si Pintar handles each query in two sequential LLM calls:

````
User query (Bahasa Indonesia)
         │
         ▼
┌─ Pass 1: Text-to-SQL ──────────────────────────┐
│  System context: DB schema + table info         │
│  Model generates: QUERY: ```sql SELECT ...```   │
│                                                 │
│  Regex Sanitizer checks:                        │
│  ✅ Allow: SELECT only                          │
│  ❌ Block: INSERT, UPDATE, DELETE, DROP, etc.   │
│  ✅ Limit: 30 rows max                          │
└──────────────────────────────────────────────────┘
         │ (SQL extracted + executed against DB)
         ▼
    Raw JSON results from database
         │
         ▼
┌─ Pass 2: SQL-to-Natural Language ──────────────┐
│  System context: "Answer in Bahasa Indonesia"   │
│  Input: raw query results as JSON               │
│  Output: narrative response + formatted data    │
│  Markdown → DOMPurify → rendered HTML           │
└──────────────────────────────────────────────────┘
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

SHA-384 pre-hashing works around Bcrypt/Argon2's 72-character input limit,
so long passwords get reduced to a fixed-length digest before they hit the KDF.

### Time Integrity

```javascript
// Calculated once at page load
const timeDiff = serverTime - clientTime

// All timestamps use corrected time
const presenceTimestamp = Date.now() + timeDiff
// → Immune to device clock manipulation
```

### Biometric Privacy

- Face images are processed entirely in-browser (Human.js + WASM)
- Only the resulting 1024-float embedding vector gets sent to the server
- Embeddings are one-way math, you can't go from embedding back to face
- Face data access is restricted to the owner and admin roles

---

## 🤝 Contributing

Contributions are welcome. The usual flow:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -m 'Add some feature'`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Open a Pull Request

Keep code consistent with the existing CodeIgniter 4 conventions, and add test
cases for new functionality.

---

## 📜 License

Licensed under the **GNU General Public License v3.0 (GPL-3.0)**.

This is a strong copyleft license: using this license means any modifications
or larger works that include it also need to make their complete source available
under the same terms.

See the [LICENSE](LICENSE) file for full details.

---

## 🙏 Acknowledgements

- **[o-present](https://github.com/josephines1/o-present)** by Josephine, the original
  open-source project this was built on top of and extensively reworked
- **[vladmandic/human](https://github.com/vladmandic/human)**, the library that makes
  browser-native biometrics possible in the first place
- **[Groq](https://groq.com)** for the low-latency LLM inference behind Si Pintar
- **[Tabler UI](https://tabler.io)** for the Bootstrap 5-based dashboard template
- **SMA Negeri 1 Balikpapan**, for the chance to run this at scale with 1,000+ real users

---

<div align="center">

Made with ❤️ in Indonesia

_"Si Pintar Urusan Presensi"_

</div>
