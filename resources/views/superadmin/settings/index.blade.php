@extends('superadmin.layout')

@section('title', 'Pengaturan Sistem')
@section('page_title', 'Pengaturan Sistem')
@section('page_description', 'Ubah nama cafe dan logo yang tampil di seluruh aplikasi.')

@push('head')
    <style>
        .settings-shell { display: grid; gap: 20px; }
        .settings-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; overflow: hidden; }
        .settings-card h2 { font-size: 16px; font-weight: 800; color: var(--fg); margin: 0 0 4px; display: flex; align-items: center; gap: 8px; }
        .settings-card h2 i { color: var(--accent); }
        .settings-card > p { color: var(--muted); margin: 0 0 24px; font-size: 13px; }
        .settings-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 24px; align-items: start; }

        /* Form Layout */
        .settings-form { display: grid; gap: 28px; }
        .drawer-field { display: flex; flex-direction: column; gap: 10px; }
        .drawer-field label { font-size: 11px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 2px; }
        .drawer-field input[type="text"] {
            width: 100%; padding: 12px 16px; border: 1.5px solid var(--border); border-radius: var(--radius-md);
            background: var(--white); color: var(--fg); font-size: 14px; font-weight: 500; outline: none;
            transition: all var(--transition);
        }
        .drawer-field textarea {
            width: 100%; min-height: 110px; resize: vertical; padding: 12px 16px; border: 1.5px solid var(--border); border-radius: var(--radius-md);
            background: var(--white); color: var(--fg); font-size: 14px; font-weight: 500; outline: none;
            transition: all var(--transition); font-family: inherit;
        }
        .drawer-field input[type="text"]:focus,
        .drawer-field textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 4px rgba(217,119,6,0.1); }
        .drawer-field input[type="file"] { display: none; }
        
        .file-input-wrapper { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin-top: 4px; }
        .file-custom-btn {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--bg); color: var(--fg-secondary); border: 1.5px solid var(--border);
            padding: 9px 16px; border-radius: var(--radius-sm); cursor: pointer;
            font-weight: 700; font-size: 13px; transition: all var(--transition);
        }
        .file-custom-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
        
        .btn-primary { background: var(--accent); color: white; border: none; padding: 10px 20px; border-radius: var(--radius-sm); cursor: pointer; font-weight: 700; }
        .btn-primary:hover { background: var(--accent-dark); transform: translateY(-1px); }
        
        .preview-box {
            background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-lg);
            padding: 24px 20px; text-align: center; display: grid; gap: 12px; place-items: center;
            position: sticky; top: 20px;
        }
        .settings-subcard { padding-top: 28px; border-top: 1px solid var(--border-light); margin-top: 8px; display: grid; gap: 24px; }
        .selected-photo {
            position: relative;
            width: min(220px, 100%);
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--bg);
            box-shadow: var(--shadow-sm);
        }
        .selected-photo img {
            display: block;
            width: 100%;
            max-height: 160px;
            object-fit: contain;
            border-radius: var(--radius-md);
            background: #fff;
        }
        .banner-selected-photo {
            margin-top: 12px;
            width: min(420px, 100%);
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--bg);
            box-shadow: var(--shadow-sm);
        }
        .banner-selected-photo img {
            display: block;
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: var(--radius-md);
            background: #fff;
        }
        .photo-clear {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 28px;
            height: 28px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: #fff;
            color: var(--fg-secondary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
        }
        .photo-clear:hover {
            color: #fff;
            background: #ef4444;
            border-color: #ef4444;
        }
        .hero-preview {
            width: 100%;
            min-height: 220px;
            border-radius: var(--radius-lg);
            overflow: hidden;
            position: relative;
            background: linear-gradient(135deg, #E85D2C 0%, #F97316 50%, #FB923C 100%);
            color: #fff;
            display: grid;
            align-items: end;
            text-align: left;
            background-size: cover;
            background-position: center;
        }
        .hero-preview::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(232,93,44,0.92) 0%, rgba(249,115,22,0.86) 55%, rgba(251,146,60,0.82) 100%);
        }
        .hero-preview-copy { position: relative; z-index: 1; padding: 20px; display: grid; gap: 10px; }
        .hero-preview-tag {
            display: inline-flex; width: fit-content; align-items: center; gap: 6px; padding: 5px 12px;
            border-radius: 999px; background: rgba(255,255,255,0.18); font-size: 11px; font-weight: 700;
        }
        .hero-preview-title { font-family: 'Playfair Display', Georgia, serif; font-size: 28px; line-height: 1.05; font-weight: 900; }
        .hero-preview-desc { font-size: 13px; line-height: 1.45; color: rgba(255,255,255,0.94); }
        .hero-preview-btn {
            display: inline-flex; width: fit-content; align-items: center; gap: 6px; padding: 9px 18px;
            border-radius: 999px; background: #fff; color: var(--accent); font-weight: 800; font-size: 13px;
        }
    </style>
