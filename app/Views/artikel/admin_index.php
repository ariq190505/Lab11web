<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<form method="get" class="form-search">
    <input type="text" name="q" value="<?= $q; ?>" placeholder="Cari data">
    <input type="submit" value="Cari" class="btn btn-primary">
    <?php if($q): ?>
        <a href="<?= base_url('/admin/artikel'); ?>" class="btn btn-secondary" style="margin-left: 10px;">Reset</a>
    <?php endif; ?>
</form>

<?php if($q): ?>
    <div class="search-info">
        <p><strong>Hasil pencarian untuk:</strong> "<?= esc($q); ?>"</p>
    </div>
<?php endif; ?>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th class="col-id">ID</th>
                <th class="col-image">Gambar</th>
                <th class="col-title">Judul</th>
                <th class="col-status">Status</th>
                <th class="col-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if($artikel): ?>
                <?php foreach($artikel as $row): ?>
                    <tr>
                        <td class="col-id"><?= $row['id']; ?></td>
                        <td class="col-image">
                            <?php if(!empty($row['gambar'])): ?>
                                <img src="<?= base_url('/gambar/' . $row['gambar']); ?>" alt="<?= $row['judul']; ?>" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <span style="color: #999; font-size: 12px;">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td class="col-title">
                            <strong><?= $row['judul']; ?></strong><br>
                            <small style="color: #666;"><?= substr($row['isi'], 0, 60); ?>...</small>
                        </td>
                        <td class="col-status">
                            <span class="status-badge status-<?= $row['status'] ? 'published' : 'draft'; ?>">
                                <?= $row['status'] ? 'Published' : 'Draft'; ?>
                            </span>
                        </td>
                        <td class="col-actions">
                            <a class="btn btn-secondary" href="<?= base_url('/admin/artikel/edit/' . $row['id']);?>">Ubah</a>
                            <a class="btn btn-danger" onclick="return confirm('Yakin menghapus data?');" href="<?= base_url('/admin/artikel/delete/' . $row['id']);?>">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Belum ada data.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $pager->only(['q'])->links(); ?>

<?= $this->endSection() ?>
