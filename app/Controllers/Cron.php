<?php

namespace App\Controllers;

use App\Models\FileManagerModel;

class Cron extends BaseController
{
    public function autoCleanup()
    {
        $fileManagerModel = new FileManagerModel();
        
        $auto_delete_enabled = $fileManagerModel->getSetting('auto_delete_enabled');
        
        if ($auto_delete_enabled != '1') {
            echo "Auto-delete disabled\n";
            return;
        }

        $auto_delete_days = $fileManagerModel->getSetting('auto_delete_days');
        $cutoff_time = time() - ($auto_delete_days * 86400);

        $paths = [
            'foto_masuk' => FCPATH . 'assets/img/foto_presensi/masuk/',
            'foto_keluar' => FCPATH . 'assets/img/foto_presensi/keluar/',
            'pdf_izin' => FCPATH . 'assets/file-ketidakhadiran/',
        ];

        $total_deleted = 0;

        foreach ($paths as $type => $path) {
            if (!is_dir($path)) continue;

            $files = directory_map($path, 1);
            if (!$files) continue;

            foreach ($files as $file) {
                $file_path = $path . $file;
                if (is_file($file_path) && filemtime($file_path) < $cutoff_time) {
                    if (unlink($file_path)) {
                        $total_deleted++;
                    }
                }
            }
        }

        $fileManagerModel->updateSetting('last_cleanup', date('Y-m-d H:i:s'));
        
        echo "Cleanup completed. Deleted {$total_deleted} files.\n";
    }
}