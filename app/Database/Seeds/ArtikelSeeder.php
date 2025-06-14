<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ArtikelSeeder extends Seeder
{
    public function run()
    {
        $data = [];
        
        // Generate 25 artikel dummy untuk testing pagination
        for ($i = 1; $i <= 25; $i++) {
            $data[] = [
                'judul' => "Artikel Dummy ke-$i",
                'isi' => "Ini adalah isi artikel dummy ke-$i. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
                'slug' => "artikel-dummy-ke-$i",
                'status' => rand(0, 1), // Random status 0 atau 1
                'kategori' => ['teknologi', 'pendidikan', 'kesehatan', 'olahraga'][rand(0, 3)], // Random kategori
                'created_at' => date('Y-m-d H:i:s', strtotime("-$i days")), // Tanggal mundur
                'updated_at' => date('Y-m-d H:i:s', strtotime("-$i days"))
            ];
        }

        // Using Query Builder
        $this->db->table('artikel')->insertBatch($data);
    }
}
