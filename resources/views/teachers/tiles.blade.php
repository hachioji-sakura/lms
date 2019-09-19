@section('title')
  {{__('labels.teachers_list')}}
@endsection
@extends('dashboard.common')
@include('dashboard.tiles')

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{$domain_name}} {{__('labels.add')}}
    </a>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        {{__('labels.filter')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      @foreach(config('attribute.teacher_status') as $index => $name)
      <li class="nav-item">
        <a href="/{{$domain}}?status={{$index}}" class="nav-link @if((!isset($_status) && $index=='regular') || ($_status==$index)) active @endif">
          <i class="fa fa-list-alt nav-icon"></i>{{$name}}
        </a>
      </li>
      @endforeach
      <li class="nav-item">
        <a href="/{{$domain}}?status=all" class="nav-link @if($_status=='all') active @endif">
          <i class="fa fa-users nav-icon"></i>すべて
        </a>
      </li>
</ul>
<script>
$(function(){
  base.pageSettinged('sidemenu', null);
});
</script>
@endsection

@section('page_footer')
<dt>
  <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create">
    <i class="fa fa-plus"></i>{{$domain_name}}登録
  </a>
</dt>
@endsection