@endpush

@section('content')
    <div class="settings-shell">
        <section class="settings-card fade-in">
            <div class="card-head">
                <h2><i class="fas fa-store"></i> Brand Cafe</h2>
            </div>
            <p>Atur nama cafe dan logo yang akan tampil di login, sidebar superadmin, dan halaman lain yang memakai brand utama.</p>

            <div class="settings-grid">
                <form method="POST" action="{{ route('superadmin.settings.update') }}" class="settings-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="drawer-field">
                        <label for="cafe_name">Nama Cafe</label>
                        <input id="cafe_name" type="text" name="cafe_name" value="{{ old('cafe_name', $settings['cafe_name'] ?? config('app.name')) }}" required placeholder="Nama cafe">
                    </div>

                    <div class="drawer-field" data-cropper data-cropper-size="520">
                        <label for="logo">Logo Cafe</label>
                        <div class="file-input-wrapper">
                            <input id="logo" type="file" name="logo" accept="image/*" data-cropper-input>
                            <input type="hidden" name="cropped_logo" data-cropper-output>
                            <label for="logo" class="file-custom-btn"><i class="fas fa-image"></i> Pilih Gambar</label>
                            <span class="file-name" id="file-name-display" data-cropper-filename>
                                {{ !empty($settings['cafe_logo']) ? basename($settings['cafe_logo']) : 'Belum ada file dipilih' }}
                            </span>
                        </div>
                        <div class="selected-photo" data-cropper-preview-wrap @if(empty($settings['cafe_logo'])) hidden @endif>
                            <button type="button" class="photo-clear" data-cropper-clear aria-label="Batal pilih foto">
                                <i class="fas fa-xmark"></i>
                            </button>
                            <img
                                src="{{ !empty($settings['cafe_logo']) ? asset('storage/' . $settings['cafe_logo']) : '' }}"
                                alt="Preview logo"
                                data-cropper-preview
                                title="Klik untuk crop ulang"
                            >
                        </div>

                        <div class="cropper-modal" data-cropper-panel hidden>
                            <div class="cropper-dialog">
                                <div class="cropper-head">
                                    <strong><i class="fas fa-crop-simple"></i> Atur Crop Logo</strong>
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
                    </div>

                    <div class="settings-subcard">
                        <div class="drawer-field">
                            <label for="hero_banner_tag">Label Banner</label>
                            <input id="hero_banner_tag" type="text" name="hero_banner_tag" value="{{ old('hero_banner_tag', $settings['hero_banner_tag'] ?? 'PROMO SPESIAL HARI INI') }}" placeholder="Contoh: PROMO SPESIAL HARI INI">
                        </div>

                        <div class="drawer-field">
                            <label for="hero_banner_title">Judul Banner</label>
                            <input id="hero_banner_title" type="text" name="hero_banner_title" value="{{ old('hero_banner_title', $settings['hero_banner_title'] ?? 'Diskon 50% Untuk Semua Paket Nasi Goreng') }}" placeholder="Judul banner pelanggan">
                        </div>

                        <div class="drawer-field">
                            <label for="hero_banner_desc">Deskripsi Banner</label>
                            <textarea id="hero_banner_desc" name="hero_banner_desc" placeholder="Deskripsi singkat banner">{{ old('hero_banner_desc', $settings['hero_banner_desc'] ?? 'Nikmati paket lengkap dengan harga setengah. Berlaku sampai pukul 23:59 malam ini.') }}</textarea>
                        </div>

                        <div class="drawer-field">
                            <label for="hero_banner_button_text">Teks Tombol</label>
                            <input id="hero_banner_button_text" type="text" name="hero_banner_button_text" value="{{ old('hero_banner_button_text', $settings['hero_banner_button_text'] ?? 'Lihat Promo') }}" placeholder="Contoh: Lihat Promo">
                        </div>

                        <div class="drawer-field">
                            <label for="hero_banner_image">Foto Banner</label>
                            <div class="file-input-wrapper">
                                <input id="hero_banner_image" type="file" name="hero_banner_image" accept="image/*">
                                <label for="hero_banner_image" class="file-custom-btn"><i class="fas fa-image"></i> Pilih Foto Banner</label>
                                <span class="file-name" id="hero-banner-file-name">
                                    {{ !empty($settings['hero_banner_image']) ? basename($settings['hero_banner_image']) : 'Belum ada file dipilih' }}
                                </span>
                            </div>
                            <div class="banner-selected-photo" id="hero-banner-preview-wrap" @if(empty($settings['hero_banner_image'])) hidden @endif>
                                <img
                                    id="hero-banner-preview-image"
                                    src="{{ !empty($settings['hero_banner_image']) ? asset('storage/' . $settings['hero_banner_image']) : '' }}"
                                    alt="Preview foto banner">
                            </div>
                        </div>
                    </div>

                    <div class="actions" style="margin-top: 12px; padding-top: 24px; border-top: 1px solid var(--border-light);">
                        <button type="submit" class="btn btn-primary" style="min-height: 46px; padding: 0 32px; font-size: 14px; border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(217,119,6,0.2);">
                            <i class="fas fa-save"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>

                <div class="preview-box">
                    <div
                        class="hero-preview"
                        id="hero-preview"
                        @if(!empty($settings['hero_banner_image']))
                            style="background-image:url('{{ asset('storage/' . $settings['hero_banner_image']) }}')"
                        @endif
                    >
                        <div class="hero-preview-copy">
                            <div class="hero-preview-tag" id="hero-preview-tag"><i class="fas fa-fire"></i> <span>{{ old('hero_banner_tag', $settings['hero_banner_tag'] ?? 'PROMO SPESIAL HARI INI') }}</span></div>
                            <div class="hero-preview-title" id="hero-preview-title">{{ old('hero_banner_title', $settings['hero_banner_title'] ?? 'Diskon 50% Untuk Semua Paket Nasi Goreng') }}</div>
                            <div class="hero-preview-desc" id="hero-preview-desc">{{ old('hero_banner_desc', $settings['hero_banner_desc'] ?? 'Nikmati paket lengkap dengan harga setengah. Berlaku sampai pukul 23:59 malam ini.') }}</div>
                            <div class="hero-preview-btn" id="hero-preview-button"><i class="fas fa-tag"></i> <span>{{ old('hero_banner_button_text', $settings['hero_banner_button_text'] ?? 'Lihat Promo') }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/cafe-image-cropper.js') }}"></script>
    <script>
        (() => {
            const map = [
                ['hero_banner_tag', 'hero-preview-tag'],
                ['hero_banner_title', 'hero-preview-title'],
                ['hero_banner_desc', 'hero-preview-desc'],
                ['hero_banner_button_text', 'hero-preview-button'],
            ];

            map.forEach(([inputId, previewId]) => {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                if (!input || !preview) return;

                input.addEventListener('input', () => {
                    const target = preview.querySelector('span') || preview;
                    const value = input.value.trim();
                    if (target) {
                        target.textContent = value || target.textContent;
                    }
                });
            });

            const heroImageInput = document.getElementById('hero_banner_image');
            const heroPreview = document.getElementById('hero-preview');
            const heroFileName = document.getElementById('hero-banner-file-name');
            const heroBannerPreviewWrap = document.getElementById('hero-banner-preview-wrap');
            const heroBannerPreviewImage = document.getElementById('hero-banner-preview-image');

            heroImageInput?.addEventListener('change', (event) => {
                const file = event.target.files?.[0];
                if (!file || !heroPreview) return;
                if (heroFileName) heroFileName.textContent = file.name;
                const reader = new FileReader();
                reader.onload = (e) => {
                    const result = e.target?.result || '';
                    heroPreview.style.backgroundImage = `url('${result}')`;
                    if (heroBannerPreviewImage) heroBannerPreviewImage.src = result;
                    if (heroBannerPreviewWrap) heroBannerPreviewWrap.hidden = false;
                };
                reader.readAsDataURL(file);
            });
        })();
    </script>
@endpush
