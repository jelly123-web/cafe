@extends('superadmin.layout')

@section('title', 'Hak Akses')
@section('page_title', 'Hak Akses')
@section('page_description', 'Atur permission akun secara terpisah dari data akun pengguna.')

@section('content')
    <div class="content-toolbar">
        <form method="GET" action="{{ route('superadmin.access.index') }}" class="search-box">
            <input type="text" name="search" placeholder="Cari nama atau username" value="{{ request('search') }}">
            <button type="submit">Cari</button>
        </form>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Hak Akses</h2>
            <span>{{ $users->count() }} akun</span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Hak Akses</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $row)
                        <tr>
                            <td><strong>{{ $row->name }}</strong></td>
                            <td>{{ $row->username }}</td>
                            <td><span class="tag">{{ $row->role }}</span></td>
                            <td>
                                <div class="perm-list">
                                    @if ($row->role === 'superadmin')
                                        <span class="tag tag-success">Semua akses</span>
                                    @else
                                        @foreach ($permissionDefinitions as $key => $label)
                                            @if (data_get($row->permissions, $key))
                                                <span class="tag">{{ $label }}</span>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('superadmin.access.edit', $row) }}">Atur Akses</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
