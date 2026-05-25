@extends('superadmin.layout')

@push('head')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/superadmin/users-edit.css'])
    @else
        <link rel="stylesheet" href="{{ asset('css/superadmin/users-edit.css') }}">
    @endif
@endpush

@section('title', 'Edit Akun')
@section('page_title', 'Edit Akun')
@section('page_description', 'Ubah data akun, password, dan status aktif.')

@section('content')
    @include('superadmin.users.form')
@endsection
