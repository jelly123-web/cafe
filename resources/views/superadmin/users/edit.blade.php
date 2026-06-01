@extends('superadmin.layout')

@push('head')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');

        :root {
            --bg-main: #f9f5f0;
            --bg-card: #ffffff;
            --primary: #795548;
            --secondary: #bcaaa4;
            --accent: #d7ccc8;
            --accent-2: #c68b59;
            --highlight: #d4a373;
            --text-main: #6d4c41;
            --text-muted: #a1887f;
            --profit: #81c784;
            --loss: #e57373;
            --shadow: rgba(121, 85, 72, 0.08);
            --text: var(--text-main);
            --muted: var(--text-muted);
            --line: rgba(121, 85, 72, 0.14);
        }

        .main-panel {
            padding: 2rem 2.5rem;
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
            background-image: radial-gradient(var(--accent) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            font-size: 1.8rem;
            margin: 0.5rem 0 0.25rem;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .page-kicker {
            display: inline-flex;
            align-items: center;
            background-color: var(--highlight);
            color: #fff;
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .panel {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
            max-width: 800px;
        }

        .user-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        .form-grid label {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .form-grid label > span {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .form-grid input[type="text"],
        .form-grid input[type="password"],
        .form-grid select {
            width: 100%;
            padding: 0.65rem 1rem;
            border: 1px solid var(--accent);
            border-radius: 12px;
            background-color: var(--bg-card);
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-grid input[type="text"]:focus,
        .form-grid input[type="password"]:focus,
        .form-grid select:focus {
            border-color: var(--highlight);
            box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15);
        }

        .form-grid select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23795548' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
            cursor: pointer;
        }

        .form-error {
            font-size: 0.8rem;
            color: var(--loss);
            margin-top: 0.15rem;
            font-weight: 500;
        }

        .switch-row {
            position: relative;
            flex-direction: row !important;
            align-items: center;
            gap: 0.6rem !important;
            margin-top: 0.35rem;
            min-height: 26px;
        }

        .switch-row input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            margin: 0;
            pointer-events: none;
        }

        .switch-ui {
            position: relative;
            width: 46px;
            height: 26px;
            border: 2px solid var(--highlight);
            background: #fff;
            border-radius: 999px;
            flex-shrink: 0;
            transition: background-color 0.25s ease, border-color 0.25s ease;
            box-shadow: none;
        }

        .switch-ui::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 3px;
            width: 18px;
            height: 18px;
            background-color: #fff;
            border-radius: 50%;
            transform: translateY(-50%);
            transition: transform 0.25s ease, background-color 0.25s ease;
            box-shadow: 0 1px 2px rgba(121, 85, 72, 0.18);
        }

        .switch-row input[type="checkbox"]:checked + .switch-ui {
            background: var(--highlight);
            border-color: var(--highlight);
        }

        .switch-row input[type="checkbox"]:checked + .switch-ui::after {
            transform: translate(20px, -50%);
            background-color: #fff;
        }

        .switch-row input[type="checkbox"]:focus-visible + .switch-ui {
            box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.18);
        }

        .switch-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 0.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--accent);
        }

        .secondary-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary);
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            font-weight: 600;
            border: 1px solid var(--accent);
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .secondary-link:hover {
            background-color: var(--bg-main);
            border-color: var(--highlight);
            color: var(--highlight);
        }

        .primary-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--highlight);
            color: #fff;
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .primary-link:hover {
            background-color: #c68b59;
            transform: translateY(-2px);
        }

        @media (max-width: 1100px) {
            .app-shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-right: 0;
                border-bottom: 1px solid rgba(121, 85, 72, 0.08);
            }
        }

        @media (max-width: 768px) {
            .main-panel {
                padding: 1.5rem 1rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions a,
            .form-actions button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('title', 'Edit Akun Pengguna')
@section('page_title', 'Edit Akun Pengguna')
@section('page_description', 'Ubah data akun, password, dan status aktif.')

@section('content')
    @php
        $isEdit = true;
    @endphp

    <div class="panel">
        <form method="POST" action="{{ route('superadmin.users.update', $user) }}" class="user-form">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <label>
                    <span>Nama</span>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<small class="form-error">{{ $message }}</small>@enderror
                </label>

                <label>
                    <span>Username</span>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required>
                    @error('username')<small class="form-error">{{ $message }}</small>@enderror
                </label>

                <label>
                    <span>Role</span>
                    <select name="role" required>
                        @foreach (['superadmin' => 'Superadmin', 'admin' => 'Admin', 'kasir' => 'Kasir', 'leader_cashier' => 'Leader Kasir', 'kitchen' => 'Dapur', 'inventory' => 'Gudang'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')<small class="form-error">{{ $message }}</small>@enderror
                </label>

                <label>
                    <span>Password (kosongkan jika tidak diubah)</span>
                    <input type="password" name="password">
                    @error('password')<small class="form-error">{{ $message }}</small>@enderror
                </label>

                <label class="switch-row">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true))>
                    <span class="switch-ui" aria-hidden="true"></span>
                    <span class="switch-label">Akun aktif</span>
                </label>
            </div>

            <div class="form-actions">
                <a href="{{ route('superadmin.users.index') }}" class="secondary-link">Batal</a>
                <button type="submit" class="primary-link">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection
