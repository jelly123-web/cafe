@extends('superadmin.layout')

@section('title', 'Hak Akses')
@section('page_title', 'Hak Akses')
@section('page_description', 'Checklist hak akses tiap role dalam satu tabel. Semua perubahan disimpan ke database permission.')

@push('head')
    <style>
        /* CSS provided by user - embedded here */
        .summary-top {
            display: flex; justify-content: flex-end; align-items: center;
            margin-bottom: 20px; gap: 10px; flex-wrap: wrap;
        }
        .active-badge {
            display: inline-flex; align-items: center; gap: 6px;
            border: 1.5px solid var(--accent-light); background: var(--accent-light);
            color: var(--accent-dark); border-radius: var(--radius-full);
            padding: 6px 14px; font-weight: 700; font-size: 12px; letter-spacing: 0.3px;
        }
        .active-badge .badge-dot {
            width: 6px; height: 6px; border-radius: 50%; background: var(--accent);
            animation: dotPulse 2s infinite;
        }
        @keyframes dotPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Stats Grid */
        .stats-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 14px; margin-bottom: 24px;
        }
        .stat-card {
            background: var(--white); border: 1px solid var(--border);
            border-radius: var(--radius-md); padding: 18px 20px;
            display: flex; flex-direction: column; gap: 8px;
            transition: all 0.25s ease; position: relative; overflow: hidden;
        }
        .stat-card::after {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0;
            height: 3px; background: var(--card-accent, var(--accent));
            opacity: 0; transition: opacity var(--transition);
        }
        .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .stat-card:hover::after { opacity: 1; }
        .stat-card .card-icon {
            width: 38px; height: 38px; border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; margin-bottom: 2px;
        }
        .stat-card strong {
            font-size: 26px; font-weight: 900; color: var(--fg);
            letter-spacing: -0.5px; line-height: 1.1; font-variant-numeric: tabular-nums;
        }
        .stat-card span {
            font-size: 12px; color: var(--muted); font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.5px;
        }

        /* Panel & Table Styles */
        .access-layout { display: grid; grid-template-columns: 1fr; gap: 20px; align-items: start; }
        .panel-clean {
            background: var(--white); border: 1px solid var(--border);
            border-radius: var(--radius-lg); overflow: hidden;
        }
        .panel-clean .head {
            display: flex; justify-content: space-between; gap: 12px;
            align-items: center; border-bottom: 1px solid var(--border-light);
            padding: 18px 24px; flex-wrap: wrap;
        }
        .panel-clean h2 {
            margin: 0; font-size: 15px; font-weight: 800; color: var(--fg);
            letter-spacing: -0.2px; display: flex; align-items: center; gap: 8px;
        }
        .panel-clean h2 i { color: var(--accent); font-size: 16px; }
        .muted-note { color: var(--muted); font-size: 12px; margin-top: 2px; }

        /* Filter Toolbar */
        .filter-toolbar {
            display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;
            padding: 16px 24px; border-bottom: 1px solid var(--border-light);
        }
        .filter-field { display: flex; flex-direction: column; gap: 5px; }
        .filter-field label {
            font-size: 11px; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .filter-field input, .filter-field select {
            padding: 8px 12px; border: 1.5px solid var(--border);
            border-radius: var(--radius-sm); background: var(--white);
            font-size: 13px; font-weight: 500; color: var(--fg);
            outline: none; transition: all var(--transition);
        }
        
        /* Buttons */
        .sync-btn {
            border: 1.5px solid var(--border); background: var(--white);
            color: var(--fg-secondary); border-radius: var(--radius-sm);
            padding: 9px 18px; font-weight: 700; font-size: 13px;
            cursor: pointer; transition: all var(--transition);
            display: inline-flex; align-items: center; gap: 6px;
        }
        .sync-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
        .sync-btn.primary { background: var(--accent); border-color: var(--accent); color: white; }
        .sync-btn.primary:hover { background: var(--accent-dark); }
        .sync-btn.sm { padding: 6px 12px; font-size: 12px; }

        /* Permission Table */
        .table-wrap { overflow: auto; padding: 0; max-height: 65vh; }
        .perm-table { width: 100%; border-collapse: collapse; min-width: 100%; table-layout: fixed; }
        .perm-table th, .perm-table td {
            border-bottom: 1px solid var(--border-light); padding: 12px 16px;
            text-align: center; vertical-align: middle; font-size: 13px;
        }
        .perm-table th {
            font-size: 11px; text-transform: uppercase; color: var(--muted);
            background: var(--bg); font-weight: 700; letter-spacing: 0.7px;
            position: sticky; top: 0; z-index: 5;
        }
        .perm-table td:first-child, .perm-table th:first-child {
            width: 280px; min-width: 280px; text-align: left;
            position: sticky; left: 0; z-index: 3; background: var(--white);
        }
        .perm-table th:first-child { z-index: 6; background: var(--bg); }
        .perm-table th:not(:first-child), .perm-table td:not(:first-child) { width: 110px; }
        
        .perm-label { font-weight: 700; color: var(--fg); font-size: 13px; display: block; }
        .perm-key {
            color: var(--muted); font-size: 11px; display: block; margin-top: 2px;
            font-family: monospace;
        }
        .role-head { display: flex; flex-direction: column; gap: 3px; align-items: center; }
        .role-dot { width: 8px; height: 8px; border-radius: 50%; margin-bottom: 2px; }

        /* Custom Checkbox */
        .tick {
            -webkit-appearance: none; appearance: none;
            width: 22px; height: 22px; border: 2px solid var(--border);
            border-radius: 5px; cursor: pointer; position: relative;
        }
        .tick:checked { background: var(--accent); border-color: var(--accent); }
        .tick:checked::after {
            content: ''; position: absolute; left: 6px; top: 2px;
            width: 6px; height: 11px; border: solid white;
            border-width: 0 2.5px 2.5px 0; transform: rotate(45deg);
        }

        /* Form Actions */
        .form-actions {
            display: flex; justify-content: flex-end; gap: 10px;
            padding: 16px 24px; border-top: 1px solid var(--border-light);
            background: #FAFBFC; position: sticky; bottom: 0; z-index: 4;
        }
    </style>
@endpush

@section('content')
    @php
        $usersByRole = $users->groupBy('role');
        $roleMeta = collect($roleColumns ?? []);
        $rolesCount = $roleMeta->count();
        $permissionsCount = count($permissionDefinitions);
        $totalUsers = $users->count();
        $assignedCount = 0;
        foreach ($roleMeta as $role) {
            foreach (array_keys($permissionDefinitions) as $key) {
                if (data_get($role->permissions, $key, false)) {
                    $assignedCount++;
                }
            }
        }
    @endphp

    <div class="summary-top fade-in">
        <span class="active-badge"><span class="badge-dot"></span> {{ $rolesCount }} role aktif</span>
    </div>

    <section class="stats-grid">
        <article class="stat-card fade-in" style="--card-accent: var(--purple);">
            <div class="card-icon" style="background:var(--purple-light);color:var(--purple);"><i class="fas fa-user-shield"></i></div>
            <strong>{{ $rolesCount }}</strong>
            <span>Total Role</span>
        </article>
        <article class="stat-card fade-in" style="--card-accent: var(--blue);">
            <div class="card-icon" style="background:var(--blue-light);color:var(--blue);"><i class="fas fa-key"></i></div>
            <strong id="valPermCount">{{ $permissionsCount }}</strong>
            <span>Permission Tersedia</span>
        </article>
        <article class="stat-card fade-in" style="--card-accent: var(--teal);">
            <div class="card-icon" style="background:var(--teal-light);color:var(--teal);"><i class="fas fa-users"></i></div>
            <strong>{{ $totalUsers }}</strong>
            <span>User</span>
        </article>
        <article class="stat-card fade-in" style="--card-accent: var(--green);">
            <div class="card-icon" style="background:var(--green-light);color:var(--green);"><i class="fas fa-check-double"></i></div>
            <strong class="value-green" id="valAssigned">{{ $assignedCount }}</strong>
            <span>Permission Terpasang</span>
        </article>
    </section>

    <section class="access-layout">
        <article class="panel-clean fade-in">
            <div class="head">
                <div>
                    <h2><i class="fas fa-table-list"></i> Checklist Akses</h2>
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
                                            <span class="role-dot" style="background:var(--accent);"></span>
                                            <strong>{{ strtoupper($role->label) }}</strong>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="permBody">
                            @foreach ($permissionDefinitions as $key => $label)
                                <tr>
                                    <td>
                                        <span class="perm-label">{{ $label }}</span>
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
                    <button type="submit" class="sync-btn primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </article>
    </section>

    <script>
        (() => {
            const form = document.querySelector('[data-matrix-form]');
            const valAssigned = document.getElementById('valAssigned');
            
            const recalcStats = () => {
                let assigned = 0;
                document.querySelectorAll('.perm-table input[type="checkbox"]').forEach(input => {
                    if (input.disabled || input.checked) assigned += 1;
                });
                if (valAssigned) valAssigned.textContent = String(assigned);
            };

            if (form) {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const btn = form.querySelector('.sync-btn.primary');
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                    
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    
                    btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
                    if (response.ok) {
                        window.showToast('Hak akses berhasil disimpan.');
                        recalcStats();
                    } else {
                        window.showToast('Gagal menyimpan.', 'error');
                    }
                });
            }

            document.querySelectorAll('.tick').forEach(cb => cb.addEventListener('change', recalcStats));
        })();
    </script>
@endsection
