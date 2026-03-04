<?php
// \app\Controllers\FileManager.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\FileManagerModel;
use ZipArchive;

class FileManager extends BaseController
{
    protected $usersModel;
    protected $fileManagerModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->fileManagerModel = new FileManagerModel();
        helper(['filesystem', 'number']);
    }

    public function index()
    {
        $user_profile = $this->usersModel->getUserInfo(user_id());

        // Get file statistics
        $stats = $this->getFileStatistics();

        // Get auto-delete settings
        $auto_delete_enabled = $this->fileManagerModel->getSetting('auto_delete_enabled');
        $auto_delete_days = $this->fileManagerModel->getSetting('auto_delete_days');
        $last_cleanup = $this->fileManagerModel->getSetting('last_cleanup');

        $data = [
            'title' => 'File Manager',
            'user_profile' => $user_profile,
            'stats' => $stats,
            'auto_delete_enabled' => $auto_delete_enabled,
            'auto_delete_days' => $auto_delete_days,
            'last_cleanup' => $last_cleanup,
        ];

        return view('file_manager/index', $data);
    }

    private function getFileStatistics()
    {
        $stats = [];

        // Foto Presensi Masuk
        $path_masuk = FCPATH . 'assets/img/foto_presensi/masuk/';
        $stats['foto_masuk'] = $this->getDirectoryStats($path_masuk);

        // Foto Presensi Keluar
        $path_keluar = FCPATH . 'assets/img/foto_presensi/keluar/';
        $stats['foto_keluar'] = $this->getDirectoryStats($path_keluar);

        // PDF Ketidakhadiran
        $path_pdf = FCPATH . 'assets/file/surat_keterangan_ketidakhadiran/';
        $stats['pdf_izin'] = $this->getDirectoryStats($path_pdf, 'pdf');

        // Total
        $stats['total_files'] = $stats['foto_masuk']['count'] + 
                                $stats['foto_keluar']['count'] + 
                                $stats['pdf_izin']['count'];
        $stats['total_size'] = $stats['foto_masuk']['size'] + 
                               $stats['foto_keluar']['size'] + 
                               $stats['pdf_izin']['size'];

        return $stats;
    }

    private function getDirectoryStats($path, $extension = null)
    {
        if (!is_dir($path)) {
            return ['count' => 0, 'size' => 0, 'files' => []];
        }

        $files = directory_map($path, 1);
        $count = 0;
        $size = 0;
        $file_list = [];

        if ($files) {
            foreach ($files as $file) {
                if (is_file($path . $file)) {
                    if ($extension === null || pathinfo($file, PATHINFO_EXTENSION) === $extension) {
                        $file_size = filesize($path . $file);
                        $file_time = filemtime($path . $file);
                        
                        $count++;
                        $size += $file_size;
                        
                        $file_list[] = [
                            'name' => $file,
                            'size' => $file_size,
                            'date' => $file_time,
                            'path' => $path . $file
                        ];
                    }
                }
            }
        }

        // Sort by date descending
        usort($file_list, function($a, $b) {
            return $b['date'] - $a['date'];
        });

        return [
            'count' => $count,
            'size' => $size,
            'files' => $file_list
        ];
    }

    public function browse()
    {
        $user_profile = $this->usersModel->getUserInfo(user_id());
        $type = $this->request->getGet('type') ?? 'foto_masuk';

        $paths = [
            'foto_masuk' => FCPATH . 'assets/img/foto_presensi/masuk/',
            'foto_keluar' => FCPATH . 'assets/img/foto_presensi/keluar/',
            'pdf_izin' => FCPATH . 'assets/file/surat_keterangan_ketidakhadiran/',
        ];

        $path = $paths[$type] ?? $paths['foto_masuk'];
        $extension = ($type === 'pdf_izin') ? 'pdf' : null;

        $stats = $this->getDirectoryStats($path, $extension);

        // Pagination
        $perPage = 20;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $perPage;
        
        $total = count($stats['files']);
        $files = array_slice($stats['files'], $offset, $perPage);

        $pager = service('pager');
        $pager_links = $pager->makeLinks($page, $perPage, $total);

        $data = [
            'title' => 'Browse Files',
            'user_profile' => $user_profile,
            'type' => $type,
            'files' => $files,
            'stats' => $stats,
            'pager' => $pager_links,
            'total' => $total,
            'currentPage' => $page,
        ];

        return view('file_manager/browse', $data);
    }

    public function download()
    {
        $file = $this->request->getGet('file');
        $type = $this->request->getGet('type');

        $paths = [
            'foto_masuk' => FCPATH . 'assets/img/foto_presensi/masuk/',
            'foto_keluar' => FCPATH . 'assets/img/foto_presensi/keluar/',
            'pdf_izin' => FCPATH . 'assets/file/surat_keterangan_ketidakhadiran/',
        ];

        $base_path = $paths[$type] ?? '';
        $file_path = $base_path . basename($file);

        if (!file_exists($file_path)) {
            session()->setFlashdata('gagal', 'File tidak ditemukan.');
            return redirect()->back();
        }

        return $this->response->download($file_path, null);
    }

    public function downloadBulk()
    {
        $type = $this->request->getPost('type');
        $days = $this->request->getPost('days');

        $paths = [
            'foto_masuk' => FCPATH . 'assets/img/foto_presensi/masuk/',
            'foto_keluar' => FCPATH . 'assets/img/foto_presensi/keluar/',
            'pdf_izin' => FCPATH . 'assets/file/surat_keterangan_ketidakhadiran/',
        ];

        $path = $paths[$type] ?? '';
        $extension = ($type === 'pdf_izin') ? 'pdf' : null;

        if (!is_dir($path)) {
            session()->setFlashdata('gagal', 'Direktori tidak ditemukan.');
            return redirect()->back();
        }

        $stats = $this->getDirectoryStats($path, $extension);
        $files_to_zip = [];

        // Filter by days if specified
        if ($days && $days > 0) {
            $cutoff_time = time() - ($days * 86400);
            foreach ($stats['files'] as $file) {
                if ($file['date'] >= $cutoff_time) {
                    $files_to_zip[] = $file;
                }
            }
        } else {
            $files_to_zip = $stats['files'];
        }

        if (empty($files_to_zip)) {
            session()->setFlashdata('gagal', 'Tidak ada file untuk didownload.');
            return redirect()->back();
        }

        // Create ZIP
        $zip_name = $type . '_' . date('Y-m-d_H-i-s') . '.zip';
        $zip_path = WRITEPATH . 'uploads/' . $zip_name;

        $zip = new ZipArchive();
        if ($zip->open($zip_path, ZipArchive::CREATE) !== TRUE) {
            session()->setFlashdata('gagal', 'Gagal membuat file ZIP.');
            return redirect()->back();
        }

        foreach ($files_to_zip as $file) {
            $zip->addFile($file['path'], $file['name']);
        }

        $zip->close();

        return $this->response->download($zip_path, null)->setFileName($zip_name);
    }

    public function deleteBulk()
    {
        $type = $this->request->getPost('type');
        $days = $this->request->getPost('days');

        if (!$days || $days < 1) {
            session()->setFlashdata('gagal', 'Jumlah hari tidak valid.');
            return redirect()->back();
        }

        $paths = [
            'foto_masuk' => FCPATH . 'assets/img/foto_presensi/masuk/',
            'foto_keluar' => FCPATH . 'assets/img/foto_presensi/keluar/',
            'pdf_izin' => FCPATH . 'assets/file/surat_keterangan_ketidakhadiran/',
        ];

        $path = $paths[$type] ?? '';
        $extension = ($type === 'pdf_izin') ? 'pdf' : null;

        $stats = $this->getDirectoryStats($path, $extension);
        $cutoff_time = time() - ($days * 86400);
        $deleted_count = 0;

        foreach ($stats['files'] as $file) {
            if ($file['date'] < $cutoff_time) {
                if (unlink($file['path'])) {
                    $deleted_count++;
                }
            }
        }

        session()->setFlashdata('berhasil', "Berhasil menghapus {$deleted_count} file.");
        return redirect()->to(base_url('file-manager/browse?type=' . $type));
    }

    public function updateSettings()
    {
        $auto_delete_enabled = $this->request->getPost('auto_delete_enabled') ? '1' : '0';
        $auto_delete_days = $this->request->getPost('auto_delete_days');

        $this->fileManagerModel->updateSetting('auto_delete_enabled', $auto_delete_enabled);
        $this->fileManagerModel->updateSetting('auto_delete_days', $auto_delete_days);

        session()->setFlashdata('berhasil', 'Pengaturan berhasil disimpan.');
        return redirect()->to(base_url('file-manager'));
    }

    public function uploadLogo()
    {
        $validation = $this->validate([
            'logo' => [
                'rules' => 'uploaded[logo]|max_size[logo,2048]|is_image[logo]',
                'errors' => [
                    'uploaded' => 'Logo harus diupload.',
                    'max_size' => 'Ukuran maksimal 2MB.',
                    'is_image' => 'File harus berupa gambar.'
                ]
            ]
        ]);

        if (!$validation) {
            session()->setFlashdata('gagal', implode('<br>', $this->validator->getErrors()));
            return redirect()->back();
        }

        $logo = $this->request->getFile('logo');
        $logo_path = FCPATH . 'assets/img/company/logo.png';

        // Backup old logo
        if (file_exists($logo_path)) {
            $backup_path = FCPATH . 'assets/img/company/logo_backup_' . date('Y-m-d_H-i-s') . '.png';
            copy($logo_path, $backup_path);
        }

        // Move new logo
        $logo->move(FCPATH . 'assets/img/company/', 'logo.png', true);

        session()->setFlashdata('berhasil', 'Logo berhasil diperbarui.');
        return redirect()->to(base_url('file-manager'));
    }

    public function preview()
    {
        $file = $this->request->getGet('file');
        $type = $this->request->getGet('type');

        $paths = [
            'foto_masuk' => FCPATH . 'assets/img/foto_presensi/masuk/',
            'foto_keluar' => FCPATH . 'assets/img/foto_presensi/keluar/',
            'pdf_izin' => FCPATH . 'assets/file/surat_keterangan_ketidakhadiran/',
        ];

        $base_path = $paths[$type] ?? '';
        $file_path = $base_path . basename($file);

        if (!file_exists($file_path)) {
            return $this->response->setJSON(['error' => 'File tidak ditemukan']);
        }

        $mime_type = mime_content_type($file_path);
        
        if (strpos($mime_type, 'image') !== false) {
            // Return image
            $this->response->setHeader('Content-Type', $mime_type);
            return $this->response->setBody(file_get_contents($file_path));
        } elseif ($mime_type === 'application/pdf') {
            // Return PDF
            $this->response->setHeader('Content-Type', 'application/pdf');
            return $this->response->setBody(file_get_contents($file_path));
        }

        return $this->response->setJSON(['error' => 'Preview tidak tersedia']);
    }

    public function runCleanup()
    {
        $auto_delete_enabled = $this->fileManagerModel->getSetting('auto_delete_enabled');
        $auto_delete_days = $this->fileManagerModel->getSetting('auto_delete_days');

        if ($auto_delete_enabled != '1') {
            session()->setFlashdata('gagal', 'Auto-delete tidak diaktifkan.');
            return redirect()->to(base_url('file-manager'));
        }

        $paths = [
            'foto_masuk' => FCPATH . 'assets/img/foto_presensi/masuk/',
            'foto_keluar' => FCPATH . 'assets/img/foto_presensi/keluar/',
            'pdf_izin' => FCPATH . 'assets/file/surat_keterangan_ketidakhadiran/',
        ];

        $total_deleted = 0;

        foreach ($paths as $type => $path) {
            $extension = ($type === 'pdf_izin') ? 'pdf' : null;
            $stats = $this->getDirectoryStats($path, $extension);
            $cutoff_time = time() - ($auto_delete_days * 86400);

            foreach ($stats['files'] as $file) {
                if ($file['date'] < $cutoff_time) {
                    if (unlink($file['path'])) {
                        $total_deleted++;
                    }
                }
            }
        }

        // Update last cleanup time
        $this->fileManagerModel->updateSetting('last_cleanup', date('Y-m-d H:i:s'));

        session()->setFlashdata('berhasil', "Cleanup selesai. {$total_deleted} file dihapus.");
        return redirect()->to(base_url('file-manager'));
    }
}