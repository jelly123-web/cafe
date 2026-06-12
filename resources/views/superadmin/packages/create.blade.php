@extends('superadmin.layout')

@push('head')
    <style>
        .panel {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
        }
    </style>
@endpush

@section('title', 'Tambah Paket')
@section('kicker', 'Superadmin')
@section('page_title', 'Tambah Paket Makanan')
@section('page_description', '')

@section('content')
    @include('superadmin.packages.form', [
        'package' => $package,
        'menus' => $menus,
        'selectedMenus' => $selectedMenus,
        'mode' => $mode,
    ])
@endsection
