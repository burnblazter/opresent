<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $groups = [
            ['name' => 'admin', 'description' => 'Administrator development'],
            ['name' => 'pegawai', 'description' => 'User attendance development'],
            ['name' => 'head', 'description' => 'Manager development'],
            ['name' => 'helper', 'description' => 'Assistant development'],
            ['name' => 'kiosk', 'description' => 'Kiosk development'],
        ];
        $builder = $this->db->table('auth_groups');
        foreach ($groups as $group) {
            if (!$builder->where('name', $group['name'])->countAllResults()) {
                $builder->insert($group);
            }
        }
    }
}