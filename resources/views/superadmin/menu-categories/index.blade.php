@extends('superadmin.layout')

@section('title', 'Kategori Menu')
@section('page_title', 'Kategori Menu')
@section('page_description', 'Tambah, edit, dan hapus kategori menu yang dipakai di form menu.')

@push('head')
    <style>
        .drawer-backdrop { position: fixed; inset: 0; background: rgba(56, 37, 30, 0.32); backdrop-filter: blur(2px); z-index: 1200; opacity: 0; visibility: hidden; transition: 0.2s ease; }
        .drawer-backdrop.open { opacity: 1; visibility: visible; }
        .category-drawer { position: fixed; top: 0; right: 0; width: min(520px, 95vw); height: 100vh; background: linear-gradient(180deg, #fffdfb 0%, #fff 100%); z-index: 1201; transform: translateX(102%); transition: transform 0.2s ease; box-shadow: -10px 0 34px rgba(79, 53, 43, 0.18); display: grid; grid-template-rows: auto 1fr auto; }
        .category-drawer.open { transform: translateX(0); }
        .drawer-head { padding: 1.15rem 1.25rem; border-bottom: 1px solid var(--accent); display:flex; justify-content:space-between; align-items:center; background: #fff; }
        .drawer-head h3 { margin: 0; font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.35rem; }
        .drawer-close { border: 1px solid var(--accent); background: #fff; color: var(--primary); border-radius: 12px; padding: 0.45rem 0.85rem; cursor: pointer; font-weight: 600; }
        .drawer-body { padding: 1rem 1.25rem 1.25rem; overflow-y: auto; }
        .drawer-field { display: flex; flex-direction: column; gap: 0.4rem; }
        .drawer-field label { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); }
        .drawer-field input { width: 100%; padding: 0.65rem 1rem; border: 1px solid var(--accent); border-radius: 12px; background: #fff; color: var(--text-main); font-size: 0.95rem; outline: none; }
        .drawer-field input:focus { border-color: var(--highlight); box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15); }
        .form-error { font-size: 0.82rem; color: var(--loss); margin-top: .5rem; display:block; }
        .drawer-foot { padding: 1rem 1.25rem; border-top: 1px solid var(--accent); display:flex; justify-content:flex-end; gap:0.7rem; background: #fff; }
        .btn-drawer-cancel { border: 1px solid var(--accent); background: #fff; color: var(--primary); border-radius: 12px; padding: 0.6rem 1rem; cursor:pointer; font-weight: 600; }
        .category-action-btn {
            border: 0;
            background: var(--highlight);
            color: #fff;
            border-radius: 12px;
            padding: 0.72rem 1.25rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
            transition: all 0.2s ease;
        }
        .category-action-btn:hover {
            background: #c68b59;
            transform: translateY(-2px);
        }
        .category-delete-btn {
            border: 1px solid #ffcdd2;
            background: transparent;
            color: var(--loss);
            border-radius: 12px;
            padding: 0.72rem 1.25rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .category-delete-btn:hover {
            background: #FFEBEE;
            border-color: var(--loss);
        }

        .category-pagination {
            margin-top: 1.25rem;
        }

        .pagination-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid var(--accent);
        }

        .pagination-meta {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .pagination-links {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .pagination-link,
        .pagination-dots {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 0.85rem;
            border-radius: 12px;
            border: 1px solid var(--accent);
            background: var(--bg-card);
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .pagination-link.active {
            background: var(--highlight);
            color: #fff;
            border-color: var(--highlight);
        }

        @media (max-width: 768px) {
            .content-toolbar { flex-direction: column; align-items: stretch; gap: 0.75rem; margin-bottom: 1.25rem; }
            .search-box { width: 100%; }
            .search-box input { min-width: auto; flex: 1; }
            .primary-link { width: 100%; justify-content: center; }
            
            .panel { padding: 1.25rem; }
            .panel-head { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
            .panel-head h2 { font-size: 1.5rem; }
            
            .table-wrap { margin: 0 -0.5rem; }
            table th, table td { padding: 0.75rem 0.5rem; font-size: 0.85rem; }
            
            .drawer-head h3 { font-size: 1.15rem; }
            .drawer-body { padding: 1rem; }
            .drawer-foot { padding: 1rem; }
            
            .pagination-wrap { flex-direction: column; align-items: center; text-align: center; }
        }
        .pagination-link.disabled {
            color: var(--text-muted);
            background: #f9f5f0;
            cursor: not-allowed;
        }

        .pagination-dots {
            border-color: transparent;
            background: transparent;
        }
    </style>
@endpush

@section('content')
    <div class="content-toolbar">
        <div></div>
        <div style="display:flex; gap:.65rem; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
            @if ($categories->total() > 0)
                <form method="POST" action="{{ route('superadmin.menu-categories.destroy-all') }}" onsubmit="return confirm('Hapus semua kategori? Tindakan ini tidak bisa dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="category-delete-btn">Hapus Semua Kategori</button>
                </form>
            @endif
            <button type="button" id="openCreateDrawer" class="category-action-btn">+ Tambah Kategori</button>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Kategori</h2>
            <span>{{ $categories->total() }} kategori</span>
        </div>

        <div class="category-list">
            @forelse ($categories as $category)
                <div class="category-row" data-category-id="{{ $category->id }}">
                    <div>
                        <strong>{{ $category->name }}</strong>
                        <div class="muted">{{ $category->menus_count }} menu</div>
                    </div>
                    <div class="actions">
                        <button type="button" class="btn-open-edit" data-id="{{ $category->id }}" data-name="{{ $category->name }}" style="border:none;background:transparent;color:#c68b59;font-weight:600;cursor:pointer;padding:0;">Edit</button>
                        <button type="button" class="btn-delete-category" data-id="{{ $category->id }}" style="border:none;background:transparent;color:#e57373;font-weight:600;cursor:pointer;padding:0;">Hapus</button>
                    </div>
                </div>
            @empty
                <div class="alert">Belum ada kategori.</div>
            @endforelse
        </div>

        @if ($categories->total() > 0)
            <div class="category-pagination">
                {{ $categories->links('components.pagination') }}
            </div>
        @endif
    </div>

    <div id="drawerBackdrop" class="drawer-backdrop"></div>
    <aside id="categoryDrawer" class="category-drawer" aria-hidden="true">
        <div class="drawer-head">
            <h3 id="drawerTitle">Tambah Kategori</h3>
            <button type="button" class="drawer-close" id="closeDrawerBtn">Tutup</button>
        </div>
        <form id="drawerForm" method="POST" action="{{ route('superadmin.menu-categories.store') }}">
            @csrf
            <input type="hidden" id="methodSpoof" name="_method" value="">
            <div class="drawer-body">
                <div class="drawer-field">
                    <label for="drawer_name">Nama Kategori</label>
                    <input id="drawer_name" type="text" name="name" required>
                </div>
                <small class="form-error" id="drawerError"></small>
            </div>
            <div class="drawer-foot">
                <button type="button" class="btn-drawer-cancel" id="cancelDrawerBtn">Batal</button>
                <button type="submit" class="category-action-btn" id="submitDrawerBtn">Buat Kategori</button>
            </div>
        </form>
    </aside>

    <script>
        (function () {
            const drawer = document.getElementById('categoryDrawer');
            const backdrop = document.getElementById('drawerBackdrop');
            const openBtn = document.getElementById('openCreateDrawer');
            const closeBtn = document.getElementById('closeDrawerBtn');
            const cancelBtn = document.getElementById('cancelDrawerBtn');
            const form = document.getElementById('drawerForm');
            const title = document.getElementById('drawerTitle');
            const methodSpoof = document.getElementById('methodSpoof');
            const nameInput = document.getElementById('drawer_name');
            const errorEl = document.getElementById('drawerError');
            const submitBtn = document.getElementById('submitDrawerBtn');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const list = document.querySelector('.category-list');
            const countEl = document.querySelector('.panel-head span');

            const openDrawer = () => { drawer.classList.add('open'); backdrop.classList.add('open'); drawer.setAttribute('aria-hidden', 'false'); };
            const closeDrawer = () => { drawer.classList.remove('open'); backdrop.classList.remove('open'); drawer.setAttribute('aria-hidden', 'true'); };
            const resetError = () => { errorEl.textContent = ''; };
            const setCreateMode = () => {
                title.textContent = 'Tambah Kategori';
                form.action = "{{ route('superadmin.menu-categories.store') }}";
                methodSpoof.value = '';
                nameInput.value = '';
                submitBtn.textContent = 'Buat Kategori';
                resetError();
            };
            const setEditMode = (btn) => {
                title.textContent = 'Edit Kategori';
                form.action = "{{ url('superadmin/menu-categories') }}/" + btn.getAttribute('data-id');
                methodSpoof.value = 'PUT';
                nameInput.value = btn.getAttribute('data-name') || '';
                submitBtn.textContent = 'Simpan Perubahan';
                resetError();
            };

            openBtn?.addEventListener('click', () => { setCreateMode(); openDrawer(); });
            [closeBtn, cancelBtn, backdrop].forEach((el) => el?.addEventListener('click', closeDrawer));

            const bindActions = (root) => {
                root.querySelectorAll('.btn-open-edit').forEach((btn) => {
                    btn.addEventListener('click', () => { setEditMode(btn); openDrawer(); });
                });
                root.querySelectorAll('.btn-delete-category').forEach((btn) => {
                    btn.addEventListener('click', async () => {
                        const id = btn.getAttribute('data-id');
                        if (!id || !confirm('Hapus kategori ini?')) return;
                        const res = await fetch("{{ url('superadmin/menu-categories') }}/" + id, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json'
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({ _method: 'DELETE' })
                        });
                        if (res.status === 419) {
                            window.showToast?.('Sesi habis, memuat ulang halaman...', 'error');
                            window.location.reload();
                            return;
                        }
                        const payload = await res.json();
                        if (!res.ok) return window.showToast?.(payload.message || 'Gagal menghapus kategori.', 'error');
                        list.querySelector('[data-category-id="' + id + '"]')?.remove();
                        if (!list.querySelector('[data-category-id]')) {
                            list.innerHTML = '<div class="alert">Belum ada kategori.</div>';
                        }
                        updateCount();
                        window.showToast?.(payload.message || 'Kategori berhasil dihapus.', 'success');
                    });
                });
            };
            bindActions(document);

            const updateCount = () => {
                const count = list.querySelectorAll('[data-category-id]').length;
                if (countEl) countEl.textContent = count + ' kategori';
            };

            const upsertCategory = (category) => {
                if (!category) return;
                let row = list.querySelector('[data-category-id="' + category.id + '"]');
                const html = `
                    <div>
                        <strong>${category.name}</strong>
                        <div class="muted">${category.menus_count ?? 0} menu</div>
                    </div>
                    <div class="actions">
                        <button type="button" class="btn-open-edit" data-id="${category.id}" data-name="${category.name}" style="border:none;background:transparent;color:#c68b59;font-weight:600;cursor:pointer;padding:0;">Edit</button>
                        <button type="button" class="btn-delete-category" data-id="${category.id}" style="border:none;background:transparent;color:#e57373;font-weight:600;cursor:pointer;padding:0;">Hapus</button>
                    </div>
                `;
                if (!row) {
                    list.querySelector('.alert')?.remove();
                    row = document.createElement('div');
                    row.className = 'category-row';
                    row.setAttribute('data-category-id', String(category.id));
                    row.innerHTML = html;
                    list.prepend(row);
                } else {
                    row.innerHTML = html;
                }
                bindActions(row);
                updateCount();
            };

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                resetError();
                submitBtn.disabled = true;
                const fd = new FormData(form);
                if (csrf) {
                    fd.set('_token', csrf);
                }
                if (methodSpoof.value === 'PUT') fd.set('_method', 'PUT');
                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        credentials: 'same-origin',
                        body: fd
                    });
                    if (res.status === 419) {
                        window.showToast?.('Sesi habis, memuat ulang halaman...', 'error');
                        window.location.reload();
                        return;
                    }
                    const payload = await res.json();
                    if (!res.ok) throw new Error(payload.message || (payload.errors ? Object.values(payload.errors)[0][0] : 'Gagal menyimpan kategori.'));
                    window.showToast?.(payload.message || 'Kategori tersimpan.', 'success');
                    closeDrawer();
                    upsertCategory(payload.category);
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
