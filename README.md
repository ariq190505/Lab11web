# Lab7Web - Praktikum Pemrograman Web 2
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
4. [Setup & Konfigurasi](#setup--konfigurasi)
5. [Testing & Demonstrasi](#testing--demonstrasi)
6. [Screenshots](#screenshots)
7. [Kesimpulan](#kesimpulan)

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

### Demo Data Generator:
- **Create Images**: `http://localhost:8080/dummydata/createImages`
- **Create Articles**: `http://localhost:8080/dummydata/createArticles`

---

## 📱 Screenshots Praktikum

### Modul 4 - Authentication
![Login Form](screenshots/login_form.png)
*Form login dengan validasi*

![Admin Protected](screenshots/admin_protected.png)
*Area admin yang terproteksi dengan auth filter*

### Modul 5 - Pagination & Search
![Pagination](screenshots/pagination.png)
*Pagination dengan 10 artikel per halaman*

![Search Function](screenshots/search_function.png)
*Fitur pencarian di multiple field*

### Modul 6 - Upload Gambar
![Upload Form](screenshots/upload_form.png)
*Form upload gambar dengan preview*

![Admin with Images](screenshots/admin_with_images.png)
*Admin panel dengan kolom gambar dan thumbnail*

![Article with Image](screenshots/article_with_image.png)
*Detail artikel dengan gambar*

---

## 🎓 Kesimpulan Pembelajaran

### **Konsep yang Berhasil Dipelajari:**

1. **MVC Architecture** - Pemisahan logic, data, dan presentation
2. **Authentication & Authorization** - Sistem keamanan dengan session
3. **File Upload Management** - Upload, validasi, dan manajemen file
4. **Database Pagination** - Optimasi query untuk data besar
5. **Search Functionality** - Filter data dengan multiple criteria
6. **Responsive Design** - UI yang mobile-friendly
7. **Error Handling** - Graceful error management
8. **Security Best Practices** - Validation, sanitization, protection

### **Skills yang Dikembangkan:**
- ✅ **Backend Development** dengan CodeIgniter 4
- ✅ **Database Design** dan optimization
- ✅ **Frontend Integration** dengan responsive CSS
- ✅ **File Management** dan security
- ✅ **User Experience** design
- ✅ **Testing** dan debugging
- ✅ **Documentation** dan version control

### **Hasil Akhir:**
Aplikasi web lengkap dengan sistem manajemen artikel yang mencakup authentication, CRUD operations, file upload, pagination, search, dan responsive design. Semua modul terintegrasi dengan baik dan siap untuk deployment production.

---

## 🧪 Testing & Demonstrasi

### URL Testing untuk Setiap Modul:

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

### Demo Data Generator:
- **Create Images**: `http://localhost:8080/dummydata/createImages`
- **Create Articles**: `http://localhost:8080/dummydata/createArticles`

---

## 📱 Screenshots

### Modul 4 - Authentication
![Login Form](screenshots/login_form.png)
*Form login dengan validasi dan session management*

![Admin Protected](screenshots/admin_protected.png)
*Area admin yang terproteksi dengan auth filter*

### Modul 5 - Pagination & Search
![Pagination](screenshots/pagination.png)
*Pagination dengan 10 artikel per halaman*

![Search Function](screenshots/search_function.png)
*Fitur pencarian di multiple field dengan reset*

### Modul 6 - Upload Gambar
![Upload Form](screenshots/upload_form.png)
*Form upload gambar dengan file input dan validasi*

![Admin with Images](screenshots/admin_with_images.png)
*Admin panel dengan kolom gambar dan thumbnail preview*

![Article with Image](screenshots/article_with_image.png)
*Detail artikel dengan gambar yang responsive*

---

## 🎓 Kesimpulan

### **Fitur yang Berhasil Diimplementasikan:**

#### ✅ **Modul 4 - Authentication & Authorization**
- Login/logout system dengan session management
- Auth filter untuk proteksi admin area
- Password hashing dengan bcrypt
- Flash messages untuk user feedback

#### ✅ **Modul 5 - Pagination dan Pencarian**
- Pagination dengan 10 artikel per halaman
- Search di multiple field (judul, isi, kategori)
- Pagination links yang preserve search parameters
- Reset search functionality

#### ✅ **Modul 6 - Upload Gambar**
- File upload dengan validasi (JPG, PNG, GIF)
- Image preview di admin panel (thumbnails)
- File management (auto delete old files)
- Image display di detail artikel
- Auto directory creation

### **Skills yang Dikembangkan:**
- ✅ **Authentication & Security** - Session management, password hashing
- ✅ **Database Optimization** - Pagination, search queries
- ✅ **File Management** - Upload, validation, cleanup
- ✅ **User Experience** - Responsive design, feedback messages
- ✅ **Error Handling** - Graceful degradation, validation

### **Teknologi yang Digunakan:**
- **Backend**: CodeIgniter 4, PHP 8+
- **Database**: MySQL dengan ORM
- **Frontend**: HTML5, CSS3, Responsive Design
- **Security**: Session management, CSRF protection, file validation

### **Hasil Akhir:**
Aplikasi web lengkap dengan sistem manajemen artikel yang mencakup authentication, pagination, search, dan file upload. Semua modul terintegrasi dengan baik dan siap untuk deployment production.

---

**© 2024 - Lab7Web Praktikum CodeIgniter 4**
**Universitas Pelita Bangsa - Pemrograman Web 2**

