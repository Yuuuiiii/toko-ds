<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko DS</title>
    <link rel="icon" type="image/webp" href="assets/img/logo.webp">
    <link rel="stylesheet" href="assets/css/global.css">
    <style>
        /* CSS Animasi dan Layout Landing Page */
        body { 
            margin: 0; padding: 0; height: 100vh; overflow: hidden; 
            font-family: 'Inter', sans-serif; background: #0f172a;
        }
        
        /* Container Background dengan efek transisi */
        .bg-container {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-image: url('assets/img/bg-login.webp');
            background-size: cover; background-position: center;
            z-index: 1; transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Overlay gelap agar teks selalu terbaca di atas background */
        .bg-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6); z-index: 2;
            transition: all 0.6s ease;
        }

        /* State saat tombol masuk diklik (Blur & Darken) */
        .bg-container.blur-active { filter: blur(12px); transform: scale(1.05); }
        .bg-overlay.dark-active { background: rgba(15, 23, 42, 0.85); }

        /* Konten Landing (Sebelum klik Masuk) */
        .landing-content {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            z-index: 10; transition: all 0.5s ease; text-align: center;
        }
        .landing-content h1 {
            font-size: 48px; color: white; margin: 0 0 10px 0; font-weight: 900; letter-spacing: -1px;
            text-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }
        .landing-content h1 span { color: var(--accent, #4361ee); }
        .landing-content p {
            font-size: 16px; color: #cbd5e1; margin: 0 0 40px 0; font-weight: 500; letter-spacing: 1px; text-transform: uppercase;
            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }
        .btn-landing {
            padding: 16px 48px; font-size: 16px; font-weight: 700; color: white;
            background: var(--accent, #4361ee); border: none; border-radius: 50px;
            cursor: pointer; box-shadow: 0 8px 24px rgba(67, 97, 238, 0.4);
            transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 1px;
        }
        .btn-landing:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(67, 97, 238, 0.6); }
        
        /* State saat landing content disembunyikan */
        .landing-content.hidden { opacity: 0; pointer-events: none; transform: translateY(-30px); }

        /* Wrapper Form Login */
        .login-wrapper {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            display: flex; justify-content: center; align-items: center;
            z-index: 20; opacity: 0; pointer-events: none; 
            transform: translateY(30px) scale(0.95);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* State saat form login dimunculkan */
        .login-wrapper.active { opacity: 1; pointer-events: all; transform: translateY(0) scale(1); }

        /* Desain Card Login lama yang sudah diadaptasi */
        .login-card { 
            background: var(--bg-surface, #1e293b); padding: 40px; 
            border-radius: 16px; border: 1px solid var(--border, #334155); 
            width: 100%; max-width: 400px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); 
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { 
            display: block; color: var(--text-secondary, #94a3b8); 
            margin-bottom: 8px; font-size: 12px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;
        }
        .form-group input { 
            width: 100%; height: 48px; padding: 0 16px; border-radius: 8px; 
            border: 1px solid var(--border, #334155); font-size: 14px;
            background: rgba(255,255,255,0.03); color: white; outline: none; box-sizing: border-box; transition: 0.2s;
        }
        .form-group input:focus { border-color: var(--accent, #4361ee); background: rgba(255,255,255,0.05); }
        
        .btn-login { 
            width: 100%; height: 50px; background: var(--accent, #4361ee); 
            color: white; border: none; border-radius: 8px; 
            font-weight: 700; cursor: pointer; margin-top: 10px; font-size: 15px; transition: all 0.2s;
        }
        .btn-login:disabled { background: #64748b; cursor: not-allowed; }
        
        .btn-back {
            width: 100%; height: 50px; background: transparent; 
            color: var(--text-secondary, #94a3b8); border: 1px solid var(--border, #334155); border-radius: 8px; 
            font-weight: 600; cursor: pointer; margin-top: 12px; font-size: 14px; transition: all 0.2s;
        }
        .btn-back:hover { background: rgba(255,255,255,0.05); color: white; }

        .error-box { 
            background: rgba(239, 68, 68, 0.1); color: #ef4444; 
            border: 1px solid rgba(239, 68, 68, 0.3); padding: 12px; border-radius: 8px; 
            margin-bottom: 20px; text-align: center; font-size: 13px; font-weight: 600;
            display: none; 
        }
    </style> <script>
        // Cek Token JWT seperti biasa
        const token = localStorage.getItem('jwt_token');
        if (token) {
            try {
                const base64Url = token.split('.')[1];
                const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
                const jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
                    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                }).join(''));
                
                const userData = JSON.parse(jsonPayload);
                
                if ((userData.exp * 1000) > Date.now()) {
                    const role = userData.role.toLowerCase();
                    if (role === 'admin') window.location.replace('pages/owner/dashboard.php');
                    else if (role === 'kasir') window.location.replace('pages/kasir/transaksi.php');
                    else window.location.replace('pages/gudang/dashboard.php');
                } else {
                    localStorage.removeItem('jwt_token'); 
                }
            } catch(e) {
                localStorage.removeItem('jwt_token');
            }
        }
    </script>
</head>
<body>

    <div class="bg-container" id="bgImage"></div>
    <div class="bg-overlay" id="bgOverlay"></div>

    <div class="landing-content" id="landingContent">
        <h1>TOKO<span>DS</span></h1>
        <button class="btn-landing" id="btnMulai">Masuk Aplikasi</button>
    </div>

    <div class="login-wrapper" id="loginWrapper">
        <div class="login-card">
            <h2 style="color: white; text-align: center; margin: 0 0 4px 0;">Selamat Datang</h2>
            <p style="color: var(--text-secondary, #94a3b8); text-align: center; margin-bottom: 24px; font-size: 13px;">Silakan login dengan akun Anda</p>
            
            <div class="error-box" id="errorBox"></div>

            <form id="loginForm">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" id="username" placeholder="Masukkan username" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" placeholder="Masukkan sandi" required>
                </div>
                <button type="submit" class="btn-login" id="btnLogin">Masuk ke Sistem</button>
                <button type="button" class="btn-back" id="btnKembali">Kembali</button>
            </form>
        </div>
    </div>

    <script>
        // --- LOGIKA ANIMASI UI ---
        const btnMulai = document.getElementById('btnMulai');
        const btnKembali = document.getElementById('btnKembali');
        const landingContent = document.getElementById('landingContent');
        const loginWrapper = document.getElementById('loginWrapper');
        const bgImage = document.getElementById('bgImage');
        const bgOverlay = document.getElementById('bgOverlay');
        const usernameInput = document.getElementById('username');

        // Saat klik Masuk Aplikasi
        btnMulai.addEventListener('click', () => {
            bgImage.classList.add('blur-active');
            bgOverlay.classList.add('dark-active');
            landingContent.classList.add('hidden');
            loginWrapper.classList.add('active');
            setTimeout(() => usernameInput.focus(), 300); // Auto-focus input
        });

        // Saat klik Kembali (Batal)
        btnKembali.addEventListener('click', () => {
            bgImage.classList.remove('blur-active');
            bgOverlay.classList.remove('dark-active');
            landingContent.classList.remove('hidden');
            loginWrapper.classList.remove('active');
            
            // Reset form jika dibatalkan
            document.getElementById('loginForm').reset();
            document.getElementById('errorBox').style.display = 'none';
        });

        // --- LOGIKA API LOGIN ---
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault(); 

            const passInput = document.getElementById('password').value;
            const btnLogin = document.getElementById('btnLogin');
            const errorBox = document.getElementById('errorBox');

            errorBox.style.display = 'none';
            btnLogin.disabled = true;
            btnLogin.innerText = 'Memeriksa kredensial...';

            try {
                const response = await fetch('api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        username: usernameInput.value.trim(),
                        password: passInput
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    btnLogin.style.background = 'var(--success, #22c55e)';
                    btnLogin.innerText = 'Berhasil! Mengalihkan...';

                    localStorage.setItem('jwt_token', data.token);

                    setTimeout(() => {
                        const role = data.user.role.toLowerCase();
                        if (role === 'admin') window.location.replace('pages/owner/dashboard.php');
                        else if (role === 'kasir') window.location.replace('pages/kasir/transaksi.php');
                        else window.location.replace('pages/gudang/dashboard.php');
                    }, 800); 

                } else {
                    errorBox.innerText = data.message;
                    errorBox.style.display = 'block';
                    btnLogin.disabled = false;
                    btnLogin.innerText = 'Masuk ke Sistem';
                }
            } catch (error) {
                console.error("Login Error:", error);
                errorBox.innerText = 'Terjadi kesalahan jaringan atau server.';
                errorBox.style.display = 'block';
                btnLogin.disabled = false;
                btnLogin.innerText = 'Masuk ke Sistem';
            }
        });
    </script>
</body>
</html>