@extends('superadmin.layout')

@section('title', 'Tambah Kategori')
@section('page_title', 'Tambah Kategori')
@section('page_description', 'Buat kategori menu baru.')

@section('content')
    @include('superadmin.menu-categories.form')
@endsection
