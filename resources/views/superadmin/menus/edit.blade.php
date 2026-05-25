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
            --highlight: #d4a373;
            --text-main: #6d4c41;
            --text-muted: #a1887f;
            --profit: #81c784;
            --loss: #e57373;
            --shadow: rgba(121, 85, 72, 0.08);
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

        .panel {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
            max-width: 800px;
        }

        .menu-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        input[type="text"],
        input[type="number"],
        select {
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

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: var(--highlight);
            box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15);
        }

        select {
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

        .image-box {
            grid-column: span 2;
            display: grid;
            grid-template-columns: 1fr 150px;
            gap: 1.5rem;
            align-items: start;
        }

        input[type="file"] {
            display: none;
        }

        .file-input-wrapper {
            margin-top: 0.15rem;
        }

        .file-custom-btn {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            justify-content: center;
            background-color: var(--secondary);
            color: #fff !important;
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: background 0.2s ease;
            margin-bottom: 0.5rem;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .file-custom-btn:hover {
            background-color: var(--primary);
            color: #fff !important;
        }

        .file-name {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-style: italic;
        }

        .image-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 16px;
            border: 2px dashed var(--accent);
            padding: 4px;
            background-color: var(--bg-main);
            box-shadow: 0 4px 10px var(--shadow);
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

            .image-box {
                grid-column: span 1;
                grid-template-columns: 1fr;
            }

            .image-preview {
                width: 100%;
                height: 180px;
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

@section('title', 'Edit Menu')
@section('page_title', 'Edit Menu')
@section('page_description', 'Ubah data menu, kategori, foto, dan harga.')

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('superadmin.menus.update', $menu) }}" class="menu-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="field">
                    <label for="code">Kode Menu</label>
                    <input id="code" type="text" name="code" value="{{ old('code', $menu->code) }}" required>
                    @error('code')<small class="form-error">{{ $message }}</small>@enderror
                </div>

                <div class="field">
                    <label for="menu_category_id">Kategori</label>
                    <select id="menu_category_id" name="menu_category_id">
                        <option value="">Tanpa kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('menu_category_id', $menu->menu_category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('menu_category_id')<small class="form-error">{{ $message }}</small>@enderror
                </div>

                <div class="field">
                    <label for="name">Nama Menu</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $menu->name) }}" required>
                    @error('name')<small class="form-error">{{ $message }}</small>@enderror
                </div>

                <div class="field">
                    <label for="selling_price">Harga Jual</label>
                    <input id="selling_price" type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $menu->selling_price) }}" required>
                    @error('selling_price')<small class="form-error">{{ $message }}</small>@enderror
                </div>

                <div class="field">
                    <label for="cost_price">Harga Modal</label>
                    <input id="cost_price" type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $menu->cost_price) }}" required>
                    @error('cost_price')<small class="form-error">{{ $message }}</small>@enderror
                </div>

                <div class="field image-box">
                    <div class="file-input-wrapper">
                        <label for="image">Foto Menu</label>
                        <br>
                        <input id="image" data-menu-image-input type="file" name="image" accept="image/*">
                        <label for="image" class="file-custom-btn">Pilih Gambar</label>
                        <div class="file-name" id="file-name-display">
                            {{ $menu->image_path ? basename($menu->image_path) : 'Belum ada file dipilih' }}
                        </div>
                        @error('image')<small class="form-error">{{ $message }}</small>@enderror
                    </div>
                    <img
                        class="image-preview"
                        data-menu-image-preview
                        src="{{ $menu->image_path ? asset('storage/' . $menu->image_path) : asset('images/menu-placeholder.svg') }}"
                        alt="Preview menu"
                    >
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('superadmin.menus.index') }}" class="secondary-link">Batal</a>
                <button type="submit" class="primary-link">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        const fileInput = document.querySelector('[data-menu-image-input]');
        const fileDisplay = document.getElementById('file-name-display');
        const imagePreview = document.querySelector('[data-menu-image-preview]');

        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : fileDisplay.textContent;
                fileDisplay.textContent = fileName;

                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        imagePreview.src = event.target.result;
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            });
        }
    </script>
@endsection
