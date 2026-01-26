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

public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->lokasiModel = new LokasiPresensiModel();
        $this->presensiModel = new PresensiModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->ketidakhadiranModel = new KetidakhadiranModel(); 
        $this->hariLiburModel = new HariLiburModel();
        $this->faceDescriptorModel = new faceDescriptorModel();

        helper(['telegram', 'text']);
    }

    public function presensiMasuk()
    {
        $user_profile = $this->usersModel->getUserInfo(user_id());

        $latitude_pegawai = $this->request->getVar('latitude_pegawai');
        $longitude_pegawai = $this->request->getVar('longitude_pegawai');
        $latitude_kantor = $this->request->getVar('latitude_kantor');
        $longitude_kantor = $this->request->getVar('longitude_kantor');
        $radius = $this->request->getVar('radius');
        $zona_waktu = $this->request->getVar('zona_waktu');
        $tanggal_masuk = $this->request->getVar('tanggal_masuk');
        $jam_masuk = $this->request->getVar('jam_masuk');

        if (empty($latitude_pegawai) || empty($longitude_pegawai)) {
            session()->setFlashdata('gagal', 'Lokasi Anda tidak terdeteksi. Mohon aktifkan fitur lokasi di perangkat Anda dan refresh halaman ini.');
            return redirect()->to(base_url());
        }

        if (empty($latitude_kantor) || empty($longitude_kantor)) {
            session()->setFlashdata('gagal', 'Lokasi presensi tidak valid. Mohon hubungi Admin.');
            return redirect()->to(base_url());
        }

        $perbedaan_koordinat = $longitude_pegawai - $longitude_kantor;

        if (!$perbedaan_koordinat) {
            session()->setFlashdata('warning', 'Mohon refresh halaman ini.');
            return redirect()->to(base_url());
        }

        $jarak = sin(deg2rad($latitude_pegawai)) * sin(deg2rad($latitude_kantor)) + cos(deg2rad($latitude_pegawai)) * cos(deg2rad($latitude_kantor)) * cos(deg2rad($perbedaan_koordinat));
        $jarak = acos($jarak);
        $jarak = rad2deg($jarak);
        $mil = $jarak * 60 * 1.1515;
        $km = $mil * 1.609344;
        $meter = $km * 1000;

        if ($meter > $radius) {
            session()->setFlashdata('gagal', 'Anda berada di luar area kantor');
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
        // Validasi face recognition
        $face_verified = $this->request->getPost('face_verified');
        $face_similarity = $this->request->getPost('face_similarity');
        
        if ($face_verified !== 'true' || floatval($face_similarity) < 0.5) {
            session()->setFlashdata('gagal', 'Verifikasi wajah gagal. Pastikan wajah Anda terlihat jelas dan sudah terdaftar.');
            return redirect()->to(base_url());
        }

        // Validasi foto
        $foto = $this->request->getPost('image-cam');
        if (empty($foto)) {
            session()->setFlashdata('gagal', 'Foto presensi tidak boleh kosong');
            return redirect()->to(base_url());
        }

        // Decode foto
        $foto = str_replace('data:image/jpeg;base64,', '', $foto);
        $foto = base64_decode($foto, true);
        
        if ($foto === false) {
            session()->setFlashdata('gagal', 'Format foto tidak valid');
            return redirect()->to(base_url());
        }

        // Simpan foto
        $username = $this->request->getPost('username');
        $nama_foto = 'masuk-' . date('Y-m-d-H-i-s') . '-' . $username . '.png';
        $folder = FCPATH . 'assets/img/foto_presensi/masuk/';
        
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        
        $file_path = $folder . $nama_foto;
        
        if (!file_put_contents($file_path, $foto)) {
            session()->setFlashdata('gagal', 'Gagal menyimpan foto presensi masuk');
            return redirect()->to(base_url());
        }

        $id_pegawai = $this->request->getPost('id_pegawai');
        $tanggal_masuk = $this->request->getPost('tanggal_masuk');
        $jam_masuk = $this->request->getPost('jam_masuk');

        try {
            $this->presensiModel->save([
                'id_pegawai' => $id_pegawai,
                'tanggal_masuk' => $tanggal_masuk,
                'jam_masuk' => $jam_masuk,
                'foto_masuk' => $nama_foto,
            ]);
        } catch (\Exception $e) {
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            log_message('error', 'Gagal simpan presensi: ' . $e->getMessage());
            session()->setFlashdata('gagal', 'Gagal menyimpan presensi');
            return redirect()->to(base_url());
        }

        // Telegram notifikasi
        try {
            $detail_pegawai = $this->pegawaiModel->getPegawaiById($id_pegawai);
            
            if ($detail_pegawai) {
                $jam_jadwal = $detail_pegawai->jam_masuk;
                
                if (strtotime($jam_masuk) > strtotime($jam_jadwal)) {
                    $status_text = 'TERLAMBAT ⚠️';
                } else {
                    $status_text = 'TEPAT WAKTU ✅';
                }
                
                $tanggal_indo = format_tanggal_indo($tanggal_masuk);
                
                $pesan  = "<b>🟢 PRESENSI MASUK (Face Recognition)</b>\n\n";
                $pesan .= "👤 <b>Nama:</b> " . esc($detail_pegawai->nama) . "\n";
                $pesan .= "🆔 <b>Nomor induk:</b> " . esc($detail_pegawai->nomor_induk) . "\n";
                $pesan .= "💼 <b>Unit:</b> " . esc($detail_pegawai->jabatan) . "\n";
                $pesan .= "📅 <b>Tanggal:</b> " . $tanggal_indo . "\n";
                $pesan .= "🕐 <b>Jam Masuk:</b> " . $jam_masuk . "\n";
                // Commented due to privacy concerns
                // $pesan .= "📍 <b>Lokasi:</b> " . esc($detail_pegawai->nama_lokasi) . "\n";
                // $pesan .= "🎯 <b>Akurasi Wajah:</b> " . round($face_similarity * 100, 1) . "%\n\n";
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
        $user_profile = $this->usersModel->getUserInfo(user_id());
        $presensi_masuk = $this->presensiModel->cekPresensiMasuk($user_profile->id_pegawai, date('Y-m-d'));

        $latitude_pegawai = $this->request->getVar('latitude_pegawai');
        $longitude_pegawai = $this->request->getVar('longitude_pegawai');
        $latitude_kantor = $this->request->getVar('latitude_kantor');
        $longitude_kantor = $this->request->getVar('longitude_kantor');
        $radius = $this->request->getVar('radius');
        $zona_waktu = $this->request->getVar('zona_waktu');
        $tanggal_keluar = $this->request->getPost('tanggal_keluar');
        $jam_keluar = $this->request->getPost('jam_keluar');

        // Jika user menonaktifkan lokasi, maka arahkan kembali ke halaman home
        if (empty($latitude_pegawai) || empty($longitude_pegawai)) {
            session()->setFlashdata('gagal', 'Lokasi Anda tidak terdeteksi. Mohon aktifkan fitur lokasi di perangkat Anda dan refresh halaman ini.');
            return redirect()->to(base_url());
        }

        // Jika lokasi presensi tidak terdeteksi, maka arahkan kembali ke halaman home
        if (empty($latitude_kantor) || empty($longitude_kantor)) {
            session()->setFlashdata('gagal', 'Lokasi presensi tidak valid. Mohon hubungi Admin.');
            return redirect()->to(base_url());
        }

        // Cek Perbedaan Koordinat Pegawai dengan Lokasi Presensi
        $perbedaan_koordinat = $longitude_pegawai - $longitude_kantor;
        $jarak = sin(deg2rad($latitude_pegawai)) * sin(deg2rad($latitude_kantor)) + cos(deg2rad($latitude_pegawai)) * cos(deg2rad($latitude_kantor)) * cos(deg2rad($perbedaan_koordinat));
        $jarak = acos($jarak);
        $jarak = rad2deg($jarak);
        $mil = $jarak * 60 * 1.1515;
        $km = $mil * 1.609344;
        $meter = $km * 1000;

        if ($meter > $radius) {
            session()->setFlashdata('gagal', 'Anda berada di luar area kantor');
            return redirect()->to(base_url());
        }

        $data = [
            'title' => 'Presensi Keluar',
            'user_profile' => $user_profile,
            'latitude_pegawai' => $latitude_pegawai,
            'longitude_pegawai' => $longitude_pegawai,
            'latitude_kantor' => $latitude_kantor,
            'longitude_kantor' => $longitude_kantor,
            'radius' => $radius,
            'tanggal_keluar' => $tanggal_keluar,
            'jam_keluar' => $jam_keluar,
            'data_presensi_masuk' => $presensi_masuk,
        ];

        return view('presensi/presensi_keluar', $data);
    }

    public function simpanPresensiKeluar()
    {
        $face_verified = $this->request->getPost('face_verified');
        $face_similarity = $this->request->getPost('face_similarity');
        
        // Batas similarity bisa disesuaikan, standar biasanya 0.7 atau 0.8
        if ($face_verified !== 'true' || floatval($face_similarity) < 0.5) {
            session()->setFlashdata('gagal', 'Verifikasi wajah gagal. Pastikan wajah Anda terlihat jelas dan sudah terdaftar.');
            return redirect()->to(base_url());
        }

        $foto = $this->request->getPost('image-cam');
        if (empty($foto)) {
            session()->setFlashdata('gagal', 'Foto presensi tidak boleh kosong');
            return redirect()->to(base_url());
        }

        $foto = str_replace('data:image/jpeg;base64,', '', $foto);
        $foto = base64_decode($foto, true);

        if ($foto === false) {
            session()->setFlashdata('gagal', 'Format foto tidak valid');
            return redirect()->to(base_url());
        }

        $username = $this->request->getPost('username');
        $nama_foto = 'keluar-' . date('Y-m-d-H-i-s') . '-' . $username . '.png';
        $folder = FCPATH . 'assets/img/foto_presensi/keluar/';
        
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        
        $file_path = $folder . $nama_foto;

        if (!file_put_contents($file_path, $foto)) {
            session()->setFlashdata('gagal', 'Gagal menyimpan foto presensi keluar');
            return redirect()->to(base_url());
        }

        $id_presensi = $this->request->getPost('id_presensi');
        $tanggal_keluar = $this->request->getPost('tanggal_keluar');
        $jam_keluar = $this->request->getPost('jam_keluar');

        try {
            $this->presensiModel->save([
                'id' => $id_presensi, 
                'tanggal_keluar' =>  $tanggal_keluar,
                'jam_keluar' => $jam_keluar,
                'foto_keluar' => $nama_foto,
            ]);
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

        $worksheet->setCellValue('A1', 'Rekap Presensi Pegawai');
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
        header('Content-Disposition: attachment;filename="PresenSi_Rekap Presensi Pegawai_' . $data_pegawai->nama . '_' . date('Y-m-d', strtotime($tanggal_awal)) . '_' . date('Y-m-d', strtotime($tanggal_akhir)) . '.xlsx"');
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
        if (empty($tanggal_filter)) {
            $tanggal_filter = date('Y-m-d');
        }

        $data_presensi_pegawai = $this->presensiModel->getLaporanHarianLengkap($tanggal_filter);

        $data_presensi = $data_presensi_pegawai['laporan-harian'];
        $pager = $data_presensi_pegawai['links'];
        $total = $data_presensi_pegawai['total'];
        $perPage = $data_presensi_pegawai['perPage'];

        // Proses Status menggunakan helper yang konsisten
        foreach ($data_presensi as $key => $value) {
            $data_presensi[$key]->status_kehadiran = $this->_tentukanStatus($value, $tanggal_filter);
        }

        $data = [
            'title' => 'Laporan Presensi Harian',
            'user_profile' => $user_profile,
            'data_tanggal' => date('d F Y', strtotime($tanggal_filter)),
            'tanggal_filter' => $tanggal_filter,
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
        if (empty($tanggal_filter)) {
            $tanggal_filter = date('Y-m-d');
        }

        $data_presensi = $this->presensiModel->getLaporanHarianLengkapNoPage($tanggal_filter);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        // ========== HEADER TITLE ==========
        $worksheet->setCellValue('A1', 'LAPORAN PRESENSI HARIAN');
        $worksheet->mergeCells('A1:J1');
        
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

        // ========== INFO TANGGAL ==========
        $worksheet->setCellValue('A3', 'Tanggal');
        $worksheet->setCellValue('B3', ':');
        $worksheet->setCellValue('C3', date('d F Y', strtotime($tanggal_filter)));
        
        $worksheet->getStyle('A3')->getFont()->setBold(true);
        $worksheet->getStyle('C3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1e3a8a']]
        ]);

        // ========== HEADER KOLOM ==========
        $headers = [
            'A6' => 'NO',
            'B6' => 'NOMOR INDUK',
            'C6' => 'NAMA',
            'D6' => 'STATUS',
            'E6' => 'JAM MASUK',
            'F6' => 'FOTO MASUK',
            'G6' => 'JAM PULANG',
            'H6' => 'FOTO PULANG',
            'I6' => 'TOTAL JAM KERJA',
            'J6' => 'KETERLAMBATAN'
        ];

        foreach ($headers as $cell => $value) {
            $worksheet->setCellValue($cell, $value);
        }

        // Style Header Kolom
        $worksheet->getStyle('A6:J6')->applyFromArray([
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

        foreach ($data_presensi as $data) {
            $status = $this->_tentukanStatus($data, $tanggal_filter);
            
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
                    $status_color = '16a34a'; // Hijau
                    break;
                case 'Sakit':
                    $status_color = 'eab308'; // Kuning
                    break;
                case 'Izin':
                    $status_color = '3b82f6'; // Biru
                    break;
                case 'Libur':
                    $status_color = '6b7280'; // Abu
                    break;
                case 'Alpha':
                    $status_color = 'dc2626'; // Merah
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
            $worksheet->setCellValue('D' . $data_start_row, $status);
            $worksheet->setCellValue('E' . $data_start_row, $jam_masuk);
            $worksheet->setCellValue('F' . $data_start_row, $foto_masuk);
            $worksheet->setCellValue('G' . $data_start_row, $jam_keluar);
            $worksheet->setCellValue('H' . $data_start_row, $foto_keluar);
            $worksheet->setCellValue('I' . $data_start_row, $total_jam_kerja);
            $worksheet->setCellValue('J' . $data_start_row, $keterlambatan);

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
            $worksheet->getStyle('D' . $data_start_row)->applyFromArray([
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

            $data_start_row++;
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

        // Set minimum width untuk kolom tertentu
        $worksheet->getColumnDimension('C')->setWidth(25); // Nama
        $worksheet->getColumnDimension('I')->setWidth(20); // Total Jam Kerja
        $worksheet->getColumnDimension('J')->setWidth(20); // Keterlambatan

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Presensi_Harian_' . $tanggal_filter . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    private function _generateDataBulanan($bulan, $tahun)
    {
        // 1. Ambil semua pegawai
        $semua_pegawai = $this->pegawaiModel->findAll();
        
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

        if (empty($filter_bulan)) $filter_bulan = date('m');
        if (empty($filter_tahun)) $filter_tahun = date('Y');

        $data_bulan = $filter_tahun . '-' . $filter_bulan;

        // 1. Generate Data Lengkap (Hadir + Alpha)
        $data_lengkap = $this->_generateDataBulanan($filter_bulan, $filter_tahun);

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

        $data = [
            'title' => 'Laporan Presensi Bulanan',
            'user_profile' => $user_profile,
            'tahun_mulai' => $tahun_mulai,
            'data_bulan' => $data_bulan,
            'data_presensi' => $data_presensi, // Data yang sudah dipotong
            'currentPage' => $currentPage,
            'pager' => $pager_links, // Link pagination
            'total' => $total,
            'perPage' => $perPage,
            'filter_bulan' => $filter_bulan,
            'filter_tahun' => $filter_tahun,
        ];

        return view('presensi/laporan_presensi_bulanan', $data);
    }

    public function laporanBulananExcel()
    {
        $filter_bulan = $this->request->getPOST('filter_bulan');
        $filter_tahun = $this->request->getPOST('filter_tahun');
        
        if (empty($filter_tahun)) $filter_tahun = date('Y');
        if (empty($filter_bulan)) $filter_bulan = date('m');

        $data_presensi = $this->_generateDataBulanan($filter_bulan, $filter_tahun);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        // ========== HEADER TITLE ==========
        $worksheet->setCellValue('A1', 'LAPORAN PRESENSI BULANAN');
        $worksheet->mergeCells('A1:I1');
        
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

        // ========== INFO PERIODE ==========
        $worksheet->setCellValue('A3', 'Bulan');
        $worksheet->setCellValue('B3', ':');
        $worksheet->setCellValue('C3', date('F', mktime(0, 0, 0, $filter_bulan, 10)));
        
        $worksheet->setCellValue('A4', 'Tahun');
        $worksheet->setCellValue('B4', ':');
        $worksheet->setCellValue('C4', $filter_tahun);
        
        $worksheet->getStyle('A3:A4')->getFont()->setBold(true);
        $worksheet->getStyle('C3:C4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1e3a8a']]
        ]);

        // ========== HEADER KOLOM ==========
        $headers = [
            'A6' => 'NO',
            'B6' => 'NOMOR INDUK',
            'C6' => 'NAMA',
            'D6' => 'TANGGAL',
            'E6' => 'STATUS',
            'F6' => 'JAM MASUK',
            'G6' => 'JAM PULANG',
            'H6' => 'TOTAL JAM KERJA',
            'I6' => 'KETERLAMBATAN'
        ];

        foreach ($headers as $cell => $value) {
            $worksheet->setCellValue($cell, $value);
        }

        $worksheet->getStyle('A6:I6')->applyFromArray([
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

        if (!empty($data_presensi)) {
            foreach ($data_presensi as $data) {
                
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
                $worksheet->setCellValue('D' . $data_start_row, date('d/m/Y', strtotime($data->tanggal_masuk)));
                $worksheet->setCellValue('E' . $data_start_row, $status);
                $worksheet->setCellValue('F' . $data_start_row, $jam_masuk);
                $worksheet->setCellValue('G' . $data_start_row, $jam_keluar);
                $worksheet->setCellValue('H' . $data_start_row, $total_jam_kerja_format);
                $worksheet->setCellValue('I' . $data_start_row, $keterlambatan_format);

                // Style row
                $worksheet->getStyle('A' . $data_start_row . ':I' . $data_start_row)->applyFromArray($styleBorder);
                
                // Zebra striping
                if ($nomor % 2 == 0) {
                    $worksheet->getStyle('A' . $data_start_row . ':I' . $data_start_row)->applyFromArray([
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

                // Center align
                $worksheet->getStyle('A' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle('B' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle('D' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle('E' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle('F' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle('G' . $data_start_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $data_start_row++;
            }
        }

        // ========== FOOTER ==========
        $footer_row = $data_start_row + 2;
        $worksheet->setCellValue('A' . $footer_row, 'PresenSI - "Si Pintar Urusan Presensi"');
        $worksheet->mergeCells('A' . $footer_row . ':I' . $footer_row);
        
        $footer_row++;
        $worksheet->setCellValue('A' . $footer_row, 'Diekspor pada: ' . date('d F Y H:i:s'));
        $worksheet->mergeCells('A' . $footer_row . ':I' . $footer_row);

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
        foreach (range('A', 'I') as $col) {
            $worksheet->getColumnDimension($col)->setAutoSize(true);
        }

        $worksheet->getColumnDimension('C')->setWidth(25);
        $worksheet->getColumnDimension('H')->setWidth(20);
        $worksheet->getColumnDimension('I')->setWidth(20);

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
        // Hanya user dengan group 3 (pegawai) yang bisa akses
        // if (!in_groups(3)) {
        //     return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        // }

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