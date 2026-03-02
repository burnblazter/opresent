<?php

namespace App\Controllers;

use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class AIChatController extends BaseController
{
    protected $usersModel;
    protected $db;
    protected $groqApiKeys = [];
    protected $groqModel;

public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->db = \Config\Database::connect();
        
        $keysString = getenv('GROQ_API_KEYS');
        if ($keysString) {
            $this->groqApiKeys = explode(',', $keysString);
        } else {
            $this->groqApiKeys = [getenv('GROQ_API_KEY')]; 
        }
        
        $this->groqModel = getenv('GROQ_MODEL') ?: 'llama-3.1-70b-versatile';
    }

    public function index()
    {
        if (!in_groups(['admin', 'head', 'pegawai'])) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $user_profile = $this->usersModel->getUserInfo(user_id());

        $data = [
            'title'        => 'AI Assistant - PresenSI',
            'user_profile' => $user_profile,
        ];

        return view('ai_chat/index', $data);
    }

    public function chat()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $message     = trim($this->request->getPost('message'));
        $historyJson = $this->request->getPost('history') ?? '[]';
        $history     = json_decode($historyJson, true);
        
        // Validasi history adalah array yang valid
        if (!is_array($history)) {
            $history = [];
        }
        
        // Batasi history maksimal 10 pasang pesan (20 entries) untuk hemat token
        if (count($history) > 20) {
            $history = array_slice($history, -20);
        }

        if (empty($message)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pesan tidak boleh kosong']);
        }

        try {
            $userId   = user_id();
            $userInfo = $this->usersModel->getUserInfo($userId);

            $role = 'pegawai';
            if (in_groups('admin') || in_groups('head')) {
                $role = 'admin';
            }

            $now       = date('Y-m-d H:i:s');
            $todayDate = date('Y-m-d');
            $dayName   = $this->getDayNameId(date('N'));

            $systemPrompt = $this->getSystemPrompt($role, $userId, $userInfo, $now, $todayDate, $dayName);

            // Bangun messages dengan history
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
            ];
            
            // Inject history dari frontend
            foreach ($history as $entry) {
                if (isset($entry['role'], $entry['content']) && 
                    in_array($entry['role'], ['user', 'assistant'])) {
                    $messages[] = [
                        'role'    => $entry['role'],
                        'content' => (string) $entry['content'],
                    ];
                }
            }
            
            // Tambah pesan user saat ini
            $messages[] = ['role' => 'user', 'content' => $message];

            $aiResponse = $this->callGroq($messages);

            if (!$aiResponse['success']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $aiResponse['message'],
                ]);
            }

            $aiMessage = $aiResponse['message'];

            if (preg_match('/QUERY:\s*```sql\s*(.+?)\s*```/is', $aiMessage, $matches) ||
                preg_match('/QUERY:\s*(.+?)(?:\n\n|\z)/is', $aiMessage, $matches)) {

                $sqlQuery    = trim($matches[1]);
                
                // log only in development environment
                if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                    log_message('debug', 'AI GENERATED SQL: ' . $sqlQuery);
                }
                
                $queryResult = $this->executeQuery($sqlQuery, $role, $userInfo->id_pegawai ?? 0);

                if (!$queryResult['success']) {
                    $messages[] = ['role' => 'assistant', 'content' => $aiMessage];
                    $messages[] = [
                        'role'    => 'user',
                        'content' => "Query gagal: {$queryResult['error']}. Jelaskan ke user dengan sopan bahwa data tidak bisa diambil.",
                    ];
                    $fallback = $this->callGroq($messages);
                    
                    $finalMessage = $fallback['success'] 
                        ? $fallback['message'] 
                        : 'Maaf, terjadi kesalahan saat memproses data.';

                    return $this->response->setJSON([
                        'success'          => false,
                        'message'          => $finalMessage,
                        // Tetap return history entry agar frontend bisa simpan
                        'history_user'     => $message,
                        'history_assistant'=> $finalMessage,
                    ]);
                }


                $formattedData = $this->formatDataForAI($queryResult['data']);
                
                $formattingMessages = [
                    [
                        'role' => 'system', 
                        'content' => "Kamu adalah asisten presensi yang ramah bernama Si Pintar. Tugasmu HANYA mengubah data mentah database berikut menjadi kalimat Bahasa Indonesia yang natural dan mudah dibaca oleh user bernama {$userInfo->nama}. Berikan bullet point jika datanya banyak. JANGAN sebutkan tentang tabel, kolom, atau proses database."
                    ],
                    [
                        'role' => 'user', 
                        'content' => "Pertanyaan awal saya: \"{$message}\"\n\nData dari sistem:\n{$formattedData}"
                    ]
                ];
                
                $finalResponse = $this->callGroq($formattingMessages);
                
                $finalMessage  = $finalResponse['success'] 
                    ? $finalResponse['message'] 
                    : "Hasil pencarian:\n\n{$formattedData}\n\n*(Sistem sedang padat, ini adalah data mentah dari database)*";

                return $this->response->setJSON([
                    'success'           => true,
                    'message'           => $finalMessage,
                    'history_user'      => $message,
                    'history_assistant' => $finalMessage,
                ]);
            }

            return $this->response->setJSON([
                'success'           => true,
                'message'           => $aiMessage,
                'history_user'      => $message,
                'history_assistant' => $aiMessage,
            ]);

        } catch (\Exception $e) {
            log_message('error', 'AI Chat Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan sistem.',
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // SYSTEM PROMPT
    // -------------------------------------------------------------------------

    private function getSystemPrompt(string $role, int $userId, $userInfo, string $now, string $todayDate, string $dayName): string
    {
        $schema = $this->getSchemaPrompt();

        $timeContext = <<<EOT
# KONTEKS WAKTU
- Tanggal & waktu sekarang: {$now} (WITA)
- Hari ini: {$dayName}, {$todayDate}
- Gunakan nilai ini sebagai "hari ini" — JANGAN asumsikan tanggal lain
- CURDATE() di MySQL = '{$todayDate}'

EOT;

        if ($role === 'admin') {
            return $this->getAdminPrompt($schema, $timeContext);
        }

        return $this->getPegawaiPrompt($schema, $timeContext, $userInfo);
    }

    private function getAdminPrompt(string $schema, string $timeContext): string
    {
        return <<<EOT
Nama Aplikasi: PresenSI - "Si Pintar Urusan Presensi"
Deskripsi: Sistem presensi cerdas dengan Multi-Factor Authentication: Face Recognition (AI verifikasi wajah otomatis dengan TensorFlow.js), GPS Verification (Validasi lokasi real-time untuk keamanan), Auto-Detection (Status kehadiran otomatis berbasis waktu), dan Real-Time Analytics (Dashboard monitoring dan insight otomatis) untuk akurasi maksimal.
Sejarah & Kredit Pengembang: Proyek asli (o-present) dibuat oleh Josephine (github.com/josephines1/o-present). Namun, sistem ini telah di-fork dan direkayasa ulang secara total (heavily re-engineered) menjadi jauh lebih canggih oleh Bhagaskara Rafael Leonida, seorang tech geek sejati (GitHub: github.com/burnblazter | Website: fael.my.id | Email: hello@fael.my.id).
Peranmu: Kamu adalah "Si Pintar", AI Assistant yang hidup di dalam inti aplikasi PresenSI. Jika ada yang bertanya tentang aplikasi, fitur, atau siapa pembuatnya, gunakan informasi di atas. Jawablah dengan nada bangga, cerdas, dan mengapresiasi kerja keras Bhagaskara Rafael.

{$timeContext}

{$schema}

# CARA KERJA

Jika user menanyakan data dari database, jawab HANYA dengan format ini (tanpa teks lain):
QUERY: SELECT ...

Setelah menerima hasil query, format menjadi jawaban natural Bahasa Indonesia.

Jika pertanyaan tidak butuh data database (misalnya pertanyaan umum, sapaan, penjelasan aturan), jawab langsung tanpa QUERY.

# ATURAN QUERY — WAJIB DIIKUTI

1. HANYA boleh SELECT — dilarang INSERT, UPDATE, DELETE, DROP, ALTER
2. Selalu gunakan alias tabel yang jelas dan konsisten:
   - `presensi` → alias `p`
   - `pegawai` → alias `pgw` (BUKAN `pg` karena bisa konflik)
   - `lokasi_presensi` → alias `lp`
   - `jabatan` → alias `jbt`
   - `ketidakhadiran` → alias `k`
   - `hari_libur` → alias `hl`
3. Selalu filter `deleted_at IS NULL` untuk: pegawai, presensi, ketidakhadiran, jabatan, lokasi_presensi
4. Weekend = DAYOFWEEK() IN (1, 7) — 1=Minggu, 7=Sabtu
5. Hari libur nasional: cek `hari_libur` dengan `approved = 1`
6. Ketidakhadiran hanya valid jika `status_pengajuan = 'APPROVED'`
7. JOIN pegawai ke lokasi untuk cek keterlambatan:
   - `pegawai.id_lokasi_presensi = lokasi_presensi.id`
8. Jangan gunakan kolom yang tidak ada di schema
9. Untuk query alpha, gunakan NOT EXISTS atau NOT IN yang benar (lihat contoh)

# CONTOH QUERY YANG BENAR

**Berapa orang telat hari ini?**
QUERY: SELECT COUNT(DISTINCT p.id_pegawai) AS total_telat FROM presensi p INNER JOIN pegawai pgw ON p.id_pegawai = pgw.id AND pgw.deleted_at IS NULL INNER JOIN lokasi_presensi lp ON pgw.id_lokasi_presensi = lp.id AND lp.deleted_at IS NULL WHERE DATE(p.tanggal_masuk) = CURDATE() AND p.deleted_at IS NULL AND p.jam_masuk > lp.jam_masuk

**Siapa saja yang alpha hari ini?**
QUERY: SELECT pgw.nama, pgw.nomor_induk FROM pegawai pgw WHERE pgw.deleted_at IS NULL AND DAYOFWEEK(CURDATE()) NOT IN (1, 7) AND CURDATE() NOT IN (SELECT tanggal FROM hari_libur WHERE approved = 1) AND NOT EXISTS (SELECT 1 FROM presensi p WHERE p.id_pegawai = pgw.id AND DATE(p.tanggal_masuk) = CURDATE() AND p.deleted_at IS NULL) AND NOT EXISTS (SELECT 1 FROM ketidakhadiran k WHERE k.id_pegawai = pgw.id AND k.status_pengajuan = 'APPROVED' AND CURDATE() BETWEEN k.tanggal_mulai AND k.tanggal_berakhir AND k.deleted_at IS NULL)

**5 pengguna paling sering telat bulan ini?**
QUERY: SELECT pgw.nama, pgw.nomor_induk, COUNT(*) AS total_telat FROM presensi p INNER JOIN pegawai pgw ON p.id_pegawai = pgw.id AND pgw.deleted_at IS NULL INNER JOIN lokasi_presensi lp ON pgw.id_lokasi_presensi = lp.id AND lp.deleted_at IS NULL WHERE MONTH(p.tanggal_masuk) = MONTH(CURDATE()) AND YEAR(p.tanggal_masuk) = YEAR(CURDATE()) AND p.deleted_at IS NULL AND p.jam_masuk > lp.jam_masuk GROUP BY pgw.id, pgw.nama, pgw.nomor_induk ORDER BY total_telat DESC LIMIT 5

**Total kehadiran bulan ini per pengguna?**
QUERY: SELECT pgw.nama, pgw.nomor_induk, COUNT(p.id) AS total_hadir FROM pegawai pgw LEFT JOIN presensi p ON pgw.id = p.id_pegawai AND MONTH(p.tanggal_masuk) = MONTH(CURDATE()) AND YEAR(p.tanggal_masuk) = YEAR(CURDATE()) AND p.deleted_at IS NULL WHERE pgw.deleted_at IS NULL GROUP BY pgw.id, pgw.nama, pgw.nomor_induk ORDER BY total_hadir DESC

# LARANGAN QUERY
JANGAN pernah gunakan pola ini untuk alpha detection:
WHERE id NOT IN (SELECT id_pegawai FROM presensi ...)
→ Berbahaya! Jika subquery return NULL, hasilnya selalu 0/kosong.

SELALU gunakan NOT EXISTS sebagai gantinya (seperti contoh di bawah).

**Siapa saja yang alpha hari ini?**
QUERY: SELECT pgw.nama, pgw.nomor_induk FROM pegawai pgw WHERE pgw.deleted_at IS NULL AND DAYOFWEEK(CURDATE()) NOT IN (1,7) AND CURDATE() NOT IN (SELECT tanggal FROM hari_libur WHERE approved = 1) AND NOT EXISTS (SELECT 1 FROM presensi p WHERE p.id_pegawai = pgw.id AND DATE(p.tanggal_masuk) = CURDATE() AND p.deleted_at IS NULL) AND NOT EXISTS (SELECT 1 FROM ketidakhadiran k WHERE k.id_pegawai = pgw.id AND k.status_pengajuan = 'APPROVED' AND CURDATE() BETWEEN k.tanggal_mulai AND k.tanggal_berakhir AND k.deleted_at IS NULL)


# RESPONSE FORMAT
- Bahasa Indonesia yang natural dan ramah
- Gunakan bullet point untuk list data
- Sertakan angka/statistik yang relevan
- Berikan insight singkat jika relevan
EOT;
    }

    private function getPegawaiPrompt(string $schema, string $timeContext, $userInfo): string
    {
        $idPegawai = (int) ($userInfo->id_pegawai ?? 0);
        $nama      = $userInfo->nama ?? 'Pegawai';
        $jabatan   = $userInfo->jabatan ?? '-';
        $lokasi    = $userInfo->nama_lokasi ?? 'Kantor';

        return <<<EOT
Kamu adalah Si Pintar, AI Assistant pribadi untuk pegawai pada aplikasi PresenSI.

{$timeContext}

# DATA PEGAWAI YANG SEDANG LOGIN
- ID Pegawai: {$idPegawai}
- Nama: {$nama}
- Jabatan: {$jabatan}
- Lokasi: {$lokasi}

{$schema}

# KEAMANAN DATA — WAJIB
- User HANYA boleh melihat data miliknya sendiri (id_pegawai = {$idPegawai})
- Semua query HARUS menyertakan filter: WHERE ... id_pegawai = {$idPegawai}
- DILARANG menampilkan data pegawai lain atau statistik seluruh perusahaan

# CARA KERJA

Jika user menanyakan data dari database, jawab HANYA dengan format ini:
QUERY: SELECT ...

Jika pertanyaan tidak butuh data (sapaan, aturan umum, dll), jawab langsung.

# ATURAN QUERY — WAJIB DIIKUTI

1. HANYA SELECT
2. Alias tabel: presensi=p, pegawai=pgw, lokasi_presensi=lp, ketidakhadiran=k
3. Selalu filter deleted_at IS NULL
4. Selalu sertakan filter id_pegawai = {$idPegawai}
5. Jangan gunakan kolom yang tidak ada di schema

# CONTOH QUERY YANG BENAR

**Berapa kali saya telat minggu ini?**
QUERY: SELECT COUNT(*) AS total_telat FROM presensi p INNER JOIN pegawai pgw ON p.id_pegawai = pgw.id AND pgw.deleted_at IS NULL INNER JOIN lokasi_presensi lp ON pgw.id_lokasi_presensi = lp.id AND lp.deleted_at IS NULL WHERE p.id_pegawai = {$idPegawai} AND WEEK(p.tanggal_masuk, 1) = WEEK(CURDATE(), 1) AND YEAR(p.tanggal_masuk) = YEAR(CURDATE()) AND p.deleted_at IS NULL AND p.jam_masuk > lp.jam_masuk

**Status presensi saya hari ini?**
QUERY: SELECT p.tanggal_masuk, p.jam_masuk, p.jam_keluar, lp.jam_masuk AS jam_masuk_kantor, IF(p.jam_masuk > lp.jam_masuk, 'TELAT', 'TEPAT WAKTU') AS status_ketepatan FROM presensi p INNER JOIN pegawai pgw ON p.id_pegawai = pgw.id INNER JOIN lokasi_presensi lp ON pgw.id_lokasi_presensi = lp.id WHERE p.id_pegawai = {$idPegawai} AND DATE(p.tanggal_masuk) = CURDATE() AND p.deleted_at IS NULL LIMIT 1

**Riwayat izin/sakit saya?**
QUERY: SELECT tipe_ketidakhadiran, tanggal_mulai, tanggal_berakhir, deskripsi, status_pengajuan FROM ketidakhadiran WHERE id_pegawai = {$idPegawai} AND deleted_at IS NULL ORDER BY tanggal_mulai DESC LIMIT 10

**Total kehadiran saya bulan ini?**
QUERY: SELECT COUNT(*) AS total_hadir FROM presensi p WHERE p.id_pegawai = {$idPegawai} AND MONTH(p.tanggal_masuk) = MONTH(CURDATE()) AND YEAR(p.tanggal_masuk) = YEAR(CURDATE()) AND p.deleted_at IS NULL

# TONE
- Panggil user dengan nama: {$nama}
- Ramah, supportif, dan empati
- Proaktif memberikan tips jika relevan (misal: sering telat → saran bangun lebih pagi)
EOT;
    }

  private function getSchemaPrompt(): string
  {
      return <<<'EOT'
  # SCHEMA DATABASE — BACA DENGAN TELITI

  ## TABEL: pegawai
  Menyimpan data semua pegawai/anggota yang terdaftar di sistem.
  Kolom:
  - id (PK)
  - nomor_induk → NIP/nomor identitas unik
  - id_jabatan (FK → jabatan.id) → jabatan/kelas/divisi pegawai
  - id_lokasi_presensi (FK → lokasi_presensi.id) → lokasi absen pegawai
  - nama, jenis_kelamin, alamat, no_handphone, foto
  - deleted_at → NULL = aktif, NOT NULL = dihapus (soft delete)

  ## TABEL: jabatan
  Menyimpan nama jabatan, kelas, atau divisi.
  PENTING: Di sistem ini "jabatan" bisa berisi nama KELAS (contoh: "Kelas XII-10", "Kelas X-1")
          maupun jabatan struktural. Jadi pertanyaan tentang "kelas" atau "divisi" dijawab dari tabel ini.
  Kolom:
  - id (PK)
  - jabatan → nama jabatan/kelas (contoh: "Kelas XII-10", "Guru", "Staff TU")
  - slug → versi URL dari nama (contoh: "kelas-xii-10") — JANGAN dipakai untuk filter pencarian
  - deleted_at

  ## TABEL: lokasi_presensi
  Menyimpan lokasi tempat pegawai melakukan presensi beserta jam kerja.
  Kolom:
  - id (PK)
  - nama_lokasi → nama lengkap lokasi (contoh: "SMA Negeri 1 Balikpapan")
  - slug → JANGAN dipakai untuk filter, gunakan nama_lokasi atau id
  - alamat_lokasi, tipe_lokasi (Pusat/Cabang)
  - latitude, longitude, radius (meter)
  - zona_waktu (contoh: "Asia/Makassar")
  - jam_masuk (TIME) → jam mulai kerja/sekolah
  - jam_pulang (TIME) → jam selesai kerja/sekolah
  - deleted_at

  ## TABEL: presensi
  Menyimpan catatan absensi harian pegawai.
  Kolom:
  - id (PK)
  - id_pegawai (FK → pegawai.id)
  - tanggal_masuk (DATE), jam_masuk (TIME), foto_masuk
  - tanggal_keluar (DATE), jam_keluar (TIME), foto_keluar
  - deleted_at
  CATATAN: jam_keluar = '00:00:00' dan tanggal_keluar = '0000-00-00' artinya belum pulang.

  ## TABEL: ketidakhadiran
  Menyimpan pengajuan izin atau sakit pegawai.
  Kolom:
  - id (PK)
  - id_pegawai (FK → pegawai.id)
  - tipe_ketidakhadiran → ENUM: 'IZIN' atau 'SAKIT'
  - tanggal_mulai (DATE), tanggal_berakhir (DATE)
  - deskripsi, file
  - status_pengajuan → 'PENDING', 'APPROVED', atau 'REJECTED'
  - catatan_admin
  - deleted_at
  PENTING: Hanya yang status_pengajuan = 'APPROVED' yang dianggap valid/resmi.

  ## TABEL: hari_libur
  Menyimpan tanggal libur nasional atau cuti bersama.
  Kolom:
  - id (PK)
  - tanggal (DATE)
  - keterangan → nama hari libur
  - approved (TINYINT) → 1 = aktif/berlaku, 0 = tidak aktif
  - source → sumber data

  # RELASI ANTAR TABEL

  pegawai.id_jabatan          → jabatan.id
  pegawai.id_lokasi_presensi  → lokasi_presensi.id
  presensi.id_pegawai         → pegawai.id
  ketidakhadiran.id_pegawai   → pegawai.id

  # ATURAN QUERY — WAJIB DIIKUTI

  ## Alias tabel yang harus selalu digunakan:
  - pegawai          → pgw
  - jabatan          → jbt
  - lokasi_presensi  → lp
  - presensi         → p
  - ketidakhadiran   → k
  - hari_libur       → hl

  ## Soft delete — selalu tambahkan filter ini:
  - pgw.deleted_at IS NULL
  - p.deleted_at IS NULL
  - k.deleted_at IS NULL
  - jbt.deleted_at IS NULL
  - lp.deleted_at IS NULL

  ## Pencarian nama — JANGAN pakai slug, gunakan LIKE pada kolom nama:
  - Lokasi  → lp.nama_lokasi LIKE '%kata kunci%'
  - Jabatan → jbt.jabatan LIKE '%kata kunci%'
  - Pegawai → pgw.nama LIKE '%kata kunci%'

  ## Hanya SELECT yang diizinkan — dilarang INSERT, UPDATE, DELETE, DROP, ALTER.

  # BUSINESS RULES

  ## Status Kehadiran (urutan prioritas):
  1. HADIR    = ada record di presensi dengan DATE(tanggal_masuk) = tanggal yang dicari
  2. IZIN/SAKIT = ada di ketidakhadiran dengan status_pengajuan = 'APPROVED'
                dan tanggal yang dicari BETWEEN tanggal_mulai AND tanggal_berakhir
  3. LIBUR    = DAYOFWEEK(tanggal) IN (1, 7)  ← 1=Minggu, 7=Sabtu
                ATAU tanggal ada di hari_libur WHERE approved = 1
  4. ALPHA    = tidak ada presensi + tidak ada izin APPROVED + bukan libur

  ## Deteksi Alpha — WAJIB gunakan NOT EXISTS (BUKAN NOT IN):
  Alasan: NOT IN dengan subquery yang kosong/NULL akan return 0 hasil (bug MySQL klasik).

  ## Keterlambatan:
  TELAT jika presensi.jam_masuk > lokasi_presensi.jam_masuk
  (join melalui pgw.id_lokasi_presensi = lp.id)

  # CONTOH QUERY BENAR

  ## Berapa orang di kelas/jabatan tertentu? (misal: "XII-10" atau "Guru")
  QUERY: SELECT COUNT(*) AS total FROM pegawai pgw INNER JOIN jabatan jbt ON pgw.id_jabatan = jbt.id AND jbt.deleted_at IS NULL WHERE pgw.deleted_at IS NULL AND jbt.jabatan LIKE '%XII-10%'

  ## Siapa saja di kelas XII-10?
  QUERY: SELECT pgw.nama, pgw.nomor_induk, jbt.jabatan FROM pegawai pgw INNER JOIN jabatan jbt ON pgw.id_jabatan = jbt.id AND jbt.deleted_at IS NULL WHERE pgw.deleted_at IS NULL AND jbt.jabatan LIKE '%XII-10%' ORDER BY pgw.nama ASC

  ## Jam masuk lokasi tertentu? (pencarian nama fleksibel)
  QUERY: SELECT nama_lokasi, jam_masuk, jam_pulang FROM lokasi_presensi WHERE deleted_at IS NULL AND nama_lokasi LIKE '%Balikpapan%'

  ## Berapa orang alpha hari ini?
  QUERY: SELECT COUNT(*) AS total_alpha FROM pegawai pgw WHERE pgw.deleted_at IS NULL AND DAYOFWEEK(CURDATE()) NOT IN (1, 7) AND CURDATE() NOT IN (SELECT tanggal FROM hari_libur WHERE approved = 1) AND NOT EXISTS (SELECT 1 FROM presensi p WHERE p.id_pegawai = pgw.id AND DATE(p.tanggal_masuk) = CURDATE() AND p.deleted_at IS NULL) AND NOT EXISTS (SELECT 1 FROM ketidakhadiran k WHERE k.id_pegawai = pgw.id AND k.status_pengajuan = 'APPROVED' AND CURDATE() BETWEEN k.tanggal_mulai AND k.tanggal_berakhir AND k.deleted_at IS NULL)

  ## Siapa saja yang alpha hari ini?
  QUERY: SELECT pgw.nama, pgw.nomor_induk, jbt.jabatan FROM pegawai pgw INNER JOIN jabatan jbt ON pgw.id_jabatan = jbt.id AND jbt.deleted_at IS NULL WHERE pgw.deleted_at IS NULL AND DAYOFWEEK(CURDATE()) NOT IN (1, 7) AND CURDATE() NOT IN (SELECT tanggal FROM hari_libur WHERE approved = 1) AND NOT EXISTS (SELECT 1 FROM presensi p WHERE p.id_pegawai = pgw.id AND DATE(p.tanggal_masuk) = CURDATE() AND p.deleted_at IS NULL) AND NOT EXISTS (SELECT 1 FROM ketidakhadiran k WHERE k.id_pegawai = pgw.id AND k.status_pengajuan = 'APPROVED' AND CURDATE() BETWEEN k.tanggal_mulai AND k.tanggal_berakhir AND k.deleted_at IS NULL)

  ## Siapa telat hari ini? (dengan info kelas/jabatan)
  QUERY: SELECT pgw.nama, pgw.nomor_induk, jbt.jabatan, p.jam_masuk, lp.jam_masuk AS jam_masuk_kantor, TIMEDIFF(p.jam_masuk, lp.jam_masuk) AS selisih_telat FROM presensi p INNER JOIN pegawai pgw ON p.id_pegawai = pgw.id AND pgw.deleted_at IS NULL INNER JOIN jabatan jbt ON pgw.id_jabatan = jbt.id AND jbt.deleted_at IS NULL INNER JOIN lokasi_presensi lp ON pgw.id_lokasi_presensi = lp.id AND lp.deleted_at IS NULL WHERE DATE(p.tanggal_masuk) = CURDATE() AND p.deleted_at IS NULL AND p.jam_masuk > lp.jam_masuk ORDER BY selisih_telat DESC

  ## Alpha di kelas tertentu hari ini?
  QUERY: SELECT pgw.nama, pgw.nomor_induk, jbt.jabatan FROM pegawai pgw INNER JOIN jabatan jbt ON pgw.id_jabatan = jbt.id AND jbt.deleted_at IS NULL WHERE pgw.deleted_at IS NULL AND jbt.jabatan LIKE '%XII-10%' AND DAYOFWEEK(CURDATE()) NOT IN (1, 7) AND CURDATE() NOT IN (SELECT tanggal FROM hari_libur WHERE approved = 1) AND NOT EXISTS (SELECT 1 FROM presensi p WHERE p.id_pegawai = pgw.id AND DATE(p.tanggal_masuk) = CURDATE() AND p.deleted_at IS NULL) AND NOT EXISTS (SELECT 1 FROM ketidakhadiran k WHERE k.id_pegawai = pgw.id AND k.status_pengajuan = 'APPROVED' AND CURDATE() BETWEEN k.tanggal_mulai AND k.tanggal_berakhir AND k.deleted_at IS NULL)

  ## Rekap kehadiran per kelas/jabatan hari ini?
  QUERY: SELECT jbt.jabatan, COUNT(DISTINCT pgw.id) AS total_anggota, COUNT(DISTINCT p.id_pegawai) AS hadir, COUNT(DISTINCT pgw.id) - COUNT(DISTINCT p.id_pegawai) AS belum_hadir FROM pegawai pgw INNER JOIN jabatan jbt ON pgw.id_jabatan = jbt.id AND jbt.deleted_at IS NULL LEFT JOIN presensi p ON pgw.id = p.id_pegawai AND DATE(p.tanggal_masuk) = CURDATE() AND p.deleted_at IS NULL WHERE pgw.deleted_at IS NULL GROUP BY jbt.id, jbt.jabatan ORDER BY jbt.jabatan ASC

  ## 5 pengguna paling sering telat bulan ini?
  QUERY: SELECT pgw.nama, pgw.nomor_induk, jbt.jabatan, COUNT(*) AS total_telat FROM presensi p INNER JOIN pegawai pgw ON p.id_pegawai = pgw.id AND pgw.deleted_at IS NULL INNER JOIN jabatan jbt ON pgw.id_jabatan = jbt.id AND jbt.deleted_at IS NULL INNER JOIN lokasi_presensi lp ON pgw.id_lokasi_presensi = lp.id AND lp.deleted_at IS NULL WHERE MONTH(p.tanggal_masuk) = MONTH(CURDATE()) AND YEAR(p.tanggal_masuk) = YEAR(CURDATE()) AND p.deleted_at IS NULL AND p.jam_masuk > lp.jam_masuk GROUP BY pgw.id, pgw.nama, pgw.nomor_induk, jbt.jabatan ORDER BY total_telat DESC LIMIT 5
  EOT;
  }

    // -------------------------------------------------------------------------
    // GROQ API W/ MULTI-KEY ROTATION (POOLING)
    // -------------------------------------------------------------------------

    private function callGroq(array $messages): array
    {
        if (empty($this->groqApiKeys)) {
            return ['success' => false, 'message' => 'Sistem AI belum dikonfigurasi (API Key kosong).'];
        }

        $payload = json_encode([
            'model'       => $this->groqModel,
            'messages'    => $messages,
            'temperature' => 0.2, 
            'max_tokens'  => 1024,
        ]);

        $lastResponse = '';
        $lastWaitTime = 10;

        // Loop melalui semua API Key yang kita punya
        foreach ($this->groqApiKeys as $index => $apiKey) {
            $apiKey = trim($apiKey);
            $keyNumber = $index + 1;

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://api.groq.com/openai/v1/chat/completions',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 30, 
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey,
                ],
                CURLOPT_SSL_VERIFYPEER => false
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error    = curl_error($curl);
            curl_close($curl);

            if ($error) {
                log_message('error', "Groq cURL error pada Key #{$keyNumber}: " . $error);
                return ['success' => false, 'message' => 'Koneksi ke server AI terputus. Silakan coba lagi ya.'];
            }

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                if (!isset($data['choices'][0]['message']['content'])) {
                    log_message('error', 'Groq invalid response: ' . $response);
                    return ['success' => false, 'message' => 'AI memberikan respon yang tidak bisa dipahami.'];
                }

                // log_message('debug', "AI sukses merespon menggunakan Key #{$keyNumber}");
                
                return [
                    'success' => true,
                    'message' => $data['choices'][0]['message']['content'],
                ];
            }

            // --- HANDLING RATE LIMIT (429) ---
            if ($httpCode === 429) {
                log_message('warning', "Groq 429 Rate Limit pada Key #{$keyNumber}. Pindah ke key berikutnya...");
                $lastResponse = $response;
                
                if (preg_match('/try again in ([0-9]+(?:\.[0-9]+)?)s/i', $response, $matches)) {
                    $lastWaitTime = ceil((float)$matches[1]);
                }
                
                continue; 
            }

            // Jika error lain (misal 401 Unauthorized karena key salah), kita log dan coba key lain
            log_message('error', "Groq HTTP {$httpCode} pada Key #{$keyNumber}: {$response}");
            continue;
        }

        // =========================================================
        // JIKA KODE MENCAPAI TITIK INI = SEMUA API KEY SUDAH LIMIT/GAGAL
        // =========================================================
        
        log_message('error', 'SEMUA API KEY GROQ MENGALAMI RATE LIMIT / ERROR.');
        
        return [
            'success' => false, 
            'message' => "Waduh, antrean pertanyaan sedang sangat penuh nih. Boleh tunggu sekitar {$lastWaitTime} detik lalu coba tanya lagi? 🙏"
        ];
    }
    
    // -------------------------------------------------------------------------
    // EXECUTE QUERY
    // -------------------------------------------------------------------------

    private function executeQuery(string $sql, string $role, int $idPegawai): array
    {
        try {
            $sql = trim($sql);

            // Hapus trailing semicolon
            $sql = rtrim($sql, ';');

            // Cek hanya SELECT
            if (!preg_match('/^\s*SELECT\b/i', $sql)) {
                log_message('warning', 'AI Chat: Non-SELECT query blocked: ' . $sql);
                return ['success' => false, 'error' => 'Hanya query SELECT yang diizinkan'];
            }

            // Blacklist keyword berbahaya
            $blocked = ['INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'CREATE', 'TRUNCATE', 'EXEC', 'EXECUTE', 'GRANT', 'REVOKE'];
            foreach ($blocked as $kw) {
                if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $sql)) {
                    log_message('warning', "AI Chat: Blocked keyword '{$kw}' in query");
                    return ['success' => false, 'error' => "Keyword '{$kw}' tidak diizinkan"];
                }
            }

            // Security untuk pegawai: pastikan ada filter id_pegawai
            if ($role === 'pegawai') {
                $pattern = '/\bid_pegawai\s*=\s*' . $idPegawai . '\b/i';
                if (!preg_match($pattern, $sql)) {
                    log_message('warning', "AI Chat: Pegawai query missing id_pegawai={$idPegawai} filter. SQL: {$sql}");
                    return ['success' => false, 'error' => 'Query tidak menyertakan filter id_pegawai yang valid'];
                }
            }

            // log_message('debug', 'AI GENERATED SQL: ' . $sql);
            $query = $this->db->query($sql);

            if ($query === false) {
                $dbError = $this->db->error();
                log_message('error', 'AI Chat DB error: ' . json_encode($dbError) . ' | SQL: ' . $sql);
                return ['success' => false, 'error' => 'Database error: ' . ($dbError['message'] ?? 'Unknown')];
            }

            return ['success' => true, 'data' => $query->getResultArray()];

        } catch (\Exception $e) {
            log_message('error', 'AI Chat executeQuery exception: ' . $e->getMessage() . ' | SQL: ' . $sql);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // -------------------------------------------------------------------------
    // FORMAT DATA
    // -------------------------------------------------------------------------

    private function formatDataForAI(array $data): string
    {
        if (empty($data)) {
            return 'Tidak ada data ditemukan.';
        }

        // Single value (COUNT, SUM, dll)
        if (count($data) === 1 && count($data[0]) === 1) {
            $key   = array_key_first($data[0]);
            $value = $data[0][$key];
            return "Hasil ({$key}): {$value}";
        }

        $totalBaris = count($data);
        $limit = 30;
        
        $formatted = 'Total baris data asli: ' . $totalBaris . "\n\n";
        
        foreach ($data as $i => $row) {
            // Hentikan iterasi jika sudah mencapai limit
            if ($i >= $limit) {
                $sisa = $totalBaris - $limit;
                $formatted .= "... (dan {$sisa} data lainnya tidak ditampilkan agar respon lebih cepat)\n";
                break;
            }

            $formatted .= 'Baris ' . ($i + 1) . ":\n";
            foreach ($row as $col => $val) {
                $formatted .= "  {$col}: {$val}\n";
            }
            $formatted .= "\n";
        }

        return $formatted;
    }

    // -------------------------------------------------------------------------
    // HELPER
    // -------------------------------------------------------------------------

    private function getDayNameId(int $dayNum): string
    {
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
        return $days[$dayNum] ?? 'Tidak diketahui';
    }
}