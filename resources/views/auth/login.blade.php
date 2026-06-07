<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ $cafeBrand['name'] ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --white: #ffffff;
            --border: #E5E7EB;
            --border-light: #F3F4F6;
            --radius-lg: 16px;
            --radius-md: 10px;
            --radius-sm: 8px;
            --radius-full: 9999px;
            --accent: #D97706;
            --accent-dark: #B45309;
            --accent-light: #FFFBEB;
            --fg: #111827;
            --fg-secondary: #374151;
            --muted: #6B7280;
            --green: #059669;
            --green-light: #D1FAE5;
            --red: #DC2626;
            --red-light: #FEE2E2;
            --bg: #F9FAFB;
            --shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --font: 'Inter', sans-serif;
            --transition: 0.2s ease;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font);
            background-color: var(--bg);
            /* Subtle pattern overlay */
            background-image: radial-gradient(#E5E7EB 1px, transparent 1px);
            background-size: 20px 20px;
            color: var(--fg);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }

        .shell {
            width: 100%;
            max-width: 440px;
            animation: fadeIn .4s ease-out;
        }

        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(15px); } 
            to { opacity: 1; transform: translateY(0); } 
        }

        /* ===== CARD ===== */
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: var(--shadow-lg);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        /* ===== LOGO ===== */
        .logo-wrap {
            margin-bottom: 24px;
        }

        .logo-wrap img {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            object-fit: cover;
            box-shadow: 0 8px 24px rgba(217, 119, 6, 0.15);
            border: 3px solid var(--accent-light);
        }

        /* ===== TYPOGRAPHY ===== */
        .card h2 {
            font-size: 24px;
            font-weight: 900;
            color: var(--fg);
            margin: 0 0 4px;
            letter-spacing: -0.5px;
        }

        .card p {
            font-size: 14px;
            color: var(--muted);
            margin: 0 0 32px;
            font-weight: 500;
        }

        /* ===== ERROR ALERT ===== */
        .error {
            width: 100%;
            padding: 14px 18px;
            background: var(--red-light);
            color: var(--red);
            border: 1px solid #FECACA;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ===== FORM ===== */
        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            text-align: left;
        }

        .field label {
            font-size: 12px;
            font-weight: 800;
            color: var(--fg-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 14px;
            transition: color var(--transition);
        }

        .field input[type="text"],
        .field input[type="password"] {
            width: 100%;
            padding: 14px 16px 14px 44px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--bg);
            color: var(--fg);
            font-size: 15px;
            font-weight: 500;
            font-family: var(--font);
            outline: none;
            transition: all var(--transition);
        }

        .field input:focus {
            border-color: var(--accent);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.1);
        }

        .field input:focus + i,
        .field input:focus ~ i { /* If icon is after input */
            color: var(--accent);
        }
        
        /* Adjust icon color on focus (if icon is before input) */
        .input-wrap:focus-within i {
            color: var(--accent);
        }

        /* ===== CHECKBOX ===== */
        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--fg-secondary);
            font-weight: 600;
            cursor: pointer;
        }

        .checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border-radius: 6px;
            border: 1.5px solid var(--border);
            accent-color: var(--accent);
            cursor: pointer;
        }

        .forgot-link {
            font-size: 13px;
            font-weight: 700;
            color: var(--accent);
            text-decoration: none;
            transition: color var(--transition);
        }

        .forgot-link:hover {
            color: var(--accent-dark);
            text-decoration: underline;
        }

        /* ===== BUTTON ===== */
        button[type="submit"] {
            width: 100%;
            padding: 16px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-size: 15px;
            font-weight: 800;
            font-family: var(--font);
            cursor: pointer;
            transition: all var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
        }

        button[type="submit"]:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(217, 119, 6, 0.3);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            position: relative;
        }

        button:disabled::after {
            content: "";
            position: absolute;
            width: 18px;
            height: 18px;
            top: 50%;
            right: 20px;
            margin-top: -9px;
            border: 2.5px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: rotate .6s linear infinite;
        }

        @keyframes rotate { 
            from { transform: rotate(0deg); } 
            to { transform: rotate(360deg); } 
        }

        /* ===== FOOTER TEXT ===== */
        .card-footer {
            margin-top: 32px;
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
        }

        .card-footer a {
            color: var(--accent-dark);
            font-weight: 800;
            text-decoration: none;
        }

        .card-footer a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .card { padding: 36px 24px; }
            .card h2 { font-size: 20px; }
        }
    </style>
    @include('components.page-transition-guard')
</head>
<body>
    <main class="shell">
        <section class="card">
            <div class="logo-wrap">
                @if(isset($cafeBrand['logo']) && $cafeBrand['logo'])
                    <img src="{{ asset('storage/' . $cafeBrand['logo']) }}" alt="Logo {{ $cafeBrand['name'] ?? 'Cafe' }}">
                @else
                    <img src="https://placehold.co/72x72/D97706/FFFFFF?text={{ substr($cafeBrand['name'] ?? 'Cafe', 0, 2) }}" alt="Logo Cafe">
                @endif
            </div>
            <h2>Selamat Datang!</h2>
            <p>Silahkan login ke panel dashboard.</p>

            @if (session('error'))
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @elseif ($errors->any())
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form id="loginForm" method="POST" action="{{ route('login.store') }}" data-turbo="false" autocomplete="off">
                @csrf
                <div class="field">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <input id="username" name="username" type="text" placeholder="Masukkan username" value="{{ old('username') }}" autocomplete="username" required autofocus>
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <input id="password" name="password" type="password" placeholder="Masukkan password" autocomplete="current-password" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                <div class="row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        Ingat saya
                    </label>
                    <a href="#" class="forgot-link">Lupa Password?</a>
                </div>
                <button type="submit" id="submitBtn">Masuk Dashboard</button>
            </form>
            
            <div class="card-footer">
                Belum punya akun? <a href="#">Hubungi Admin</a>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerText = 'Memproses...';
        });
    </script>
</body>
</html>
