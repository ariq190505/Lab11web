<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?> - Lab7Web</title>
    <link rel="stylesheet" href="<?= base_url('/style.css');?>">
    <style>
        .register-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
        }
        .register-form {
            width: 100%;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group input:focus {
            border-color: #428bca;
            outline: none;
        }
        .btn-register {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-register:hover {
            background-color: #1e7e34;
        }
        .text-center {
            text-align: center;
        }
        .login-link {
            margin-top: 20px;
            text-align: center;
        }
        .login-link a {
            color: #428bca;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2 class="text-center">Register Admin</h2>
        
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
        
        <form class="register-form" method="post" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required value="<?= old('username') ?>">
            </div>
            
            <div class="form-group">
                <label for="useremail">Email:</label>
                <input type="email" id="useremail" name="useremail" required value="<?= old('useremail') ?>">
            </div>
            
            <div class="form-group">
                <label for="userpassword">Password:</label>
                <input type="password" id="userpassword" name="userpassword" required>
            </div>
            
            <button type="submit" class="btn-register">Register</button>
        </form>
        
        <div class="login-link">
            <p>Sudah punya akun? <a href="<?= base_url('/auth/login') ?>">Login di sini</a></p>
        </div>
        
        <div class="login-link">
            <p><a href="<?= base_url('/') ?>">← Kembali ke Website</a></p>
        </div>
    </div>
</body>
</html>
