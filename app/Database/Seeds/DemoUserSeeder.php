<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $people = $this->db->table('pegawai')->select('id, nomor_induk')->get()->getResultArray();
        $peopleByNumber = array_column($people, 'id', 'nomor_induk');
        $users = [
            ['email' => 'admin@example.test', 'username' => 'demo-admin', 'nomor_induk' => 'EMP-0001', 'group' => 'admin'],
            ['email' => 'staff@example.test', 'username' => 'demo-staff', 'nomor_induk' => 'EMP-0002', 'group' => 'pegawai'],
        ];
        $userBuilder = $this->db->table('users');
        $groupBuilder = $this->db->table('auth_groups');
        $membershipBuilder = $this->db->table('auth_groups_users');
        $passwordHash = password_hash(base64_encode(hash('sha384', 'ChangeMe123!', true)), PASSWORD_DEFAULT);
        foreach ($users as $user) {
            $existing = $userBuilder->where('email', $user['email'])->get()->getRow();
            if (!$existing) {
                $userBuilder->insert([
                    'id_pegawai' => $peopleByNumber[$user['nomor_induk']],
                    'email' => $user['email'],
                    'username' => $user['username'],
                    'password_hash' => $passwordHash,
                    'active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $userId = $this->db->insertID();
            } else {
                $userId = $existing->id;
            }
            $groupId = $groupBuilder->where('name', $user['group'])->get()->getRow()->id;
            if (!$membershipBuilder->where(['group_id' => $groupId, 'user_id' => $userId])->countAllResults()) {
                $membershipBuilder->insert(['group_id' => $groupId, 'user_id' => $userId]);
            }
        }
    }
}