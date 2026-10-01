<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoAttendanceSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $pegawaiId = $this->db->table('pegawai')->where('nomor_induk', 'EMP-0002')->get()->getRow()->id;
        $presensi = $this->db->table('presensi');
        if (!$presensi->where(['id_pegawai' => $pegawaiId, 'tanggal_masuk' => '2030-01-02'])->countAllResults()) {
            $presensi->insert([
                'id_pegawai' => $pegawaiId,
                'tanggal_masuk' => '2030-01-02',
                'jam_masuk' => '08:02:00',
                'foto_masuk' => '',
                'tanggal_keluar' => '2030-01-02',
                'jam_keluar' => '17:01:00',
                'foto_keluar' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $absence = $this->db->table('ketidakhadiran');
        if (!$absence->where(['id_pegawai' => $pegawaiId, 'tanggal_mulai' => '2030-01-03'])->countAllResults()) {
            $absence->insert([
                'id_pegawai' => $pegawaiId,
                'tipe_ketidakhadiran' => 'IZIN',
                'tanggal_mulai' => '2030-01-03',
                'tanggal_berakhir' => '2030-01-03',
                'deskripsi' => 'Contoh izin untuk demo',
                'file' => '',
                'status_pengajuan' => 'APPROVED',
                'catatan_admin' => 'Data demo',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}