@section('domain', 'attributes')
@section('domain_name', $select_key_name)
@section('title')
  @yield('domain_name')一覧
@endsection
@extends('dashboard.common')
@include('attributes.menu.page_sidemenu')
@include('dashboard.lists')
@section('page_footer')
  <dt>
    <a class="btn btn-app" href="/@yield('domain')/create?key={{$select_key}}">
      <i class="fa fa-plus"></i>@yield('domain_name')登録
    </a>
  </dt>
@endsection
