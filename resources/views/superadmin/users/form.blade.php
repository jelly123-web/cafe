@php
    $isEdit = isset($mode) && $mode === 'edit';
@endphp

<div class="panel">
    <form method="POST" action="{{ $isEdit ? route('superadmin.users.update', $user) : route('superadmin.users.store') }}" class="user-form" enctype="multipart/form-data">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="form-grid">
            <div style="grid-column: 1 / -1; display: flex; align-items: center; gap: 1.5rem; margin-bottom: 0.5rem; padding: 1rem; background: var(--bg-main); border-radius: 12px; border: 1px dashed var(--accent);">
                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 4px 10px var(--shadow);">
                <label style="flex: 1; cursor: pointer;">
                    <span style="display: block; margin-bottom: 0.5rem;">Foto Profil</span>
                    <input type="file" name="profile_photo" accept="image/*" style="font-size: 0.85rem; color: var(--text-muted);">
                    @error('profile_photo')<small class="form-error">{{ $message }}</small>@enderror
                </label>
            </div>

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
                <span>Password {{ $isEdit ? '(kosongkan jika tidak diubah)' : '' }}</span>
                <input type="password" name="password" {{ $isEdit ? '' : 'required' }}>
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
            <button type="submit" class="primary-link">{{ $isEdit ? 'Simpan Perubahan' : 'Buat Akun' }}</button>
        </div>
    </form>
</div>
