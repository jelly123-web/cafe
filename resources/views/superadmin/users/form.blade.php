@php
    $isEdit = isset($mode) && $mode === 'edit';
@endphp

<div class="panel">
    <form method="POST" action="{{ $isEdit ? route('superadmin.users.update', $user) : route('superadmin.users.store') }}" class="user-form">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="form-grid">
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
                    @foreach (['superadmin' => 'Superadmin', 'admin' => 'Admin', 'staff' => 'Staff'] as $value => $label)
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
                <span>Akun aktif</span>
            </label>
        </div>

        <div class="form-actions">
            <a href="{{ route('superadmin.users.index') }}" class="secondary-link">Batal</a>
            <button type="submit" class="primary-link">{{ $isEdit ? 'Simpan Perubahan' : 'Buat Akun' }}</button>
        </div>
    </form>
</div>
