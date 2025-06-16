# Lab11Web - Praktikum Pemrograman Web 2
**Modul 4, 5, 6 - Authentication, Pagination & Upload Gambar**

## 📚 Informasi Praktikum
- **Mata Kuliah**: Pemrograman Web 2
- **Framework**: CodeIgniter 4
- **Database**: MySQL
- **Topik**: Authentication, Pagination, Search, Upload Gambar

## 📋 Daftar Modul
1. [Modul 4 - Authentication & Authorization](#modul-4---authentication--authorization)
2. [Modul 5 - Pagination dan Pencarian](#modul-5---pagination-dan-pencarian)
3. [Modul 6 - Upload Gambar](#modul-6---upload-gambar)
4. [Modul 8 - AJAX Implementation](#modul-8---ajax-implementation)
5. [Setup & Konfigurasi](#setup--konfigurasi)
6. [Testing & Demonstrasi](#testing--demonstrasi)
7. [Screenshots](#screenshots)
8. [Kesimpulan](#kesimpulan)

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

### Database Setup
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

CREATE TABLE user (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    useremail VARCHAR(100) NOT NULL,
    userpassword VARCHAR(255) NOT NULL
);
```

### Konfigurasi Environment
**File**: `.env`
```env
database.default.hostname = localhost
database.default.database = lab_ci4
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

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

## 🔄 Modul 8 - AJAX Implementation

### Tujuan
Mengimplementasikan operasi CRUD menggunakan AJAX dengan jQuery untuk memberikan pengalaman user yang lebih responsif tanpa reload halaman.

### Langkah-langkah Implementasi

#### 8.1 Setup jQuery Library
**Download jQuery 3.6.0:**
```bash
curl -o public/assets/js/jquery-3.6.0.min.js https://code.jquery.com/jquery-3.6.0.min.js
```

**Struktur Folder Assets:**
```
public/
├── assets/
│   ├── js/
│   │   └── jquery-3.6.0.min.js
│   └── css/
└── ...
```

#### 8.2 Membuat AJAX Controller
**File**: `app/Controllers/AjaxController.php`
```php
<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\ArtikelModel;

class AjaxController extends Controller
{
    public function index()
    {
        return view('ajax/index');
    }

    public function getData()
    {
        $model = new ArtikelModel();
        $data = $model->findAll();
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
}
```

#### 8.3 Update Routes untuk AJAX
**File**: `app/Config/Routes.php`
```php
// AJAX routes
$routes->group('ajax', function($routes) {
    $routes->get('/', 'AjaxController::index');
    $routes->get('getData', 'AjaxController::getData');
    $routes->get('getById/(:num)', 'AjaxController::getById/$1');
    $routes->post('delete/(:num)', 'AjaxController::delete/$1');
    $routes->post('create', 'AjaxController::create');
    $routes->post('update/(:num)', 'AjaxController::update/$1');
});
```

#### 8.4 Membuat AJAX Views
**File**: `app/Views/ajax/simple_view.php` - View sederhana untuk demonstrasi AJAX
```php
<?= $this->include('template/header'); ?>

<div class="container">
    <div class="page-header">
        <h1>Data Artikel</h1>
    </div>

    <div class="table-container">
        <table class="table-data" id="artikelTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" class="loading">Loading data...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/js/jquery-3.6.0.min.js') ?>"></script>
<script>
$(document).ready(function() {
    // Load data function
    function loadData() {
        $.ajax({
            url: "<?= base_url('ajax/getData') ?>",
            method: "GET",
            dataType: "json",
            success: function(data) {
                var tableBody = "";
                if (data && data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        var statusClass = row.status == 1 ? 'status-published' : 'status-draft';
                        var statusText = row.status == 1 ? 'Published' : 'Draft';

                        tableBody += '<tr>';
                        tableBody += '<td><span class="id-number">' + row.id + '</span></td>';
                        tableBody += '<td>' + (row.judul || 'No Title') + '</td>';
                        tableBody += '<td><span class="status ' + statusClass + '">' + statusText + '</span></td>';
                        tableBody += '<td>';
                        tableBody += '<a href="<?= base_url('artikel/') ?>' + (row.slug || row.id) + '" class="btn btn-primary">View</a>';
                        tableBody += '<a href="<?= base_url('admin/artikel/edit/') ?>' + row.id + '" class="btn btn-primary">Edit</a>';
                        tableBody += '<a href="#" class="btn btn-danger btn-delete" data-id="' + row.id + '">Delete</a>';
                        tableBody += '</td>';
                        tableBody += '</tr>';
                    }
                } else {
                    tableBody = '<tr><td colspan="4" style="text-align: center;">Tidak ada data artikel</td></tr>';
                }
                $('#artikelTable tbody').html(tableBody);
            }
        });
    }

    // Delete function
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        if (confirm('Apakah Anda yakin ingin menghapus artikel ini?')) {
            $.ajax({
                url: "<?= base_url('ajax/delete/') ?>" + id,
                method: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.status === 'OK') {
                        alert('Artikel berhasil dihapus!');
                        loadData();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        }
    });

    loadData(); // Load data on page ready
});
</script>

<?= $this->include('template/footer'); ?>
```

#### 8.5 Implementasi Desain Responsif
**Styling CSS untuk tampilan yang simpel dan elegan:**
```css
/* Clean & Elegant Design */
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background-color: #f8f9fa;
    color: #2c3e50;
    line-height: 1.6;
}

.table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
}

.table-data thead th {
    background: #f8f9fa;
    color: #495057;
    font-weight: 600;
    padding: 20px 15px;
    text-align: left;
    border-bottom: 2px solid #e9ecef;
}

.table-data tbody tr:hover {
    background-color: #f8f9fa;
}

/* Responsive Design */
@media (max-width: 768px) {
    .table-data th, .table-data td {
        padding: 12px 8px;
        font-size: 13px;
    }
}
```

**✅ Hasil**:
- Interface AJAX yang simpel dan elegan
- Tabel responsif dengan 5 artikel (ID 1-5)
- Operasi CRUD tanpa reload halaman
- Design yang clean dan professional
- Error handling yang robust

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

### ✅ **Modul 8 - AJAX Implementation**
- jQuery 3.6.0 integration
- AJAX CRUD operations (Create, Read, Update, Delete)
- Real-time data loading tanpa reload halaman
- Modal interface untuk add/edit
- JSON response handling
- Error handling dan user feedback

### 🚀 **Fitur Tambahan**
- Responsive design
- Flash messages
- Form validation
- Clean URLs dengan slug
- Status badge (Published/Draft)
- Error handling yang robust
- Interactive AJAX interface

---

## 🧪 Testing dan Demonstrasi

### URL Testing untuk Setiap Modul:

#### **Modul 1-3 (Basic Setup)**
- **Home**: `http://localhost:8080/`
- **About**: `http://localhost:8080/about`
- **Contact**: `http://localhost:8080/contact`
- **Artikel List**: `http://localhost:8080/artikel`

#### **Modul 4 (Authentication)**
- **Login**: `http://localhost:8080/simplelogin` (admin/admin123)
- **Logout**: `http://localhost:8080/simplelogout`
- **Protected Admin**: `http://localhost:8080/admin/artikel` (requires login)

#### **Modul 5 (Pagination & Search)**
- **Admin Panel**: `http://localhost:8080/admin/artikel`
- **Search Test**: Gunakan form pencarian di admin panel
- **Pagination Test**: Navigasi antar halaman

#### **Modul 6 (Upload Gambar)**
- **Add Article**: `http://localhost:8080/admin/artikel/add`
- **Edit Article**: `http://localhost:8080/admin/artikel/edit/[id]`
- **View with Image**: `http://localhost:8080/artikel/[slug]`

#### **Modul 8 (AJAX Implementation)**
- **AJAX Demo**: `http://localhost/Lab11Web/public/ajax`
- **AJAX Artikel View**: `http://localhost/Lab11Web/public/ajax/artikel`
- **Simple AJAX View**: `http://localhost/Lab11Web/public/ajax/simple`
- **AJAX API Endpoints**:
  - GET Data (5 pertama dari ID 1): `http://localhost/Lab11Web/public/ajax/getData`
  - GET All Data (urutan ID 1 ke atas): `http://localhost/Lab11Web/public/ajax/getAllData`
  - Get by ID: `http://localhost/Lab11Web/public/ajax/getById/[id]`
  - Delete: `POST http://localhost/Lab11Web/public/ajax/delete/[id]`
  - Create: `POST http://localhost/Lab11Web/public/ajax/create`
  - Update: `POST http://localhost/Lab11Web/public/ajax/update/[id]`

### Demo Data Generator:
- **Create Images**: `http://localhost:8080/dummydata/createImages`
- **Create Articles**: `http://localhost:8080/dummydata/createArticles`

---

## 📱 Screenshots Praktikum

### Modul 4 - Authentication
![image](https://github.com/user-attachments/assets/a981f0e6-c8c7-471c-a363-40c52c669dd6)
*Form login dengan validasi*

### Modul 5 - Pagination & Search
![image](https://github.com/user-attachments/assets/9d5d90b9-38b1-49f0-9445-61539df3d53c)
*Pagination dengan 10 artikel per halaman*

![image](https://github.com/user-attachments/assets/7922fc9d-9971-4ab8-9a58-49a05e0b4afa)
*Fitur pencarian di multiple field*

### Modul 6 - Upload Gambar
![Article with Image](screenshots/article_with_image.png)
*Detail artikel dengan gambar*

### Modul 8 - AJAX Implementation

#### 8.1 Setup jQuery Library
![jQuery Setup](screenshots/jquery_setup.png)
*Download dan setup jQuery 3.6.0 dari CDN*

**Langkah-langkah:**
1. **Download jQuery**: Menggunakan curl untuk download jQuery 3.6.0
   ```bash
   curl -o public/assets/js/jquery-3.6.0.min.js https://code.jquery.com/jquery-3.6.0.min.js
   ```

2. **Verifikasi File**: Memastikan jQuery berhasil didownload
   ```bash
   ls -la public/assets/js/
   ```

#### 8.2 Membuat AJAX Controller
![AJAX Controller](screenshots/ajax_controller.png)
*AjaxController.php dengan method CRUD lengkap*

**Langkah-langkah:**
1. **Buat Controller**: `app/Controllers/AjaxController.php`
2. **Method getData()**: Mengambil 5 artikel pertama (ID 1-5)
3. **Method delete()**: Menghapus artikel dengan response JSON
4. **Method getById()**: Mengambil artikel berdasarkan ID
5. **Method create/update()**: CRUD operations lengkap

#### 8.3 Konfigurasi Routes
![Routes Configuration](screenshots/ajax_routes.png)
*Konfigurasi routes untuk AJAX endpoints*

**Routes yang ditambahkan:**
```php
// AJAX routes
$routes->group('ajax', function($routes) {
    $routes->get('/', 'AjaxController::index');
    $routes->get('simple', 'AjaxController::simple');
    $routes->get('getData', 'AjaxController::getData');
    $routes->get('getAllData', 'AjaxController::getAllData');
    $routes->get('getById/(:num)', 'AjaxController::getById/$1');
    $routes->post('delete/(:num)', 'AjaxController::delete/$1');
    $routes->post('create', 'AjaxController::create');
    $routes->post('update/(:num)', 'AjaxController::update/$1');
});
```

#### 8.4 Simple AJAX View
![Simple AJAX View](screenshots/simple_ajax_view.png)
*Tampilan sederhana dengan tabel data artikel*

**Fitur:**
- Tabel responsif dengan 5 artikel (ID 1-5)
- Loading state saat fetch data
- Status badge (Published/Draft)
- Action buttons (View, Edit, Delete)
- Konfirmasi delete dengan alert

#### 8.5 Advanced AJAX View
![Advanced AJAX View](screenshots/advanced_ajax_view.png)
*Tampilan lengkap dengan action bar dan fitur tambahan*

**Fitur Tambahan:**
- Action bar dengan tombol Refresh dan Load All
- Alert system dengan auto-hide
- Smooth animations dan transitions
- Better error handling
- Professional styling

#### 8.6 AJAX API Testing
![AJAX API Test](screenshots/ajax_api_test.png)
*Testing AJAX endpoints dengan halaman test*

**Endpoints yang ditest:**
- `GET /ajax/getData` - 5 artikel pertama
- `GET /ajax/getAllData` - Semua artikel
- `GET /ajax/getById/1` - Artikel by ID
- `POST /ajax/delete/1` - Delete artikel

#### 8.7 Responsive Design
![Responsive Design](screenshots/responsive_design.png)
*Tampilan responsif di berbagai ukuran layar*

**Breakpoints:**
- Desktop (>768px): Full layout
- Tablet (≤768px): Adjusted spacing
- Mobile (≤480px): Stacked buttons

#### 8.8 Error Handling
![Error Handling](screenshots/error_handling.png)
*Error handling dan user feedback*

**Error States:**
- Network errors
- Server errors
- Empty data states
- Loading states

---

## 🎓 Kesimpulan Pembelajaran

### **Konsep yang Berhasil Dipelajari:**

1. **MVC Architecture** - Pemisahan logic, data, dan presentation
2. **Authentication & Authorization** - Sistem keamanan dengan session
3. **File Upload Management** - Upload, validasi, dan manajemen file
4. **Database Pagination** - Optimasi query untuk data besar
5. **Search Functionality** - Filter data dengan multiple criteria
6. **AJAX Implementation** - Operasi asynchronous dengan jQuery
7. **JSON API Development** - RESTful endpoints untuk AJAX
8. **Responsive Design** - UI yang mobile-friendly dan elegan
9. **Error Handling** - Graceful error management
10. **Security Best Practices** - Validation, sanitization, protection

### **Skills yang Dikembangkan:**
- ✅ **Backend Development** dengan CodeIgniter 4
- ✅ **AJAX & jQuery** untuk operasi asynchronous
- ✅ **JSON API Development** dan response handling
- ✅ **Database Design** dan optimization
- ✅ **Frontend Integration** dengan responsive CSS
- ✅ **File Management** dan security
- ✅ **User Experience** design yang simpel dan elegan
- ✅ **Testing** dan debugging
- ✅ **Documentation** dan version control

### **Hasil Akhir:**
Aplikasi web lengkap dengan sistem manajemen artikel yang mencakup:
- ✅ **Authentication & Authorization** - Login/logout dengan session management
- ✅ **CRUD Operations** - Create, Read, Update, Delete artikel
- ✅ **File Upload** - Upload dan manajemen gambar artikel
- ✅ **Pagination & Search** - Navigasi data yang efisien
- ✅ **AJAX Implementation** - Operasi real-time tanpa reload halaman
- ✅ **Responsive Design** - UI yang simpel, elegan, dan mobile-friendly
- ✅ **API Endpoints** - RESTful JSON API untuk integrasi
- ✅ **Error Handling** - Graceful error management dan user feedback

Semua modul terintegrasi dengan baik menggunakan arsitektur MVC CodeIgniter 4 dan siap untuk deployment production.

## Author

### Nama: [Ariq Ibtihal]
### NIM: [312310446]
### Kelas: [TI.23.A5]
