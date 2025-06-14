<?= $this->include('template/header'); ?>

<h1><?= $title; ?></h1>
<hr>
<p><?= $content; ?></p>

<div class="contact-form">
    <h3>Hubungi Kami</h3>
    <form>
        <div class="form-group">
            <label for="name">Nama:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="message">Pesan:</label>
            <textarea id="message" name="message" rows="5" required></textarea>
        </div>
        <button type="submit">Kirim Pesan</button>
    </form>
</div>

<?= $this->include('template/footer'); ?>
