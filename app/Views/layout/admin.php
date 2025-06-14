<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Admin Panel' ?> - Admin Panel</title>
    <link rel="stylesheet" href="<?= base_url('/style.css');?>">
</head>
<body>
    <div id="container">
        <header>
            <h1>Admin Panel - Lab7Web</h1>
        </header>
        <nav>
            <a href="<?= base_url('/admin/artikel');?>">Dashboard</a>
            <a href="<?= base_url('/admin/artikel');?>">Artikel</a>
            <a href="<?= base_url('/admin/artikel/add');?>">Tambah Artikel</a>
            <a href="<?= base_url('/');?>" target="_blank">Lihat Website</a>
            <a href="<?= base_url('/user/logout');?>" style="float: right;">Logout (<?= session()->get('username') ?>)</a>
        </nav>
        <section id="wrapper">
            <section id="main" style="width: 100%; float: none;">
                <div class="admin-content">
                    <h2><?= $title ?? 'Admin Panel' ?></h2>
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-error">
                            <?= session()->getFlashdata('error'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?= $this->renderSection('content') ?>
                </div>
            </section>
        </section>
        <footer>
            <p>&copy; 2021 - Universitas Pelita Bangsa - Admin Panel</p>
        </footer>
    </div>
</body>
</html>
