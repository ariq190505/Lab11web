<?php

namespace App\Controllers;

use App\Models\ArtikelModel;

class DummyData extends BaseController
{
    public function createArticles()
    {
        $model = new ArtikelModel();
        
        // Sample articles with image placeholders
        $articles = [
            [
                'judul' => 'Teknologi AI Terbaru 2025',
                'isi' => 'Artificial Intelligence terus berkembang pesat di tahun 2025. Berbagai inovasi baru bermunculan dalam bidang machine learning, deep learning, dan neural networks. Teknologi ini semakin memudahkan kehidupan manusia dalam berbagai aspek.',
                'slug' => 'teknologi-ai-terbaru-2025',
                'gambar' => 'ai-technology.jpg',
                'status' => 1
            ],
            [
                'judul' => 'Tips Belajar Programming untuk Pemula',
                'isi' => 'Belajar programming bisa menjadi tantangan bagi pemula. Artikel ini memberikan tips dan trik untuk memulai journey programming dengan lebih mudah dan efektif. Mulai dari memilih bahasa pemrograman hingga praktik coding yang baik.',
                'slug' => 'tips-belajar-programming-pemula',
                'gambar' => 'programming-tips.jpg',
                'status' => 1
            ],
            [
                'judul' => 'Framework Web Development Populer',
                'isi' => 'Dalam dunia web development, framework menjadi tools yang sangat penting. Artikel ini membahas berbagai framework populer seperti React, Vue, Angular untuk frontend dan Laravel, CodeIgniter untuk backend development.',
                'slug' => 'framework-web-development-populer',
                'gambar' => 'web-framework.jpg',
                'status' => 1
            ],
            [
                'judul' => 'Database Design Best Practices',
                'isi' => 'Merancang database yang baik adalah kunci sukses aplikasi. Artikel ini membahas best practices dalam database design, normalisasi, indexing, dan optimasi query untuk performa yang maksimal.',
                'slug' => 'database-design-best-practices',
                'gambar' => 'database-design.jpg',
                'status' => 0
            ],
            [
                'judul' => 'Mobile App Development Trends',
                'isi' => 'Tren pengembangan aplikasi mobile terus berubah. Cross-platform development dengan Flutter dan React Native semakin populer. Progressive Web Apps juga menjadi alternatif menarik untuk pengembangan aplikasi mobile.',
                'slug' => 'mobile-app-development-trends',
                'gambar' => 'mobile-development.jpg',
                'status' => 1
            ]
        ];
        
        $inserted = 0;
        foreach ($articles as $article) {
            // Check if article already exists
            $existing = $model->where('slug', $article['slug'])->first();
            if (!$existing) {
                $model->insert($article);
                $inserted++;
            }
        }
        
        echo "<h2>Dummy Data Creation</h2>";
        echo "<p>$inserted artikel berhasil ditambahkan!</p>";
        echo "<p><a href='" . base_url('/admin/artikel') . "'>Lihat Admin Panel</a></p>";
        echo "<p><a href='" . base_url('/artikel') . "'>Lihat Daftar Artikel</a></p>";
    }
    
    public function createImages()
    {
        // Create placeholder images
        $imageDir = ROOTPATH . 'public/gambar/';
        
        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0755, true);
        }
        
        $images = [
            'ai-technology.jpg',
            'programming-tips.jpg', 
            'web-framework.jpg',
            'database-design.jpg',
            'mobile-development.jpg'
        ];
        
        $created = 0;
        foreach ($images as $imageName) {
            $imagePath = $imageDir . $imageName;
            if (!file_exists($imagePath)) {
                // Create a simple colored rectangle as placeholder
                $width = 400;
                $height = 300;
                $image = imagecreate($width, $height);
                
                // Random colors
                $bgColor = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
                $textColor = imagecolorallocate($image, 255, 255, 255);
                
                // Add text
                $text = str_replace(['.jpg', '-'], [' ', ' '], $imageName);
                $text = ucwords($text);
                
                // Add text to image (if GD has font support)
                if (function_exists('imagestring')) {
                    imagestring($image, 5, 50, 140, $text, $textColor);
                }
                
                // Save as JPEG
                imagejpeg($image, $imagePath, 80);
                imagedestroy($image);
                $created++;
            }
        }
        
        echo "<h2>Placeholder Images Creation</h2>";
        echo "<p>$created gambar placeholder berhasil dibuat!</p>";
        echo "<p>Images created in: /public/gambar/</p>";
        echo "<p><a href='" . base_url('/dummydata/createArticles') . "'>Create Articles</a></p>";
    }
}
