<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuthGroupsPermissionsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // --- HEAD (Group 1) ---
            [
                'group_id'          => 1,
                'permission_id'     => 1, // kelola_data
            ],
            [
                'group_id'          => 1,
                'permission_id'     => 2, // isi_presensi
            ],
            [
                'group_id'          => 1,
                'permission_id'     => 3, // kelola_pengajuan_cuti
            ],

            // --- ADMINISTRATOR (Group 2) ---
            [
                'group_id'          => 2,
                'permission_id'     => 2, // isi_presensi (Sudah ada sebelumnya)
            ],
            [
                'group_id'          => 2,
                'permission_id'     => 1, // Agar Admin bisa akses menu kelola data
            ],
            [
                'group_id'          => 2,
                'permission_id'     => 3, // Agar Admin bisa approve/reject cuti
            ],

            // --- PEGAWAI (Group 3) ---
            [
                'group_id'          => 3,
                'permission_id'     => 1,
            ],
            [
                'group_id'          => 3,
                'permission_id'     => 3,
            ],
        ];

        // Using Query Builder
        // Hapus data lama dulu agar tidak duplikat saat dijalankan ulang (Opsional tapi disarankan saat dev)
        // $this->db->table('auth_groups_permissions')->truncate(); 
        
        $this->db->table('auth_groups_permissions')->insertBatch($data);
    }
}