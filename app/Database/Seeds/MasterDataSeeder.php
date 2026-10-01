<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $jabatan = $this->db->table('jabatan');
        if (!$jabatan->where('slug', 'developer')->countAllResults()) {
            $jabatan->insert(['jabatan' => 'Developer', 'slug' => 'developer', 'created_at' => $now, 'updated_at' => $now]);
        }
        if (!$jabatan->where('slug', 'staff')->countAllResults()) {
            $jabatan->insert(['jabatan' => 'Staff', 'slug' => 'staff', 'created_at' => $now, 'updated_at' => $now]);
        }

        $lokasi = $this->db->table('lokasi_presensi');
        if (!$lokasi->where('slug', 'kantor-demo')->countAllResults()) {
            $lokasi->insert([
                'nama_lokasi' => 'Kantor Demo',
                'slug' => 'kantor-demo',
                'alamat_lokasi' => 'Alamat fiktif untuk development',
                'tipe_lokasi' => 'Kantor',
                'latitude' => '0.000000',
                'longitude' => '0.000000',
                'radius' => 100,
                'zona_waktu' => 'Asia/Jakarta',
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '17:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $jabatanIds = $this->db->table('jabatan')->select('id, slug')->get()->getResultArray();
        $jabatanBySlug = array_column($jabatanIds, 'id', 'slug');
        $lokasiId = $lokasi->where('slug', 'kantor-demo')->get()->getRow()->id;
        $pegawai = $this->db->table('pegawai');
        $people = [
            ['nomor_induk' => 'EMP-0001', 'nama' => 'Budi Santoso', 'jenis_kelamin' => 'L', 'id_jabatan' => $jabatanBySlug['developer'], 'alamat' => 'Alamat dummy 1', 'no_handphone' => '000000000001'],
            ['nomor_induk' => 'EMP-0002', 'nama' => 'Siti Rahma', 'jenis_kelamin' => 'P', 'id_jabatan' => $jabatanBySlug['staff'], 'alamat' => 'Alamat dummy 2', 'no_handphone' => '000000000002'],
        ];
        foreach ($people as $person) {
            if (!$pegawai->where('nomor_induk', $person['nomor_induk'])->countAllResults()) {
                $pegawai->insert($person + ['id_lokasi_presensi' => $lokasiId, 'foto' => '', 'created_at' => $now, 'updated_at' => $now]);
            }
        }

        $holiday = $this->db->table('hari_libur');
        if (!$holiday->where('tanggal', '2030-01-01')->countAllResults()) {
            $holiday->insert(['tanggal' => '2030-01-01', 'keterangan' => 'Hari demo', 'source' => 'demo', 'approved' => 1, 'created_at' => $now, 'updated_at' => $now]);
        }
    }
}