<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePresensiTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'jabatan' => ['type' => 'varchar', 'constraint' => 255],
            'slug' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('slug');
        $this->forge->createTable('jabatan', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARACTER SET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_lokasi' => ['type' => 'varchar', 'constraint' => 255],
            'slug' => ['type' => 'varchar', 'constraint' => 255],
            'alamat_lokasi' => ['type' => 'varchar', 'constraint' => 255],
            'tipe_lokasi' => ['type' => 'varchar', 'constraint' => 255],
            'latitude' => ['type' => 'varchar', 'constraint' => 50],
            'longitude' => ['type' => 'varchar', 'constraint' => 50],
            'radius' => ['type' => 'int', 'constraint' => 11],
            'zona_waktu' => ['type' => 'varchar', 'constraint' => 100],
            'jam_masuk' => ['type' => 'time'],
            'jam_pulang' => ['type' => 'time'],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('slug');
        $this->forge->createTable('lokasi_presensi', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARACTER SET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nomor_induk' => ['type' => 'varchar', 'constraint' => 50],
            'id_jabatan' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'id_lokasi_presensi' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'nama' => ['type' => 'varchar', 'constraint' => 255],
            'jenis_kelamin' => ['type' => 'varchar', 'constraint' => 10],
            'alamat' => ['type' => 'varchar', 'constraint' => 255],
            'no_handphone' => ['type' => 'varchar', 'constraint' => 255],
            'foto' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nomor_induk');
        $this->forge->addKey('id_jabatan');
        $this->forge->addKey('id_lokasi_presensi');
        $this->forge->addForeignKey('id_jabatan', 'jabatan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_lokasi_presensi', 'lokasi_presensi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pegawai', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARACTER SET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        $this->db->query('ALTER TABLE `users` ADD COLUMN `id_pegawai` INT UNSIGNED NULL AFTER `id`, ADD INDEX `users_id_pegawai_index` (`id_pegawai`), ADD CONSTRAINT `users_id_pegawai_foreign` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_pegawai' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'tanggal_masuk' => ['type' => 'date'],
            'jam_masuk' => ['type' => 'time'],
            'foto_masuk' => ['type' => 'varchar', 'constraint' => 255],
            'tanggal_keluar' => ['type' => 'date'],
            'jam_keluar' => ['type' => 'time'],
            'foto_keluar' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['id_pegawai', 'tanggal_masuk']);
        $this->forge->addForeignKey('id_pegawai', 'pegawai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('presensi', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARACTER SET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_pegawai' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'tipe_ketidakhadiran' => ['type' => 'enum', 'constraint' => ['IZIN', 'SAKIT']],
            'tanggal_mulai' => ['type' => 'date'],
            'tanggal_berakhir' => ['type' => 'date'],
            'deskripsi' => ['type' => 'varchar', 'constraint' => 255],
            'file' => ['type' => 'varchar', 'constraint' => 255],
            'status_pengajuan' => ['type' => 'varchar', 'constraint' => 20],
            'catatan_admin' => ['type' => 'mediumtext', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['id_pegawai', 'tanggal_mulai']);
        $this->forge->addForeignKey('id_pegawai', 'pegawai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ketidakhadiran', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARACTER SET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tanggal' => ['type' => 'date'],
            'keterangan' => ['type' => 'varchar', 'constraint' => 255],
            'source' => ['type' => 'varchar', 'constraint' => 50, 'null' => true],
            'approved' => ['type' => 'tinyint', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('tanggal');
        $this->forge->createTable('hari_libur', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARACTER SET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_pegawai' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'descriptor' => ['type' => 'mediumtext'],
            'label' => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'model_version' => ['type' => 'varchar', 'constraint' => 50, 'default' => 'human-v1'],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('id_pegawai');
        $this->forge->addForeignKey('id_pegawai', 'pegawai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('face_descriptors', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARACTER SET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_pegawai' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'descriptor' => ['type' => 'mediumtext'],
            'image_path' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'reason' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status' => ['type' => 'enum', 'constraint' => ['pending', 'approved', 'rejected'], 'default' => 'pending'],
            'label' => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'model_version' => ['type' => 'varchar', 'constraint' => 50, 'default' => 'human-v1'],
            'approved_by' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approved_at' => ['type' => 'datetime', 'null' => true],
            'rejection_reason' => ['type' => 'mediumtext', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('id_pegawai');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('id_pegawai', 'pegawai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('face_descriptors_request', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARACTER SET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'varchar', 'constraint' => 255],
            'token' => ['type' => 'varchar', 'constraint' => 255],
            'created_time' => ['type' => 'int', 'constraint' => 11],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addKey('token');
        $this->forge->createTable('email_tokens', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARACTER SET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'setting_key' => ['type' => 'varchar', 'constraint' => 100],
            'setting_value' => ['type' => 'text', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('setting_key');
        $this->forge->createTable('file_manager_settings', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARACTER SET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('face_descriptors_request', true);
        $this->forge->dropTable('face_descriptors', true);
        $this->forge->dropTable('email_tokens', true);
        $this->forge->dropTable('file_manager_settings', true);
        $this->forge->dropTable('hari_libur', true);
        $this->forge->dropTable('ketidakhadiran', true);
        $this->forge->dropTable('presensi', true);
        $this->forge->dropForeignKey('users', 'users_id_pegawai_foreign');
        $this->forge->dropColumn('users', 'id_pegawai');
        $this->forge->dropTable('pegawai', true);
        $this->forge->dropTable('lokasi_presensi', true);
        $this->forge->dropTable('jabatan', true);
    }
}