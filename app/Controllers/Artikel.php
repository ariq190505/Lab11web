<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Artikel extends BaseController
{
    public function index()
    {
        $title = 'Daftar Artikel';

        try {
            $model = new ArtikelModel();
            $artikel = $model->where('status', 1)->findAll(); // Hanya artikel published
        } catch (\Exception $e) {
            // If database error, use dummy data
            $artikel = [
                [
                    'id' => 1,
                    'judul' => 'Artikel Demo 1',
                    'isi' => 'Ini adalah artikel demo untuk testing.',
                    'slug' => 'artikel-demo-1',
                    'gambar' => '',
                    'status' => 1
                ],
                [
                    'id' => 2,
                    'judul' => 'Artikel Demo 2',
                    'isi' => 'Ini adalah artikel demo kedua untuk testing.',
                    'slug' => 'artikel-demo-2',
                    'gambar' => '',
                    'status' => 1
                ]
            ];
        }

        return view('artikel/index', compact('artikel', 'title'));
    }

    public function view($slug)
    {
        $model = new ArtikelModel();
        $artikel = $model->where([
            'slug' => $slug
        ])->first();

        // Menampilkan error apabila data tidak ada.
        if (!$artikel) {
            throw PageNotFoundException::forPageNotFound();
        }

        $title = $artikel['judul'];
        return view('artikel/detail', compact('artikel', 'title'));
    }

    public function admin_index()
    {
        $title = 'Daftar Artikel';
        $q = $this->request->getVar('q') ?? '';
        $model = new ArtikelModel();

        if ($q) {
            // Pencarian di multiple field
            $artikel = $model->groupStart()
                           ->like('judul', $q)
                           ->orLike('isi', $q)
                           ->orLike('kategori', $q)
                           ->groupEnd()
                           ->paginate(10);
        } else {
            // Tampilkan semua data jika tidak ada pencarian
            $artikel = $model->paginate(10);
        }

        $data = [
            'title' => $title,
            'q' => $q,
            'artikel' => $artikel,
            'pager' => $model->pager,
        ];
        return view('artikel/admin_index', $data);
    }

    public function add()
    {
        $title = "Tambah Artikel";

        // Cek apakah form sudah di-submit
        if ($this->request->getMethod() === 'post') {
            // validasi data.
            $validation = \Config\Services::validation();
            $validation->setRules([
                'judul' => 'required|min_length[3]|max_length[200]',
                'isi' => 'required|min_length[10]'
            ]);
            $isDataValid = $validation->withRequest($this->request)->run();

            if ($isDataValid) {
                // Handle file upload
                $file = $this->request->getFile('gambar');
                $gambarName = '';

                if ($file && $file->isValid() && !$file->hasMoved()) {
                    // Create gambar directory if not exists
                    if (!is_dir(ROOTPATH . 'public/gambar')) {
                        mkdir(ROOTPATH . 'public/gambar', 0755, true);
                    }

                    // Move file to gambar directory
                    $file->move(ROOTPATH . 'public/gambar');
                    $gambarName = $file->getName();
                }

                $artikel = new ArtikelModel();
                $artikel->insert([
                    'judul' => $this->request->getPost('judul'),
                    'isi' => $this->request->getPost('isi'),
                    'slug' => url_title($this->request->getPost('judul')),
                    'gambar' => $gambarName,
                    'status' => $this->request->getPost('status') ?? 0
                ]);

                session()->setFlashdata('success', 'Artikel berhasil ditambahkan!');
                return redirect('admin/artikel');
            } else {
                $errors = $validation->getErrors();
                $errorMessage = '';
                foreach ($errors as $error) {
                    $errorMessage .= $error . ' ';
                }
                session()->setFlashdata('error', trim($errorMessage));
            }
        }

        return view('artikel/form_add', compact('title'));
    }

    public function edit($id)
    {
        $artikel = new ArtikelModel();

        // Cek apakah form sudah di-submit
        if ($this->request->getMethod() === 'post') {
            // validasi data.
            $validation = \Config\Services::validation();
            $validation->setRules([
                'judul' => 'required|min_length[3]|max_length[200]',
                'isi' => 'required|min_length[10]'
            ]);
            $isDataValid = $validation->withRequest($this->request)->run();

            if ($isDataValid) {
                // Get current data
                $currentData = $artikel->where('id', $id)->first();
                $gambarName = $currentData['gambar']; // Keep current image by default

                // Handle file upload
                $file = $this->request->getFile('gambar');
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    // Create gambar directory if not exists
                    if (!is_dir(ROOTPATH . 'public/gambar')) {
                        mkdir(ROOTPATH . 'public/gambar', 0755, true);
                    }

                    // Delete old image if exists
                    if (!empty($currentData['gambar']) && file_exists(ROOTPATH . 'public/gambar/' . $currentData['gambar'])) {
                        unlink(ROOTPATH . 'public/gambar/' . $currentData['gambar']);
                    }

                    // Move new file
                    $file->move(ROOTPATH . 'public/gambar');
                    $gambarName = $file->getName();
                }

                $artikel->update($id, [
                    'judul' => $this->request->getPost('judul'),
                    'isi' => $this->request->getPost('isi'),
                    'slug' => url_title($this->request->getPost('judul')),
                    'gambar' => $gambarName,
                    'status' => $this->request->getPost('status') ?? 0,
                ]);

                session()->setFlashdata('success', 'Artikel berhasil diubah!');
                return redirect('admin/artikel');
            } else {
                $errors = $validation->getErrors();
                $errorMessage = '';
                foreach ($errors as $error) {
                    $errorMessage .= $error . ' ';
                }
                session()->setFlashdata('error', trim($errorMessage));
            }
        }

        // ambil data lama
        $data = $artikel->where('id', $id)->first();

        if (!$data) {
            session()->setFlashdata('error', 'Artikel tidak ditemukan!');
            return redirect('admin/artikel');
        }

        $title = "Edit Artikel";
        return view('artikel/form_edit', compact('title', 'data'));
    }

    public function delete($id)
    {
        $artikel = new ArtikelModel();

        // Cek apakah artikel ada
        $data = $artikel->where('id', $id)->first();
        if (!$data) {
            session()->setFlashdata('error', 'Artikel tidak ditemukan!');
            return redirect('admin/artikel');
        }

        // Delete image file if exists
        if (!empty($data['gambar']) && file_exists(ROOTPATH . 'public/gambar/' . $data['gambar'])) {
            unlink(ROOTPATH . 'public/gambar/' . $data['gambar']);
        }

        $artikel->delete($id);
        session()->setFlashdata('success', 'Artikel berhasil dihapus!');
        return redirect('admin/artikel');
    }
}
