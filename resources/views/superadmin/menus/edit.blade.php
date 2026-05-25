@extends('superadmin.layout')

@section('title', 'Edit Menu')
@section('page_title', 'Edit Menu')
@section('page_description', 'Ubah data menu, kategori, foto, dan harga.')

@section('content')
    @include('superadmin.menus.form')
@endsection
