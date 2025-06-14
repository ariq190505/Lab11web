<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin',
                'useremail' => 'admin@lab7web.com',
                'userpassword' => password_hash('admin123', PASSWORD_DEFAULT)
            ]
        ];

        // Using Query Builder
        $this->db->table('user')->insertBatch($data);
    }
}
