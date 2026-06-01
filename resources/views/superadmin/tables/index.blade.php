@extends('superadmin.layout')

@section('title', 'Meja')
@section('page_title', 'Meja Cafe')
@section('page_description', 'Lihat, tambah, dan kelola meja yang dipakai pelanggan untuk scan QR.')

@push('head')
    <style>
        .table-toolbar {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .table-toolbar h2 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            margin: 0 0 0.25rem;
            font-size: 1.2rem;
        }

        .table-toolbar p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .table-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.5rem;
        }

        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
            padding: 1.25rem;
            display: grid;
            gap: 1.25rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .table-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px var(--shadow);
            border-color: rgba(212, 163, 115, 0.3);
        }

        .table-card-head {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: start;
        }

        .table-card h3 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            margin-bottom: 0.25rem;
            font-size: 1.2rem;
        }

        .table-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            background: #efebe9;
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .qr-box {
            display: grid;
            gap: 0.75rem;
            justify-items: start;
            padding-top: 0.75rem;
            border-top: 1px dashed var(--accent);
        }

        .qr-preview {
            width: 140px;
            height: 140px;
            padding: 8px;
            background: #fff;
            border-radius: 12px;
            border: 2px dashed var(--accent);
            box-shadow: 0 2px 8px var(--shadow);
            display: block;
            object-fit: contain;
            margin-bottom: 0.5rem;
        }

        .qr-box small {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .qr-token-label {
            font-family: monospace;
            font-size: 0.75rem;
            color: var(--text-muted);
            background: #f5f5f5;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            word-break: break-all;
        }

        .table-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .primary-link,
        .secondary-link,
        .danger-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.85rem;
            font-family: inherit;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .primary-link {
            background: var(--highlight);
            color: #fff;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
            border: none;
        }

        .primary-link:hover {
            background: #c68b59;
            transform: translateY(-2px);
        }

        .secondary-link {
            background: transparent;
            color: var(--primary);
            border-color: var(--accent);
        }

        .secondary-link:hover {
            border-color: var(--highlight);
            color: var(--highlight);
            background: #fffaf5;
        }

        .danger-link {
            background: transparent;
            color: var(--loss);
            border-color: #f8d7da;
        }

        .danger-link:hover {
            background: #fff0f0;
            border-color: var(--loss);
        }

        .pagination-area {
            margin-top: 1.5rem;
        }

        .table-empty {
            grid-column: 1 / -1;
            color: var(--text-muted);
            padding: 1.25rem;
        }

        .table-actions form {
            margin: 0;
        }

        .drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(56, 37, 30, 0.32);
            backdrop-filter: blur(2px);
            z-index: 1200;
            opacity: 0;
            visibility: hidden;
            transition: 0.2s ease;
        }

        .drawer-backdrop.open {
            opacity: 1;
            visibility: visible;
        }

        .table-drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: min(560px, 95vw);
            height: 100vh;
            background: linear-gradient(180deg, #fffdfb 0%, #fff 100%);
            z-index: 1201;
            transform: translateX(102%);
            transition: transform 0.2s ease;
            box-shadow: -10px 0 34px rgba(79, 53, 43, 0.18);
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        .table-drawer.open {
            transform: translateX(0);
        }

        .drawer-head {
            padding: 1.15rem 1.25rem;
            border-bottom: 1px solid var(--accent);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
        }

        .drawer-head h3 {
            margin: 0;
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            font-size: 1.35rem;
        }

        .drawer-close {
            border: 1px solid var(--accent);
            background: #fff;
            color: var(--primary);
            border-radius: 12px;
            padding: 0.45rem 0.85rem;
            cursor: pointer;
            font-weight: 600;
        }

        .drawer-body {
            padding: 1rem 1.25rem 1.25rem;
            overflow-y: auto;
        }

        .drawer-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .drawer-field {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .drawer-field label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .drawer-field input[type="text"] {
            width: 100%;
            padding: 0.65rem 1rem;
            border: 1px solid var(--accent);
            border-radius: 12px;
            background: #fff;
            color: var(--text-main);
        }

        .drawer-field input[type="text"]:focus {
            border-color: var(--highlight);
            box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15);
            outline: none;
        }

        .switch-row {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.35rem;
            min-height: 28px;
            cursor: pointer;
        }

        .switch-text {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1;
            white-space: nowrap;
        }

        .switch-row input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .switch-ui {
            width: 44px;
            height: 24px;
            background-color: var(--accent);
            border-radius: 50px;
            position: relative;
            display: inline-block;
            transition: background-color 0.25s ease;
            flex-shrink: 0;
        }

        .switch-ui::after {
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

        .switch-row input[type="checkbox"]:checked + .switch-ui {
            background-color: var(--highlight);
        }

        .switch-row input[type="checkbox"]:checked + .switch-ui::after {
            transform: translateX(20px);
        }

        .drawer-foot {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--accent);
            display: flex;
            justify-content: flex-end;
            gap: 0.7rem;
            background: #fff;
        }

        .btn-drawer-cancel {
            border: 1px solid var(--accent);
            background: #fff;
            color: var(--primary);
            border-radius: 12px;
            padding: 0.6rem 1rem;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-drawer-cancel:hover {
            background: #fffaf5;
        }

        .form-error {
            font-size: 0.82rem;
            color: var(--loss);
            min-height: 1rem;
        }

        .qr-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(56, 37, 30, 0.35);
            backdrop-filter: blur(2px);
            opacity: 0;
            visibility: hidden;
            transition: 0.2s ease;
            z-index: 1300;
        }

        .qr-modal-backdrop.open { opacity: 1; visibility: visible; }

        .qr-modal {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -45%);
            width: min(420px, 92vw);
            background: #fff;
            border: 1px solid var(--accent);
            border-radius: 18px;
            box-shadow: 0 14px 30px rgba(48, 28, 21, 0.22);
            z-index: 1301;
            opacity: 0;
            visibility: hidden;
            transition: 0.2s ease;
        }

        .qr-modal.open { opacity: 1; visibility: visible; transform: translate(-50%, -50%); }
        .qr-modal-head { padding: 12px 14px; border-bottom: 1px solid var(--accent); display: flex; justify-content: space-between; align-items: center; gap: 8px; }
        .qr-modal-body { padding: 14px; display: grid; gap: 10px; justify-items: center; }
        .qr-modal-img { width: 220px; height: 220px; border: 1px dashed var(--accent); border-radius: 14px; padding: 8px; background: #fff; object-fit: contain; }
        .qr-url { width: 100%; font-size: 12px; color: var(--text-muted); word-break: break-all; text-align: center; }

        @media (max-width: 768px) {
            .table-grid {
                grid-template-columns: 1fr;
            }

            .table-toolbar {
                padding: 1rem 1.1rem;
            }

            .table-card {
                padding: 1rem;
            }
        }
        @media (max-width: 768px) {
            .table-toolbar { flex-direction: column; align-items: stretch; padding: 1rem; }
            .table-toolbar div { width: 100%; }
            .primary-link { width: 100%; justify-content: center; }
            
            .table-grid { grid-template-columns: 1fr; gap: 1rem; }
            .table-card { padding: 1rem; }
            .qr-preview { width: 100%; height: auto; aspect-ratio: 1/1; max-width: 200px; margin: 0 auto 0.5rem; }
            
            .drawer-head h3 { font-size: 1.15rem; }
            .drawer-body { padding: 1rem; }
            .drawer-foot { padding: 1rem; }
        }
    </style>
@endpush

@section('content')
    <div class="table-toolbar">
        <div>
            <h2>Daftar Meja</h2>
            <p>Setiap meja punya QR unik untuk scan pelanggan.</p>
        </div>
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
            <button type="button" class="primary-link" id="openCreateDrawer">+ Tambah Meja</button>
            <form method="POST" action="{{ route('superadmin.tables.destroy-all') }}" onsubmit="return confirm('Hapus semua meja? Aksi ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-link">Hapus Semua Meja</button>
            </form>
        </div>
    </div>

    <div class="table-grid">
        @forelse ($tables as $table)
            <article class="table-card" data-table-id="{{ $table->id }}">
                <div class="table-card-head">
                    <div>
                        <span class="table-pill">Meja {{ $table->number }}</span>
                        <h3>{{ $table->name }}</h3>
                        <p style="margin:0;color:var(--text-muted);font-size:0.9rem;">Status: {{ $table->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                        <p style="margin:0;color:var(--text-muted);font-size:0.9rem;">Total transaksi: {{ $table->sales_count }}</p>
                    </div>
                </div>

                <div class="qr-box">
                    <img
                        class="qr-preview"
                        src="{{ route('superadmin.tables.qr', $table) }}"
                        alt="QR Meja {{ $table->number }}"
                        loading="lazy"
                    >
                    <small>Scan QR untuk membuka halaman pelanggan meja ini.</small>
                    <div class="qr-token-label">Token: {{ $table->qr_token }}</div>
                </div>

                <div class="table-actions">
                    <a class="secondary-link" href="{{ route('tables.show', $table) }}" target="_blank" rel="noopener">Buka Halaman</a>
                    <button
                        type="button"
                        class="secondary-link btn-show-qr"
                        data-show-url="{{ route('tables.show', $table) }}"
                        data-qr-url="{{ route('superadmin.tables.qr', $table) }}"
                    >Lihat QR</button>
                    <button
                        type="button"
                        class="secondary-link btn-open-edit"
                        data-id="{{ $table->id }}"
                        data-number="{{ $table->number }}"
                        data-name="{{ $table->name }}"
                        data-active="{{ $table->is_active ? 1 : 0 }}"
                    >Edit</button>
                    <form method="POST" action="{{ route('superadmin.tables.destroy', $table) }}" onsubmit="return confirm('Hapus meja ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="danger-link">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="table-card table-empty">
                <div>Belum ada meja.</div>
            </div>
        @endforelse
    </div>

    <div class="pagination-area">
        {{ $tables->links('components.pagination') }}
    </div>

    <div id="drawerBackdrop" class="drawer-backdrop"></div>
    <aside id="tableDrawer" class="table-drawer" aria-hidden="true">
        <div class="drawer-head">
            <h3 id="drawerTitle">Tambah Meja</h3>
            <button type="button" class="drawer-close" id="closeDrawerBtn">Tutup</button>
        </div>
        <form id="drawerForm" class="drawer-form" method="POST" action="{{ route('superadmin.tables.store') }}">
            @csrf
            <input type="hidden" name="_method" id="methodSpoof" value="">
            <div class="drawer-body">
                <div class="drawer-field">
                    <label for="drawer_number">Nomor Meja</label>
                    <input id="drawer_number" type="text" name="number" required>
                </div>
                <div class="drawer-field">
                    <label for="drawer_name">Nama Meja</label>
                    <input id="drawer_name" type="text" name="name" required>
                </div>
                <div class="drawer-field">
                    <label for="drawer_is_active">Status</label>
                    <label class="switch-row" for="drawer_is_active">
                        <input id="drawer_is_active" type="checkbox" name="is_active" value="1" checked>
                        <span class="switch-ui" aria-hidden="true"></span>
                        <span class="switch-text">Aktif</span>
                    </label>
                </div>
                <small class="form-error" id="drawerError"></small>
            </div>
            <div class="drawer-foot">
                <button type="button" class="btn-drawer-cancel" id="cancelDrawerBtn">Batal</button>
                <button type="submit" class="primary-link" id="submitDrawerBtn">Buat Meja</button>
            </div>
        </form>
    </aside>

    <div id="qrModalBackdrop" class="qr-modal-backdrop"></div>
    <section id="qrModal" class="qr-modal" aria-hidden="true">
        <div class="qr-modal-head">
            <strong>QR Meja</strong>
            <button type="button" class="drawer-close" id="closeQrModalBtn">Tutup</button>
        </div>
        <div class="qr-modal-body">
            <img id="qrPreviewImg" class="qr-modal-img" alt="QR Meja">
            <div class="qr-url" id="qrPreviewUrl"></div>
        </div>
    </section>

    <script>
        (function () {
            const drawer = document.getElementById('tableDrawer');
            const backdrop = document.getElementById('drawerBackdrop');
            const openCreateBtn = document.getElementById('openCreateDrawer');
            const closeBtn = document.getElementById('closeDrawerBtn');
            const cancelBtn = document.getElementById('cancelDrawerBtn');
            const title = document.getElementById('drawerTitle');
            const form = document.getElementById('drawerForm');
            const methodSpoof = document.getElementById('methodSpoof');
            const submitBtn = document.getElementById('submitDrawerBtn');
            const errorEl = document.getElementById('drawerError');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const qrModal = document.getElementById('qrModal');
            const qrBackdrop = document.getElementById('qrModalBackdrop');
            const closeQrBtn = document.getElementById('closeQrModalBtn');
            const qrPreviewImg = document.getElementById('qrPreviewImg');
            const qrPreviewUrl = document.getElementById('qrPreviewUrl');

            const openDrawer = () => {
                drawer.classList.add('open');
                backdrop.classList.add('open');
                drawer.setAttribute('aria-hidden', 'false');
            };
            const closeDrawer = () => {
                drawer.classList.remove('open');
                backdrop.classList.remove('open');
                drawer.setAttribute('aria-hidden', 'true');
            };
            const openQrModal = (url, qrUrl) => {
                qrPreviewUrl.textContent = url;
                qrPreviewImg.src = qrUrl || '';
                qrModal.classList.add('open');
                qrBackdrop.classList.add('open');
                qrModal.setAttribute('aria-hidden', 'false');
            };
            const closeQrModal = () => {
                qrModal.classList.remove('open');
                qrBackdrop.classList.remove('open');
                qrModal.setAttribute('aria-hidden', 'true');
            };

            const setCreateMode = () => {
                title.textContent = 'Tambah Meja';
                form.action = "{{ route('superadmin.tables.store') }}";
                methodSpoof.value = '';
                form.reset();
                document.getElementById('drawer_is_active').checked = true;
                submitBtn.textContent = 'Buat Meja';
                errorEl.textContent = '';
            };

            const setEditMode = (btn) => {
                const id = btn.getAttribute('data-id');
                title.textContent = 'Edit Meja';
                form.action = "{{ url('superadmin/tables') }}/" + id;
                methodSpoof.value = 'PUT';
                document.getElementById('drawer_number').value = btn.getAttribute('data-number') || '';
                document.getElementById('drawer_name').value = btn.getAttribute('data-name') || '';
                document.getElementById('drawer_is_active').checked = btn.getAttribute('data-active') === '1';
                submitBtn.textContent = 'Simpan Perubahan';
                errorEl.textContent = '';
            };

            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (m) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            }[m]));

            const cardMarkup = (payload) => `
                <div class="table-card-head">
                    <div>
                        <span class="table-pill">Meja ${escapeHtml(payload.number)}</span>
                        <h3>${escapeHtml(payload.name)}</h3>
                        <p style="margin:0;color:var(--text-muted);font-size:0.9rem;">Status: ${payload.is_active ? 'Aktif' : 'Nonaktif'}</p>
                        <p style="margin:0;color:var(--text-muted);font-size:0.9rem;">Total transaksi: ${payload.sales_count ?? 0}</p>
                    </div>
                </div>

                <div class="qr-box">
                    <img
                        class="qr-preview"
                        src="${escapeHtml(payload.qr_url)}"
                        alt="QR Meja ${escapeHtml(payload.number)}"
                        loading="lazy"
                    >
                    <small>Scan QR untuk membuka halaman pelanggan meja ini.</small>
                    <div class="qr-token-label">Token: ${escapeHtml(payload.qr_token)}</div>
                </div>

                <div class="table-actions">
                    <a class="secondary-link" href="${escapeHtml(payload.show_url)}" target="_blank" rel="noopener">Buka Halaman</a>
                    <button type="button" class="secondary-link btn-show-qr" data-show-url="${escapeHtml(payload.show_url)}" data-qr-url="${escapeHtml(payload.qr_url || '')}">Lihat QR</button>
                    <button
                        type="button"
                        class="secondary-link btn-open-edit"
                        data-id="${payload.id}"
                        data-number="${escapeHtml(payload.number)}"
                        data-name="${escapeHtml(payload.name)}"
                        data-active="${payload.is_active ? 1 : 0}"
                    >Edit</button>
                    <form method="POST" action="${escapeHtml(payload.delete_url)}" onsubmit="return confirm('Hapus meja ini?')">
                        <input type="hidden" name="_token" value="${escapeHtml(csrf)}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="danger-link">Hapus</button>
                    </form>
                </div>
            `;

            const bindEditButtons = (root = document) => {
                root.querySelectorAll('.btn-open-edit').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        setEditMode(btn);
                        openDrawer();
                    });
                });
                root.querySelectorAll('.btn-show-qr').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const url = btn.getAttribute('data-show-url');
                        const qrUrl = btn.getAttribute('data-qr-url');
                        if (url) openQrModal(url, qrUrl);
                    });
                });
            };

            openCreateBtn?.addEventListener('click', () => {
                setCreateMode();
                openDrawer();
            });

            [closeBtn, cancelBtn, backdrop].forEach((el) => el?.addEventListener('click', closeDrawer));
            [closeQrBtn, qrBackdrop].forEach((el) => el?.addEventListener('click', closeQrModal));
            bindEditButtons();

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                errorEl.textContent = '';
                submitBtn.disabled = true;

                const formData = new FormData(form);
                if (!document.getElementById('drawer_is_active').checked) {
                    formData.delete('is_active');
                }
                if (methodSpoof.value === 'PUT') formData.set('_method', 'PUT');

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: formData
                    });

                    const payload = await res.json();
                    if (!res.ok) {
                        const msg = payload.message || (payload.errors ? Object.values(payload.errors)[0][0] : 'Gagal menyimpan meja.');
                        throw new Error(msg);
                    }

                    const data = payload.table;
                    const grid = document.querySelector('.table-grid');
                    let card = grid.querySelector('[data-table-id="' + data.id + '"]');
                    if (!card) {
                        grid.querySelector('.table-empty')?.remove();
                        card = document.createElement('article');
                        card.className = 'table-card';
                        card.setAttribute('data-table-id', data.id);
                        grid.prepend(card);
                    }
                    card.innerHTML = cardMarkup(data);
                    bindEditButtons(card);
                    closeDrawer();
                    window.showToast?.(payload.message || 'Meja tersimpan.', 'success');
                } catch (err) {
                    errorEl.textContent = err.message || 'Terjadi kesalahan.';
                    window.showToast?.(err.message || 'Terjadi kesalahan.', 'error');
                } finally {
                    submitBtn.disabled = false;
                }
            });
        })();
    </script>
@endsection
