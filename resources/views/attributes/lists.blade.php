@section('domain', 'attributes')
@section('domain_name', '属性')
@section('title')
  @yield('domain_name')一覧
@endsection
@extends('dashboard.common')
@include('dashboard.menu.page_sidemenu')
@include('dashboard.menu.page_footer')
@include('dashboard.lists')
