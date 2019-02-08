@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')
@include('dashboard.tiles')

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        フィルタリング
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item hr-1">
        @component('components.search_word', ['search_word' => $search_word])
        @endcomponent
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=0" class="nav-link">
          <i class="fa fa-user-check nav-icon"></i>登録済み
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=1" class="nav-link">
          <i class="fa fa-user-lock nav-icon"></i>仮登録
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=9" class="nav-link">
          <i class="fa fa-user-times nav-icon"></i>削除済み
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}" class="nav-link">
          <i class="fa fa-users nav-icon"></i>すべて
        </a>
      </li>
</ul>
@endsection

@section('page_footer')
<dt>
  <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create">
    <i class="fa fa-plus"></i>{{$domain_name}}登録
  </a>
</dt>
@endsection
