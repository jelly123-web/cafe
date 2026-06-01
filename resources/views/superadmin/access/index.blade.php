@extends('superadmin.layout')

@push('head')
    <style>
        .summary-top {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 1.2rem;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .active-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border: 1px solid #ffe0b2;
            background: #fff8ef;
            color: #c67f2f;
            border-radius: 999px;
            padding: 0.45rem 0.85rem;
            font-weight: 700;
            font-size: 0.85rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.2rem;
        }
        .stat-card {
            background: #fff;
            border: 1px solid var(--accent);
            border-radius: 16px;
            padding: 1rem;
            box-shadow: 0 4px 15px var(--shadow);
        }
        .stat-card strong {
            display: block;
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            font-size: 2rem;
            line-height: 1;
            margin-bottom: 0.25rem;
        }
        .stat-card span { color: var(--text-muted); font-size: 0.9rem; }
        .access-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 1rem;
            align-items: start;
        }
        .panel-clean {
            background: #fff;
            border: 1px solid var(--accent);
            border-radius: 18px;
            box-shadow: 0 4px 15px var(--shadow);
            overflow: hidden;
        }
        .panel-clean .head {
            display: flex;
            justify-content: space-between;
            gap: 0.9rem;
            align-items: center;
            border-bottom: 1px solid var(--accent);
            padding: 1rem 1.15rem;
        }
        .panel-clean h2 {
            margin: 0;
            color: var(--primary);
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2rem;
        }
        .muted-note { color: var(--text-muted); font-size: 0.93rem; margin-top: 0.2rem; }
        .sync-btn {
            border: 1px solid #ffd8a8;
            background: #fff8ef;
            color: #c67f2f;
            border-radius: 12px;
            padding: 0.58rem 0.95rem;
            font-weight: 700;
            cursor: pointer;
        }
        .sync-btn.primary {
            background: var(--highlight);
            border-color: var(--highlight);
            color: #fff;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            padding: 0.9rem;
            border-top: 1px solid var(--accent);
            background: #fff;
            position: sticky;
            bottom: 0;
            z-index: 2;
        }
        .table-wrap { overflow: auto; padding: 0.9rem; }
        .perm-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 100%;
            table-layout: fixed;
        }
        .perm-table th,
        .perm-table td {
            border-bottom: 1px solid var(--accent);
            padding: 0.8rem 0.55rem;
            text-align: center;
            vertical-align: middle;
        }
        .perm-table th {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: var(--text-muted);
            background: #faf6f1;
            font-weight: 700;
            letter-spacing: 0.4px;
        }
        .perm-table th:first-child,
        .perm-table td:first-child {
            width: 290px;
            min-width: 290px;
        }
        .perm-table th:not(:first-child),
        .perm-table td:not(:first-child) {
            width: 120px;
        }
        .perm-table td:first-child,
        .perm-table th:first-child {
            text-align: left;
            position: sticky;
            left: 0;
            background: #fff;
            z-index: 3;
            box-shadow: 8px 0 0 #fff;
        }
        .perm-key { color: var(--text-muted); font-size: 0.78rem; display: block; margin-top: 0.15rem; }
        .role-head { display: grid; gap: 0.12rem; justify-items: center; line-height: 1.15; }
        .role-head strong { color: var(--primary); font-size: 0.84rem; white-space: normal; word-break: break-word; }
        .role-head small { color: var(--text-muted); font-size: 0.68rem; white-space: normal; word-break: break-word; }
        .tick {
            width: 22px;
            height: 22px;
            accent-color: #f08d23;
            cursor: pointer;
        }
        .toast {
            position: fixed;
            right: 1rem;
            top: 1rem;
            z-index: 9999;
            background: #1f2937;
            color: #fff;
            border-radius: 14px;
            padding: 0.85rem 1rem;
            box-shadow: 0 14px 32px rgba(0, 0, 0, 0.2);
            transform: translateY(-12px);
            opacity: 0;
            pointer-events: none;
            transition: all 0.22s ease;
            max-width: min(92vw, 420px);
            font-size: 0.92rem;
        }
        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }
        .toast.success { background: #2e7d32; }
        .toast.error { background: #c62828; }
        .panel-side { padding: 1rem 1.15rem; }
        .side-box {
            border: 1px solid var(--accent);
            border-radius: 12px;
            padding: 0.85rem;
            margin-bottom: 0.75rem;
            background: #fff;
            display: flex;
            justify-content: space-between;
            gap: 0.6rem;
            align-items: center;
        }
        .side-box strong { color: var(--primary); display: block; }
        .side-box p { margin: 0; color: var(--text-muted); font-size: 0.86rem; }
        .side-box .num { font-size: 2rem; color: #f08d23; font-weight: 800; line-height: 1; }
        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('title', 'Hak Akses')
@section('page_title', 'Table Access')
@section('page_description', 'Checklist hak akses tiap role dalam satu tabel. Semua perubahan disimpan ke database permission.')

@section('content')
    @php
        $usersByRole = $users->groupBy('role');
        $roleMeta = collect($roleColumns ?? []);
        $rolesCount = $roleMeta->count();
        $permissionsCount = count($permissionDefinitions);
        $totalUsers = $users->count();
        $assignedCount = 0;
        foreach ($roleMeta as $role) {
            if ($role->role === 'superadmin') {
                $assignedCount += $permissionsCount;
            } else {
                foreach (array_keys($permissionDefinitions) as $key) {
                    if (data_get($role->permissions, $key, false)) {
                        $assignedCount++;
                    }
                }
            }
        }
        $adminLike = $roleMeta->filter(function ($role) {
            return in_array((string) $role->role, ['superadmin', 'admin', 'leader_cashier'], true)
                || data_get($role->permissions, 'superadmin_users')
                || data_get($role->permissions, 'superadmin_menus')
                || data_get($role->permissions, 'superadmin_reports');
        })->count();
    @endphp

    <div class="summary-top">
        <span class="active-badge">{{ $rolesCount }} role aktif</span>
    </div>

    <section class="stats-grid">
        <article class="stat-card"><strong>{{ $rolesCount }}</strong><span>Total Role</span></article>
        <article class="stat-card"><strong>{{ $permissionsCount }}</strong><span>Permission Tersedia</span></article>
        <article class="stat-card"><strong>{{ $totalUsers }}</strong><span>User Dalam Semua Role</span></article>
        <article class="stat-card"><strong>{{ $assignedCount }}</strong><span>Permission Terpasang</span></article>
    </section>

    <section class="access-layout">
        <article class="panel-clean">
            <div class="head">
                <div>
                    <h2>Checklist Akses</h2>
                    <div class="muted-note">Centang permission yang boleh dilakukan role terkait.</div>
                </div>
            </div>
            <form id="matrixForm" method="POST" action="{{ route('superadmin.access.matrix.update') }}" data-matrix-form>
                @csrf
                <div class="table-wrap">
                    <table class="perm-table">
                        <thead>
                            <tr>
                                <th>Permission</th>
                                @foreach ($roleMeta as $role)
                                    <th>
                                        <div class="role-head">
                                            <strong>{{ strtoupper($role->label) }}</strong>
                                            <small>
                                                {{ strtoupper(match ($role->role) {
                                                    'staff' => 'kasir',
                                                    'leader_cashier' => 'leader kasir',
                                                    'inventory' => 'gudang',
                                                    'kitchen' => 'dapur',
                                                    default => str_replace('_', ' ', $role->role),
                                                }) }}
                                            </small>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissionDefinitions as $key => $label)
                                <tr>
                                    <td>
                                        <strong>{{ $label }}</strong>
                                        <span class="perm-key">{{ $key }}</span>
                                    </td>
                                    @foreach ($roleMeta as $role)
                                        <td>
                                            @if ($role->role === 'superadmin')
                                                <input class="tick" type="checkbox" checked disabled>
                                            @else
                                                <input class="tick" type="checkbox" name="permissions[{{ $role->role }}][{{ $key }}]" value="1" @checked(data_get($role->permissions, $key, false))>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="form-actions">
                    <button type="submit" class="sync-btn primary">Save Perubahan</button>
                </div>
            </form>
        </article>

    </section>
    <div class="toast" id="accessToast" aria-live="polite" aria-atomic="true"></div>

    <script>
        (() => {
            const form = document.querySelector('[data-matrix-form]');
            const toast = document.getElementById('accessToast');
            const stats = {
                roles: document.querySelector('.stats-grid .stat-card:nth-child(1) strong'),
                permissions: document.querySelector('.stats-grid .stat-card:nth-child(2) strong'),
                users: document.querySelector('.stats-grid .stat-card:nth-child(3) strong'),
                assigned: document.querySelector('.stats-grid .stat-card:nth-child(4) strong'),
            };
            const roleHeaders = Array.from(document.querySelectorAll('.perm-table thead th')).slice(1);
            const permissionRows = Array.from(document.querySelectorAll('.perm-table tbody tr'));

            const showToast = (message, type = 'success') => {
                if (!toast) return;
                toast.textContent = message;
                toast.className = `toast ${type} show`;
                window.clearTimeout(window.__accessToastTimer);
                window.__accessToastTimer = window.setTimeout(() => {
                    toast.classList.remove('show');
                }, 2400);
            };

            const recalcAssigned = () => {
                let assigned = 0;
                permissionRows.forEach((row) => {
                    const checks = row.querySelectorAll('input[type="checkbox"]');
                    checks.forEach((input) => {
                        if (input.disabled) {
                            assigned += 1;
                            return;
                        }
                        if (input.checked) assigned += 1;
                    });
                });
                if (stats.assigned) stats.assigned.textContent = String(assigned);
            };

            const recalcRoles = () => {
                if (stats.roles) stats.roles.textContent = String(roleHeaders.length);
            };

            if (form) {
                form.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    const payload = new FormData(form);
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: payload,
                        });
                        const data = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal menyimpan hak akses.');
                        }
                        showToast(data.message || 'Hak akses berhasil disimpan.', 'success');
                        recalcAssigned();
                        recalcRoles();
                    } catch (err) {
                        showToast(err.message || 'Gagal menyimpan hak akses.', 'error');
                    }
                });
            }

            recalcAssigned();
            recalcRoles();
        })();
    </script>
@endsection
