<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profil - {{ config('app.name') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/dashboard.css'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endif
    <style>
        .profile-card {
            background: #fff;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 2rem auto;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
        }
        .btn-save {
            background: #795548;
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-save:hover {
            background: #5d4037;
            transform: translateY(-2px);
        }
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .alert-error {
            background: #ffebee;
            color: #c62828;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #795548;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <main class="dashboard-shell">
        <div style="max-width: 600px; margin: 0 auto;">
            <a href="{{ route('dashboard') }}" class="back-link">&larr; Kembali ke Dashboard</a>
            
            <div class="profile-card">
                <h1>Edit Profil</h1>
                <p style="margin-bottom: 2rem; color: #777;">Perbarui informasi akun Anda di sini.</p>

                @if(session('success') || session('status'))
                    <div class="alert alert-success">
                        {{ session('success') ?: session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Nomor Telepon</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" placeholder="Contoh: 08123456789">
                    </div>

                    <hr style="margin: 2rem 0; border: 0; border-top: 1px solid #eee;">
                    
                    <h3 style="margin-bottom: 1rem;">Ganti Password</h3>
                    <p style="font-size: 0.85rem; color: #888; margin-bottom: 1rem;">Kosongkan jika tidak ingin mengganti password.</p>

                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" id="password" name="password">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation">
                    </div>

                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
