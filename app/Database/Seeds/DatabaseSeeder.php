<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(MasterDataSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(DemoUserSeeder::class);
        $this->call(DemoAttendanceSeeder::class);
    }
}