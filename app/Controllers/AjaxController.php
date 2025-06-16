<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\Request;
use CodeIgniter\HTTP\Response;
use App\Models\ArtikelModel;

class AjaxController extends Controller
{
    public function index()
    {
        return view('ajax/index');
    }

    public function artikel()
    {
        $data = [
            'title' => 'Data Artikel - AJAX Demo',
            'subtitle' => 'Menampilkan data artikel dengan teknologi AJAX'
        ];
        return view('ajax/artikel_data', $data);
    }

    public function simple()
    {
        $data = [
            'title' => 'Data Artikel - Simple AJAX'
        ];
        return view('ajax/simple_view', $data);
    }

    public function getData()
    {
        $model = new ArtikelModel();

        // Ambil 5 artikel pertama dimulai dari ID 1
        $data = $model->orderBy('id', 'ASC')->findAll(5);

        // Kirim data dalam format JSON
        return $this->response->setJSON($data);
    }

    public function getAllData()
    {
        $model = new ArtikelModel();

        // Ambil semua artikel dimulai dari ID 1
        $data = $model->orderBy('id', 'ASC')->findAll();

        // Kirim data dalam format JSON
        return $this->response->setJSON($data);
    }

    public function getDataRange($start = 1, $limit = 5)
    {
        $model = new ArtikelModel();

        // Ambil artikel dengan range tertentu
        $data = $model->where('id >=', $start)
                     ->orderBy('id', 'ASC')
                     ->findAll($limit);

        // Kirim data dalam format JSON
        return $this->response->setJSON($data);
    }

    public function delete($id)
    {
        $model = new ArtikelModel();
        $result = $model->delete($id);
        
        $data = [
            'status' => $result ? 'OK' : 'ERROR',
            'message' => $result ? 'Data berhasil dihapus' : 'Gagal menghapus data'
        ];
        
        // Kirim data dalam format JSON
        return $this->response->setJSON($data);
    }

    public function getById($id)
    {
        $model = new ArtikelModel();
        $data = $model->find($id);
        
        if ($data) {
            return $this->response->setJSON([
                'status' => 'OK',
                'data' => $data
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'ERROR',
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }

    public function create()
    {
        $model = new ArtikelModel();
        
        $data = [
            'judul' => $this->request->getPost('judul'),
            'isi' => $this->request->getPost('isi'),
            'slug' => url_title($this->request->getPost('judul')),
            'status' => $this->request->getPost('status') ?? 0
        ];

        $result = $model->insert($data);
        
        if ($result) {
            return $this->response->setJSON([
                'status' => 'OK',
                'message' => 'Data berhasil ditambahkan',
                'id' => $result
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'ERROR',
                'message' => 'Gagal menambahkan data'
            ]);
        }
    }

    public function update($id)
    {
        $model = new ArtikelModel();
        
        $data = [
            'judul' => $this->request->getPost('judul'),
            'isi' => $this->request->getPost('isi'),
            'slug' => url_title($this->request->getPost('judul')),
            'status' => $this->request->getPost('status') ?? 0
        ];

        $result = $model->update($id, $data);
        
        if ($result) {
            return $this->response->setJSON([
                'status' => 'OK',
                'message' => 'Data berhasil diupdate'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'ERROR',
                'message' => 'Gagal mengupdate data'
            ]);
        }
    }
}
