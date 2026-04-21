<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Username & PW Sementara
    if ($username === 'admin' && $password === '123') {
        // Jika benar, arahkan ke dashboard owner
        header('Location: ' . BASE_URL . '/pages/owner/laporan.php');
        exit;
    } else if ($username === 'kasir' && $password === '123') {
        // Jika benar, arahkan ke terminal kasir
        header('Location: ' . BASE_URL . '/pages/kasir/transaksi.php');
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Login — Toko DS</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/global.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: var(--bg-base);
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: var(--bg-surface);
            padding: 40px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-card);
            text-align: center;
        }
        .login-header { margin-bottom: 32px; }
        .form-group { text-align: left; margin-bottom: 20px; }
        .form-label { 
            display: block; 
            font-size: 12px; 
            font-weight: 600; 
            color: var(--text-muted); 
            margin-bottom: 8px; 
            text-transform: uppercase;
        }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s;
        }
        .form-input:focus { border-color: var(--accent); }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
        }
        .btn-login:hover { background: var(--accent-hover); transform: translateY(-1px); }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <h1 class="logo-text">TOKO<span class="logo-accent">DS</span></h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 8px;">Silakan masuk ke akun Anda</p>
    </div>

    <form action="" method="POST">
        <div class="form-group">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-input" placeholder="Masukkan username" required>
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-input" placeholder="••••••••" required>
        </div>
        
        <button type="submit" class="btn-login">MASUK KE SISTEM</button>
    </form>
</div>

</body>
</html>