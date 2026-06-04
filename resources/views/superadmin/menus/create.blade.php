@extends('superadmin.layout')

@section('title', 'Tambah Menu')
@section('page_title', 'Tambah Menu')
@section('page_description', 'Isi informasi menu baru beserta harga dan fotonya.')

@section('content')
    @include('superadmin.menus.form')
@endsection
