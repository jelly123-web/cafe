@extends('superadmin.layout')

@push('head')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/superadmin/users.css'])
    @else
        <link rel="stylesheet" href="{{ asset('css/superadmin/users.css') }}">
    @endif
@endpush

@section('title', 'Tambah Akun')
@section('page_title', 'Tambah Akun')
@section('page_description', 'Buat akun baru, atur role, dan status aktif.')

@section('content')
    @include('superadmin.users.form')
@endsection
