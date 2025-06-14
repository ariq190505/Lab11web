# Lab7Web - Praktikum Pemrograman Web 2
**Repository Lengkap untuk Praktikum CodeIgniter 4**

## 📚 Informasi Praktikum
- **Mata Kuliah**: Pemrograman Web 2
- **Framework**: CodeIgniter 4
- **Database**: MySQL
- **Topik**: Routing, Controller, View, CRUD, Layout & View Cell

## 📋 Daftar Modul
1. [Modul 1 - Routing & Controller](#modul-1---routing--controller)
2. [Modul 2 - CRUD Operations](#modul-2---crud-operations)
3. [Modul 3 - View Layout & View Cell](#modul-3---view-layout--view-cell)
4. [Setup & Konfigurasi](#setup--konfigurasi)
5. [Screenshots](#screenshots)

---

## 🚀 Setup & Konfigurasi

### Tools yang Digunakan
- **XAMPP** - Web server lokal dengan MySQL
- **CodeIgniter 4** - PHP Framework
- **VS Code** - Code Editor
- **phpMyAdmin** - Database management

### Menjalankan Server
```bash
php spark serve
```
Server: `http://localhost:8080`

### Struktur Project Akhir
```
peraktikumweb/
├── app/
│   ├── Controllers/
│   │   ├── Home.php
│   │   ├── Page.php
│   │   └── Artikel.php
│   ├── Models/
│   │   └── ArtikelModel.php
│   ├── Views/
│   │   ├── layout/
│   │   │   ├── main.php
│   │   │   └── admin.php
│   │   ├── components/
│   │   │   └── artikel_terkini.php
│   │   ├── artikel/
│   │   └── template/
│   ├── Cells/
│   │   └── ArtikelTerkini.php
│   └── Database/Migrations/
├── public/
│   └── style.css
└── .env
```

---

## 📖 Modul 1 - Routing & Controller

### 1.1 Membuat Controller
**File**: `app/Controllers/Page.php`
```php
<?php
namespace App\Controllers;

class Page extends BaseController
{
    public function about()
    {
        return view('about', [
            'title' => 'Halaman About',
            'content' => 'Ini adalah halaman about yang menjelaskan tentang aplikasi ini.'
        ]);
    }

    public function contact()
    {
        return view('contact', [
            'title' => 'Kontak Kami',
            'content' => 'Ini adalah halaman kontak.'
        ]);
    }
}
```

### 1.2 Konfigurasi Routing
**File**: `app/Config/Routes.php`
```php
$routes->get('/', 'Home::index');
$routes->get('/about', 'Page::about');
$routes->get('/contact', 'Page::contact');
$routes->get('/artikel', 'Artikel::index');
$routes->get('/artikel/(:segment)', 'Artikel::view/$1');
```

### 1.3 Membuat View
**File**: `app/Views/about.php`
```php
<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<h1><?= $title; ?></h1>
<hr>
<p><?= $content; ?></p>
<?= $this->endSection() ?>
```

**✅ Hasil**: Routing dan controller berhasil dibuat dengan navigation yang berfungsi.

---

## 🗄️ Modul 2 - CRUD Operations

### 2.1 Setup Database
**Database**: `lab_ci4`
```sql
CREATE DATABASE lab_ci4;
USE lab_ci4;

CREATE TABLE artikel (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    isi TEXT,
    gambar VARCHAR(200),
    status TINYINT(1) DEFAULT 0,
    slug VARCHAR(200),
    kategori VARCHAR(100),
    created_at DATETIME,
    updated_at DATETIME
);
```

### 2.2 Konfigurasi Database
**File**: `.env`
```env
database.default.hostname = localhost
database.default.database = lab_ci4
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

### 2.3 Membuat Model
**File**: `app/Models/ArtikelModel.php`
```php
<?php
namespace App\Models;
use CodeIgniter\Model;

class ArtikelModel extends Model
{
    protected $table = 'artikel';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['judul', 'isi', 'status', 'slug', 'gambar', 'kategori'];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
```

### 2.4 Membuat Controller CRUD
**File**: `app/Controllers/Artikel.php`
```php
<?php
namespace App\Controllers;
use App\Models\ArtikelModel;

class Artikel extends BaseController
{
    public function index()
    {
        $model = new ArtikelModel();
        $artikel = $model->where('status', 1)->findAll();
        return view('artikel/index', compact('artikel', 'title'));
    }

    public function add()
    {
        if ($this->request->getMethod() === 'post') {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'judul' => 'required|min_length[3]',
                'isi' => 'required|min_length[10]'
            ]);

            if ($validation->withRequest($this->request)->run()) {
                $artikel = new ArtikelModel();
                $artikel->insert([
                    'judul' => $this->request->getPost('judul'),
                    'isi' => $this->request->getPost('isi'),
                    'slug' => url_title($this->request->getPost('judul')),
                    'status' => $this->request->getPost('status') ?? 0
                ]);
                return redirect('admin/artikel');
            }
        }
        return view('artikel/form_add', ['title' => 'Tambah Artikel']);
    }

    public function edit($id)
    {
        $artikel = new ArtikelModel();
        if ($this->request->getMethod() === 'post') {
            // Update logic
            $artikel->update($id, [
                'judul' => $this->request->getPost('judul'),
                'isi' => $this->request->getPost('isi'),
                'slug' => url_title($this->request->getPost('judul')),
                'status' => $this->request->getPost('status')
            ]);
            return redirect('admin/artikel');
        }

        $data = $artikel->find($id);
        return view('artikel/form_edit', compact('data', 'title'));
    }

    public function delete($id)
    {
        $artikel = new ArtikelModel();
        $artikel->delete($id);
        return redirect('admin/artikel');
    }
}
```

### 2.5 Routing CRUD
**File**: `app/Config/Routes.php`
```php
$routes->get('/artikel', 'Artikel::index');
$routes->get('/artikel/(:segment)', 'Artikel::view/$1');
$routes->group('admin', function($routes) {
    $routes->get('artikel', 'Artikel::admin_index');
    $routes->get('artikel/add', 'Artikel::add');
    $routes->post('artikel/add', 'Artikel::add');
    $routes->get('artikel/edit/(:num)', 'Artikel::edit/$1');
    $routes->post('artikel/edit/(:num)', 'Artikel::edit/$1');
    $routes->get('artikel/delete/(:num)', 'Artikel::delete/$1');
});
```

**✅ Hasil**: CRUD operations lengkap dengan Create, Read, Update, Delete.

---

## 🎨 Modul 3 - View Layout & View Cell

### 3.1 Membuat Layout Utama
**File**: `app/Views/layout/main.php`
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'My Website' ?></title>
    <link rel="stylesheet" href="<?= base_url('/style.css');?>">
</head>
<body>
    <div id="container">
        <header>
            <h1>Layout Sederhana</h1>
        </header>
        <nav>
            <a href="<?= base_url('/');?>">Home</a>
            <a href="<?= base_url('/artikel');?>">Artikel</a>
            <a href="<?= base_url('/about');?>">About</a>
            <a href="<?= base_url('/contact');?>">Kontak</a>
        </nav>
        <section id="wrapper">
            <section id="main">
                <?= $this->renderSection('content') ?>
            </section>
            <aside id="sidebar">
                <?= view_cell('App\\Cells\\ArtikelTerkini::render') ?>
                <div class="widget-box">
                    <h3 class="title">Widget Header</h3>
                    <ul>
                        <li><a href="#">Widget Link</a></li>
                        <li><a href="#">Widget Link</a></li>
                    </ul>
                </div>
            </aside>
        </section>
        <footer>
            <p>&copy; 2021 - Universitas Pelita Bangsa</p>
        </footer>
    </div>
</body>
</html>
```

### 3.2 Mengubah View ke Layout Baru
**File**: `app/Views/home.php`
```php
<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<h1><?= $title; ?></h1>
<hr>
<p><?= $content; ?></p>
<?= $this->endSection() ?>
```

### 3.3 Membuat View Cell
**File**: `app/Cells/ArtikelTerkini.php`
```php
<?php
namespace App\Cells;
use CodeIgniter\View\Cell;
use App\Models\ArtikelModel;

class ArtikelTerkini extends Cell
{
    public function render($kategori = null)
    {
        $model = new ArtikelModel();
        $query = $model->where('status', 1);

        if ($kategori) {
            $query = $query->where('kategori', $kategori);
        }

        $artikel = $query->orderBy('created_at', 'DESC')->limit(5)->findAll();

        return view('components/artikel_terkini', [
            'artikel' => $artikel,
            'kategori' => $kategori
        ]);
    }
}
```

**File**: `app/Views/components/artikel_terkini.php`
```php
<h3>Artikel Terkini<?= $kategori ? ' - ' . ucfirst($kategori) : '' ?></h3>
<?php if($artikel): ?>
    <ul>
        <?php foreach ($artikel as $row): ?>
            <li>
                <a href="<?= base_url('/artikel/' . $row['slug']) ?>"><?= $row['judul'] ?></a>
                <?php if($row['kategori']): ?>
                    <small style="color: #999;"><?= $row['kategori'] ?></small>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Belum ada artikel.</p>
<?php endif; ?>
```

### 3.4 Penggunaan View Cell
```php
// Semua artikel
<?= view_cell('App\\Cells\\ArtikelTerkini::render') ?>

// Artikel kategori tertentu
<?= view_cell('App\\Cells\\ArtikelTerkini::render', ['kategori' => 'teknologi']) ?>
```

**✅ Hasil**: Layout yang konsisten dan komponen yang dapat digunakan ulang.

---

## 📸 Screenshots

### Halaman Utama
![Home Page](screenshots/home.png)
*Halaman utama dengan layout baru dan sidebar artikel terkini*

### Daftar Artikel
![Artikel List](screenshots/artikel_list.png)
*Halaman daftar artikel dengan View Cell sidebar*

### Detail Artikel
![Artikel Detail](screenshots/artikel_detail.png)
*Halaman detail artikel dengan layout yang konsisten*

### Admin Panel
![Admin Panel](screenshots/admin_panel.png)
*Admin panel dengan layout terpisah untuk manajemen artikel*

### Form Tambah Artikel
![Form Add](screenshots/form_add.png)
*Form tambah artikel dengan validasi*

### Form Edit Artikel
![Form Edit](screenshots/form_edit.png)
*Form edit artikel dengan data yang sudah terisi*

---

## 🎯 Fitur yang Berhasil Diimplementasikan

### ✅ **Modul 1 - Routing & Controller**
- Routing dengan auto-routing dan manual routing
- Controller dengan multiple methods
- Navigation yang berfungsi
- Halaman About, Contact, dan Home

### ✅ **Modul 2 - CRUD Operations**
- **Create**: Tambah artikel dengan validasi
- **Read**: Tampil daftar dan detail artikel
- **Update**: Edit artikel existing
- **Delete**: Hapus artikel dengan konfirmasi
- Model dengan ORM CodeIgniter
- Database integration dengan MySQL

### ✅ **Modul 3 - View Layout & View Cell**
- Layout utama dengan extend/section pattern
- Layout admin terpisah
- View Cell untuk komponen yang dapat digunakan ulang
- Sidebar dinamis dengan artikel terkini
- Support kategori pada View Cell

### ✅ **Fitur Tambahan**
- Timestamps (created_at, updated_at)
- Kategori artikel
- Flash messages
- Form validation
- Responsive design
- Admin panel yang lengkap

---

## 🚀 Cara Menjalankan Aplikasi

### 1. **Persiapan Environment**
```bash
# Clone repository
git clone [repository-url]
cd peraktikumweb

# Pastikan XAMPP sudah running
# - Apache service: ON
# - MySQL service: ON
```

### 2. **Setup Database**
```sql
-- Buka phpMyAdmin: http://localhost/phpmyadmin
CREATE DATABASE lab_ci4;
USE lab_ci4;

CREATE TABLE artikel (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    isi TEXT,
    gambar VARCHAR(200),
    status TINYINT(1) DEFAULT 0,
    slug VARCHAR(200),
    kategori VARCHAR(100),
    created_at DATETIME,
    updated_at DATETIME
);
```

### 3. **Konfigurasi CodeIgniter**
```bash
# Copy file .env
cp env .env

# Edit file .env dan sesuaikan konfigurasi database
```

### 4. **Menjalankan Server**
```bash
# Jalankan development server
php spark serve

# Akses: http://localhost:8080
```

### 5. **Testing URL**
- **Halaman Publik**: `http://localhost:8080`
- **Daftar Artikel**: `http://localhost:8080/artikel`
- **Admin Panel**: `http://localhost:8080/admin/artikel`
- **Tambah Artikel**: `http://localhost:8080/admin/artikel/add`

---

## 📚 Pembelajaran dan Kesimpulan

### **Konsep yang Dipelajari:**

#### **1. MVC Architecture**
- **Model**: Mengelola data dan business logic
- **View**: Menampilkan data ke user
- **Controller**: Mengatur alur aplikasi

#### **2. Routing System**
- Auto-routing untuk kemudahan development
- Manual routing untuk kontrol yang lebih baik
- Route grouping untuk admin area

#### **3. View Layout vs View Cell**
| Aspek | View Layout | View Cell |
|-------|-------------|-----------|
| **Fungsi** | Template struktur halaman | Komponen yang dapat digunakan ulang |
| **Penggunaan** | `extend/section` | `view_cell()` |
| **Scope** | Seluruh halaman | Bagian kecil halaman |
| **Data** | Dari controller | Mengambil data sendiri |

#### **4. Database Operations**
- Migration untuk perubahan struktur database
- Model dengan ORM untuk query yang mudah
- Timestamps untuk tracking perubahan data

### **Best Practices yang Diterapkan:**
- ✅ Separation of concerns
- ✅ DRY (Don't Repeat Yourself) principle
- ✅ Form validation dan security
- ✅ Responsive design
- ✅ Clean URL dengan slug
- ✅ Error handling yang baik

### **Hasil Akhir:**
Aplikasi web lengkap dengan fitur CRUD, layout yang konsisten, dan komponen yang dapat digunakan ulang. Semua modul terintegrasi dengan baik dan siap untuk pengembangan lebih lanjut.

---

## 👨‍💻 Author & Credits

**Praktikum Pemrograman Web 2**
- **Mata Kuliah**: Pemrograman Web 2
- **Dosen**: Agung Nugroho
- **Universitas**: Pelita Bangsa, Bekasi

**Mahasiswa:**
- **Nama**: [Nama Lengkap Anda]
- **NIM**: [NIM Anda]
- **Kelas**: [Kelas Anda]

---

## 📄 License

This project is created for educational purposes as part of Web Programming 2 course at Universitas Pelita Bangsa.

---

## 🔐 Modul 4 - Authentication & Authorization

### Tujuan
Mengimplementasikan sistem login dan logout dengan session management untuk melindungi area admin.

### Langkah-langkah Implementasi

#### 4.1 Membuat Tabel User
```sql
CREATE TABLE user (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    useremail VARCHAR(100) NOT NULL,
    userpassword VARCHAR(255) NOT NULL
);

INSERT INTO user (username, useremail, userpassword) VALUES
('admin', 'admin@lab7web.com', '$2y$10$...');
```

#### 4.2 Membuat User Model
**File**: `app/Models/UserModel.php`
```php
<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'useremail', 'userpassword'];
}
```

#### 4.3 Membuat Auth Controller
**File**: `app/Controllers/User.php`
```php
public function login()
{
    if ($this->request->getMethod() === 'post') {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['userpassword'])) {
            session()->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'logged_in' => true
            ]);
            return redirect()->to('/admin/artikel');
        } else {
            session()->setFlashdata('error', 'Username atau password salah!');
        }
    }
    return view('auth/login', ['title' => 'Login']);
}
```

#### 4.4 Membuat Auth Filter
**File**: `app/Filters/Auth.php`
```php
<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/user/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
```

**✅ Hasil**: Sistem authentication dengan session management yang aman.

---

## 📄 Modul 5 - Pagination dan Pencarian

### Tujuan
Mengimplementasikan pagination untuk membatasi data per halaman dan fitur pencarian untuk memfilter artikel.

### Langkah-langkah Implementasi

#### 5.1 Update Controller untuk Pagination
**File**: `app/Controllers/Artikel.php`
```php
public function admin_index()
{
    $title = 'Daftar Artikel';
    $q = $this->request->getVar('q') ?? '';
    $model = new ArtikelModel();

    if ($q) {
        $artikel = $model->groupStart()
                       ->like('judul', $q)
                       ->orLike('isi', $q)
                       ->orLike('kategori', $q)
                       ->groupEnd()
                       ->paginate(10);
    } else {
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
```

#### 5.2 Form Pencarian
**File**: `app/Views/artikel/admin_index.php`
```php
<form method="get" class="form-search">
    <input type="text" name="q" value="<?= $q; ?>" placeholder="Cari data">
    <input type="submit" value="Cari" class="btn btn-primary">
    <?php if($q): ?>
        <a href="<?= base_url('/admin/artikel'); ?>" class="btn btn-secondary">Reset</a>
    <?php endif; ?>
</form>

<?php if($q): ?>
    <div class="search-info">
        <p><strong>Hasil pencarian untuk:</strong> "<?= esc($q); ?>"</p>
    </div>
<?php endif; ?>
```

#### 5.3 Pagination Links
```php
<?= $pager->only(['q'])->links(); ?>
```

**✅ Hasil**:
- Pagination dengan 10 artikel per halaman
- Pencarian di multiple field (judul, isi, kategori)
- Parameter pencarian dipertahankan di pagination

---

## 📸 Modul 6 - Upload Gambar

### Tujuan
Menambahkan fitur upload gambar pada artikel dengan validasi file dan manajemen file yang proper.

### Langkah-langkah Implementasi

#### 6.1 Update Controller untuk Upload
**File**: `app/Controllers/Artikel.php`
```php
public function add()
{
    if ($this->request->getMethod() === 'post') {
        $validation = \Config\Services::validation();
        $validation->setRules(['judul' => 'required']);
        $isDataValid = $validation->withRequest($this->request)->run();

        if ($isDataValid) {
            $file = $this->request->getFile('gambar');
            $gambarName = '';

            if ($file && $file->isValid() && !$file->hasMoved()) {
                if (!is_dir(ROOTPATH . 'public/gambar')) {
                    mkdir(ROOTPATH . 'public/gambar', 0755, true);
                }
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

            return redirect('admin/artikel');
        }
    }
    return view('artikel/form_add', ['title' => 'Tambah Artikel']);
}
```

#### 6.2 Update Form dengan File Input
**File**: `app/Views/artikel/form_add.php`
```php
<form action="" method="post" enctype="multipart/form-data">
    <p>
        <label for="judul">Judul Artikel:</label>
        <input type="text" name="judul" required>
    </p>
    <p>
        <label for="isi">Isi Artikel:</label>
        <textarea name="isi" cols="50" rows="10"></textarea>
    </p>
    <p>
        <label for="gambar">Gambar Artikel:</label>
        <input type="file" name="gambar" accept="image/*">
        <small>Format: JPG, PNG, GIF (Max: 2MB)</small>
    </p>
    <p>
        <input type="submit" value="Simpan" class="btn btn-primary">
    </p>
</form>
```

#### 6.3 Menampilkan Gambar di Admin Panel
**File**: `app/Views/artikel/admin_index.php`
```php
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Gambar</th>
            <th>Judul</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($artikel as $row): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td>
                    <?php if(!empty($row['gambar'])): ?>
                        <img src="<?= base_url('/gambar/' . $row['gambar']); ?>"
                             style="width: 60px; height: 40px; object-fit: cover;">
                    <?php else: ?>
                        <span>No Image</span>
                    <?php endif; ?>
                </td>
                <td><?= $row['judul']; ?></td>
                <td><?= $row['status'] ? 'Published' : 'Draft'; ?></td>
                <td>
                    <a href="<?= base_url('/admin/artikel/edit/' . $row['id']);?>">Edit</a>
                    <a href="<?= base_url('/admin/artikel/delete/' . $row['id']);?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

**✅ Hasil**:
- Upload gambar dengan validasi file
- Preview gambar di admin panel
- File management (hapus file lama saat update/delete)
- Display gambar di detail artikel

---

## 🎯 Ringkasan Fitur Lengkap

### ✅ **Modul 4 - Authentication**
- Login/logout system
- Session management
- Auth filter untuk proteksi admin area
- Password hashing dengan bcrypt

### ✅ **Modul 5 - Pagination & Search**
- Pagination dengan 10 item per halaman
- Search di multiple field
- Pagination links dengan parameter search
- Reset search functionality

### ✅ **Modul 6 - Upload Gambar**
- File upload dengan validasi
- Image preview di admin panel
- File management (create/update/delete)
- Support multiple image formats

### 🚀 **Fitur Tambahan**
- Responsive design
- Flash messages
- Form validation
- Clean URLs dengan slug
- Status badge (Published/Draft)
- Error handling yang robust

---

**© 2024 - Lab7Web Praktikum CodeIgniter 4**

