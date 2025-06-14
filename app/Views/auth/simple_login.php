<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="<?= base_url('/style.css'); ?>">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2>Simple Login</h2>
            <p class="subtitle">Testing Login System</p>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error">
                    <?= session()->getFlashdata('error'); ?>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success'); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="<?= base_url('/simplelogin'); ?>">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <div class="login-info">
                <h4>Test Credentials:</h4>
                <p><strong>Username:</strong> admin</p>
                <p><strong>Password:</strong> admin123</p>
            </div>
            
            <div class="debug-links">
                <h4>Debug Tools:</h4>
                <a href="<?= base_url('/sessiondebug/test'); ?>">Test Session</a> |
                <a href="<?= base_url('/dbtest/test'); ?>">Test Database</a> |
                <a href="<?= base_url('/sessiondebug/forceLogin'); ?>">Force Login</a>
            </div>
        </div>
    </div>
</body>
</html>
