@extends('superadmin.layout')

@section('title', 'Tambah Meja')
@section('page_title', 'Tambah Meja')
@section('page_description', 'Buat meja baru agar bisa dibuat QR scan pelanggan.')

@push('head')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');

        :root {
            --bg-main: #F9F5F0;
            --bg-card: #FFFFFF;
            --primary: #795548;
            --secondary: #bcaaa4;
            --accent: #D7CCC8;
            --highlight: #D4A373;
            --text-main: #6D4C41;
            --text-muted: #A1887F;
            --profit: #81c784;
            --loss: #E57373;
            --shadow: rgba(121, 85, 72, 0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
            background-image: radial-gradient(var(--accent) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .app-shell {
            display: grid;
            grid-template-columns: 290px minmax(0, 1fr);
            min-height: 100vh;
        }

        .sidebar {
            padding: 1.75rem 1.4rem;
            border-right: 1px solid rgba(121, 85, 72, 0.08);
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .sidebar-brand h2 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            font-size: 1.8rem;
            margin: 0.5rem 0 0.25rem;
        }

        .sidebar-brand p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .badge {
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

        .nav-menu {
            display: grid;
            gap: 0.75rem;
        }

        .nav-item {
            text-decoration: none;
            color: var(--text-main);
            background: var(--bg-card);
            border-radius: 16px;
            padding: 0.9rem 1rem;
            box-shadow: 0 4px 15px var(--shadow);
            border: 1px solid transparent;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .nav-item.active, .nav-item:hover {
            border-color: rgba(212, 163, 115, 0.35);
            background: #fffaf5;
        }

        .sidebar-footer {
            margin-top: auto;
            display: grid;
            gap: 0.85rem;
        }

        .user-card {
            background: var(--bg-card);
            border-radius: 18px;
            padding: 1rem;
            box-shadow: 0 4px 15px var(--shadow);
        }

        .user-card span, .user-card small {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .user-card strong {
            display: block;
            margin: 0.35rem 0;
            color: var(--primary);
        }

        .logout {
            background-color: var(--highlight);
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
            width: 100%;
            font-size: 0.95rem;
        }

        .logout:hover {
            background-color: #c68b59;
            transform: translateY(-2px);
        }

        .main-panel {
            padding: 2rem 2.5rem;
        }

        .page-header {
            margin-bottom: 1.5rem;
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

        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
            padding: 1.5rem 2rem;
            max-width: 760px;
        }

        .form-grid {
            display: grid;
            gap: 1.25rem;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .field label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .field input[type="text"] {
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

        .field input[type="text"]:focus {
            border-color: var(--highlight);
            box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15);
        }

        .form-error {
            font-size: 0.8rem;
            color: var(--loss);
            margin-top: 0.15rem;
            font-weight: 500;
        }

        .checkline {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.5rem;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .checkline input[type="checkbox"] {
            appearance: none;
            width: 44px;
            height: 24px;
            background-color: var(--accent);
            border-radius: 50px;
            position: relative;
            cursor: pointer;
            transition: background-color 0.3s ease;
            flex-shrink: 0;
        }

        .checkline input[type="checkbox"]::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background-color: #fff;
            border-radius: 50%;
            transition: transform 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .checkline input[type="checkbox"]:checked {
            background-color: var(--highlight);
        }

        .checkline input[type="checkbox"]:checked::after {
            transform: translateX(20px);
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 0.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--accent);
            flex-wrap: wrap;
        }

        .primary-link,
        .secondary-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .primary-link {
            background: var(--highlight);
            color: #fff;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
            border: none;
        }

        .primary-link:hover {
            background-color: #c68b59;
            transform: translateY(-2px);
        }

        .secondary-link {
            background: transparent;
            color: var(--primary);
            border-color: var(--accent);
        }

        .secondary-link:hover {
            background-color: var(--bg-main);
            border-color: var(--highlight);
            color: var(--highlight);
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

            .form-card {
                padding: 1.25rem;
            }

            .actions {
                flex-direction: column;
            }

            .actions a,
            .actions button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
    <div class="form-card">
        <form method="POST" action="{{ route('superadmin.tables.store') }}" class="form-grid">
            @csrf

            <div class="field">
                <label for="number">Nomor Meja</label>
                <input id="number" type="text" name="number" value="{{ old('number') }}" placeholder="Contoh: 1" required>
                @error('number')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="field">
                <label for="name">Nama Meja</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Meja 1 - Jendela" required>
                @error('name')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <label class="checkline">
                <input type="checkbox" name="is_active" value="1" checked>
                <span>Meja aktif</span>
            </label>

            <div class="actions">
                <button class="primary-link" type="submit">Simpan</button>
                <a class="secondary-link" href="{{ route('superadmin.tables.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
