@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{$domain_name}}登録
    </a>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        曜日別
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
         <a href="/{{$domain}}" class="nav-link">
           <i class="fa fa-calendar nav-icon"></i>すべて
         </a>
       </li>
      @foreach($attributes['lesson_week'] as $index => $week)
      <li class="nav-item">
         <a href="/{{$domain}}?lesson_week={{$index}}" class="nav-link">
           <i class="fa fa-calendar nav-icon"></i>{{$week}}曜日
         </a>
       </li>
       @endforeach
    </ul>
  </li>
</ul>
@endsection


@section('contents')
<div class="card-header">
  <h3 class="card-title">@yield('title')</h3>
</div>
<div class="card-body table-responsive p-0">
  @component('components.list', ['items' => $items, 'fields' => $fields, 'domain' => $domain, 'domain_name' => $domain_name])
  @endcomponent
</div>
@endsection


@section('page_footer')
  <dt>
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create">
      <i class="fa fa-plus"></i>{{$domain_name}}登録
    </a>
  </dt>
@endsection
