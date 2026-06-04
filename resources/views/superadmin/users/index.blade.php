@extends('superadmin.layout')

@push('head')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/superadmin/users.css'])
    @else
        <link rel="stylesheet" href="{{ asset('css/superadmin/users.css') }}">
    @endif
    <style>
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
        .drawer-backdrop.open { opacity: 1; visibility: visible; }
        .user-drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: min(520px, 95vw);
            height: 100vh;
            background: linear-gradient(180deg, #fffdfb 0%, #fff 100%);
            z-index: 1201;
            transform: translateX(102%);
            transition: transform 0.2s ease;
            box-shadow: -10px 0 34px rgba(79, 53, 43, 0.18);
            display: grid;
            grid-template-rows: auto 1fr auto;
        }
        .user-drawer.open { transform: translateX(0); }
        .drawer-head {
            padding: 1.15rem 1.25rem;
            border-bottom: 1px solid var(--accent);
            display:flex;
            justify-content:space-between;
            align-items:center;
            background: #fff;
            position: sticky;
            top: 0;
            z-index: 2;
        }
        .drawer-head h3 { margin: 0; font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.35rem; }
        .drawer-close {
            border: 1px solid var(--accent);
            background: #fff;
            color: var(--primary);
            border-radius: 12px;
            padding: 0.45rem 0.85rem;
            cursor: pointer;
            font-weight: 600;
        }
        .drawer-close:hover { background: #fffaf5; border-color: var(--highlight); color: var(--highlight); }
        .drawer-body { padding: 1rem 1.25rem 1.25rem; overflow-y: auto; }
        .user-form { display: flex; flex-direction: column; gap: 1.5rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .drawer-field { display: flex; flex-direction: column; gap: 0.4rem; }
        .drawer-field label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }
        .drawer-field input[type="text"],
        .drawer-field input[type="password"],
        .drawer-field select,
        .drawer-field input[type="file"] {
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
        .drawer-field input[type="text"]:focus,
        .drawer-field input[type="password"]:focus,
        .drawer-field select:focus,
        .drawer-field input[type="file"]:focus {
            border-color: var(--highlight);
            box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15);
        }
        .drawer-field select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23795548' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
            cursor: pointer;
        }
        .drawer-field input[type="file"] {
            padding: 0.54rem 0.7rem;
            background: #fffaf5;
        }
        .drawer-field input[type="file"][data-cropper-input] {
            display: none;
        }
        .drawer-field input[type="file"]::file-selector-button {
            border: 1px solid var(--accent);
            border-radius: 9px;
            padding: 0.4rem 0.7rem;
            margin-right: 0.55rem;
            background: #fff;
            color: var(--primary);
            cursor: pointer;
            font-weight: 600;
        }
        .photo-picker {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }
        .photo-picker-btn {
            border: 1px solid var(--accent);
            border-radius: 12px;
            padding: 0.65rem 1rem;
            background: #fffaf5;
            color: var(--primary);
            cursor: pointer;
            font-weight: 700;
        }
        .photo-name {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-style: italic;
        }
        .cropper-box {
            position: relative;
            width: 116px;
            margin-top: 0.35rem;
        }
        .cropper-box img {
            width: 116px;
            height: 116px;
            object-fit: cover;
            border: 1px dashed var(--accent);
            border-radius: 16px;
            background: #fffaf5;
            cursor: pointer;
        }
        .photo-clear {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
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
            z-index: 1700;
            display: grid;
            place-items: center;
            background: rgba(56, 37, 30, 0.34);
            backdrop-filter: blur(3px);
        }
        .cropper-modal[hidden] { display: none !important; }
        .cropper-dialog {
            width: min(540px, calc(100vw - 2rem));
            background: #fff;
            border: 1px solid var(--accent);
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(62,39,35,.18);
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
        .cropper-foot { border-bottom: 0; border-top: 1px solid var(--accent); justify-content: flex-end; }
        .cropper-head strong { color: var(--primary); font-family: 'Playfair Display', Georgia, serif; font-size: 1.2rem; }
        .cropper-body {
            display: grid;
            place-items: center;
            gap: 0.85rem;
            padding: 1.15rem;
        }
        .cropper-canvas {
            width: min(340px, 78vw);
            height: min(340px, 78vw);
            border: 1px dashed var(--accent);
            border-radius: 18px;
            background: #fffaf5;
            cursor: move;
            touch-action: none;
        }
        .cropper-control {
            display: grid;
            gap: 0.35rem;
            width: min(340px, 78vw);
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
        .cropper-done { background: var(--highlight); color: #fff; border-color: var(--highlight); }
        .drawer-field.switch-row {
            flex-direction: row;
            align-items: center;
            gap: 0.7rem;
            margin-top: 0.25rem;
        }
        .drawer-field.switch-row .switch-label-wrap {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            cursor: pointer;
        }
        .drawer-field.switch-row input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            pointer-events: none;
        }
        .drawer-field.switch-row .switch-ui {
            position: relative;
            width: 46px;
            height: 26px;
            border-radius: 999px;
            background: var(--accent);
            transition: background-color .25s ease;
            flex-shrink: 0;
        }
        .drawer-field.switch-row .switch-ui::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,.12);
            transition: transform .25s ease;
        }
        .drawer-field.switch-row input[type="checkbox"]:checked + .switch-ui {
            background: var(--highlight);
        }
        .drawer-field.switch-row input[type="checkbox"]:checked + .switch-ui::after {
            transform: translateX(20px);
        }
        .drawer-field.switch-row .switch-text {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1;
        }
        .drawer-foot {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--accent);
            display:flex;
            justify-content:flex-end;
            gap:0.7rem;
            background: #fff;
            position: sticky;
            bottom: 0;
        }
        .btn-drawer-cancel {
            border: 1px solid var(--accent);
            background: #fff;
            color: var(--primary);
            border-radius: 12px;
            padding: 0.6rem 1rem;
            cursor:pointer;
            font-weight: 600;
        }
        .btn-drawer-cancel:hover { background: #fffaf5; border-color: var(--highlight); color: var(--highlight); }
        .drawer-foot .primary-link { border: 0; }
        .drawer-foot .primary-link:hover { transform: translateY(-1px); }

        .user-drawer .drawer-head {
            padding: 1.25rem 1.4rem;
        }

        .user-drawer .drawer-body {
            padding: 1.2rem 1.4rem 1.4rem;
        }

        .user-drawer .user-form {
            gap: 1.25rem;
        }

        .user-drawer .form-grid {
            gap: 1.4rem 1.2rem;
        }

        .user-drawer .drawer-field {
            gap: 0.55rem;
        }

        .user-drawer .drawer-field input[type="text"],
        .user-drawer .drawer-field input[type="password"],
        .user-drawer .drawer-field select {
            min-height: 48px;
        }

        .user-drawer .drawer-foot {
            padding: 1.15rem 1.4rem;
            gap: 0.9rem;
            background: #fff;
        }

        .user-drawer .btn-drawer-cancel,
        .user-drawer .primary-link {
            min-height: 46px;
            min-width: 104px;
            padding-left: 1.15rem;
            padding-right: 1.15rem;
        }

        .user-drawer .drawer-close {
            min-height: 40px;
            padding: 0.5rem 0.95rem;
            border-radius: 14px;
            background: linear-gradient(180deg, #fff 0%, #fffaf6 100%);
            box-shadow: 0 2px 8px rgba(121, 85, 72, 0.08);
        }

        .user-drawer .photo-picker-btn {
            min-height: 46px;
            padding: 0.72rem 1.05rem;
            border-radius: 14px;
            background: linear-gradient(180deg, #fff 0%, #fff7f0 100%);
            border: 1px solid var(--accent);
            box-shadow: 0 2px 8px rgba(121, 85, 72, 0.06);
        }

        .user-drawer .drawer-field input[type="file"] {
            min-height: 48px;
            border-radius: 14px;
        }

        .user-drawer .switch-ui {
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.03);
        }

        .user-drawer .drawer-foot .primary-link {
            background: linear-gradient(180deg, #e2b68c 0%, #d4a373 100%);
            box-shadow: 0 6px 14px rgba(212, 163, 115, 0.22);
        }

        .user-drawer .btn-drawer-cancel {
            background: linear-gradient(180deg, #fff 0%, #fffaf6 100%);
            box-shadow: 0 2px 8px rgba(121, 85, 72, 0.06);
        }

        .user-drawer .btn-open-edit {
            padding: 0.4rem 0.8rem;
            border-radius: 999px;
            background: #fff8f1;
            border: 1px solid rgba(198, 139, 89, 0.18);
            box-shadow: 0 2px 6px rgba(121, 85, 72, 0.04);
        }

        .user-drawer .btn-open-edit:hover {
            text-decoration: none;
            background: #fff1e6;
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
            
            .form-grid { grid-template-columns: 1fr; }
            .drawer-head h3 { font-size: 1.15rem; }
            .drawer-body { padding: 1rem; }
            .drawer-foot { padding: 1rem; }
        }
        .pagination-area { margin-top: 1.25rem; }
    </style>
@endpush

@section('title', 'Akun Pengguna')
@section('page_title', 'Akun Pengguna')
@section('page_description', 'Tambah, edit, dan hapus akun pengguna.')

@section('content')
    <div class="content-toolbar">
        <form method="GET" action="{{ route('superadmin.users.index') }}" class="search-box" id="usersSearchForm">
            <input type="text" name="search" id="usersSearchInput" placeholder="Cari nama atau username" value="{{ request('search') }}">
            <button type="submit">Cari</button>
        </form>

        <button type="button" id="openCreateDrawer" class="primary-link">+ Tambah Akun</button>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Akun</h2>
            <span id="userCount">{{ $users->total() }} akun</span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersTbody">
                    @foreach ($users as $row)
                        <tr data-user-id="{{ $row->id }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <img src="{{ $row->profile_photo_url }}" alt="{{ $row->name }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid var(--accent);">
                                    <strong>{{ $row->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $row->username }}</td>
                            <td><span class="tag">{{ $row->roleLabel() }}</span></td>
                            <td>
                                <span class="tag {{ $row->is_active ? 'tag-success' : 'tag-muted' }}">
                                    {{ $row->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <button
                                        type="button"
                                        class="btn-open-edit"
                                        data-id="{{ $row->id }}"
                                        data-name="{{ $row->name }}"
                                        data-username="{{ $row->username }}"
                                        data-role="{{ $row->role }}"
                                        data-is-active="{{ $row->is_active ? 1 : 0 }}"
                                        style="border:none;background:transparent;color:#c68b59;font-weight:600;cursor:pointer;padding:0;"
                                    >Edit</button>
                                    @if (auth()->id() !== $row->id && $row->role !== 'superadmin')
                                        <button type="button" class="btn-delete-user" data-id="{{ $row->id }}">Hapus</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-area" id="usersPagination">{{ $users->links('components.pagination') }}</div>
    </div>

    <div id="drawerBackdrop" class="drawer-backdrop"></div>
    <aside id="userDrawer" class="user-drawer" aria-hidden="true">
        <div class="drawer-head">
            <h3 id="drawerTitle">Tambah Akun</h3>
            <button type="button" class="drawer-close" id="closeDrawerBtn">Tutup</button>
        </div>
        <form id="drawerForm" method="POST" action="{{ route('superadmin.users.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="methodSpoof" name="_method" value="">
            <div class="drawer-body">
                <div class="user-form">
                    <div class="form-grid">
                        <div class="drawer-field">
                            <label for="drawer_name">Nama</label>
                            <input id="drawer_name" type="text" name="name" required>
                        </div>
                        <div class="drawer-field">
                            <label for="drawer_username">Username</label>
                            <input id="drawer_username" type="text" name="username" required maxlength="30">
                        </div>
                        <div class="drawer-field">
                            <label for="drawer_role">Role</label>
                            <select id="drawer_role" name="role" required>
                                @foreach (['superadmin' => 'Superadmin', 'admin' => 'Admin', 'kasir' => 'Kasir', 'leader_cashier' => 'Leader Kasir', 'kitchen' => 'Dapur', 'inventory' => 'Gudang'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="drawer-field">
                            <label for="drawer_password">Password <span id="passwordHint">(wajib)</span></label>
                            <input id="drawer_password" type="password" name="password">
                        </div>
                        <div class="drawer-field" data-cropper data-cropper-size="520">
                            <label for="drawer_photo">Foto Profil (opsional)</label>
                            <div class="photo-picker">
                                <input id="drawer_photo" type="file" name="profile_photo" accept="image/*" data-cropper-input>
                                <input type="hidden" name="cropped_profile_photo" id="drawer_cropped_photo" data-cropper-output>
                                <label for="drawer_photo" class="photo-picker-btn">Pilih Foto</label>
                                <span class="photo-name" id="drawerPhotoName" data-cropper-filename>Belum ada file dipilih</span>
                            </div>
                            <div class="cropper-box" data-cropper-preview-wrap hidden>
                                <button type="button" class="photo-clear" data-cropper-clear aria-label="Batal pilih foto">x</button>
                                <img src="" alt="Preview foto profil" data-cropper-preview title="Klik untuk crop ulang">
                            </div>
                            <div class="cropper-modal" data-cropper-panel hidden>
                                <div class="cropper-dialog">
                                    <div class="cropper-head">
                                        <strong>Atur Crop Foto</strong>
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
                        <div class="drawer-field switch-row">
                            <label for="drawer_active" class="switch-label-wrap">
                                <input id="drawer_active" type="checkbox" name="is_active" value="1" checked>
                                <span class="switch-ui" aria-hidden="true"></span>
                                <span class="switch-text">Akun aktif</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="drawer-foot">
                <button type="button" class="btn-drawer-cancel" id="cancelDrawerBtn">Batal</button>
                <button type="submit" class="primary-link" id="submitDrawerBtn">Simpan</button>
            </div>
        </form>
    </aside>

    <script src="{{ asset('js/cafe-image-cropper.js') }}?v=4"></script>
    <script>
        (function () {
            const drawer = document.getElementById('userDrawer');
            const backdrop = document.getElementById('drawerBackdrop');
            const openCreateBtn = document.getElementById('openCreateDrawer');
            const closeBtn = document.getElementById('closeDrawerBtn');
            const cancelBtn = document.getElementById('cancelDrawerBtn');
            const title = document.getElementById('drawerTitle');
            const form = document.getElementById('drawerForm');
            const methodSpoof = document.getElementById('methodSpoof');
            const nameInput = document.getElementById('drawer_name');
            const usernameInput = document.getElementById('drawer_username');
            const roleInput = document.getElementById('drawer_role');
            const activeInput = document.getElementById('drawer_active');
            const passwordInput = document.getElementById('drawer_password');
            const passwordHint = document.getElementById('passwordHint');
            const photoInput = document.getElementById('drawer_photo');
            const croppedPhotoInput = document.getElementById('drawer_cropped_photo');
            const photoName = document.getElementById('drawerPhotoName');
            const cropperPanel = document.querySelector('#drawer_photo')?.closest('[data-cropper]')?.querySelector('[data-cropper-panel]');
            const cropperPreviewWrap = document.querySelector('#drawer_photo')?.closest('[data-cropper]')?.querySelector('[data-cropper-preview-wrap]');
            const submitBtn = document.getElementById('submitDrawerBtn');
            const tbody = document.getElementById('usersTbody');
            const userCount = document.getElementById('userCount');
            const usersPagination = document.getElementById('usersPagination');
            const searchForm = document.getElementById('usersSearchForm');
            const searchInput = document.getElementById('usersSearchInput');
            const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const currentUserId = {{ (int) auth()->id() }};

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

            const esc = (str) => String(str ?? '').replace(/[&<>"']/g, function (m) {
                return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[m];
            });

            const updateCount = () => {
                if (!userCount || !tbody) return;
                userCount.textContent = tbody.querySelectorAll('tr[data-user-id]').length + ' akun';
            };

            const renderRows = (users) => {
                if (!tbody) return;
                tbody.innerHTML = '';
                users.forEach((user) => {
                    const tr = document.createElement('tr');
                    tr.setAttribute('data-user-id', String(user.id));
                    tbody.appendChild(tr);
                    upsertRow(user);
                });
                if (!users.length) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = '<td colspan="5" style="color:var(--text-muted);">Tidak ada akun ditemukan.</td>';
                    tbody.appendChild(tr);
                }
                updateCount();
            };

            const setTotalCount = (total) => {
                if (!userCount) return;
                userCount.textContent = Number(total || 0) + ' akun';
            };

            const upsertRow = (user) => {
                if (!tbody || !user) return;
                let tr = tbody.querySelector('tr[data-user-id="' + user.id + '"]');
                const canDelete = Number(user.id) !== Number(currentUserId) && !!user.can_delete;
                const html = `
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <img src="${esc(user.profile_photo_url)}" alt="${esc(user.name)}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid var(--accent);">
                            <strong>${esc(user.name)}</strong>
                        </div>
                    </td>
                    <td>${esc(user.username)}</td>
                    <td><span class="tag">${esc(user.role_label)}</span></td>
                    <td><span class="tag ${user.is_active ? 'tag-success' : 'tag-muted'}">${user.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
                    <td>
                        <div class="actions">
                            <button
                                type="button"
                                class="btn-open-edit"
                                data-id="${user.id}"
                                data-name="${esc(user.name)}"
                                data-username="${esc(user.username)}"
                                data-role="${esc(user.role)}"
                                data-is-active="${user.is_active ? 1 : 0}"
                                style="border:none;background:transparent;color:#c68b59;font-weight:600;cursor:pointer;padding:0;"
                            >Edit</button>
                            ${canDelete ? `<button type="button" class="btn-delete-user" data-id="${user.id}">Hapus</button>` : ''}
                        </div>
                    </td>
                `;
                if (tr) {
                    tr.innerHTML = html;
                } else {
                    tr = document.createElement('tr');
                    tr.setAttribute('data-user-id', String(user.id));
                    tr.innerHTML = html;
                    tbody.prepend(tr);
                }
                bindRowActions(tr);
                updateCount();
            };

            const removeRow = (id) => {
                const tr = tbody?.querySelector('tr[data-user-id="' + id + '"]');
                if (tr) tr.remove();
                updateCount();
            };

            const setCreateMode = () => {
                title.textContent = 'Tambah Akun';
                form.action = "{{ route('superadmin.users.store') }}";
                methodSpoof.value = '';
                nameInput.value = '';
                usernameInput.value = '';
                roleInput.value = 'kasir';
                activeInput.checked = true;
                passwordInput.value = '';
                passwordInput.required = true;
                passwordHint.textContent = '(wajib)';
                if (photoInput) photoInput.value = '';
                if (croppedPhotoInput) croppedPhotoInput.value = '';
                if (photoName) photoName.textContent = 'Belum ada file dipilih';
                if (cropperPanel) cropperPanel.hidden = true;
                if (cropperPreviewWrap) cropperPreviewWrap.hidden = true;
                submitBtn.textContent = 'Buat Akun';
            };

            const setEditMode = (btn) => {
                title.textContent = 'Edit Akun';
                const id = btn.getAttribute('data-id');
                form.action = "{{ url('superadmin/users') }}/" + id;
                methodSpoof.value = 'PUT';
                nameInput.value = btn.getAttribute('data-name') || '';
                usernameInput.value = btn.getAttribute('data-username') || '';
                roleInput.value = btn.getAttribute('data-role') || 'kasir';
                activeInput.checked = (btn.getAttribute('data-is-active') === '1');
                passwordInput.value = '';
                passwordInput.required = false;
                passwordHint.textContent = '(kosongkan jika tidak diubah)';
                if (photoInput) photoInput.value = '';
                if (croppedPhotoInput) croppedPhotoInput.value = '';
                if (photoName) photoName.textContent = 'Kosongkan jika tidak ganti foto';
                if (cropperPanel) cropperPanel.hidden = true;
                if (cropperPreviewWrap) cropperPreviewWrap.hidden = true;
                submitBtn.textContent = 'Simpan Perubahan';
            };

            openCreateBtn.addEventListener('click', function () {
                setCreateMode();
                openDrawer();
            });

            const bindRowActions = (root) => {
                root.querySelectorAll('.btn-open-edit').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        setEditMode(btn);
                        openDrawer();
                    });
                });

                root.querySelectorAll('.btn-delete-user').forEach(function (btn) {
                    btn.addEventListener('click', async function () {
                        const id = btn.getAttribute('data-id');
                        if (!id) return;
                        if (!confirm('Hapus akun ini?')) return;
                        try {
                            const res = await fetch("{{ url('superadmin/users') }}/" + id, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': getCsrfToken(),
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ _method: 'DELETE' })
                            });
                            const payload = await res.json();
                            if (!res.ok) throw new Error(payload.message || 'Gagal menghapus akun.');
                            removeRow(id);
                            window.showToast?.(payload.message || 'Akun berhasil dihapus.', 'success');
                        } catch (err) {
                            window.showToast?.(err.message || 'Terjadi kesalahan.', 'error', 4200);
                        }
                    });
                });
            };

            bindRowActions(document);

            let searchDebounce;
            const runLiveSearch = async () => {
                const q = (searchInput?.value || '').trim();
                const url = new URL("{{ route('superadmin.users.index') }}", window.location.origin);
                if (q) url.searchParams.set('search', q);
                try {
                    const res = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const payload = await res.json();
                    if (!res.ok) throw new Error(payload.message || 'Gagal mengambil data user.');
                    renderRows(payload.users || []);
                    setTotalCount(payload.total || 0);
                    if (usersPagination && typeof payload.pagination === 'string') {
                        usersPagination.innerHTML = payload.pagination;
                    }
                    const nextUrl = q ? ("{{ route('superadmin.users.index') }}" + '?search=' + encodeURIComponent(q)) : "{{ route('superadmin.users.index') }}";
                    window.history.replaceState({}, '', nextUrl);
                } catch (err) {
                    window.showToast?.(err.message || 'Gagal mencari akun.', 'error', 3500);
                }
            };

            searchForm?.addEventListener('submit', function (e) {
                e.preventDefault();
                runLiveSearch();
            });

            searchInput?.addEventListener('input', function () {
                clearTimeout(searchDebounce);
                searchDebounce = setTimeout(runLiveSearch, 280);
            });

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const isEdit = methodSpoof.value === 'PUT';
                const isCreate = !isEdit;
                const formData = new FormData(form);
                const targetUrl = form.action;
                const method = isEdit ? 'POST' : 'POST';
                if (isEdit) formData.set('_method', 'PUT');

                try {
                    submitBtn.disabled = true;
                    const res = await fetch(targetUrl, {
                        method,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: formData
                    });

                    if (res.status === 419) {
                        window.showToast?.('Sesi telah berakhir, silakan refresh halaman.', 'error');
                        return;
                    }

                    const text = await res.text();
                    let payload = {};
                    try { payload = text ? JSON.parse(text) : {}; } catch (_) {}
                    if (!res.ok) {
                        const msg = payload.message || (payload.errors ? Object.values(payload.errors)[0][0] : 'Gagal menyimpan data.');
                        throw new Error(msg);
                    }
                    const currentQuery = (searchInput?.value || '').trim().toLowerCase();
                    const userName = String(payload.user?.name || '').toLowerCase();
                    const userUsername = String(payload.user?.username || '').toLowerCase();
                    const matchQuery = !currentQuery || userName.includes(currentQuery) || userUsername.includes(currentQuery);

                    if (isCreate && currentQuery && !matchQuery) {
                        searchInput.value = '';
                        await runLiveSearch();
                    } else {
                        upsertRow(payload.user);
                    }
                    closeDrawer();
                    window.showToast?.(payload.message || 'Berhasil disimpan.', 'success');
                } catch (err) {
                    window.showToast?.(err.message || 'Terjadi kesalahan.', 'error', 4500);
                } finally {
                    submitBtn.disabled = false;
                }
            });

            [closeBtn, cancelBtn, backdrop].forEach(function (el) {
                el.addEventListener('click', closeDrawer);
            });
        })();
    </script>
@endsection
