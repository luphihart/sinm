<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ \App\Models\Setting::get('app_name', 'Sistem Informasi Nilai Murid') }}</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>">
    
    <!-- Google Fonts (Outfit) -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --font-family: 'Outfit', sans-serif;
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-light: #f1f5f9;
            --card-light: rgba(255, 255, 255, 0.85);
            --bg-dark: #0f172a;
            --card-dark: rgba(30, 41, 59, 0.85);
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        [data-bs-theme="dark"] body {
            background-color: var(--bg-dark);
            color: #f8fafc;
        }

        /* Glowing Blobs Background */
        .glow-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        .blob {
            position: absolute;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
        }
        .blob-1 {
            background-color: #3b82f6;
            top: -50px;
            left: -50px;
            animation: float-1 12s infinite alternate;
        }
        .blob-2 {
            background-color: #0d9488;
            bottom: -50px;
            right: -50px;
            animation: float-2 15s infinite alternate;
        }
        @keyframes float-1 {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(40px, 20px) scale(1.05); }
            100% { transform: translate(0, 0) scale(1); }
        }
        @keyframes float-2 {
            0% { transform: translate(0, 0) scale(1.05); }
            50% { transform: translate(-20px, -40px) scale(0.95); }
            100% { transform: translate(0, 0) scale(1.05); }
        }

        .login-card {
            background: var(--card-light);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            transition: all 0.3s;
            z-index: 10;
        }

        [data-bs-theme="dark"] .login-card {
            background: var(--card-dark);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        }

        .login-header {
            padding: 40px 40px 15px 40px;
            text-align: center;
        }

        .login-body {
            padding: 0 40px 40px 40px;
        }

        .brand-icon {
            width: 64px;
            height: 64px;
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px auto;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.05);
        }

        [data-bs-theme="dark"] .brand-icon {
            background-color: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.95rem;
            border-color: rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.6);
            transition: all 0.2s;
        }

        [data-bs-theme="dark"] .form-control {
            background-color: rgba(15, 23, 42, 0.6);
            border-color: rgba(255, 255, 255, 0.1);
            color: #f8fafc;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
            border-color: var(--primary-color);
            background-color: white;
        }

        [data-bs-theme="dark"] .form-control:focus {
            background-color: #0f172a;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25);
        }

        .input-group-text {
            border-color: rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.6);
            color: #64748b;
        }

        [data-bs-theme="dark"] .input-group-text {
            background-color: rgba(15, 23, 42, 0.6);
            border-color: rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }

        .btn-login {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            transition: all 0.2s;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
            color: white;
        }

        [data-bs-theme="dark"] .btn-login {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        [data-bs-theme="dark"] .btn-login:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(0, 0, 0, 0.05);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            backdrop-filter: blur(5px);
            transition: all 0.2s;
            z-index: 100;
        }

        [data-bs-theme="dark"] .theme-toggle {
            background: rgba(30, 41, 59, 0.5);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .theme-toggle:hover {
            transform: scale(1.05);
            background-color: rgba(255, 255, 255, 0.8);
        }

        [data-bs-theme="dark"] .theme-toggle:hover {
            background-color: rgba(30, 41, 59, 0.8);
        }

        /* Dark mode typography settings */
        [data-bs-theme="dark"] .text-muted {
            color: #94a3b8 !important;
        }
        [data-bs-theme="dark"] label {
            color: #e2e8f0 !important;
        }
        [data-bs-theme="dark"] h3 {
            color: #f8fafc !important;
        }
        
        .login-footer {
            z-index: 10;
            color: #64748b;
        }
        [data-bs-theme="dark"] .login-footer {
            color: #94a3b8 !important;
        }
        
        .cursor-pointer {
            cursor: pointer;
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            .login-card {
                border-radius: 16px;
            }
            .login-header {
                padding: 30px 20px 10px 20px !important;
            }
            .login-body {
                padding: 0 20px 30px 20px !important;
            }
            .brand-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
                margin-bottom: 12px;
            }
            .login-header h4 {
                font-size: 1.25rem !important;
            }
        }
    </style>
</head>
<body>

    <!-- Glowing Background blobs -->
    <div class="glow-bg">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <!-- Theme Toggle button -->
    <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle Theme">
        <i class="fa-solid fa-moon text-secondary" id="themeIcon"></i>
    </button>

    <div class="login-card">
        <div class="login-header">
            <div class="brand-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h4 class="fw-800 text-center m-0 text-primary-custom" style="letter-spacing: -0.5px; font-size: 1.5rem;">{{ \App\Models\Setting::get('app_name', 'SINM') }}</h4>
            <p class="text-muted small mt-2" style="font-size: 0.85rem;">Portal Akademik Nilai Murid</p>
        </div>

        <div class="login-body">
            @if($errors->any() && !$errors->has('login_identifier') && !$errors->has('password'))
                <div class="alert alert-danger border-0 small py-2 mb-3" style="border-radius: 8px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="login_identifier" class="form-label small fw-600">NIS / NISN / Username</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0" style="border-radius: 12px 0 0 12px;">
                            <i class="fa-solid fa-user text-muted"></i>
                        </span>
                        <input type="text" 
                               name="login_identifier" 
                               id="login_identifier" 
                               class="form-control border-start-0 @error('login_identifier') is-invalid @enderror" 
                               placeholder="Masukkan NIS atau Username" 
                               value="{{ old('login_identifier') }}"
                               style="border-radius: 0 12px 12px 0;"
                               required>
                        @error('login_identifier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label small fw-600">Password</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0" style="border-radius: 12px 0 0 12px;">
                            <i class="fa-solid fa-lock text-muted"></i>
                        </span>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" 
                               placeholder="Masukkan password" 
                               required>
                        <span class="input-group-text border-start-0 cursor-pointer" id="togglePassword" style="border-radius: 0 12px 12px 0;">
                            <i class="fa-solid fa-eye text-muted" id="eyeIcon"></i>
                        </span>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 form-check d-flex align-items-center">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label small text-muted ms-2" for="remember">Ingat Saya</label>
                </div>

                <button type="submit" class="btn btn-login">
                    Masuk <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Login Footer -->
    <div class="login-footer text-center mt-4 small">
        {{ \App\Models\Setting::get('footer_text', '© ' . date('Y') . ' SINM. All Rights Reserved.') }}
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('themeIcon');
            
            if (html.getAttribute('data-bs-theme') === 'light') {
                html.setAttribute('data-bs-theme', 'dark');
                icon.className = 'fa-solid fa-sun text-warning';
                localStorage.setItem('theme', 'dark');
            } else {
                html.setAttribute('data-bs-theme', 'light');
                icon.className = 'fa-solid fa-moon text-secondary';
                localStorage.setItem('theme', 'light');
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme');
            const icon = document.getElementById('themeIcon');
            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                if (icon) icon.className = 'fa-solid fa-sun text-warning';
            }
            
            // Password Show/Hide toggle
            const togglePasswordButton = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (togglePasswordButton && passwordInput && eyeIcon) {
                togglePasswordButton.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    if (type === 'password') {
                        eyeIcon.className = 'fa-solid fa-eye text-muted';
                    } else {
                        eyeIcon.className = 'fa-solid fa-eye-slash text-muted';
                    }
                });
            }
        });
    </script>
</body>
</html>
