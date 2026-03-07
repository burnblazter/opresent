<?php
// \app\Controllers\Kiosk.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\Controllers;

use App\Models\FaceDescriptorModel;
use App\Models\UsersModel;
use App\Models\PegawaiModel;
use App\Models\PresensiModel;
use App\Models\LokasiPresensiModel;

class Kiosk extends BaseController
{
    protected $usersModel;
    protected $pegawaiModel;
    protected $presensiModel;
    protected $lokasiModel;
    protected $faceDescriptorModel;

    private const MIN_FACE_SIMILARITY = 0.62;
    private const MAX_IMAGE_SIZE = 5242880; // 5MB
    private const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png'];
    private const IDEMPOTENCY_WINDOW_SECONDS = 30;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->presensiModel = new PresensiModel();
        $this->lokasiModel = new LokasiPresensiModel();
        $this->faceDescriptorModel = new FaceDescriptorModel();
        
        helper(['telegram', 'text']);
    }

    /**
     * Halaman utama kiosk - fullscreen scanner
     */
    public function index()
    {
        // Validasi: hanya user dengan group kiosk
        if (!in_groups('kiosk')) {
            return redirect()->to(base_url())->with('gagal', 'Akses ditolak. Halaman ini khusus operator kiosk.');
        }

        // Ambil data operator kiosk untuk mendapatkan lokasi presensi
        $operator = $this->usersModel->getUserInfo(user_id());
        
        if (!$operator) {
            return redirect()->to(base_url('login'))->with('gagal', 'Data operator tidak ditemukan');
        }

        // Ambil lokasi presensi berdasarkan id_lokasi_presensi operator
        $lokasi = $this->lokasiModel->find($operator->id_lokasi_presensi);
        
        if (!$lokasi) {
            return redirect()->to(base_url())->with('gagal', 'Lokasi presensi operator tidak ditemukan. Hubungi admin.');
        }

        // Set timezone sesuai lokasi
        if (in_array($lokasi->zona_waktu, timezone_identifiers_list())) {
            date_default_timezone_set($lokasi->zona_waktu);
        }

        $data = [
            'title' => 'Kiosk Mode - Presensi Mandiri',
            'operator' => $operator,
            'lokasi' => $lokasi,
        ];

        return view('kiosk/index', $data);
    }


      /**
     * API: Cari pegawai berdasarkan nomor_induk dari QR code
     */
    public function cariPegawai()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }

        if (!in_groups('kiosk')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $nomor_induk = $this->request->getPost('nomor_induk');
        
        if (empty($nomor_induk)) {
            return $this->response->setJSON([
                'success' => false,
                'csrf_hash' => csrf_hash(), // Update token
                'message' => 'Nomor induk tidak boleh kosong'
            ]);
        }

        $nomor_induk = strip_tags(trim($nomor_induk));

        // Cari pegawai dan paksa menjadi Object
        $pegawai_data = $this->pegawaiModel->where('nomor_induk', $nomor_induk)->where('deleted_at', null)->first();

        if (!$pegawai_data) {
            return $this->response->setJSON([
                'success' => false,
                'csrf_hash' => csrf_hash(), // Update token
                'message' => 'Pegawai dengan nomor induk "' . htmlspecialchars($nomor_induk, ENT_QUOTES, 'UTF-8') . '" tidak ditemukan'
            ]);
        }

        $pegawai = (object) $pegawai_data; // FIX: Pastikan jadi object

        // Ambil semua face descriptors pegawai ini
        $descriptors = $this->faceDescriptorModel->getDescriptorsByPegawai($pegawai->id);
        
        $descriptorArray = [];
        foreach ($descriptors as $desc) {
            $descriptorData = json_decode($desc->descriptor, true);
            if (!is_array($descriptorData) || count($descriptorData) < 100) continue;
            
            $descriptorArray[] = [
                'id' => $desc->id,
                'descriptor' => $descriptorData,
                'label' => $desc->label ?? 'Default'
            ];
        }

        // Ambil lokasi presensi dan paksa jadi object
        $lokasi_data = $this->lokasiModel->find($pegawai->id_lokasi_presensi);
        if (!$lokasi_data) {
            return $this->response->setJSON([
                'success' => false,
                'csrf_hash' => csrf_hash(), // Update token
                'message' => 'Lokasi presensi pegawai tidak ditemukan'
            ]);
        }
        $lokasi = (object) $lokasi_data;

        // FIX: Path foto disesuaikan dengan folder sistem existing Anda
        $foto = !empty($pegawai->foto) ? $pegawai->foto : 'default.jpg';

        return $this->response->setJSON([
            'success' => true,
            'csrf_hash' => csrf_hash(), // Kirim token baru agar bisa scan berkali-kali
            'data' => [
                'id_pegawai' => $pegawai->id,
                'nomor_induk' => $pegawai->nomor_induk,
                'nama' => $pegawai->nama,
                'foto' => base_url('assets/img/user_profile/' . $foto), // FIX Path
                'descriptors' => $descriptorArray,
                'jam_masuk' => $lokasi->jam_masuk,
                'jam_pulang' => $lokasi->jam_pulang,
                'zona_waktu' => $lokasi->zona_waktu
            ]
        ]);
    }

    /**
     * Simpan presensi dari kiosk
     */
    public function simpanPresensi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }

        // Validasi face recognition
        $face_verified = $this->request->getPost('face_verified');
        $face_similarity = filter_var($this->request->getPost('face_similarity'), FILTER_VALIDATE_FLOAT);
        
        if ($face_verified !== 'true' || $face_similarity === false || $face_similarity < self::MIN_FACE_SIMILARITY) {
            return $this->response->setJSON(['success' => false, 'csrf_hash' => csrf_hash(), 'message' => 'Verifikasi wajah gagal. Similarity: ' . ($face_similarity ?: 'N/A')]);
        }

        $id_pegawai = filter_var($this->request->getPost('id_pegawai'), FILTER_VALIDATE_INT);
        $mode = $this->request->getPost('mode');
        
        if (!$id_pegawai || !in_array($mode, ['masuk', 'keluar'])) {
            return $this->response->setJSON(['success' => false, 'csrf_hash' => csrf_hash(), 'message' => 'Data tidak valid']);
        }

        $pegawai_data = $this->pegawaiModel->find($id_pegawai);
        if (!$pegawai_data) return $this->response->setJSON(['success' => false, 'csrf_hash' => csrf_hash(), 'message' => 'Data pegawai tidak ditemukan']);
        $pegawai = (object) $pegawai_data;

        $lokasi_data = $this->lokasiModel->find($pegawai->id_lokasi_presensi);
        if (!$lokasi_data) return $this->response->setJSON(['success' => false, 'csrf_hash' => csrf_hash(), 'message' => 'Lokasi presensi tidak ditemukan']);
        $lokasi = (object) $lokasi_data;

        // Set timezone
        if (in_array($lokasi->zona_waktu, timezone_identifiers_list())) {
            date_default_timezone_set($lokasi->zona_waktu);
        }

        $tanggal_sekarang = date('Y-m-d');
        $jam_sekarang = date('H:i:s');
        $jam_pulang_target = strtotime($lokasi->jam_pulang);
        $jam_current = strtotime($jam_sekarang);

        // Cek existing presensi hari ini
        $presensi_hari_ini = $this->presensiModel->where(['id_pegawai' => $id_pegawai, 'tanggal_masuk' => $tanggal_sekarang])->first();

        if ($mode === 'masuk') {
            if ($presensi_hari_ini) {
                $jam_masuk_existing = is_object($presensi_hari_ini) ? $presensi_hari_ini->jam_masuk : $presensi_hari_ini['jam_masuk'];
                $selisih_detik = abs($jam_current - strtotime($jam_masuk_existing));
                
                if ($selisih_detik < self::IDEMPOTENCY_WINDOW_SECONDS) {
                    return $this->response->setJSON([
                        'success' => true,
                        'csrf_hash' => csrf_hash(),
                        'duplicate' => true,
                        'message' => 'Presensi masuk sudah tercatat',
                        'data' => [
                            'nama' => $pegawai->nama, 'jam' => $jam_masuk_existing,
                            'status' => $this->getPesanLayarKiosk('masuk', $jam_masuk_existing, $lokasi)
                        ]
                    ]);
                }
                return $this->response->setJSON(['success' => false, 'csrf_hash' => csrf_hash(), 'message' => 'Anda sudah melakukan presensi masuk hari ini']);
            }
        } else { 
            if (!$presensi_hari_ini) {
                return $this->response->setJSON(['success' => false, 'csrf_hash' => csrf_hash(), 'message' => 'Anda belum melakukan presensi masuk hari ini']);
            }

            $tgl_keluar_existing = is_object($presensi_hari_ini) ? $presensi_hari_ini->tanggal_keluar : $presensi_hari_ini['tanggal_keluar'];
            $jam_keluar_existing = is_object($presensi_hari_ini) ? $presensi_hari_ini->jam_keluar : $presensi_hari_ini['jam_keluar'];

            if ($tgl_keluar_existing !== '0000-00-00' && $tgl_keluar_existing !== null) {
                $selisih_detik = abs($jam_current - strtotime($jam_keluar_existing));
                if ($selisih_detik < self::IDEMPOTENCY_WINDOW_SECONDS) {
                    return $this->response->setJSON([
                        'success' => true, 'csrf_hash' => csrf_hash(), 'duplicate' => true, 'message' => 'Presensi keluar sudah tercatat',
                        'data' => [
                            'nama' => $pegawai->nama, 'jam' => $jam_keluar_existing, 
                            'status' => $this->getPesanLayarKiosk('keluar', $jam_keluar_existing, $lokasi)
                        ]
                    ]);
                }
                return $this->response->setJSON(['success' => false, 'csrf_hash' => csrf_hash(), 'message' => 'Anda sudah melakukan presensi keluar hari ini']);
            }

            if ($jam_current < $jam_pulang_target) {
                $selisih_menit = round(($jam_pulang_target - $jam_current) / 60);
                return $this->response->setJSON(['success' => false, 'csrf_hash' => csrf_hash(), 'message' => "Belum waktunya pulang. Tunggu {$selisih_menit} menit lagi."]);
            }
        }

        // Proses foto
        $foto = $this->request->getPost('image');
        $foto = base64_decode(preg_replace('/^data:image\/(jpeg|png);base64,/', '', $foto), true);
        
        $folder_name = ($mode === 'masuk') ? 'masuk' : 'keluar';
        $folder = FCPATH . 'assets/img/foto_presensi/' . $folder_name . '/';
        if (!is_dir($folder)) mkdir($folder, 0750, true);

        $nama_foto = $folder_name . '-' . date('Y-m-d-His') . '-' . preg_replace('/[^a-zA-Z0-9]/', '', $pegawai->nomor_induk) . '.png';
        $file_path = realpath($folder) . DIRECTORY_SEPARATOR . $nama_foto;
        file_put_contents($file_path, $foto);

        try {
            $this->presensiModel->db->transStart();
            
            $id_presensi_update = is_object($presensi_hari_ini) ? $presensi_hari_ini->id : $presensi_hari_ini['id'] ?? null;

            if ($mode === 'masuk') {
                $this->presensiModel->save(['id_pegawai' => $id_pegawai, 'tanggal_masuk' => $tanggal_sekarang, 'jam_masuk' => $jam_sekarang, 'foto_masuk' => $nama_foto]);
            } else {
                $this->presensiModel->save(['id' => $id_presensi_update, 'tanggal_keluar' => $tanggal_sekarang, 'jam_keluar' => $jam_sekarang, 'foto_keluar' => $nama_foto]);
            }
            $this->presensiModel->db->transComplete();
        } catch (\Exception $e) {
            if (file_exists($file_path)) unlink($file_path);
            return $this->response->setJSON(['success' => false, 'csrf_hash' => csrf_hash(), 'message' => 'Gagal menyimpan presensi']);
        }

        // Kirim notif Telegram ke Orang Tua (Format Resmi)
        try { 
            $this->sendTelegramNotification($pegawai, $mode, $jam_sekarang, $lokasi); 
        } catch (\Exception $e) {}

        // Dapatkan Pesan Lucu khusus Layar Kiosk
        $status_text = $this->getPesanLayarKiosk($mode, $jam_sekarang, $lokasi);

        return $this->response->setJSON([
            'success' => true,
            'csrf_hash' => csrf_hash(),
            'message' => 'Presensi ' . $mode . ' berhasil disimpan',
            'data' => [
                'nama' => $pegawai->nama, 'nomor_induk' => $pegawai->nomor_induk,
                'jam' => $jam_sekarang, 'status' => $status_text,
                'foto' => base_url('assets/img/foto_presensi/' . $folder_name . '/' . $nama_foto)
            ]
        ]);
    }

    /**
     * Helper: Pesan Keterlambatan (Khusus Layar Kiosk)
     */
    private function getPesanLayarKiosk($mode, $jam_sekarang, $lokasi)
    {
        if ($mode === 'masuk') {
            $timestamp_masuk = strtotime($jam_sekarang);
            $timestamp_target = strtotime($lokasi->jam_masuk);

            if ($timestamp_masuk > $timestamp_target) {
                $selisih = $timestamp_masuk - $timestamp_target;
                $jam_lat = floor($selisih / 3600);
                $menit_lat = floor(($selisih % 3600) / 60);
                
                $pesan_masuk = [
                    "Terlambat %d Jam %d Menit. Gerbang sekolah hampir karatan menunggu Anda.",
                    "Keterlambatan %d Jam %d Menit tercatat. Apakah jalanan ke sekolah dipenuhi rintangan naga?",
                    "Anda tiba %d Jam %d Menit lebih lambat. Guru piket sudah hafal langkah kaki Anda.",
                    "Waktu keterlambatan: %d Jam %d Menit. Alarm pagi sepertinya butuh diganti dengan sirine.",
                    "Masuk terlambat %d Jam %d Menit. KBM sudah berjalan setengah bab, selamat mengejar.",
                    "Terlambat %d Jam %d Menit. Kami apresiasi kedatangan Anda, walau matahari sudah tinggi.",
                    "Tercatat lambat %d Jam %d Menit. Pastikan alasan Anda lebih masuk akal dari 'ban bocor'.",
                    "Keterlambatan: %d Jam %d Menit. Semoga esok hari gravitasi kasur Anda sedikit berkurang.",
                    "Anda masuk terlambat %d Jam %d Menit. Buku poin menanti nama Anda dengan antusias.",
                    "Terlambat %d Jam %d Menit. Silakan bergegas, sebelum bel istirahat berbunyi."
                ];
                return sprintf($pesan_masuk[array_rand($pesan_masuk)], $jam_lat, $menit_lat);
            }
            return 'Tepat Waktu';
        } else {
            $jam_pulang_target = strtotime($lokasi->jam_pulang);
            $jam_current = strtotime($jam_sekarang);
            
            if ($jam_current > $jam_pulang_target) {
                $selisih = $jam_current - $jam_pulang_target;
                $jam_lebih = floor($selisih / 3600);
                $menit_lebih = floor(($selisih % 3600) / 60);
                
                if ($jam_lebih > 0 || $menit_lebih > 0) {
                    $pesan_pulang = [
                        "Pulang lambat %d Jam %d Menit. Apakah Anda sedang membangun candi di kelas?",
                        "Tercatat pulang lambat %d Jam %d Menit. Satpam sekolah berterima kasih atas temannya malam ini.",
                        "Anda pulang %d Jam %d Menit dari jadwal. Jangan lupa sekolah ini bukan kos-kosan Anda.",
                        "Overtime %d Jam %d Menit. Penjaga gerbang sudah bersiap mengunci Anda dari dalam.",
                        "Pulang telat %d Jam %d Menit. Apakah Anda kandidat ketua OSIS seumur hidup?",
                        "Lambat %d Jam %d Menit saat pulang. Dedikasi Anda melampaui batas kewajaran seorang siswa.",
                        "Waktu tambahan: %d Jam %d Menit. Hati-hati, penunggu lorong sekolah mulai mengajak kenalan.",
                        "Pulang mundur %d Jam %d Menit. Kami harap Anda tidak sedang membuat markas rahasia di gudang.",
                        "Anda pulang lambat %d Jam %d Menit. Orang tua Anda mungkin sudah mulai mencari.",
                        "Ekstra waktu %d Jam %d Menit. Jika sekolah memberi gaji lembur, Anda sudah kaya raya."
                    ];
                    return sprintf($pesan_pulang[array_rand($pesan_pulang)], $jam_lebih, $menit_lebih);
                }
            }
            return 'Tepat Waktu';
        }
    }

    /**
     * Helper: Kirim notifikasi Telegram
     */
    private function sendTelegramNotification($pegawai, $mode, $jam, $lokasi)
    {
        if ($mode === 'keluar') {
            return;
        }

        $tanggal_indo = format_tanggal_indo(date('Y-m-d'));
        
        $jam_jadwal = $lokasi->jam_masuk;
        $timestamp_scan = strtotime($jam);
        $timestamp_jadwal = strtotime($jam_jadwal);
        
        if ($timestamp_scan > $timestamp_jadwal) {
            $status_text = 'TERLAMBAT ⚠️';
            $selisih = $timestamp_scan - $timestamp_jadwal;
            $jam_lat = floor($selisih / 3600);
            $menit_lat = floor(($selisih % 3600) / 60);
            $keterangan_waktu = "⏳ <b>Terlambat:</b> {$jam_lat} Jam {$menit_lat} Menit";
        } else {
            $status_text = 'TEPAT WAKTU ✅';
            $selisih = $timestamp_jadwal - $timestamp_scan;
            $menit_awal = floor(($selisih % 3600) / 60);
            $keterangan_waktu = "✨ <b>Info:</b> Datang {$menit_awal} menit lebih awal";
        }
        
        $pesan  = "<b>🟢 PresenSi [Kiosk]</b>\n";
        $pesan .= "-----------------------------------\n";
        $pesan .= "👤 <b>Nama:</b> " . htmlspecialchars($pegawai->nama, ENT_QUOTES, 'UTF-8') . "\n";
        $pesan .= "🆔 <b>Nomor Induk:</b> " . htmlspecialchars($pegawai->nomor_induk, ENT_QUOTES, 'UTF-8') . "\n";
        $pesan .= "🏢 <b>Unit:</b> " . htmlspecialchars($detail_pegawai->jabatan ?? 'Tidak ada unit', ENT_QUOTES, 'UTF-8') . "\n";
        $pesan .= "📅 <b>Tanggal:</b> " . htmlspecialchars($tanggal_indo, ENT_QUOTES, 'UTF-8') . "\n";
        $pesan .= "-----------------------------------\n";
        $pesan .= "⏰ <b>Jadwal Masuk:</b> " . $jam_jadwal . "\n";
        $pesan .= "🕐 <b>Waktu Scan:</b> " . htmlspecialchars($jam, ENT_QUOTES, 'UTF-8') . "\n";
        $pesan .= "-----------------------------------\n";
        $pesan .= $keterangan_waktu . "\n";
        $pesan .= "<b>Status:</b> " . $status_text;
        
        send_telegram_notification($pesan);
    }
}