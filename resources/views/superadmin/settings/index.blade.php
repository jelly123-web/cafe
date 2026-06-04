@extends('superadmin.layout')

@section('title', 'Pengaturan Sistem')
@section('kicker', 'Konfigurasi')
@section('page_title', 'Pengaturan Sistem')
@section('page_description', 'Ubah nama cafe dan logo yang tampil di seluruh aplikasi.')

@push('head')
    <style>
        :root {
            --bg-main: #F9F5F0;
            --bg-card: #FFFFFF;
            --primary: #795548;
            --secondary: #BCAAA4;
            --accent: #D7CCC8;
            --highlight: #D4A373;
            --text-main: #6D4C41;
            --text-muted: #A1887F;
            --profit: #81C784;
            --loss: #E57373;
            --shadow: rgba(121, 85, 72, 0.08);
        }

        .settings-shell {
            display: grid;
            gap: 1.5rem;
        }

        .settings-card {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
            padding: 1.5rem 2rem;
        }

        .settings-card h2 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            font-size: 1.4rem;
            margin: 0 0 0.5rem;
        }

        .settings-card > p {
            color: var(--text-muted);
            margin: 0 0 1.5rem;
            font-size: 0.95rem;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 2rem;
            align-items: start;
        }

        .settings-form {
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
            border: 1px solid var(--accent);
            border-radius: 12px;
            padding: 0.7rem 1rem;
            background: var(--bg-card);
            color: var(--text-main);
            font: inherit;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .field input[type="text"]:focus {
            border-color: var(--highlight);
            box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15);
        }

        .field input[type="file"] {
            display: none;
        }

        .file-input-wrapper {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .file-custom-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 128px;
            background-color: var(--accent);
            color: var(--primary);
            border: 1px solid transparent;
            padding: 0.75rem 1.4rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            line-height: 1;
            transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
            white-space: nowrap;
        }

        .file-custom-btn:hover {
            background-color: #cab7ae;
            box-shadow: 0 4px 12px rgba(121, 85, 72, 0.12);
            transform: translateY(-1px);
        }

        .file-name {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-style: italic;
        }

        .selected-photo {
            position: relative;
            width: 132px;
            margin-top: 0.85rem;
        }

        .selected-photo img {
            width: 132px;
            height: 132px;
            object-fit: cover;
            border-radius: 18px;
            border: 1px dashed var(--accent);
            background: #fffaf5;
            cursor: pointer;
            box-shadow: 0 4px 15px var(--shadow);
        }

        .photo-clear {
            position: absolute;
            top: -9px;
            right: -9px;
            width: 26px;
            height: 26px;
            border-radius: 999px;
            border: 1px solid #FFCDD2;
            background: #fff;
            color: var(--loss);
            cursor: pointer;
            font-weight: 800;
            line-height: 1;
        }

        .cropper-modal {
            position: fixed;
            inset: 0;
            z-index: 1600;
            display: grid;
            place-items: center;
            background: rgba(56, 37, 30, 0.34);
            backdrop-filter: blur(3px);
        }

        .cropper-modal[hidden] {
            display: none !important;
        }

        .cropper-dialog {
            width: min(560px, calc(100vw - 2rem));
            background: #fff;
            border: 1px solid var(--accent);
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(62, 39, 35, 0.18);
            overflow: hidden;
        }

        .cropper-head, .cropper-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.15rem;
            border-bottom: 1px solid var(--accent);
        }

        .cropper-foot {
            border-bottom: 0;
            border-top: 1px solid var(--accent);
            justify-content: flex-end;
        }

        .cropper-head strong {
            color: var(--primary);
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.25rem;
        }

        .cropper-body {
            padding: 1.15rem;
            display: grid;
            place-items: center;
            gap: 0.85rem;
        }

        .cropper-canvas {
            width: min(360px, 78vw);
            height: min(360px, 78vw);
            border: 1px dashed var(--accent);
            border-radius: 18px;
            background: #fffaf5;
            cursor: move;
            touch-action: none;
        }

        .cropper-control {
            display: grid;
            gap: 0.35rem;
            width: min(360px, 78vw);
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .cropper-control input { accent-color: var(--highlight); }

        .cropper-close, .cropper-done {
            border: 1px solid var(--accent);
            background: #fff;
            color: var(--primary);
            border-radius: 12px;
            padding: 0.55rem 1rem;
            cursor: pointer;
            font-weight: 700;
        }

        .cropper-done {
            background: var(--highlight);
            color: #fff;
            border-color: var(--highlight);
        }

        .form-error {
            font-size: 0.8rem;
            color: var(--loss);
            margin-top: 0.15rem;
            font-weight: 500;
        }

        .preview-box {
            background: #FFFAF5;
            border: 1px solid var(--accent);
            border-radius: 18px;
            padding: 1.5rem 1.25rem;
            text-align: center;
            display: grid;
            gap: 0.75rem;
            place-items: center;
        }

        .preview-box img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 28px;
            box-shadow: 0 4px 15px rgba(121, 85, 72, 0.12);
        }

        .preview-box .placeholder {
            width: 120px;
            height: 120px;
            border-radius: 28px;
            display: grid;
            place-items: center;
            background: var(--highlight);
            color: #fff;
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.5rem;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(212, 163, 115, 0.24);
        }

        .preview-box strong {
            color: var(--primary);
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.3rem;
        }

        .preview-box small {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }

        .primary-link, .secondary-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.3s ease;
            border: 1px solid;
        }

        .primary-link {
            background-color: var(--highlight);
            color: #fff;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
            border-color: var(--highlight);
        }

        .primary-link:hover {
            background-color: #c68b59;
            border-color: #c68b59;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 163, 115, 0.4);
        }

        .secondary-link {
            background: transparent;
            border-color: var(--accent);
            color: var(--primary);
        }

        .secondary-link:hover {
            border-color: var(--highlight);
            color: var(--highlight);
        }

        @media (max-width: 900px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="settings-shell">
        <section class="settings-card">
            <h2>Brand Cafe</h2>
            <p>Atur nama cafe dan logo yang akan tampil di login, sidebar superadmin, dan halaman lain yang memakai brand utama.</p>

            <div class="settings-grid">
                <form method="POST" action="{{ route('superadmin.settings.update') }}" class="settings-form" enctype="multipart/form-data" data-turbo="false">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label for="cafe_name">Nama Cafe</label>
                        <input
                            id="cafe_name"
                            type="text"
                            name="cafe_name"
                            value="{{ old('cafe_name', $settings['cafe_name'] ?? config('app.name')) }}"
                            placeholder="Nama cafe"
                            required
                        >
                        @error('cafe_name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field" data-cropper data-cropper-size="520">
                        <label for="logo">Logo Cafe</label>
                        <div class="file-input-wrapper">
                            <input id="logo" type="file" name="logo" accept="image/*" data-cropper-input>
                            <input type="hidden" name="cropped_logo" data-cropper-output>
                            <label for="logo" class="file-custom-btn">Pilih Gambar</label>
                            <span class="file-name" id="file-name-display" data-cropper-filename>Belum ada file dipilih</span>
                        </div>
                        <div class="selected-photo" data-cropper-preview-wrap hidden>
                            <button type="button" class="photo-clear" data-cropper-clear aria-label="Batal pilih foto">x</button>
                            <img src="" alt="Preview logo dipilih" data-cropper-preview title="Klik untuk crop ulang">
                        </div>
                        <div class="cropper-modal" data-cropper-panel hidden>
                            <div class="cropper-dialog">
                                <div class="cropper-head">
                                    <strong>Atur Crop Logo</strong>
                                    <button type="button" class="cropper-close" data-cropper-close>Tutup</button>
                                </div>
                                <div class="cropper-body">
                                    <canvas class="cropper-canvas" data-cropper-canvas></canvas>
                                    <label class="cropper-control">
                                        Zoom crop
                                        <input type="range" min="1" max="3" step="0.05" value="1" data-cropper-zoom>
                                    </label>
                                </div>
                                <div class="cropper-foot">
                                    <button type="button" class="cropper-done" data-cropper-close>Pakai Crop Ini</button>
                                </div>
                            </div>
                        </div>
                        @error('logo')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="actions">
                        <button type="submit" class="primary-link">Simpan Pengaturan</button>
                        <a href="{{ route('superadmin.dashboard') }}" class="secondary-link">Kembali</a>
                    </div>
                </form>

                <div class="preview-box">
                    <span style="color:var(--text-muted);font-size:0.85rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;width:100%;">Preview Brand</span>
                    @if (!empty($cafeBrand['logo_url']))
                        <img src="{{ $cafeBrand['logo_url'] }}" alt="{{ $cafeBrand['name'] ?? 'Cafe' }}">
                    @else
                        <div class="placeholder">{{ strtoupper(substr($cafeBrand['name'] ?? 'Cafe', 0, 4)) }}</div>
                    @endif
                    <strong>{{ $cafeBrand['name'] ?? config('app.name') }}</strong>
                    <small>Logo dan nama ini dipakai di seluruh aplikasi.</small>
                </div>
            </div>
        </section>
    </div>

    <script src="{{ asset('js/cafe-image-cropper.js') }}?v=4"></script>
    <script>
        const fileInput = document.getElementById('logo');
        const fileNameDisplay = document.getElementById('file-name-display');

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : 'Belum ada file dipilih';
                fileNameDisplay.textContent = fileName;
            });
        }
    </script>
@endsection
