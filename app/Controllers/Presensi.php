<?php

namespace App\Controllers;

use App\Models\FaceDescriptorModel;
use App\Models\HariLiburModel;
use App\Models\KetidakhadiranModel;
use DateTime;
use App\Models\UsersModel;
use App\Models\PegawaiModel;
use App\Models\PresensiModel;
use App\Models\LokasiPresensiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Presensi extends BaseController
{
    protected $usersModel;
    protected $lokasiModel;
    protected $presensiModel;
    protected $pegawaiModel;
    protected $ketidakhadiranModel;
    protected $hariLiburModel;
    protected $faceDescriptorModel;

    // PERBAIKAN: Konstanta untuk validasi
    private const MAX_DISTANCE_METERS = 10000; // Maksimal 10km untuk mencegah abuse
    private const MIN_FACE_SIMILARITY = 0.62; // Threshold minimum untuk face recognition
    private const MAX_IMAGE_SIZE = 5242880; // 5MB dalam bytes
    private const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png'];
    
    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->lokasiModel = new LokasiPresensiModel();
        $this->presensiModel = new PresensiModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->ketidakhadiranModel = new KetidakhadiranModel(); 
        $this->hariLiburModel = new HariLiburModel();
        $this->faceDescriptorModel = new FaceDescriptorModel();

        helper(['telegram', 'text']);
    }

    /**
     * PERBAIKAN: Fungsi helper untuk validasi koordinat
     */
    private function validateCoordinates($latitude, $longitude): bool
    {
        if (empty($latitude) || empty($longitude)) {
            return false;
        }

        $lat = filter_var($latitude, FILTER_VALIDATE_FLOAT);
        $lng = filter_var($longitude, FILTER_VALIDATE_FLOAT);

        if ($lat === false || $lng === false) {
            return false;
        }

        // Validasi range koordinat
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return false;
        }

        return true;
    }

    /**
     * PERBAIKAN: Fungsi helper untuk menghitung jarak dengan validasi
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): ?float
    {
        try {
            // Validasi input
            $lat1 = floatval($lat1);
            $lon1 = floatval($lon1);
            $lat2 = floatval($lat2);
            $lon2 = floatval($lon2);

            $perbedaan_koordinat = $lon1 - $lon2;
            
            $jarak = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + 
                     cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
                     cos(deg2rad($perbedaan_koordinat));
            
            // Cek untuk menghindari domain error pada acos
            if ($jarak > 1) $jarak = 1;
            if ($jarak < -1) $jarak = -1;
            
            $jarak = acos($jarak);
            $jarak = rad2deg($jarak);
            $mil = $jarak * 60 * 1.1515;
            $km = $mil * 1.609344;
            $meter = $km * 1000;

            // Validasi hasil perhitungan
            if ($meter < 0 || $meter > self::MAX_DISTANCE_METERS) {
                log_message('warning', 'Jarak tidak valid: ' . $meter);
                return null;
            }

            return $meter;
        } catch (\Exception $e) {
            log_message('error', 'Error menghitung jarak: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * PERBAIKAN: Sanitasi nama file
     */
    private function sanitizeFilename($filename): string
    {
        // Hapus karakter berbahaya
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        // Batasi panjang filename
        $filename = substr($filename, 0, 255);
        return $filename;
    }

    public function presensiMasuk()
    {
        // PERBAIKAN: Validasi user terautentikasi
        if (!logged_in()) {
            return redirect()->to(base_url('login'));
        }

        $user_profile = $this->usersModel->getUserInfo(user_id());
        
        if (!$user_profile) {
            session()->setFlashdata('gagal', 'Data user tidak ditemukan');
            return redirect()->to(base_url());
        }

        // PERBAIKAN: Validasi dan sanitasi input
        $latitude_pegawai = $this->request->getVar('latitude_pegawai');
        $longitude_pegawai = $this->request->getVar('longitude_pegawai');
        $latitude_kantor = $this->request->getVar('latitude_kantor');
        $longitude_kantor = $this->request->getVar('longitude_kantor');
        $radius = $this->request->getVar('radius');
        $zona_waktu = $this->request->getVar('zona_waktu');
        $tanggal_masuk = $this->request->getVar('tanggal_masuk');
        $jam_masuk = $this->request->getVar('jam_masuk');

        // Validasi koordinat pegawai
        if (!$this->validateCoordinates($latitude_pegawai, $longitude_pegawai)) {
            session()->setFlashdata('gagal', 'Lokasi Anda tidak terdeteksi. Mohon aktifkan fitur lokasi di perangkat Anda dan refresh halaman ini.');
            return redirect()->to(base_url());
        }

        // Validasi koordinat kantor
        if (!$this->validateCoordinates($latitude_kantor, $longitude_kantor)) {
            session()->setFlashdata('gagal', 'Lokasi presensi tidak valid. Mohon hubungi Admin.');
            return redirect()->to(base_url());
        }

        // PERBAIKAN: Validasi radius
        $radius = filter_var($radius, FILTER_VALIDATE_FLOAT);
        if ($radius === false || $radius <= 0 || $radius > 10000) {
            session()->setFlashdata('gagal', 'Radius presensi tidak valid');
            return redirect()->to(base_url());
        }

        // Hitung jarak dengan fungsi yang sudah diperbaiki
        $meter = $this->calculateDistance(
            $latitude_pegawai, 
            $longitude_pegawai,
            $latitude_kantor, 
            $longitude_kantor
        );

        if ($meter === null) {
            session()->setFlashdata('gagal', 'Gagal menghitung jarak. Silakan coba lagi.');
            return redirect()->to(base_url());
        }

        if ($meter > $radius) {
            session()->setFlashdata('gagal', 'Anda berada di luar area kantor (' . number_format($meter, 0) . ' meter dari kantor)');
            return redirect()->to(base_url());
        }

        // PERBAIKAN: Validasi format tanggal dan jam
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_masuk)) {
            session()->setFlashdata('gagal', 'Format tanggal tidak valid');
            return redirect()->to(base_url());
        }

        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $jam_masuk)) {
            session()->setFlashdata('gagal', 'Format jam tidak valid');
            return redirect()->to(base_url());
        }

        $data = [
            'title' => 'Presensi Masuk',
            'user_profile' => $user_profile,
            'latitude_pegawai' => $latitude_pegawai,
            'longitude_pegawai' => $longitude_pegawai,
            'latitude_kantor' => $latitude_kantor,
            'longitude_kantor' => $longitude_kantor,
            'radius' => $radius,
            'tanggal_masuk' => $tanggal_masuk,
            'jam_masuk' => $jam_masuk,
        ];

        return view('presensi/presensi_masuk', $data);
    }

    public function simpanPresensiMasuk()
    {
        // PERBAIKAN: Validasi CSRF token (sudah built-in di CodeIgniter 4)
        if (!$this->validate(['csrf_test_name' => 'required'])) {
            session()->setFlashdata('gagal', 'Token keamanan tidak valid');
            return redirect()->to(base_url());
        }

        // PERBAIKAN: Validasi user terautentikasi
        if (!logged_in()) {
            session()->setFlashdata('gagal', 'Anda harus login terlebih dahulu');
            return redirect()->to(base_url('login'));
        }

        // PERBAIKAN: Validasi face recognition dengan threshold yang lebih ketat
        $face_verified = $this->request->getPost('face_verified');
        $face_similarity = $this->request->getPost('face_similarity');
        
        // Validasi tipe data
        $face_similarity = filter_var($face_similarity, FILTER_VALIDATE_FLOAT);
        
        if ($face_verified !== 'true' || $face_similarity === false || $face_similarity < self::MIN_FACE_SIMILARITY) {
            log_message('warning', 'Face verification failed. Similarity: ' . $face_similarity);
            session()->setFlashdata('gagal', 'Verifikasi wajah gagal. Pastikan wajah Anda terlihat jelas dan sudah terdaftar.');
            return redirect()->to(base_url());
        }

        // PERBAIKAN: Validasi dan sanitasi foto
        $foto = $this->request->getPost('image-cam');
        if (empty($foto)) {
            session()->setFlashdata('gagal', 'Foto presensi tidak boleh kosong');
            return redirect()->to(base_url());
        }

        // Validasi format base64
        if (!preg_match('/^data:image\/(jpeg|png);base64,/', $foto)) {
            session()->setFlashdata('gagal', 'Format foto tidak valid');
            return redirect()->to(base_url());
        }

        // Decode foto
        $foto = preg_replace('/^data:image\/(jpeg|png);base64,/', '', $foto);
        $foto = base64_decode($foto, true);
        
        if ($foto === false) {
            session()->setFlashdata('gagal', 'Format foto tidak valid');
            return redirect()->to(base_url());
        }

        // PERBAIKAN: Validasi ukuran foto
        if (strlen($foto) > self::MAX_IMAGE_SIZE) {
            session()->setFlashdata('gagal', 'Ukuran foto terlalu besar (maksimal 5MB)');
            return redirect()->to(base_url());
        }

        // PERBAIKAN: Validasi tipe MIME foto
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($foto);
        
        if (!in_array($mime, self::ALLOWED_IMAGE_TYPES)) {
            session()->setFlashdata('gagal', 'Tipe foto tidak diizinkan');
            return redirect()->to(base_url());
        }

        // PERBAIKAN: Sanitasi username dan buat nama file yang aman
        $username = $this->request->getPost('username');
        $username = $this->sanitizeFilename($username);
        
        if (empty($username)) {
            session()->setFlashdata('gagal', 'Username tidak valid');
            return redirect()->to(base_url());
        }

        $nama_foto = 'masuk-' . date('Y-m-d-His') . '-' . $username . '.png';
        $nama_foto = $this->sanitizeFilename($nama_foto);
        
        $folder = FCPATH . 'assets/img/foto_presensi/masuk/';
        
        // PERBAIKAN: Validasi dan buat folder dengan permission yang aman
        if (!is_dir($folder)) {
            if (!mkdir($folder, 0750, true)) {
                log_message('error', 'Gagal membuat folder: ' . $folder);
                session()->setFlashdata('gagal', 'Gagal membuat folder penyimpanan');
                return redirect()->to(base_url());
            }
        }
        
        // PERBAIKAN: Validasi path untuk mencegah path traversal
        $file_path = realpath($folder) . DIRECTORY_SEPARATOR . $nama_foto;
        
        if (strpos($file_path, realpath($folder)) !== 0) {
            log_message('error', 'Path traversal attempt detected: ' . $file_path);
            session()->setFlashdata('gagal', 'Path file tidak valid');
            return redirect()->to(base_url());
        }
        
        if (!file_put_contents($file_path, $foto)) {
            log_message('error', 'Gagal menulis file: ' . $file_path);
            session()->setFlashdata('gagal', 'Gagal menyimpan foto presensi masuk');
            return redirect()->to(base_url());
        }

        // PERBAIKAN: Validasi ID pegawai
        $id_pegawai = $this->request->getPost('id_pegawai');
        $id_pegawai = filter_var($id_pegawai, FILTER_VALIDATE_INT);
        
        if ($id_pegawai === false || $id_pegawai <= 0) {
            if (file_exists($file_path)) unlink($file_path);
            session()->setFlashdata('gagal', 'ID pegawai tidak valid');
            return redirect()->to(base_url());
        }

        // PERBAIKAN: Validasi tanggal dan jam
        $tanggal_masuk = $this->request->getPost('tanggal_masuk');
        $jam_masuk = $this->request->getPost('jam_masuk');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_masuk)) {
            if (file_exists($file_path)) unlink($file_path);
            session()->setFlashdata('gagal', 'Format tanggal tidak valid');
            return redirect()->to(base_url());
        }

        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $jam_masuk)) {
            if (file_exists($file_path)) unlink($file_path);
            session()->setFlashdata('gagal', 'Format jam tidak valid');
            return redirect()->to(base_url());
        }

        // PERBAIKAN: Cek apakah sudah presensi hari ini
        $cek_presensi = $this->presensiModel->where([
            'id_pegawai' => $id_pegawai,
            'tanggal_masuk' => $tanggal_masuk
        ])->first();

        if ($cek_presensi) {
            if (file_exists($file_path)) unlink($file_path);
            session()->setFlashdata('gagal', 'Anda sudah melakukan presensi masuk hari ini');
            return redirect()->to(base_url());
        }

        try {
            // PERBAIKAN: Gunakan transaction untuk data consistency
            $this->presensiModel->db->transStart();
            
            $this->presensiModel->save([
                'id_pegawai' => $id_pegawai,
                'tanggal_masuk' => $tanggal_masuk,
                'jam_masuk' => $jam_masuk,
                'foto_masuk' => $nama_foto,
            ]);
            
            $this->presensiModel->db->transComplete();
            
            if ($this->presensiModel->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
        } catch (\Exception $e) {
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            log_message('error', 'Gagal simpan presensi: ' . $e->getMessage());
            session()->setFlashdata('gagal', 'Gagal menyimpan presensi');
            return redirect()->to(base_url());
        }

        // PERBAIKAN: Telegram notifikasi dengan sanitasi data
        try {
            // 1. Ambil Data Pegawai (Untuk Nama & NIP)
            $detail_pegawai = $this->pegawaiModel->getPegawaiById($id_pegawai);
            
            // 2. Ambil Data User & Lokasi (Untuk Jam Jadwal)
            $user_info = $this->usersModel->getUserInfo(user_id());
            $lokasi_presensi = $this->lokasiModel->where('nama_lokasi', $user_info->lokasi_presensi)->first();
            
            if ($detail_pegawai && $lokasi_presensi) {
                // Ambil jam masuk dari tabel LOKASI
                $jam_jadwal = $lokasi_presensi->jam_masuk; 
                
                $timestamp_masuk = strtotime($jam_masuk); // Waktu scan (aktual)
                $timestamp_jadwal = strtotime($jam_jadwal); // Waktu jadwal (target)
                
                $keterangan_waktu = "";
                $status_text = "";
                
                // Hitung Selisih
                if ($timestamp_masuk > $timestamp_jadwal) {
                    $status_text = 'TERLAMBAT ⚠️';
                    
                    // Hitung keterlambatan
                    $selisih = $timestamp_masuk - $timestamp_jadwal;
                    $jam_lat = floor($selisih / 3600);
                    $menit_lat = floor(($selisih % 3600) / 60);
                    
                    $keterangan_waktu = "⏳ <b>Terlambat:</b> {$jam_lat} Jam {$menit_lat} Menit";
                } else {
                    $status_text = 'TEPAT WAKTU ✅';
                    
                    // (Opsional) Hitung datang lebih awal berapa menit
                    $selisih = $timestamp_jadwal - $timestamp_masuk;
                    $menit_awal = floor(($selisih % 3600) / 60);
                    
                    $keterangan_waktu = "✨ <b>Info:</b> Datang {$menit_awal} menit lebih awal";
                }
                
                $tanggal_indo = format_tanggal_indo($tanggal_masuk);
                
                // Format Pesan Telegram
                $pesan  = "<b>🟢 LAPORAN KEDATANGAN SISWA</b>\n";
                $pesan .= "-----------------------------------\n";
                $pesan .= "👤 <b>Nama:</b> " . htmlspecialchars($detail_pegawai->nama, ENT_QUOTES, 'UTF-8') . "\n";
                $pesan .= "🆔 <b>Nomor Induk:</b> " . htmlspecialchars($detail_pegawai->nomor_induk, ENT_QUOTES, 'UTF-8') . "\n";
                $pesan .= "📅 <b>Tanggal:</b> " . htmlspecialchars($tanggal_indo, ENT_QUOTES, 'UTF-8') . "\n";
                $pesan .= "-----------------------------------\n";
                $pesan .= "⏰ <b>Jadwal Masuk:</b> " . $jam_jadwal . "\n";
                $pesan .= "🕐 <b>Waktu Scan:</b> " . htmlspecialchars($jam_masuk, ENT_QUOTES, 'UTF-8') . "\n";
                $pesan .= "-----------------------------------\n";
                $pesan .= $keterangan_waktu . "\n";
                $pesan .= "<b>Status:</b> " . $status_text;
                
                $result = send_telegram_notification($pesan);
                
                if ($result === false) {
                    log_message('warning', 'Notifikasi Telegram gagal untuk: ' . $detail_pegawai->nama);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Error notifikasi Telegram: ' . $e->getMessage());
        }

        session()->setFlashdata('berhasil', 'Presensi masuk berhasil disimpan dengan verifikasi wajah');
        return redirect()->to(base_url());
    }

 public function presensiKeluar()
    {
        // 1. Cek Login
        if (!logged_in()) {
            return redirect()->to(base_url('login'));
        }

        // 2. Ambil Data User
        $user_profile = $this->usersModel->getUserInfo(user_id());
        
        if (!$user_profile) {
            session()->setFlashdata('gagal', 'Data user tidak ditemukan');
            return redirect()->to(base_url());
        }

        // 3. AMBIL DATA LOKASI KANTOR (Ini yang bikin error "Undefined variable user_lokasi_presensi")
        $user_lokasi_presensi = $this->lokasiModel->where('nama_lokasi', $user_profile->lokasi_presensi)->first();
        
        if (!$user_lokasi_presensi) {
            session()->setFlashdata('gagal', 'Data lokasi presensi user tidak ditemukan');
            return redirect()->to(base_url());
        }

        // 4. Cek Presensi Masuk Hari Ini
        $tanggal_hari_ini = date('Y-m-d');
        $presensi_masuk = $this->presensiModel->cekPresensiMasuk($user_profile->id_pegawai, $tanggal_hari_ini);
        
        // Handle jika belum presensi masuk (biar view tidak error saat akses properti object)
        if (!$presensi_masuk) {
            $presensi_masuk = (object) [
                'id' => 0,
                'tanggal_masuk' => '0000-00-00', // Dummy date
                'tanggal_keluar' => '0000-00-00'
            ];
            $jumlah_presensi_masuk = 0;
        } else {
            $jumlah_presensi_masuk = 1;
        }

        // 5. Cek Status Izin/Sakit (Ini yang bikin error "Undefined variable status_ketidakhadiran")
        $cek_izin = $this->ketidakhadiranModel->where([
            'id_pegawai' => $user_profile->id_pegawai,
            'tanggal_mulai <=' => $tanggal_hari_ini,
            'tanggal_berakhir >=' => $tanggal_hari_ini,
            'status_pengajuan' => 'APPROVED'
        ])->first();
        
        $status_ketidakhadiran = $cek_izin ? 1 : 0;

        // 6. Siapkan Data Input dari Form (jika ada refresh/redirect)
        $latitude_pegawai = $this->request->getVar('latitude_pegawai');
        $longitude_pegawai = $this->request->getVar('longitude_pegawai');
        $latitude_kantor = $this->request->getVar('latitude_kantor');
        $longitude_kantor = $this->request->getVar('longitude_kantor');
        $radius = $this->request->getVar('radius');
        $tanggal_keluar = $this->request->getPost('tanggal_keluar');
        $jam_keluar = $this->request->getPost('jam_keluar');

        // 7. DATA LENGKAP UNTUK VIEW
        $data = [
            'title' => 'Presensi Keluar',
            'user_profile' => $user_profile,
            'user_lokasi_presensi' => $user_lokasi_presensi, // PENTING
            'status_ketidakhadiran' => $status_ketidakhadiran, // PENTING
            'jumlah_presensi_masuk' => $jumlah_presensi_masuk, // PENTING
            'data_presensi_masuk' => $presensi_masuk,
            'jam_pulang' => $user_lokasi_presensi->jam_pulang, // PENTING
            
            // Variabel pendukung form/view
            'latitude_pegawai' => $latitude_pegawai,
            'longitude_pegawai' => $longitude_pegawai,
            'latitude_kantor' => $latitude_kantor,
            'longitude_kantor' => $longitude_kantor,
            'radius' => $radius,
            'tanggal_keluar' => $tanggal_keluar,
            'jam_keluar' => $jam_keluar,
            
            // Variabel tambahan untuk menghindari error undefined
            'tanggal_masuk' => $tanggal_hari_ini, // Solusi error "Undefined variable $tanggal_masuk"
            'server_time' => time(), // Untuk jam javascript
        ];

        return view('presensi/presensi_keluar', $data);
    }

    public function simpanPresensiKeluar()
    {
        // Validasi CSRF
        if (!$this->validate(['csrf_test_name' => 'required'])) {
            session()->setFlashdata('gagal', 'Token keamanan tidak valid');
            return redirect()->to(base_url());
        }

        // Validasi user
        if (!logged_in()) {
            session()->setFlashdata('gagal', 'Anda harus login terlebih dahulu');
            return redirect()->to(base_url('login'));
        }

        // Validasi face recognition
        $face_verified = $this->request->getPost('face_verified');
        $face_similarity = $this->request->getPost('face_similarity');
        $face_similarity = filter_var($face_similarity, FILTER_VALIDATE_FLOAT);
        
        if ($face_verified !== 'true' || $face_similarity === false || $face_similarity < self::MIN_FACE_SIMILARITY) {
            log_message('warning', 'Face verification failed on keluar. Similarity: ' . $face_similarity);
            session()->setFlashdata('gagal', 'Verifikasi wajah gagal. Pastikan wajah Anda terlihat jelas dan sudah terdaftar.');
            return redirect()->to(base_url());
        }

        // Validasi foto
        $foto = $this->request->getPost('image-cam');
        if (empty($foto)) {
            session()->setFlashdata('gagal', 'Foto presensi tidak boleh kosong');
            return redirect()->to(base_url());
        }

        if (!preg_match('/^data:image\/(jpeg|png);base64,/', $foto)) {
            session()->setFlashdata('gagal', 'Format foto tidak valid');
            return redirect()->to(base_url());
        }

        $foto = preg_replace('/^data:image\/(jpeg|png);base64,/', '', $foto);
        $foto = base64_decode($foto, true);

        if ($foto === false) {
            session()->setFlashdata('gagal', 'Format foto tidak valid');
            return redirect()->to(base_url());
        }

        // Validasi ukuran foto
        if (strlen($foto) > self::MAX_IMAGE_SIZE) {
            session()->setFlashdata('gagal', 'Ukuran foto terlalu besar (maksimal 5MB)');
            return redirect()->to(base_url());
        }

        // Validasi MIME type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($foto);
        
        if (!in_array($mime, self::ALLOWED_IMAGE_TYPES)) {
            session()->setFlashdata('gagal', 'Tipe foto tidak diizinkan');
            return redirect()->to(base_url());
        }

        // Sanitasi username dan nama file
        $username = $this->request->getPost('username');
        $username = $this->sanitizeFilename($username);
        
        if (empty($username)) {
            session()->setFlashdata('gagal', 'Username tidak valid');
            return redirect()->to(base_url());
        }

        $nama_foto = 'keluar-' . date('Y-m-d-His') . '-' . $username . '.png';
        $nama_foto = $this->sanitizeFilename($nama_foto);
        
        $folder = FCPATH . 'assets/img/foto_presensi/keluar/';
        
        if (!is_dir($folder)) {
            if (!mkdir($folder, 0750, true)) {
                log_message('error', 'Gagal membuat folder: ' . $folder);
                session()->setFlashdata('gagal', 'Gagal membuat folder penyimpanan');
                return redirect()->to(base_url());
            }
        }
        
        // Validasi path
        $file_path = realpath($folder) . DIRECTORY_SEPARATOR . $nama_foto;
        
        if (strpos($file_path, realpath($folder)) !== 0) {
            log_message('error', 'Path traversal attempt detected: ' . $file_path);
            session()->setFlashdata('gagal', 'Path file tidak valid');
            return redirect()->to(base_url());
        }

        if (!file_put_contents($file_path, $foto)) {
            log_message('error', 'Gagal menulis file: ' . $file_path);
            session()->setFlashdata('gagal', 'Gagal menyimpan foto presensi keluar');
            return redirect()->to(base_url());
        }

        // Validasi ID presensi
        $id_presensi = $this->request->getPost('id_presensi');
        $id_presensi = filter_var($id_presensi, FILTER_VALIDATE_INT);
        
        if ($id_presensi === false || $id_presensi <= 0) {
            if (file_exists($file_path)) unlink($file_path);
            session()->setFlashdata('gagal', 'ID presensi tidak valid');
            return redirect()->to(base_url());
        }

        // Validasi tanggal dan jam
        $tanggal_keluar = $this->request->getPost('tanggal_keluar');
        $jam_keluar = $this->request->getPost('jam_keluar');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_keluar)) {
            if (file_exists($file_path)) unlink($file_path);
            session()->setFlashdata('gagal', 'Format tanggal tidak valid');
            return redirect()->to(base_url());
        }

        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $jam_keluar)) {
            if (file_exists($file_path)) unlink($file_path);
            session()->setFlashdata('gagal', 'Format jam tidak valid');
            return redirect()->to(base_url());
        }

        // Validasi jam pulang - pastikan sudah waktunya
        $user_profile = $this->usersModel->getUserInfo(user_id());
        $user_lokasi = $this->lokasiModel->getWhere(['nama_lokasi' => $user_profile->lokasi_presensi])->getFirstRow();
        
        // Set timezone sesuai lokasi user
        if (in_array($user_lokasi->zona_waktu, timezone_identifiers_list())) {
            date_default_timezone_set($user_lokasi->zona_waktu);
        } else {
            date_default_timezone_set('Asia/Jakarta');
        }
        
        // Cek apakah jam saat ini sudah >= jam pulang
        $jam_sekarang = strtotime(date('H:i:s'));
        $jam_pulang_target = strtotime($user_lokasi->jam_pulang);
        
        if ($jam_sekarang < $jam_pulang_target) {
            if (file_exists($file_path)) unlink($file_path);
            $selisih_menit = round(($jam_pulang_target - $jam_sekarang) / 60);
            session()->setFlashdata('gagal', "Belum waktunya pulang. Tunggu {$selisih_menit} menit lagi.");
            return redirect()->to(base_url());
        }

        try {
            $this->presensiModel->db->transStart();
            
            $this->presensiModel->save([
                'id' => $id_presensi, 
                'tanggal_keluar' => $tanggal_keluar,
                'jam_keluar' => $jam_keluar,
                'foto_keluar' => $nama_foto,
            ]);
            
            $this->presensiModel->db->transComplete();
            
            if ($this->presensiModel->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
        } catch (\Exception $e) {
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            log_message('error', 'Gagal simpan presensi keluar: ' . $e->getMessage());
            session()->setFlashdata('gagal', 'Gagal menyimpan presensi keluar');
            return redirect()->to(base_url());
        }

        session()->setFlashdata('berhasil', 'Presensi keluar berhasil disimpan dengan verifikasi wajah');
        return redirect()->to(base_url());
    }

    public function rekapPresensiPegawai()
    {
        $currentPage = $this->request->getVar('page_rekap') ? $this->request->getVar('page_rekap') : 1;

        $user_profile = $this->usersModel->getUserInfo(user_id());
        $data_presensi_pegawai = $this->presensiModel->getDataPresensi($user_profile->id_pegawai);
        $data_lokasi_presensi_user = $this->lokasiModel->getWhere(['nama_lokasi' => $user_profile->lokasi_presensi])->getFirstRow();

        $tanggal_dari = $this->request->getGet('tanggal_dari');
        $tanggal_sampai = $this->request->getGet('tanggal_sampai');
        if (!empty($tanggal_dari) || !empty($tanggal_sampai)) {
            if ($tanggal_dari === '') {
                if ($this->presensiModel->getMinDate($user_profile->id_pegawai)) {
                    $tanggal_dari = $this->presensiModel->getMinDate($user_profile->id_pegawai);
                } else {
                    $tanggal_dari = date('Y-m-d');
                }
            }
            if ($tanggal_sampai === '') {
                $tanggal_sampai = date('Y-m-d');
            }
            $data_presensi_pegawai = $this->presensiModel->getDataPresensi($user_profile->id_pegawai, $tanggal_dari, $tanggal_sampai);
        }

        if (empty($tanggal_dari) || empty($tanggal_sampai)) {
            if ($this->presensiModel->getMinDate($user_profile->id_pegawai)) {
                $tanggal_dari = $this->presensiModel->getMinDate($user_profile->id_pegawai);
            } else {
                $tanggal_dari = date('Y-m-d');
            }
            $tanggal_sampai = date('Y-m-d');
            $data_tanggal = date('d F Y', strtotime($tanggal_dari)) . ' - ' . date('d F Y');
        } else {
            if ($tanggal_dari > $tanggal_sampai) {
                $tanggal_sampai = $tanggal_dari;
            }
            $data_tanggal = date('d F Y', strtotime($tanggal_dari)) . ' - ' . date('d F Y', strtotime($tanggal_sampai));
        }

        $data_presensi = $data_presensi_pegawai['rekap-presensi'];
        $pager = $data_presensi_pegawai['links'];
        $total = $data_presensi_pegawai['total'];
        $perPage = $data_presensi_pegawai['perPage'];

        $data = [
            'title' => 'Rekap Presensi',
            'user_profile' => $user_profile,
            'jam_masuk_kantor' => $data_lokasi_presensi_user->jam_masuk,
            'data_tanggal' => $data_tanggal,
            'data_presensi_pegawai' => $data_presensi,
            'currentPage' => $currentPage,
            'pager' => $pager,
            'total' => $total,
            'perPage' => $perPage,
            'tanggal_dari' => $tanggal_dari,
            'tanggal_sampai' => $tanggal_sampai,
        ];

        return view('presensi/rekap_presensi', $data);
    }

    public function rekapPresensiPegawaiExcel()
    {
        $data_pegawai = $this->pegawaiModel->getPegawai(user()->username)['pegawai'];

        $tanggal_awal = $this->request->getPost('tanggal_awal');
        $tanggal_akhir = $this->request->getPost('tanggal_akhir');
        if ($tanggal_awal === '') {
            $tanggal_awal = $this->presensiModel->getMinDate($data_pegawai->id);
        }
        if ($tanggal_akhir === '') {
            $tanggal_akhir = date('Y-m-d');
        }
        $data_presensi = $this->presensiModel->getDataPresensi($data_pegawai->id_pegawai, $tanggal_awal, $tanggal_akhir, true)['rekap-presensi'];

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->setCellValue('A1', 'Rekap Presensi Pengguna');
        $worksheet->setCellValue('A3', 'Tanggal Awal');
        $worksheet->setCellValue('A4', 'Tanggal Akhir');
        $worksheet->setCellValue('C3', $tanggal_awal);
        $worksheet->setCellValue('C4', $tanggal_akhir);
        $worksheet->setCellValue('E3', 'Nama');
        $worksheet->setCellValue('E4', 'Nomor Induk (NIS/NIP)');
        $worksheet->setCellValue('F3', $data_pegawai->nama);
        $worksheet->setCellValue('F4', $data_pegawai->nomor_induk);
        $worksheet->setCellValue('A6', '#');
        $worksheet->setCellValue('B6', 'TANGGAL MASUK');
        $worksheet->setCellValue('C6', 'JAM MASUK');
        $worksheet->setCellValue('D6', 'JAM PULANG');
        $worksheet->setCellValue('E6', 'TOTAL JAM KBM');
        $worksheet->setCellValue('F6', 'TOTAL JAM KETERLAMBATAN');

        $worksheet->mergeCells('A1:F1');
        $worksheet->mergeCells('A3:B3');
        $worksheet->mergeCells('A4:B4');

        $data_start_row = 7;
        $nomor = 1;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ]
        ];

        if (!empty($data_presensi)) {
            foreach ($data_presensi as $data) {
                // TOTAL JAM KERJA
                $jam_tanggal_masuk = date('Y-m-d H:i:s', strtotime($data->tanggal_masuk . ' ' . $data->jam_masuk));
                $jam_tanggal_keluar = date('Y-m-d H:i:s', strtotime($data->tanggal_keluar . ' ' . $data->jam_keluar));

                $timestamp_masuk = strtotime($jam_tanggal_masuk);
                $timestamp_keluar = strtotime($jam_tanggal_keluar);

                // Selisih dalam format time
                $selisih = $timestamp_keluar - $timestamp_masuk;

                // Selisih dalam format jam
                $total_jam_kerja = floor($selisih / 3600);

                // Selisih dalam format menit
                $selisih_menit_kerja = floor(($selisih % 3600) / 60);

                // Format string
                $total_jam_kerja_format = sprintf("%d Jam %d Menit", $total_jam_kerja, $selisih_menit_kerja);

                if ($total_jam_kerja < 0) {
                    $total_jam_kerja_format = '0 Jam 0 Menit';
                }

                // TOTAL KETERLAMBATAN
                $jam_masuk = date('H:i:s', strtotime($data->jam_masuk));
                $timestamp_jam_masuk_real = strtotime($jam_masuk);

                $jam_masuk_kantor = $data->jam_masuk_kantor;
                $timestamp_jam_masuk_kantor = strtotime($jam_masuk_kantor);

                $terlambat = $timestamp_jam_masuk_real - $timestamp_jam_masuk_kantor;
                $total_jam_keterlambatan = floor($terlambat / 3600);
                $selisih_menit_keterlambatan = floor(($terlambat % 3600) / 60);

                $total_jam_keterlambatan_format = sprintf("%d Jam %d Menit", $total_jam_keterlambatan, $selisih_menit_keterlambatan);

                if ($total_jam_keterlambatan < 0) {
                    $total_jam_keterlambatan_format = 'On Time';
                }

                $worksheet->setCellValue('A' . $data_start_row, $nomor++);
                $worksheet->setCellValue('B' . $data_start_row, $data->tanggal_masuk);
                $worksheet->setCellValue('C' . $data_start_row, $data->jam_masuk);
                $worksheet->setCellValue('D' . $data_start_row, $data->jam_keluar);
                $worksheet->setCellValue('E' . $data_start_row, $total_jam_kerja_format);
                $worksheet->setCellValue('F' . $data_start_row, $total_jam_keterlambatan_format);

                $worksheet->getStyle('A' . $data_start_row - 1 . ':F' . $data_start_row)->applyFromArray($styleArray);

                $data_start_row++;
            }
        } else {
            $worksheet->setCellValue('A' . $data_start_row, 'Tidak Ada Data');
            $worksheet->mergeCells('A' . $data_start_row . ':F' . $data_start_row);
            $worksheet->getStyle('A' . $data_start_row - 1 . ':F' . $data_start_row)->applyFromArray($styleArray);
        }

        $worksheet->getColumnDimension('A')->setAutoSize(true);
        $worksheet->getColumnDimension('B')->setAutoSize(true);
        $worksheet->getColumnDimension('C')->setAutoSize(true);
        $worksheet->getColumnDimension('D')->setAutoSize(true);
        $worksheet->getColumnDimension('E')->setAutoSize(true);
        $worksheet->getColumnDimension('F')->setAutoSize(true);

        $worksheet->getStyle('A3:C4')->applyFromArray($styleArray);
        $worksheet->getStyle('E3:F4')->applyFromArray($styleArray);
        $worksheet->getStyle('A6:F6')->getFont()->setBold(true);
        $worksheet->getStyle('A1')->getFont()->setBold(true);
        $worksheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('A1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('ffff00');
        $worksheet->getStyle('C3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $worksheet->getStyle('C4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="PresenSi_Rekap Presensi Pengguna_' . $data_pegawai->nama . '_' . date('Y-m-d', strtotime($tanggal_awal)) . '_' . date('Y-m-d', strtotime($tanggal_akhir)) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    private function _isHariLibur($tanggal) {
        $hari_ke = date('N', strtotime($tanggal));

        if ($hari_ke >= 6) {
            return true;
        }

        $cek_libur = $this->hariLiburModel->where('tanggal', $tanggal)->first();

        if ($cek_libur) {
            return true;
        }

        return false;
    }

    // Tambahkan method helper ini di controller
    private function _tentukanStatus($data, $tanggal)
    {
        $is_libur = $this->_isHariLibur($tanggal);
        
        // Prioritas 1: Ada Jam Masuk -> HADIR
        if (!empty($data->jam_masuk)) {
            return 'Hadir';
        }
        
        // Prioritas 2: Ada Data Ketidakhadiran -> IZIN / SAKIT
        if (!empty($data->tipe_ketidakhadiran)) {
            return ucfirst(strtolower($data->tipe_ketidakhadiran));
        }
        
        // Prioritas 3: Hari Libur -> LIBUR
        if ($is_libur) {
            return 'Libur';
        }
        
        // Prioritas 4: Sisanya -> ALPHA
        return 'Alpha';
    }
    
    public function laporanHarian()
    {
        $currentPage = $this->request->getVar('page_harian') ? $this->request->getVar('page_harian') : 1;
        $user_profile = $this->usersModel->getUserInfo(user_id());
        $tanggal_filter = $this->request->getGet('tanggal_filter');
        $filter_jabatan = $this->request->getGet('filter_jabatan');
        
        if (empty($tanggal_filter)) {
            $tanggal_filter = date('Y-m-d');
        }
        
        $data_presensi_pegawai = $this->presensiModel->getLaporanHarianLengkap($tanggal_filter, $filter_jabatan);
        $data_presensi = $data_presensi_pegawai['laporan-harian'];
        $pager = $data_presensi_pegawai['links'];
        $total = $data_presensi_pegawai['total'];
        $perPage = $data_presensi_pegawai['perPage'];
        
        // Proses Status menggunakan helper yang konsisten
        foreach ($data_presensi as $key => $value) {
            $data_presensi[$key]->status_kehadiran = $this->_tentukanStatus($value, $tanggal_filter);
        }
        
        // Ambil semua jabatan untuk dropdown filter
        $jabatanModel = new \App\Models\JabatanModel();
        $data_jabatan = $jabatanModel->getJabatan(false, false, 100, true);
        
        $data = [
            'title' => 'Laporan Presensi Harian',
            'user_profile' => $user_profile,
            'data_tanggal' => date('d F Y', strtotime($tanggal_filter)),
            'tanggal_filter' => $tanggal_filter,
            'filter_jabatan' => $filter_jabatan,
            'data_jabatan' => $data_jabatan['jabatan'],
            'data_presensi' => $data_presensi,
            'currentPage' => $currentPage,
            'pager' => $pager,
            'total' => $total,
            'perPage' => $perPage,
        ];
        
        return view('presensi/laporan_presensi_harian', $data);
    }

    public function laporanHarianExcel()
    {
        $tanggal_filter = $this->request->getPost('tanggal_filter');
        $filter_jabatan = $this->request->getPost('filter_jabatan');
        
        if (empty($tanggal_filter)) {
            $tanggal_filter = date('Y-m-d');
        }
        
        $data_presensi = $this->presensiModel->getLaporanHarianLengkapNoPage($tanggal_filter, $filter_jabatan);
        
        // Proses Status
        foreach ($data_presensi as $key => $value) {
            $data_presensi[$key]->status_kehadiran = $this->_tentukanStatus($value, $tanggal_filter);
        }
        
        // Group data by jabatan untuk multiple sheets
        $data_by_jabatan = [];
        foreach ($data_presensi as $data) {
            $jabatan_nama = $data->nama_jabatan ?? 'Tanpa Jabatan';
            if (!isset($data_by_jabatan[$jabatan_nama])) {
                $data_by_jabatan[$jabatan_nama] = [];
            }
            $data_by_jabatan[$jabatan_nama][] = $data;
        }
        
        // Sort jabatan alphabetically
        ksort($data_by_jabatan);
        
        $spreadsheet = new Spreadsheet();
        $sheetIndex = 0;
        
        // Loop untuk setiap jabatan = 1 sheet
        foreach ($data_by_jabatan as $nama_jabatan => $data_pegawai) {
            if ($sheetIndex > 0) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndex($sheetIndex);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Beri nama sheet sesuai jabatan (max 31 karakter untuk Excel)
            $sheet_name = substr($nama_jabatan, 0, 31);
            $worksheet->setTitle($sheet_name);
            
            // ========== HEADER TITLE ==========
            $worksheet->setCellValue('A1', 'LAPORAN PRESENSI HARIAN');
            $worksheet->mergeCells('A1:K1');
            
            // Style Header Title
            $worksheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1e3a8a']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]);
            $worksheet->getRowDimension(1)->setRowHeight(30);
            
            // ========== INFO TANGGAL & JABATAN ==========
            $worksheet->setCellValue('A3', 'Tanggal');
            $worksheet->setCellValue('B3', ':');
            $worksheet->setCellValue('C3', date('d F Y', strtotime($tanggal_filter)));
            
            $worksheet->setCellValue('A4', 'Unit');
            $worksheet->setCellValue('B4', ':');
            $worksheet->setCellValue('C4', $nama_jabatan);
            
            $worksheet->getStyle('A3:A4')->getFont()->setBold(true);
            $worksheet->getStyle('C3:C4')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '1e3a8a']]
            ]);
            
            // ========== HEADER KOLOM ==========
            $headers = [
                'A6' => 'NO',
                'B6' => 'NOMOR INDUK',
                'C6' => 'NAMA',
                'D6' => 'UNIT',
                'E6' => 'STATUS',
                'F6' => 'JAM MASUK',
                'G6' => 'FOTO MASUK',
                'H6' => 'JAM PULANG',
                'I6' => 'FOTO PULANG',
                'J6' => 'TOTAL JAM KERJA',
                'K6' => 'KETERLAMBATAN'
            ];
            foreach ($headers as $cell => $value) {
                $worksheet->setCellValue($cell, $value);
            }
            
            // Style Header Kolom
            $worksheet->getStyle('A6:K6')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1e3a8a']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
            $worksheet->getRowDimension(6)->setRowHeight(25);
            
            // ========== ISI DATA ==========
            $data_start_row = 7;
            $nomor = 1;
            $styleBorder = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ];
            
            foreach ($data_pegawai as $data) {
                $status = $data->status_kehadiran;
                
                // Inisialisasi default
                $jam_masuk = '-';
                $jam_keluar = '-';
                $foto_masuk = '-';
                $foto_keluar = '-';
                $total_jam_kerja = '-';
                $keterlambatan = '-';
                
                // Warna status
                $status_color = '000000';
                switch($status) {
                    case 'Hadir':
                        $status_color = '16a34a';
                        break;
                    case 'Sakit':
                        $status_color = 'eab308';
                        break;
                    case 'Izin':
                        $status_color = '3b82f6';
                        break;
                    case 'Libur':
                        $status_color = '6b7280';
                        break;
                    case 'Alpha':
                        $status_color = 'dc2626';
                        break;
                }
                
                // Hanya hitung jika HADIR
                if ($status == 'Hadir' && !empty($data->jam_masuk)) {
                    $jam_masuk = $data->jam_masuk;
                    $jam_keluar = ($data->jam_keluar == '00:00:00') ? 'Belum Pulang' : $data->jam_keluar;
                    $foto_masuk = 'Ada';
                    $foto_keluar = ($data->jam_keluar != '00:00:00' && $data->foto_keluar != '-') ? 'Ada' : '-';
                    
                    // Hitung Jam Kerja
                    if ($data->jam_keluar != '00:00:00') {
                        $start = strtotime($data->tanggal_masuk . ' ' . $data->jam_masuk);
                        $end = strtotime($data->tanggal_keluar . ' ' . $data->jam_keluar);
                        $selisih = $end - $start;
                        $jam = floor($selisih / 3600);
                        $menit = floor(($selisih % 3600) / 60);
                        $total_jam_kerja = $jam . ' Jam ' . $menit . ' Menit';
                    }
                    
                    // Hitung Keterlambatan
                    if (!empty($data->jam_masuk_kantor)) {
                        $timestamp_masuk = strtotime(date('H:i:s', strtotime($data->jam_masuk)));
                        $timestamp_jadwal = strtotime($data->jam_masuk_kantor);
                        $terlambat = $timestamp_masuk - $timestamp_jadwal;
                        
                        if ($terlambat > 0) {
                            $jam_lat = floor($terlambat / 3600);
                            $menit_lat = floor(($terlambat % 3600) / 60);
                            $keterlambatan = $jam_lat . ' Jam ' . $menit_lat . ' Menit';
                        } else {
                            $keterlambatan = 'On Time';
                        }
                    }
                }
                
                $worksheet->setCellValue('A' . $data_start_row, $nomor++);
                $worksheet->setCellValue('B' . $data_start_row, $data->nomor_induk);
                $worksheet->setCellValue('C' . $data_start_row, $data->nama);
                $worksheet->setCellValue('D' . $data_start_row, $data->nama_jabatan ?? '-');
                $worksheet->setCellValue('E' . $data_start_row, $status);
                $worksheet->setCellValue('F' . $data_start_row, $jam_masuk);
                $worksheet->setCellValue('G' . $data_start_row, $foto_masuk);
                $worksheet->setCellValue('H' . $data_start_row, $jam_keluar);
                $worksheet->setCellValue('I' . $data_start_row, $foto_keluar);
                $worksheet->setCellValue('J' . $data_start_row, $total_jam_kerja);
                $worksheet->setCellValue('K' . $data_start_row, $keterlambatan);
                
                // Style row
                $worksheet->getStyle('A' . $data_start_row . ':K' . $data_start_row)->applyFromArray($styleBorder);
                
                // Zebra striping
                if ($nomor % 2 == 0) {
                    $worksheet->getStyle('A' . $data_start_row . ':K' . $data_start_row)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'f3f4f6']
                        ]
                    ]);
                }
                
                // Warna status
                $worksheet->getStyle('E' . $data_start_row)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => $status_color]]
                ]);
                
                // Center align untuk kolom tertentu
                $worksheet->getStyle('A' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle('B' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle('D' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle('E' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle('F' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle('G' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle('H' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle('I' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
                $data_start_row++;
            }
            
            // ========== FOOTER ==========
            $footer_row = $data_start_row + 2;
            $worksheet->setCellValue('A' . $footer_row, 'PresenSI - "Si Pintar Urusan Presensi"');
            $worksheet->mergeCells('A' . $footer_row . ':K' . $footer_row);
            
            $footer_row++;
            $worksheet->setCellValue('A' . $footer_row, 'Diekspor pada: ' . date('d F Y H:i:s'));
            $worksheet->mergeCells('A' . $footer_row . ':K' . $footer_row);
            
            $worksheet->getStyle('A' . ($footer_row - 1) . ':A' . $footer_row)->applyFromArray([
                'font' => [
                    'italic' => true,
                    'size' => 9,
                    'color' => ['rgb' => '6b7280']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);
            
            // ========== AUTO SIZE COLUMNS ==========
            foreach (range('A', 'K') as $col) {
                $worksheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Set minimum width untuk kolom tertentu
            $worksheet->getColumnDimension('C')->setWidth(25); // Nama
            $worksheet->getColumnDimension('D')->setWidth(20); // Jabatan
            $worksheet->getColumnDimension('J')->setWidth(20); // Total Jam Kerja
            $worksheet->getColumnDimension('K')->setWidth(20); // Keterlambatan
            
            $sheetIndex++;
        }
        
        // Set sheet pertama sebagai active
        $spreadsheet->setActiveSheetIndex(0);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Presensi_Harian_' . $tanggal_filter . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    private function _generateDataBulanan($bulan, $tahun, $filter_jabatan = false)
    {
        // 1. Ambil semua pegawai (dengan filter jabatan jika ada)
        $pegawaiModel = new \App\Models\PegawaiModel();
        $builder = $pegawaiModel->builder();
        $builder->select('pegawai.*, jabatan.jabatan as nama_jabatan');
        $builder->join('jabatan', 'jabatan.id = pegawai.id_jabatan', 'left');
        
        if ($filter_jabatan) {
            $builder->where('pegawai.id_jabatan', $filter_jabatan);
        }
        
        $builder->orderBy('jabatan.jabatan', 'ASC');
        $builder->orderBy('pegawai.nama', 'ASC');
        
        $semua_pegawai = $builder->get()->getResultArray();
        
        // 2. Ambil data presensi (Masuk/Pulang)
        $presensi_raw = $this->presensiModel->getPresensiByMonth($bulan, $tahun);
        $presensi_map = [];
        foreach ($presensi_raw as $p) {
            $presensi_map[$p['pegawai_id']][$p['tanggal_masuk']] = $p;
        }
        
        // 3. AMBIL DATA KETIDAKHADIRAN (IZIN/SAKIT)
        $ketidakhadiran_raw = $this->ketidakhadiranModel->getKetidakhadiranByMonth($bulan, $tahun);
        $izin_map = [];
        // Mapping rentang tanggal izin ke tanggal satuan
        foreach ($ketidakhadiran_raw as $izin) {
            $start = new \DateTime($izin['tanggal_mulai']);
            $end = new \DateTime($izin['tanggal_berakhir']);
            $end->modify('+1 day');
            $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
            foreach ($period as $dt) {
                $date_str = $dt->format('Y-m-d');
                $izin_map[$izin['id_pegawai']][$date_str] = $izin['tipe_ketidakhadiran'];
            }
        }
        
        // 4. Generate Loop
        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $result_data = [];
        
        for ($hari = 1; $hari <= $jumlah_hari; $hari++) {
            $tanggal_curr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $hari);
            
            foreach ($semua_pegawai as $pegawai) {
                $row = new \stdClass();
                $row->nomor_induk = $pegawai['nomor_induk'];
                $row->nama = $pegawai['nama'];
                $row->nama_jabatan = $pegawai['nama_jabatan'] ?? 'Tanpa Jabatan';
                $row->tanggal_masuk = $tanggal_curr;
                
                // Cek apakah ada data presensi
                if (isset($presensi_map[$pegawai['id']][$tanggal_curr])) {
                    $data_db = $presensi_map[$pegawai['id']][$tanggal_curr];
                    $row->jam_masuk = $data_db['jam_masuk'];
                    $row->jam_keluar = $data_db['jam_keluar'];
                    $row->foto_masuk = $data_db['foto_masuk'];
                    $row->foto_keluar = $data_db['foto_keluar'];
                    $row->tanggal_keluar = isset($data_db['tanggal_keluar']) ? $data_db['tanggal_keluar'] : $tanggal_curr;
                    $row->jam_masuk_kantor = isset($data_db['jam_masuk_kantor']) ? $data_db['jam_masuk_kantor'] : null;
                    
                    // Set tipe_ketidakhadiran = null karena dia hadir
                    $row->tipe_ketidakhadiran = null;
                } else {
                    // Data Kosong
                    $row->jam_masuk = null;
                    $row->jam_keluar = null;
                    $row->foto_masuk = '-';
                    $row->foto_keluar = '-';
                    $row->tanggal_keluar = null;
                    $row->jam_masuk_kantor = null;
                    
                    // Cek apakah ada ketidakhadiran (Izin/Sakit)
                    if (isset($izin_map[$pegawai['id']][$tanggal_curr])) {
                        $row->tipe_ketidakhadiran = $izin_map[$pegawai['id']][$tanggal_curr];
                    } else {
                        $row->tipe_ketidakhadiran = null;
                    }
                }
                
                // GUNAKAN HELPER UNTUK MENENTUKAN STATUS - KONSISTEN!
                $row->status_kehadiran = $this->_tentukanStatus($row, $tanggal_curr);
                
                $result_data[] = $row;
            }
        }
        
        return $result_data;
    }

    public function laporanBulanan()
    {
        $currentPage = $this->request->getVar('page_bulanan') ? $this->request->getVar('page_bulanan') : 1;
        $user_profile = $this->usersModel->getUserInfo(user_id());
        
        $filter_bulan = $this->request->getGet('filter_bulan');
        $filter_tahun = $this->request->getGet('filter_tahun');
        $filter_jabatan = $this->request->getGet('filter_jabatan');
        
        if (empty($filter_bulan)) $filter_bulan = date('m');
        if (empty($filter_tahun)) $filter_tahun = date('Y');
        
        $data_bulan = $filter_tahun . '-' . $filter_bulan;
        
        // 1. Generate Data Lengkap (Hadir + Alpha) dengan filter jabatan
        $data_lengkap = $this->_generateDataBulanan($filter_bulan, $filter_tahun, $filter_jabatan);
        
        // 2. Manual Pagination untuk Array
        $perPage = 10;
        $total = count($data_lengkap);
        $offset = ($currentPage - 1) * $perPage;
        
        // Potong array sesuai halaman
        $data_presensi = array_slice($data_lengkap, $offset, $perPage);
        
        // Buat Pager
        $pager = service('pager');
        $pager_links = $pager->makeLinks($currentPage, $perPage, $total, 'default_full', 0, 'bulanan');
        
        if ($this->presensiModel->getMinYear()) {
            $tahun_mulai = $this->presensiModel->getMinYear();
        } else {
            $tahun_mulai = date('Y');
        }
        
        // Ambil semua jabatan untuk dropdown filter
        $jabatanModel = new \App\Models\JabatanModel();
        $data_jabatan = $jabatanModel->getJabatan(false, false, 100, true);
        
        $data = [
            'title' => 'Laporan Presensi Bulanan',
            'user_profile' => $user_profile,
            'tahun_mulai' => $tahun_mulai,
            'data_bulan' => $data_bulan,
            'data_presensi' => $data_presensi,
            'currentPage' => $currentPage,
            'pager' => $pager_links,
            'total' => $total,
            'perPage' => $perPage,
            'filter_bulan' => $filter_bulan,
            'filter_tahun' => $filter_tahun,
            'filter_jabatan' => $filter_jabatan,
            'data_jabatan' => $data_jabatan['jabatan'],
        ];
        
        return view('presensi/laporan_presensi_bulanan', $data);
    }

    public function laporanBulananExcel()
    {
        $filter_bulan = $this->request->getPOST('filter_bulan');
        $filter_tahun = $this->request->getPOST('filter_tahun');
        $filter_jabatan = $this->request->getPOST('filter_jabatan');
        
        if (empty($filter_tahun)) $filter_tahun = date('Y');
        if (empty($filter_bulan)) $filter_bulan = date('m');
        
        $data_presensi = $this->_generateDataBulanan($filter_bulan, $filter_tahun, $filter_jabatan);
        
        // Group data by jabatan untuk multiple sheets
        $data_by_jabatan = [];
        foreach ($data_presensi as $data) {
            $jabatan_nama = $data->nama_jabatan ?? 'Tanpa Jabatan';
            if (!isset($data_by_jabatan[$jabatan_nama])) {
                $data_by_jabatan[$jabatan_nama] = [];
            }
            $data_by_jabatan[$jabatan_nama][] = $data;
        }
        
        // Sort jabatan alphabetically
        ksort($data_by_jabatan);
        
        $spreadsheet = new Spreadsheet();
        $sheetIndex = 0;
        
        // Loop untuk setiap jabatan = 1 sheet
        foreach ($data_by_jabatan as $nama_jabatan => $data_pegawai) {
            if ($sheetIndex > 0) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndex($sheetIndex);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Beri nama sheet sesuai jabatan (max 31 karakter untuk Excel)
            $sheet_name = substr($nama_jabatan, 0, 31);
            $worksheet->setTitle($sheet_name);
            
            // ========== HEADER TITLE ==========
            $worksheet->setCellValue('A1', 'LAPORAN PRESENSI BULANAN');
            $worksheet->mergeCells('A1:J1');
            
            $worksheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1e3a8a']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]);
            $worksheet->getRowDimension(1)->setRowHeight(30);
            
            // ========== INFO PERIODE & JABATAN ==========
            $worksheet->setCellValue('A3', 'Bulan');
            $worksheet->setCellValue('B3', ':');
            $worksheet->setCellValue('C3', date('F', mktime(0, 0, 0, $filter_bulan, 10)));
            
            $worksheet->setCellValue('A4', 'Tahun');
            $worksheet->setCellValue('B4', ':');
            $worksheet->setCellValue('C4', $filter_tahun);
            
            $worksheet->setCellValue('A5', 'Unit');
            $worksheet->setCellValue('B5', ':');
            $worksheet->setCellValue('C5', $nama_jabatan);
            
            $worksheet->getStyle('A3:A5')->getFont()->setBold(true);
            $worksheet->getStyle('C3:C5')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '1e3a8a']]
            ]);
            
            // ========== HEADER KOLOM ==========
            $headers = [
                'A7' => 'NO',
                'B7' => 'NOMOR INDUK',
                'C7' => 'NAMA',
                'D7' => 'UNIT',
                'E7' => 'TANGGAL',
                'F7' => 'STATUS',
                'G7' => 'JAM MASUK',
                'H7' => 'JAM PULANG',
                'I7' => 'TOTAL JAM KERJA',
                'J7' => 'KETERLAMBATAN'
            ];
            foreach ($headers as $cell => $value) {
                $worksheet->setCellValue($cell, $value);
            }
            
            $worksheet->getStyle('A7:J7')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1e3a8a']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
            $worksheet->getRowDimension(7)->setRowHeight(25);
            
            // ========== ISI DATA ==========
            $data_start_row = 8;
            $nomor = 1;
            $styleBorder = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ];
            
            if (!empty($data_pegawai)) {
                foreach ($data_pegawai as $data) {
                    
                    $jam_masuk = '-';
                    $jam_keluar = '-';
                    $total_jam_kerja_format = '-';
                    $keterlambatan_format = '-';
                    $status = $data->status_kehadiran;
                    
                    // Warna status
                    $status_color = '000000';
                    switch($status) {
                        case 'Hadir':
                            $status_color = '16a34a';
                            break;
                        case 'Sakit':
                            $status_color = 'eab308';
                            break;
                        case 'Izin':
                            $status_color = '3b82f6';
                            break;
                        case 'Libur':
                            $status_color = '6b7280';
                            break;
                        case 'Alpha':
                            $status_color = 'dc2626';
                            break;
                    }
                    
                    // Hanya hitung jika HADIR
                    if ($data->status_kehadiran == 'Hadir') {
                        $jam_masuk = $data->jam_masuk;
                        $jam_keluar = ($data->jam_keluar == '00:00:00') ? 'Belum Pulang' : $data->jam_keluar;
                        
                        // Hitung Jam Kerja
                        if ($data->jam_keluar != '00:00:00') {
                            $start = strtotime($data->tanggal_masuk . ' ' . $data->jam_masuk);
                            $end = strtotime($data->tanggal_keluar . ' ' . $data->jam_keluar);
                            $selisih = $end - $start;
                            $jam = floor($selisih / 3600);
                            $menit = floor(($selisih % 3600) / 60);
                            $total_jam_kerja_format = $jam . ' Jam ' . $menit . ' Menit';
                        }
                        
                        // Hitung Keterlambatan
                        if (!empty($data->jam_masuk_kantor)) {
                            $timestamp_masuk = strtotime(date('H:i:s', strtotime($data->jam_masuk)));
                            $timestamp_jadwal = strtotime($data->jam_masuk_kantor);
                            $terlambat = $timestamp_masuk - $timestamp_jadwal;
                            
                            if ($terlambat > 0) {
                                $jam_lat = floor($terlambat / 3600);
                                $menit_lat = floor(($terlambat % 3600) / 60);
                                $keterlambatan_format = $jam_lat . ' Jam ' . $menit_lat . ' Menit';
                            } else {
                                $keterlambatan_format = 'On Time';
                            }
                        }
                    }
                    
                    $worksheet->setCellValue('A' . $data_start_row, $nomor++);
                    $worksheet->setCellValue('B' . $data_start_row, $data->nomor_induk);
                    $worksheet->setCellValue('C' . $data_start_row, $data->nama);
                    $worksheet->setCellValue('D' . $data_start_row, $data->nama_jabatan);
                    $worksheet->setCellValue('E' . $data_start_row, date('d/m/Y', strtotime($data->tanggal_masuk)));
                    $worksheet->setCellValue('F' . $data_start_row, $status);
                    $worksheet->setCellValue('G' . $data_start_row, $jam_masuk);
                    $worksheet->setCellValue('H' . $data_start_row, $jam_keluar);
                    $worksheet->setCellValue('I' . $data_start_row, $total_jam_kerja_format);
                    $worksheet->setCellValue('J' . $data_start_row, $keterlambatan_format);
                    
                    // Style row
                    $worksheet->getStyle('A' . $data_start_row . ':J' . $data_start_row)->applyFromArray($styleBorder);
                    
                    // Zebra striping
                    if ($nomor % 2 == 0) {
                        $worksheet->getStyle('A' . $data_start_row . ':J' . $data_start_row)->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'f3f4f6']
                            ]
                        ]);
                    }
                    
                    // Warna status
                    $worksheet->getStyle('F' . $data_start_row)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => $status_color]]
                    ]);
                    
                    // Center align
                    $worksheet->getStyle('A' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $worksheet->getStyle('B' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $worksheet->getStyle('D' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $worksheet->getStyle('E' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $worksheet->getStyle('F' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $worksheet->getStyle('G' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $worksheet->getStyle('H' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    
                    $data_start_row++;
                }
            }
            
            // ========== FOOTER ==========
            $footer_row = $data_start_row + 2;
            $worksheet->setCellValue('A' . $footer_row, 'PresenSI - "Si Pintar Urusan Presensi"');
            $worksheet->mergeCells('A' . $footer_row . ':J' . $footer_row);
            
            $footer_row++;
            $worksheet->setCellValue('A' . $footer_row, 'Diekspor pada: ' . date('d F Y H:i:s'));
            $worksheet->mergeCells('A' . $footer_row . ':J' . $footer_row);
            
            $worksheet->getStyle('A' . ($footer_row - 1) . ':A' . $footer_row)->applyFromArray([
                'font' => [
                    'italic' => true,
                    'size' => 9,
                    'color' => ['rgb' => '6b7280']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);
            
            // ========== AUTO SIZE COLUMNS ==========
            foreach (range('A', 'J') as $col) {
                $worksheet->getColumnDimension($col)->setAutoSize(true);
            }
            $worksheet->getColumnDimension('C')->setWidth(25);
            $worksheet->getColumnDimension('D')->setWidth(20);
            $worksheet->getColumnDimension('I')->setWidth(20);
            $worksheet->getColumnDimension('J')->setWidth(20);
            
            $sheetIndex++;
        }
        
        // Set sheet pertama sebagai active
        $spreadsheet->setActiveSheetIndex(0);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Bulanan_' . $filter_bulan . '-' . $filter_tahun . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    /**
     * API: Ambil semua face descriptors untuk recognition
     */
    public function getFaceDescriptors()
    {
        $descriptors = $this->faceDescriptorModel->getAllActiveDescriptors();
        
        $result = [];
        foreach ($descriptors as $desc) {
            // ========== FIX: DECODE DESCRIPTOR DENGAN VALIDASI ==========
            $descriptorArray = json_decode($desc->descriptor, true);
            
            // Validasi hasil decode
            if (!is_array($descriptorArray)) {
                log_message('error', 'Invalid descriptor for ID ' . $desc->id . ' - not an array');
                continue; // Skip descriptor yang corrupt
            }
            
            if (count($descriptorArray) < 100) {
                log_message('error', 'Invalid descriptor for ID ' . $desc->id . ' - too short: ' . count($descriptorArray));
                continue; // Skip descriptor yang terlalu pendek
            }
            
            // Pastikan semua nilai numerik
            $isValid = true;
            foreach ($descriptorArray as $val) {
                if (!is_numeric($val)) {
                    log_message('error', 'Invalid descriptor for ID ' . $desc->id . ' - contains non-numeric');
                    $isValid = false;
                    break;
                }
            }
            
            if (!$isValid) continue;
            
            $result[] = [
                'id' => $desc->id,
                'id_pegawai' => $desc->id_pegawai,
                'nama' => $desc->nama,
                'nomor_induk' => $desc->nomor_induk,
                'descriptor' => $descriptorArray, // ← Sudah dalam bentuk array
                'label' => $desc->label
            ];
        }

        log_message('info', 'Returning ' . count($result) . ' valid descriptors');
        
        return $this->response->setJSON($result);
    }

    /**
     * Verify face recognition sebelum simpan presensi
     */
    public function verifyFace()
    {
        // Cek user sudah login
        if (!logged_in()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        // TODO: Implement group check setelah testing selesai
        // if (!in_groups(3)) {
        //     return $this->response->setJSON(['success' => false, 'message' => 'Forbidden'])->setStatusCode(403);
        // }

        $descriptor = $this->request->getJSON(true)['descriptor'] ?? null;
        
        if (!$descriptor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Descriptor tidak valid']);
        }

        // Ambil user saat ini
        $user_profile = $this->usersModel->getUserInfo(user_id());
        $id_pegawai = $user_profile->id_pegawai;

        // Ambil descriptor pegawai dari database
        $saved_descriptors = $this->faceDescriptorModel->getDescriptorsByPegawai($id_pegawai);

        if (empty($saved_descriptors)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Wajah Anda belum terdaftar. Silakan hubungi Admin.'
            ]);
        }

        // Response untuk client-side matching
        $result = [];
        foreach ($saved_descriptors as $saved) {
            $result[] = json_decode($saved->descriptor);
        }

        return $this->response->setJSON([
            'success' => true,
            'descriptors' => $result
        ]);
    }
}