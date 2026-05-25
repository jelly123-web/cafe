@extends('superadmin.layout')

@push('head')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/superadmin/users.css'])
    @else
        <link rel="stylesheet" href="{{ asset('css/superadmin/users.css') }}">
    @endif
@endpush

@section('title', 'Akun Pengguna')
@section('page_title', 'Akun Pengguna')
@section('page_description', 'Tambah, edit, dan hapus akun pengguna.')

@section('content')
    <div class="content-toolbar">
        <form method="GET" action="{{ route('superadmin.users.index') }}" class="search-box">
            <input type="text" name="search" placeholder="Cari nama atau username" value="{{ request('search') }}">
            <button type="submit">Cari</button>
        </form>

        <a class="primary-link" href="{{ route('superadmin.users.create') }}">+ Tambah Akun</a>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Akun</h2>
            <span>{{ $users->count() }} akun</span>
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
                <tbody>
                    @foreach ($users as $row)
                        <tr>
                            <td>
                                <strong>{{ $row->name }}</strong>
                            </td>
                            <td>{{ $row->username }}</td>
                            <td><span class="tag">{{ $row->role }}</span></td>
                            <td>
                                <span class="tag {{ $row->is_active ? 'tag-success' : 'tag-muted' }}">
                                    {{ $row->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('superadmin.users.edit', $row) }}">Edit</a>
                                    @if (auth()->id() !== $row->id && $row->role !== 'superadmin')
                                        <form method="POST" action="{{ route('superadmin.users.destroy', $row) }}" onsubmit="return confirm('Hapus akun ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
