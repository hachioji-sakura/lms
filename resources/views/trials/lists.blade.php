@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')
@section('contents')
<div class="card-header">
  <h3 class="card-title">@yield('title')</h3>
</div>
<div class="card-body table-responsive p-0">
  @component('components.list', ['items' => $items, 'fields' => $fields, 'domain' => $domain, 'domain_name' => $domain_name])
  @endcomponent
</div>
@endsection

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    @component('components.search_word', ['search_word' => $search_word])
    @endcomponent
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        フィルタリング
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="/{{$domain}}?status=new" class="nav-link">
          <i class="fa fa-exclamation-triangle nav-icon"></i>未対応
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=confirm" class="nav-link">
          <i class="fa fa-check-circle nav-icon"></i>予定確認中
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=fix" class="nav-link">
          <i class="fa fa-calendar-alt nav-icon"></i>授業予定
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=cancel" class="nav-link">
          <i class="fa fa-ban nav-icon"></i>キャンセル
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=rest,absence,presence" class="nav-link">
          <i class="fa fa-history nav-icon"></i>履歴
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}" class="nav-link">
          <i class="fa fa-list-alt nav-icon"></i>すべて
        </a>
      </li>
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
  <dt>
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="">
      <i class="fa fa-plus"></i>{{$domain_name}}登録
    </a>
  </dt>
@endsection
