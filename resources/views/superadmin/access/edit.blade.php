@extends('superadmin.layout')

@section('title', 'Atur Hak Akses')
@section('page_title', 'Atur Hak Akses')
@section('page_description', 'Pilih fitur yang boleh dipakai oleh akun ini.')

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('superadmin.access.update', $user) }}" class="user-form">
            @csrf
            @method('PUT')

            <div class="panel-head">
                <h2>{{ $user->name }} - {{ $user->username }}</h2>
                <span>{{ $user->role }}</span>
            </div>

            <div class="permissions-grid">
                @foreach ($permissionDefinitions as $key => $label)
                    <label class="permission-item">
                        <input type="checkbox" name="permissions[{{ $key }}]" value="1" @checked(data_get($user->permissions, $key))>
                        <span>
                            <strong>{{ $label }}</strong>
                            <small>{{ $key }}</small>
                        </span>
                    </label>
                @endforeach
            </div>

            <div class="form-actions mt-18">
                <a href="{{ route('superadmin.access.index') }}" class="secondary-link">Batal</a>
                <button type="submit" class="primary-link">Simpan Hak Akses</button>
            </div>
        </form>
    </div>
@endsection
