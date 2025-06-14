<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error'); ?>
    </div>
<?php endif; ?>

<form action="" method="post" enctype="multipart/form-data">
    <p>
        <label for="judul">Judul Artikel:</label>
        <input type="text" id="judul" name="judul" value="<?= old('judul', $data['judul']); ?>" placeholder="Masukkan judul artikel" required>
    </p>
    <p>
        <label for="isi">Isi Artikel:</label>
        <textarea id="isi" name="isi" cols="50" rows="10" placeholder="Masukkan isi artikel"><?= old('isi', $data['isi']); ?></textarea>
    </p>
    <p>
        <label for="gambar">Gambar Artikel:</label>
        <?php if (!empty($data['gambar'])): ?>
            <div class="current-image">
                <img src="<?= base_url('/gambar/' . $data['gambar']); ?>" alt="Current Image" style="max-width: 200px; height: auto; display: block; margin: 5px 0;">
                <small>Gambar saat ini: <?= $data['gambar']; ?></small>
            </div>
        <?php endif; ?>
        <input type="file" id="gambar" name="gambar" accept="image/*">
        <small>Kosongkan jika tidak ingin mengubah gambar. Format: JPG, PNG, GIF (Max: 2MB)</small>
    </p>
    <p>
        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="0" <?= old('status', $data['status']) == '0' ? 'selected' : ''; ?>>Draft</option>
            <option value="1" <?= old('status', $data['status']) == '1' ? 'selected' : ''; ?>>Published</option>
        </select>
    </p>
    <p>
        <input type="submit" value="Update Artikel" class="btn btn-primary btn-large">
        <a href="<?= base_url('/admin/artikel'); ?>" class="btn btn-secondary">Batal</a>
    </p>
</form>

<?= $this->endSection() ?>
