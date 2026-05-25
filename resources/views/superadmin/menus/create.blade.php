@extends('superadmin.layout')

@section('title', 'Tambah Menu')
@section('page_title', 'Tambah Menu')
@section('page_description', 'Buat menu baru, pilih kategori, upload foto, dan atur harga.')

@section('content')
    @include('superadmin.menus.form')
@endsection
